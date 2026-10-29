const bcrypt = require('bcryptjs');
const express = require('express');
const { db } = require('../db');
const { APPLICATION_STATUSES } = require('../constants');
const { parseId } = require('../helpers');
const { readInternship, readOrganization } = require('../validate');
const {
  listInternships,
  getInternship,
  listOrganizations,
  getOrganization,
  getApplication,
  listApplications,
  listStudents,
  listMessages,
  dashboard
} = require('../queries');
const { flash, checkCsrf, requireAdmin, notFound } = require('../middleware');

const router = express.Router();
router.use(requireAdmin);

router.get('/', (req, res) => {
  const hash = db.prepare('SELECT password_hash FROM users WHERE id = ?').get(req.user.id).password_hash;
  res.render('admin/dashboard', {
    title: 'Office',
    stats: dashboard(),
    defaultPassword: bcrypt.compareSync('admin123', hash)
  });
});

router.get('/students', (req, res) => {
  res.render('admin/students', { title: 'Students', students: listStudents() });
});

router.post('/students/:id/delete', checkCsrf, (req, res) => {
  const id = parseId(req.params.id);
  const student = id && db.prepare("SELECT id, role FROM users WHERE id = ?").get(id);
  if (!student || student.role !== 'student') {
    flash(req, 'bad', 'That student is not on the register.');
    return res.redirect('/admin/students');
  }
  db.prepare('DELETE FROM users WHERE id = ? AND role = \'student\'').run(id);
  flash(req, 'ok', 'The student account has been removed.');
  res.redirect('/admin/students');
});

router.get('/organizations', (req, res) => {
  res.render('admin/organizations', { title: 'Hosts', hosts: listOrganizations() });
});

router.get('/organizations/new', (req, res) => {
  res.render('admin/organization-form', { title: 'New host', organizationId: null });
});

router.post('/organizations', checkCsrf, (req, res) => {
  saveOrganization(req, res, null);
});

router.get('/organizations/:id/edit', (req, res) => {
  const id = parseId(req.params.id);
  const host = id && getOrganization(id);
  if (!host) return notFound(res);
  res.render('admin/organization-form', {
    title: 'Edit host',
    organizationId: host.id,
    form: host
  });
});

router.post('/organizations/:id', checkCsrf, (req, res) => {
  const id = parseId(req.params.id);
  if (!id || !getOrganization(id)) return notFound(res);
  saveOrganization(req, res, id);
});

router.post('/organizations/:id/delete', checkCsrf, (req, res) => {
  const id = parseId(req.params.id);
  const posts = id && db.prepare('SELECT COUNT(*) AS c FROM internships WHERE organization_id = ?').get(id);
  if (!id || !getOrganization(id)) {
    flash(req, 'bad', 'That host is not in the directory.');
    return res.redirect('/admin/organizations');
  }
  if (posts.c > 0) {
    flash(req, 'bad', 'Remove this host’s internships before removing the host.');
    return res.redirect('/admin/organizations');
  }
  db.prepare('DELETE FROM organizations WHERE id = ?').run(id);
  flash(req, 'ok', 'The host has been removed from the directory.');
  res.redirect('/admin/organizations');
});

router.get('/internships', (req, res) => {
  res.render('admin/internships', {
    title: 'Internships',
    posts: listInternships({ includeClosed: true })
  });
});

router.get('/internships/new', (req, res) => {
  res.render('admin/internship-form', {
    title: 'New internship',
    internshipId: null,
    organizations: listOrganizations(),
    form: { paid: '1', status: 'open', openings: 1, duration_weeks: 12 }
  });
});

router.post('/internships', checkCsrf, (req, res) => {
  saveInternship(req, res, null);
});

router.get('/internships/:id/edit', (req, res) => {
  const id = parseId(req.params.id);
  const post = id && getInternship(id);
  if (!post) return notFound(res);
  res.render('admin/internship-form', {
    title: 'Edit internship',
    internshipId: post.id,
    organizations: listOrganizations(),
    form: post
  });
});

router.post('/internships/:id', checkCsrf, (req, res) => {
  const id = parseId(req.params.id);
  if (!id || !getInternship(id)) return notFound(res);
  saveInternship(req, res, id);
});

router.post('/internships/:id/delete', checkCsrf, (req, res) => {
  const id = parseId(req.params.id);
  if (!id || !getInternship(id)) {
    flash(req, 'bad', 'That internship is not on the board.');
    return res.redirect('/admin/internships');
  }
  db.prepare('DELETE FROM internships WHERE id = ?').run(id);
  flash(req, 'ok', 'The internship and its applications have been removed.');
  res.redirect('/admin/internships');
});

router.get('/applications', (req, res) => {
  const status = APPLICATION_STATUSES.some((item) => item.value === req.query.status)
    ? req.query.status
    : '';
  res.render('admin/applications', {
    title: 'Applications',
    applications: listApplications(status),
    status
  });
});

router.get('/applications/:id', (req, res) => {
  const id = parseId(req.params.id);
  const application = id && getApplication(id);
  if (!application) return notFound(res);
  res.render('admin/application', { title: 'Application', application });
});

router.post('/applications/:id', checkCsrf, (req, res) => {
  const id = parseId(req.params.id);
  const application = id && getApplication(id);
  if (!application) return notFound(res);
  const status = APPLICATION_STATUSES.some((item) => item.value === req.body.status)
    ? req.body.status
    : '';
  if (!status) {
    flash(req, 'bad', 'Choose a decision the office recognises.');
    return res.redirect(`/admin/applications/${id}`);
  }
  db.prepare('UPDATE applications SET status = ? WHERE id = ?').run(status, id);
  flash(req, 'ok', 'The decision has been saved.');
  res.redirect(`/admin/applications/${id}`);
});

router.get('/messages', (req, res) => {
  res.render('admin/messages', { title: 'Messages', messages: listMessages() });
});

function saveOrganization(req, res, id) {
  const { errors, data } = readOrganization(req.body);
  if (errors.length) {
    return res.status(400).render('admin/organization-form', {
      title: id ? 'Edit host' : 'New host',
      organizationId: id,
      errors,
      form: req.body
    });
  }
  if (id) {
    db.prepare(`
      UPDATE organizations
      SET name = ?, industry = ?, description = ?, website = ?, location = ?, contact_email = ?
      WHERE id = ?
    `).run(data.name, data.industry, data.description, data.website, data.location, data.contact_email, id);
    flash(req, 'ok', 'The host has been updated.');
  } else {
    db.prepare(`
      INSERT INTO organizations (name, industry, description, website, location, contact_email)
      VALUES (?, ?, ?, ?, ?, ?)
    `).run(data.name, data.industry, data.description, data.website, data.location, data.contact_email);
    flash(req, 'ok', 'The host has been added to the directory.');
  }
  res.redirect('/admin/organizations');
}

function saveInternship(req, res, id) {
  const organizations = listOrganizations();
  const { errors, data } = readInternship(req.body, organizations.map((org) => org.id));
  if (errors.length) {
    return res.status(400).render('admin/internship-form', {
      title: id ? 'Edit internship' : 'New internship',
      internshipId: id,
      organizations,
      errors,
      form: req.body
    });
  }
  const values = [
    data.organization_id,
    data.title,
    data.field,
    data.description,
    data.requirements,
    data.location,
    data.mode,
    data.duration_weeks,
    data.paid,
    data.stipend,
    data.openings,
    data.deadline,
    data.status
  ];
  if (id) {
    db.prepare(`
      UPDATE internships SET
        organization_id = ?, title = ?, field = ?, description = ?, requirements = ?,
        location = ?, mode = ?, duration_weeks = ?, paid = ?, stipend = ?,
        openings = ?, deadline = ?, status = ?
      WHERE id = ?
    `).run(...values, id);
    flash(req, 'ok', 'The internship has been updated.');
  } else {
    db.prepare(`
      INSERT INTO internships (
        organization_id, title, field, description, requirements, location, mode,
        duration_weeks, paid, stipend, openings, deadline, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `).run(...values);
    flash(req, 'ok', 'The internship is on the board.');
  }
  res.redirect('/admin/internships');
}

module.exports = router;
