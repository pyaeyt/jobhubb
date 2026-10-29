const fs = require('fs');
const path = require('path');
const bcrypt = require('bcryptjs');

let DatabaseSync;
try {
  ({ DatabaseSync } = require('node:sqlite'));
} catch (err) {
  console.error('InternHub needs Node.js 22.13 or newer, which includes SQLite.');
  process.exit(1);
}

const dataDir = path.join(__dirname, '..', 'data');
const resumeDir = path.join(dataDir, 'resumes');
fs.mkdirSync(resumeDir, { recursive: true });

const db = new DatabaseSync(path.join(dataDir, 'jobhub.db'));
db.exec('PRAGMA foreign_keys = ON');

db.exec(`
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  full_name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  student_id TEXT UNIQUE,
  faculty TEXT,
  year_of_study TEXT,
  phone TEXT,
  role TEXT NOT NULL DEFAULT 'student' CHECK (role IN ('student', 'admin')),
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS organizations (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  industry TEXT NOT NULL,
  description TEXT NOT NULL,
  website TEXT,
  location TEXT NOT NULL,
  contact_email TEXT,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS internships (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organization_id INTEGER NOT NULL REFERENCES organizations(id) ON DELETE RESTRICT,
  title TEXT NOT NULL,
  field TEXT NOT NULL,
  description TEXT NOT NULL,
  requirements TEXT NOT NULL,
  location TEXT NOT NULL,
  mode TEXT NOT NULL,
  duration_weeks INTEGER NOT NULL,
  paid INTEGER NOT NULL DEFAULT 1,
  stipend INTEGER,
  openings INTEGER NOT NULL DEFAULT 1,
  deadline TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'open' CHECK (status IN ('open', 'closed')),
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS applications (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  internship_id INTEGER NOT NULL REFERENCES internships(id) ON DELETE CASCADE,
  user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  cover_letter TEXT NOT NULL,
  resume_filename TEXT NOT NULL,
  resume_original TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'submitted' CHECK (status IN ('submitted', 'review', 'offered', 'declined')),
  applied_at TEXT NOT NULL DEFAULT (datetime('now')),
  UNIQUE (internship_id, user_id)
);

CREATE TABLE IF NOT EXISTS messages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  subject TEXT NOT NULL,
  body TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE INDEX IF NOT EXISTS idx_applications_user ON applications(user_id);
CREATE INDEX IF NOT EXISTS idx_internships_org ON internships(organization_id);
CREATE INDEX IF NOT EXISTS idx_internships_status ON internships(status);
`);

function ensureAdmin() {
  const existing = db.prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1").get();
  if (existing) return;
  db.prepare(`
    INSERT INTO users (full_name, email, password_hash, role)
    VALUES (?, ?, ?, 'admin')
  `).run('Career Registrar', 'admin@jobhub.example', bcrypt.hashSync('admin123', 10));
}

function ensureStudent(student) {
  const existing = db.prepare('SELECT id FROM users WHERE email = ?').get(student.email);
  if (existing) return existing.id;
  const info = db.prepare(`
    INSERT INTO users (full_name, email, password_hash, student_id, faculty, year_of_study, role)
    VALUES (?, ?, ?, ?, ?, ?, 'student')
  `).run(
    student.full_name,
    student.email,
    bcrypt.hashSync('student123', 10),
    student.student_id,
    student.faculty,
    student.year_of_study
  );
  return Number(info.lastInsertRowid);
}

function seedDemo() {
  const orgCount = db.prepare('SELECT COUNT(*) AS c FROM organizations').get().c;
  if (orgCount > 0) return;

  const organizations = [
    {
      name: 'Lumen Laboratory',
      industry: 'Software',
      location: 'Riverside workshop',
      website: 'https://example.com/lumen',
      contact_email: 'studio@lumen.example',
      description: 'A small software workshop that takes two computer science students each term. Interns sit with the engineers, ship one modest feature, and keep a written note of what they learned.'
    },
    {
      name: 'Northbridge Studio',
      industry: 'Design',
      location: 'Old print quarter',
      website: 'https://example.com/northbridge',
      contact_email: 'desk@northbridge.example',
      description: 'A design studio known for quiet editorial work. Students join a live client project and present their studies to the studio each Friday.'
    },
    {
      name: 'Harbor & Hale',
      industry: 'Finance',
      location: 'Harbor offices',
      website: 'https://example.com/harbor',
      contact_email: 'careers@harborhale.example',
      description: 'A long-standing accounting house. Interns prepare schedules, sit in on client meetings, and learn how a file moves from draft to signature.'
    },
    {
      name: 'Fieldnote Press',
      industry: 'Publishing',
      location: 'University press annex',
      website: 'https://example.com/fieldnote',
      contact_email: 'editors@fieldnote.example',
      description: 'An independent press that still edits on paper before anything is set in type. Interns read submissions and keep the editorial log.'
    },
    {
      name: 'Meridian Clinic',
      industry: 'Health research',
      location: 'University laboratory',
      website: 'https://example.com/meridian',
      contact_email: 'lab@meridian.example',
      description: 'A campus research clinic. Students help with records, literature notes, and the weekly lab meeting. The work is careful and closely supervised.'
    },
    {
      name: 'Atlas Civic Works',
      industry: 'Engineering',
      location: 'City works yard',
      website: 'https://example.com/atlas',
      contact_email: 'works@atlas.example',
      description: 'The municipal engineering office. Interns visit sites, update drawings, and learn how a public project is documented.'
    },
    {
      name: 'Wren Chambers',
      industry: 'Law',
      location: 'Court district',
      website: 'https://example.com/wren',
      contact_email: 'clerks@wren.example',
      description: 'A small set of chambers. Students prepare bundles, check citations, and observe how a case is put in order.'
    },
    {
      name: 'The Reading Room',
      industry: 'Archives',
      location: 'Main library',
      website: 'https://example.com/reading-room',
      contact_email: 'archives@readingroom.example',
      description: 'The university archive. Interns catalogue letters and photographs and write short finding aids for the next term’s readers.'
    }
  ];

  const insertOrg = db.prepare(`
    INSERT INTO organizations (name, industry, description, website, location, contact_email)
    VALUES (?, ?, ?, ?, ?, ?)
  `);

  const internships = [
    {
      host: 'Lumen Laboratory',
      title: 'Software Engineering Intern',
      field: 'Computer Science',
      location: 'Riverside workshop',
      mode: 'Hybrid',
      duration_weeks: 12,
      paid: 1,
      stipend: 900,
      openings: 2,
      deadline: '2026-12-18',
      description: 'You will join a small engineering desk for twelve weeks. The term’s work is a student-facing tool already in use: you will fix defects, write a short note for the next intern, and show the result to the studio.\n\nThree days are spent on site. The other two may be done from the university library.',
      requirements: 'Third-year computer science, or a graduate student in the same faculty. You should have built a small web application and be willing to read other people’s code. Bring a résumé and the name of one tutor.'
    },
    {
      host: 'Lumen Laboratory',
      title: 'Data Assistant',
      field: 'Computer Science',
      location: 'Riverside workshop',
      mode: 'On site',
      duration_weeks: 10,
      paid: 1,
      stipend: 750,
      openings: 1,
      deadline: '2027-01-15',
      description: 'The laboratory keeps a decade of project notes in uneven spreadsheets. You will tidy them, write the queries the engineers actually ask for, and leave a short guide behind.\n\nThe work is quiet and exact. It suits a student who likes order more than display.',
      requirements: 'Comfort with tables, SQL, and plain explanations. A statistics course is useful. You do not need to be a specialist.'
    },
    {
      host: 'Northbridge Studio',
      title: 'Product Design Intern',
      field: 'Design',
      location: 'Old print quarter',
      mode: 'Hybrid',
      duration_weeks: 12,
      paid: 1,
      stipend: 800,
      openings: 1,
      deadline: '2026-12-11',
      description: 'You will work on one client study from research through a presented prototype. The studio expects pencil sketches before any screen, and a short written rationale with every round.\n\nFridays are review days. Please keep them clear.',
      requirements: 'A portfolio of three projects, even if they are from studio class. Familiarity with type, grid, and a prototyping tool. Second year and above.'
    },
    {
      host: 'Harbor & Hale',
      title: 'Finance Intern',
      field: 'Business',
      location: 'Harbor offices',
      mode: 'On site',
      duration_weeks: 12,
      paid: 1,
      stipend: 850,
      openings: 2,
      deadline: '2027-01-08',
      description: 'You will prepare working papers for two client files and attend the meetings where those files are discussed. A senior clerk reviews every page before it leaves the office.\n\nDress is quiet. Hours follow the office, not the lecture timetable, so arrange your classes first.',
      requirements: 'Business or accounting student, second year or above. Careful arithmetic and a clear written sentence. Prior office experience is welcome and not required.'
    },
    {
      host: 'Harbor & Hale',
      title: 'Accounts Assistant',
      field: 'Business',
      location: 'Harbor offices',
      mode: 'On site',
      duration_weeks: 8,
      paid: 1,
      stipend: 500,
      openings: 1,
      deadline: '2026-11-27',
      description: 'An eight-week place in the bookkeeping room. You will match invoices, file receipts, and learn the monthly close. It is a good first office for a student who has not worked in finance before.',
      requirements: 'First or second year in business. Neat records and reliable attendance. Bring your student number and a short letter from a tutor if you have no previous work.'
    },
    {
      host: 'Fieldnote Press',
      title: 'Editorial Intern',
      field: 'Communications',
      location: 'University press annex',
      mode: 'On site',
      duration_weeks: 8,
      paid: 0,
      stipend: null,
      openings: 2,
      deadline: '2026-12-04',
      description: 'You will read submissions, keep the editorial log, and proof one chapter in galley. The press cannot pay a stipend. It does offer a written reference and a copy of the book you help bring in.\n\nThe room is quiet. Phones stay in the drawer.',
      requirements: 'A clear prose style and the patience to read carefully. Communications, literature, or journalism students are typical. Send a writing sample with your résumé.'
    },
    {
      host: 'Meridian Clinic',
      title: 'Research Assistant',
      field: 'Science',
      location: 'University laboratory',
      mode: 'On site',
      duration_weeks: 16,
      paid: 1,
      stipend: 700,
      openings: 1,
      deadline: '2027-01-22',
      description: 'A semester with the clinic’s records team. You will summarise papers, check consent logs, and help prepare the figures for the lab meeting. You will not handle treatment decisions.\n\nThe supervisor meets interns on Monday mornings.',
      requirements: 'Science student with a completed research-methods course. Care with confidential records is essential. A short account of one lab class should appear in your letter.'
    },
    {
      host: 'Atlas Civic Works',
      title: 'Site Engineering Intern',
      field: 'Engineering',
      location: 'City works yard',
      mode: 'On site',
      duration_weeks: 12,
      paid: 1,
      stipend: 900,
      openings: 1,
      deadline: '2027-02-05',
      description: 'You will visit two civic sites, update drawings, and write the daily note the project engineer expects. Boots are required on site days. The office days are for drawings and the file.',
      requirements: 'Civil or general engineering, second year or above. You should be able to read a simple plan. A site induction is given on the first morning.'
    },
    {
      host: 'Wren Chambers',
      title: 'Legal Research Intern',
      field: 'Law',
      location: 'Court district',
      mode: 'Hybrid',
      duration_weeks: 6,
      paid: 0,
      stipend: null,
      openings: 1,
      deadline: '2026-11-20',
      description: 'Six weeks of research support for one matter. You will check citations, prepare a bundle index, and write a two-page note. The place is unpaid and closely supervised.\n\nTwo days are in chambers. The rest may be done in the law library.',
      requirements: 'Law student who has completed the first year. Accurate citation and discretion. Please say which subjects you have taken.'
    },
    {
      host: 'The Reading Room',
      title: 'Archive Intern',
      field: 'Humanities',
      location: 'Main library',
      mode: 'On site',
      duration_weeks: 8,
      paid: 0,
      stipend: null,
      openings: 2,
      deadline: '2027-02-12',
      description: 'You will catalogue a small set of letters and write a finding aid of no more than four pages. Pencils only in the archive room. The librarian will show you the handling rules on the first day.',
      requirements: 'Humanities student with careful handwriting or a neat digital draft. An interest in history, literature, or libraries is enough. No previous archive post is expected.'
    }
  ];

  const insertPost = db.prepare(`
    INSERT INTO internships (
      organization_id, title, field, description, requirements, location, mode,
      duration_weeks, paid, stipend, openings, deadline, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'open')
  `);

  db.exec('BEGIN');
  try {
    const ids = {};
    organizations.forEach((org) => {
      const info = insertOrg.run(org.name, org.industry, org.description, org.website, org.location, org.contact_email);
      ids[org.name] = Number(info.lastInsertRowid);
    });
    internships.forEach((post) => {
      insertPost.run(
        ids[post.host],
        post.title,
        post.field,
        post.description,
        post.requirements,
        post.location,
        post.mode,
        post.duration_weeks,
        post.paid,
        post.stipend,
        post.openings,
        post.deadline
      );
    });
    ensureStudent({
      full_name: 'Elena Voss',
      email: 'elena.voss@student.example',
      student_id: 'STU-24018',
      faculty: 'Computer Science',
      year_of_study: 'Third year'
    });
    ensureStudent({
      full_name: 'James Okonkwo',
      email: 'james.okonkwo@student.example',
      student_id: 'STU-25102',
      faculty: 'Business',
      year_of_study: 'Second year'
    });
    db.exec('COMMIT');
  } catch (err) {
    db.exec('ROLLBACK');
    throw err;
  }
}

ensureAdmin();
seedDemo();

module.exports = { db, resumeDir };
