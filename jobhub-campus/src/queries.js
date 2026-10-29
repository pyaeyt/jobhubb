const { db } = require('./db');

function listInternships({
  q = '',
  field = '',
  mode = '',
  organizationId = null,
  includeClosed = false,
  limit = null
} = {}) {
  const where = [];
  const params = [];

  if (!includeClosed) where.push("i.status = 'open'");

  const term = String(q || '').trim().slice(0, 80);
  if (term) {
    const like = `%${term.replace(/[%_]/g, '')}%`;
    where.push('(i.title LIKE ? OR i.description LIKE ? OR o.name LIKE ? OR i.location LIKE ?)');
    params.push(like, like, like, like);
  }
  if (field) {
    where.push('i.field = ?');
    params.push(field);
  }
  if (mode) {
    where.push('i.mode = ?');
    params.push(mode);
  }
  if (organizationId) {
    where.push('i.organization_id = ?');
    params.push(organizationId);
  }

  let sql = `
    SELECT i.*, o.name AS organization_name,
      (SELECT COUNT(*) FROM applications a WHERE a.internship_id = i.id) AS application_count
    FROM internships i
    JOIN organizations o ON o.id = i.organization_id
    ${where.length ? `WHERE ${where.join(' AND ')}` : ''}
    ORDER BY i.deadline ASC, i.id ASC
  `;

  if (Number.isInteger(limit)) {
    sql += ' LIMIT ?';
    params.push(limit);
  }

  return db.prepare(sql).all(...params);
}

function getInternship(id) {
  return db.prepare(`
    SELECT i.*, o.name AS organization_name, o.description AS organization_description,
           o.website, o.location AS organization_location, o.industry, o.contact_email
    FROM internships i
    JOIN organizations o ON o.id = i.organization_id
    WHERE i.id = ?
  `).get(id);
}

function listOrganizations() {
  return db.prepare(`
    SELECT o.*,
      (SELECT COUNT(*) FROM internships i WHERE i.organization_id = o.id AND i.status = 'open') AS open_count
    FROM organizations o
    ORDER BY o.name COLLATE NOCASE
  `).all();
}

function getOrganization(id) {
  return db.prepare('SELECT * FROM organizations WHERE id = ?').get(id);
}

function findUserByEmail(email) {
  return db.prepare('SELECT * FROM users WHERE email = ?').get(email);
}

function findApplication(internshipId, userId) {
  return db.prepare('SELECT * FROM applications WHERE internship_id = ? AND user_id = ?').get(internshipId, userId);
}

function getApplication(id) {
  return db.prepare(`
    SELECT a.*, u.full_name, u.email, u.student_id, u.faculty, u.year_of_study, u.phone,
           i.title AS internship_title, o.name AS organization_name
    FROM applications a
    JOIN users u ON u.id = a.user_id
    JOIN internships i ON i.id = a.internship_id
    JOIN organizations o ON o.id = i.organization_id
    WHERE a.id = ?
  `).get(id);
}

function listUserApplications(userId) {
  return db.prepare(`
    SELECT a.*, i.title AS internship_title, i.location, o.name AS organization_name
    FROM applications a
    JOIN internships i ON i.id = a.internship_id
    JOIN organizations o ON o.id = i.organization_id
    WHERE a.user_id = ?
    ORDER BY a.applied_at DESC
  `).all(userId);
}

function listApplications(status) {
  const params = [];
  let where = '';
  if (status) {
    where = 'WHERE a.status = ?';
    params.push(status);
  }
  return db.prepare(`
    SELECT a.*, u.full_name, u.email, u.student_id, u.faculty,
           i.title AS internship_title, o.name AS organization_name
    FROM applications a
    JOIN users u ON u.id = a.user_id
    JOIN internships i ON i.id = a.internship_id
    JOIN organizations o ON o.id = i.organization_id
    ${where}
    ORDER BY a.applied_at DESC
  `).all(...params);
}

function listStudents() {
  return db.prepare(`
    SELECT id, full_name, email, student_id, faculty, year_of_study, phone, created_at
    FROM users
    WHERE role = 'student'
    ORDER BY full_name COLLATE NOCASE
  `).all();
}

function listMessages() {
  return db.prepare('SELECT * FROM messages ORDER BY created_at DESC').all();
}

function dashboard() {
  const count = (sql) => db.prepare(sql).get().c;
  return {
    students: count("SELECT COUNT(*) AS c FROM users WHERE role = 'student'"),
    hosts: count('SELECT COUNT(*) AS c FROM organizations'),
    openInternships: count("SELECT COUNT(*) AS c FROM internships WHERE status = 'open'"),
    awaiting: count("SELECT COUNT(*) AS c FROM applications WHERE status IN ('submitted', 'review')"),
    recent: listApplications('').slice(0, 5)
  };
}

function publicStats() {
  return {
    open: db.prepare("SELECT COUNT(*) AS c FROM internships WHERE status = 'open'").get().c,
    hosts: db.prepare('SELECT COUNT(*) AS c FROM organizations').get().c,
    fields: db.prepare("SELECT COUNT(DISTINCT field) AS c FROM internships WHERE status = 'open'").get().c
  };
}

module.exports = {
  listInternships,
  getInternship,
  listOrganizations,
  getOrganization,
  findUserByEmail,
  findApplication,
  getApplication,
  listUserApplications,
  listApplications,
  listStudents,
  listMessages,
  dashboard,
  publicStats
};
