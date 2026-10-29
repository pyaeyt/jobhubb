const path = require('path');
const express = require('express');
const session = require('express-session');
const { FIELDS, YEARS, MODES, APPLICATION_STATUSES } = require('./src/constants');
const { attachLocals } = require('./src/middleware');

require('./src/db');

const app = express();
const port = process.env.PORT || 3000;

app.disable('x-powered-by');
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.use((req, res, next) => {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('Referrer-Policy', 'same-origin');
  res.setHeader('X-Frame-Options', 'DENY');
  res.setHeader(
    'Content-Security-Policy',
    "default-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; form-action 'self'; frame-ancestors 'none'"
  );
  next();
});

app.use(express.urlencoded({ extended: false, limit: '200kb' }));
app.use(express.static(path.join(__dirname, 'public')));
app.use(session({
  name: 'jobhub.sid',
  secret: process.env.SESSION_SECRET || 'jobhub-campus-change-this-secret',
  resave: false,
  saveUninitialized: false,
  cookie: {
    httpOnly: true,
    sameSite: 'lax',
    maxAge: 1000 * 60 * 60 * 8
  }
}));
app.use(attachLocals);

app.locals.fields = FIELDS;
app.locals.years = YEARS;
app.locals.modes = MODES;
app.locals.applicationStatuses = APPLICATION_STATUSES;

app.use(require('./src/routes/pages'));
app.use('/admin', require('./src/routes/admin'));

app.use((req, res) => {
  res.status(404).render('error', {
    title: 'Not found',
    message: 'That page is not on the board.'
  });
});

app.use((err, req, res, next) => {
  console.error(err);
  if (res.headersSent) return next(err);
  res.status(500).render('error', {
    title: 'Something went wrong',
    message: 'The office could not complete that request.'
  });
});

app.listen(port, () => {
  console.log(`InternHub is running at http://localhost:${port}`);
  console.log('Registrar  admin@jobhub.example  /  admin123');
  console.log('Student    elena.voss@student.example  /  student123');
  console.log('Database   data/jobhub.db  (SQLite)');
});
