const crypto = require('crypto');
const { db } = require('./db');
const {
  paragraphs,
  deadlinePassed,
  formatDate,
  formatStipend,
  statusLabel
} = require('./helpers');

function flash(req, type, text) {
  req.session.flash = { type, text };
}

function attachLocals(req, res, next) {
  if (!req.session.csrf) req.session.csrf = crypto.randomBytes(24).toString('hex');

  let user = null;
  if (req.session.userId) {
    user = db.prepare(`
      SELECT id, full_name, email, role, student_id, faculty, year_of_study, phone
      FROM users WHERE id = ?
    `).get(req.session.userId);
    if (!user) delete req.session.userId;
  }

  req.user = user;
  res.locals.user = user;
  res.locals.csrf = req.session.csrf;
  res.locals.flash = req.session.flash || null;
  delete req.session.flash;
  res.locals.currentPath = req.path;
  res.locals.errors = [];
  res.locals.form = {};
  res.locals.paragraphs = paragraphs;
  res.locals.deadlinePassed = deadlinePassed;
  res.locals.formatDate = formatDate;
  res.locals.formatStipend = formatStipend;
  res.locals.statusLabel = statusLabel;
  next();
}

function checkCsrf(req, res, next) {
  const sent = req.body && req.body._csrf;
  if (!sent || sent !== req.session.csrf) {
    return res.status(403).render('error', {
      title: 'Form expired',
      message: 'Please go back and submit the form again.'
    });
  }
  next();
}

function requireUser(req, res, next) {
  if (!req.user) {
    req.session.returnTo = req.originalUrl;
    flash(req, 'bad', 'Sign in to continue.');
    return res.redirect('/login');
  }
  next();
}

function requireStudent(req, res, next) {
  if (!req.user) {
    req.session.returnTo = req.originalUrl;
    flash(req, 'bad', 'Sign in with your student account to continue.');
    return res.redirect('/login');
  }
  if (req.user.role !== 'student') {
    flash(req, 'bad', 'Only student accounts can do that.');
    return res.redirect('/');
  }
  next();
}

function requireAdmin(req, res, next) {
  if (!req.user) {
    req.session.returnTo = req.originalUrl;
    flash(req, 'bad', 'Sign in as the registrar to open the office.');
    return res.redirect('/login');
  }
  if (req.user.role !== 'admin') {
    flash(req, 'bad', 'The office is reserved for the registrar.');
    return res.redirect('/');
  }
  next();
}

function notFound(res) {
  return res.status(404).render('error', {
    title: 'Not found',
    message: 'That page is not on the board.'
  });
}

module.exports = {
  flash,
  attachLocals,
  checkCsrf,
  requireUser,
  requireStudent,
  requireAdmin,
  notFound
};
