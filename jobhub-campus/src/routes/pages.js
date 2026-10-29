const crypto = require('crypto');
const fs = require('fs');
const path = require('path');
const bcrypt = require('bcryptjs');
const express = require('express');
const multer = require('multer');
const { db, resumeDir } = require('../db');
const { FIELDS, MODES } = require('../constants');
const { parseId, deadlinePassed } = require('../helpers');
const { readStudent, readProfile, readMessage, readCoverLetter, clean } = require('../validate');
const {
  listInternships,
  getInternship,
  listOrganizations,
  getOrganization,
  findUserByEmail,
  findApplication,
  getApplication,
  listUserApplications,
  publicStats
} = require('../queries');
const {
  flash,
  checkCsrf,
  requireUser,
  requireStudent,
  notFound
} = require('../middleware');

const router = express.Router();

const upload = multer({
  storage: multer.diskStorage({
    destination: resumeDir,
    filename: (req, file, cb) => {
      const ext = path.extname(file.originalname).toLowerCase();
      cb(null, `${Date.now()}-${crypto.randomBytes(4).toString('hex')}${ext}`);
    }
  }),
  limits: { fileSize: 5 * 1024 * 1024 },
  fileFilter: (req, file, cb) => {
    const ext = path.extname(file.originalname).toLowerCase();
    if (['.pdf', '.doc', '.docx'].includes(ext)) return cb(null, true);
    cb(new Error('The résumé must be a PDF or Word document.'));
  }
});

function safeReturn(url, role) {
  if (!url || !url.startsWith('/') || url.startsWith('//')) {
    return role === 'admin' ? '/admin' : '/internships';
  }
  if (role !== 'admin' && url.startsWith('/admin')) return '/internships';
  if (role === 'admin') return '/admin';
  return url;
}

router.get('/', (req, res) => {
  res.render('home', {
    title: 'Home',
    posts: listInternships({ limit: 4 }),
    hosts: listOrganizations(),
    stats: publicStats()
  });
});

router.get('/internships', (req, res) => {
  const field = FIELDS.includes(req.query.field) ? req.query.field : '';
  const mode = MODES.includes(req.query.mode) ? req.query.mode : '';
  const q = clean(req.query.q || '');
  const posts = listInternships({ q, field, mode });
  res.render('internships', {
    title: 'Internships',
    posts,
    filters: { q, field, mode }
  });
});

router.get('/internships/:id', (req, res) => {
  const id = parseId(req.params.id);
  const post = id && getInternship(id);
  if (!post) return notFound(res);
  const mine = req.user && req.user.role === 'student'
    ? findApplication(post.id, req.user.id)
    : null;
  res.render('internship', { title: post.title, post, mine });
});

router.get('/organizations', (req, res) => {
  res.render('organizations', {
    title: 'Hosts',
    hosts: listOrganizations()
  });
});

router.get('/organizations/:id', (req, res) => {
  const id = parseId(req.params.id);
  const host = id && getOrganization(id);
  if (!host) return notFound(res);
  res.render('organization', {
    title: host.name,
    host,
    posts: listInternships({ organizationId: host.id })
  });
});

router.get('/about', (req, res) => {
  res.render('about', { title: 'About' });
});

router.get('/contact', (req, res) => {
  res.render('contact', { title: 'Contact' });
});

router.post('/contact', checkCsrf, (req, res) => {
  if (clean(req.body.website_confirm)) {
    flash(req, 'ok', 'Your message is with the registrar.');
    return res.redirect('/contact');
  }
  const { errors, data } = readMessage(req.body);
  if (errors.length) {
    return res.status(400).render('contact', { title: 'Contact', errors, form: req.body });
  }
  db.prepare('INSERT INTO messages (name, email, subject, body) VALUES (?, ?, ?, ?)')
    .run(data.name, data.email, data.subject, data.message);
  flash(req, 'ok', 'Your message is with the registrar.');
  res.redirect('/contact');
});

router.get('/login', (req, res) => {
  if (req.user) return res.redirect(req.user.role === 'admin' ? '/admin' : '/internships');
  res.render('login', { title: 'Sign in' });
});

router.post('/login', checkCsrf, (req, res, next) => {
  const email = clean(req.body.email).toLowerCase();
  const password = String(req.body.password || '');
  const user = email ? findUserByEmail(email) : null;
  if (!user || !bcrypt.compareSync(password, user.password_hash)) {
    return res.status(401).render('login', {
      title: 'Sign in',
      form: { email },
      errors: ['Those details do not match our register.']
    });
  }

  const destination = safeReturn(req.session.returnTo, user.role);
  req.session.regenerate((err) => {
    if (err) return next(err);
    req.session.userId = user.id;
    req.session.csrf = crypto.randomBytes(24).toString('hex');
    req.session.flash = {
      type: 'ok',
      text: user.role === 'admin' ? 'The office is open.' : 'You are signed in.'
    };
    res.redirect(destination);
  });
});

router.post('/logout', checkCsrf, (req, res) => {
  req.session.destroy(() => {
    res.clearCookie('jobhub.sid', { path: '/' });
    res.redirect('/');
  });
});

router.get('/register', (req, res) => {
  if (req.user) return res.redirect(req.user.role === 'admin' ? '/admin' : '/internships');
  res.render('register', { title: 'Register' });
});

router.post('/register', checkCsrf, (req, res, next) => {
  const { errors, data } = readStudent(req.body);
  if (findUserByEmail(data.email)) errors.push('That email is already registered. Sign in instead.');
  const takenId = db.prepare('SELECT id FROM users WHERE student_id = ?').get(data.student_id);
  if (takenId) errors.push('That student number is already registered.');
  if (errors.length) {
    return res.status(400).render('register', { title: 'Register', errors, form: req.body });
  }

  const info = db.prepare(`
    INSERT INTO users (full_name, email, password_hash, student_id, faculty, year_of_study, role)
    VALUES (?, ?, ?, ?, ?, ?, 'student')
  `).run(
    data.full_name,
    data.email,
    bcrypt.hashSync(data.password, 10),
    data.student_id,
    data.faculty,
    data.year_of_study
  );

  req.session.regenerate((err) => {
    if (err) return next(err);
    req.session.userId = Number(info.lastInsertRowid);
    req.session.csrf = crypto.randomBytes(24).toString('hex');
    req.session.flash = { type: 'ok', text: 'Your student account is ready.' };
    res.redirect('/internships');
  });
});

router.get('/apply/:id', requireStudent, (req, res) => {
  const id = parseId(req.params.id);
  const post = id && getInternship(id);
  if (!post) return notFound(res);
  const mine = findApplication(post.id, req.user.id);
  if (mine) {
    flash(req, 'ok', 'You have already applied for this internship.');
    return res.redirect(`/internships/${post.id}`);
  }
  if (post.status !== 'open' || deadlinePassed(post.deadline)) {
    flash(req, 'bad', 'This internship is no longer taking applications.');
    return res.redirect(`/internships/${post.id}`);
  }
  res.render('apply', { title: 'Apply', post });
});

router.post('/apply/:id', requireStudent, (req, res) => {
  const id = parseId(req.params.id);
  const post = id && getInternship(id);
  if (!post) return notFound(res);
  if (findApplication(post.id, req.user.id)) {
    flash(req, 'ok', 'You have already applied for this internship.');
    return res.redirect(`/internships/${post.id}`);
  }
  if (post.status !== 'open' || deadlinePassed(post.deadline)) {
    flash(req, 'bad', 'This internship is no longer taking applications.');
    return res.redirect(`/internships/${post.id}`);
  }

  upload.single('resume')(req, res, (err) => {
    if (err) {
      flash(req, 'bad', err.message || 'The résumé could not be uploaded.');
      return res.redirect(`/apply/${post.id}`);
    }
    if (!req.body || req.body._csrf !== req.session.csrf) {
      if (req.file) fs.unlink(req.file.path, () => {});
      return res.status(403).render('error', {
        title: 'Form expired',
        message: 'Please go back and submit the form again.'
      });
    }

    const { error, letter } = readCoverLetter(req.body.cover_letter);
    const errors = [];
    if (error) errors.push(error);
    if (!req.file) errors.push('Attach a résumé as a PDF or Word document.');
    if (errors.length) {
      if (req.file) fs.unlink(req.file.path, () => {});
      return res.status(400).render('apply', {
        title: 'Apply',
        post,
        errors,
        form: { cover_letter: req.body.cover_letter }
      });
    }

    try {
      db.prepare(`
        INSERT INTO applications (internship_id, user_id, cover_letter, resume_filename, resume_original)
        VALUES (?, ?, ?, ?, ?)
      `).run(
        post.id,
        req.user.id,
        letter,
        req.file.filename,
        path.basename(req.file.originalname).slice(0, 120)
      );
    } catch (insertErr) {
      if (req.file) fs.unlink(req.file.path, () => {});
      if (String(insertErr.message).includes('UNIQUE')) {
        flash(req, 'ok', 'You have already applied for this internship.');
        return res.redirect(`/internships/${post.id}`);
      }
      throw insertErr;
    }

    flash(req, 'ok', 'Your application is with the registrar.');
    res.redirect('/applications');
  });
});

router.get('/applications', requireStudent, (req, res) => {
  res.render('applications', {
    title: 'My applications',
    applications: listUserApplications(req.user.id)
  });
});

router.get('/profile', requireUser, (req, res) => {
  res.render('profile', {
    title: 'Profile',
    form: {
      full_name: req.user.full_name,
      email: req.user.email,
      phone: req.user.phone || '',
      faculty: req.user.faculty || '',
      year_of_study: req.user.year_of_study || ''
    }
  });
});

router.post('/profile', requireUser, checkCsrf, (req, res) => {
  const { errors, data } = readProfile(req.body, req.user.role);
  const other = db.prepare('SELECT id FROM users WHERE email = ? AND id <> ?').get(data.email, req.user.id);
  if (other) errors.push('That email is already registered.');
  if (errors.length) {
    return res.status(400).render('profile', { title: 'Profile', errors, form: req.body });
  }

  db.prepare(`
    UPDATE users
    SET full_name = ?, email = ?, phone = ?, faculty = ?, year_of_study = ?
    WHERE id = ?
  `).run(data.full_name, data.email, data.phone, data.faculty, data.year_of_study, req.user.id);

  if (data.password) {
    db.prepare('UPDATE users SET password_hash = ? WHERE id = ?')
      .run(bcrypt.hashSync(data.password, 10), req.user.id);
  }

  flash(req, 'ok', 'Your details have been saved.');
  res.redirect('/profile');
});

router.get('/resumes/:id', requireUser, (req, res) => {
  const id = parseId(req.params.id);
  const application = id && getApplication(id);
  if (!application) return notFound(res);
  const allowed = req.user.role === 'admin' || req.user.id === application.user_id;
  if (!allowed) return res.status(403).render('error', {
    title: 'Not available',
    message: 'That résumé belongs to another student.'
  });
  if (!/^[\w.-]+$/.test(application.resume_filename)) return notFound(res);
  const filePath = path.resolve(resumeDir, application.resume_filename);
  if (path.dirname(filePath) !== path.resolve(resumeDir)) return notFound(res);
  if (!fs.existsSync(filePath)) {
    return res.status(404).render('error', {
      title: 'Missing file',
      message: 'The résumé file is no longer on record.'
    });
  }
  res.download(filePath, application.resume_original);
});

module.exports = router;
