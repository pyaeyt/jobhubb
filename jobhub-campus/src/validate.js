const { FIELDS, MODES, YEARS } = require('./constants');

function clean(value) {
  return String(value ?? '').replace(/\s+/g, ' ').trim();
}

function block(value) {
  return String(value ?? '').replace(/\r\n/g, '\n').trim();
}

function isEmail(value) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) && value.length <= 120;
}

function readInternship(body, organizationIds) {
  const errors = [];
  const title = clean(body.title);
  if (title.length < 3 || title.length > 120) errors.push('Give the internship a title.');

  const organizationId = Number(body.organization_id);
  if (!organizationIds.includes(organizationId)) errors.push('Choose a host organization.');

  const field = FIELDS.includes(body.field) ? body.field : '';
  if (!field) errors.push('Choose a field of study.');

  const description = block(body.description);
  if (description.length < 20 || description.length > 5000) {
    errors.push('Describe the internship in a short paragraph.');
  }

  const requirements = block(body.requirements);
  if (requirements.length < 10 || requirements.length > 3000) {
    errors.push('List what the student should bring.');
  }

  const location = clean(body.location);
  if (location.length < 2 || location.length > 120) errors.push('Say where the internship takes place.');

  const mode = MODES.includes(body.mode) ? body.mode : '';
  if (!mode) errors.push('Choose on site, hybrid, or remote.');

  const durationWeeks = Number(body.duration_weeks);
  if (!Number.isInteger(durationWeeks) || durationWeeks < 1 || durationWeeks > 52) {
    errors.push('Duration should be between 1 and 52 weeks.');
  }

  let paid = -1;
  if (String(body.paid) === '1') paid = 1;
  if (String(body.paid) === '0') paid = 0;
  if (paid < 0) errors.push('Say whether the internship is paid.');

  let stipend = null;
  if (paid === 1) {
    stipend = Number(body.stipend);
    if (!Number.isInteger(stipend) || stipend < 1 || stipend > 20000) {
      errors.push('Enter a monthly stipend in whole dollars.');
    }
  }

  const openings = Number(body.openings);
  if (!Number.isInteger(openings) || openings < 1 || openings > 50) {
    errors.push('Openings should be between 1 and 50.');
  }

  const deadline = String(body.deadline || '');
  if (!/^\d{4}-\d{2}-\d{2}$/.test(deadline) || Number.isNaN(new Date(`${deadline}T00:00:00`).getTime())) {
    errors.push('Choose a real deadline.');
  }

  const status = body.status === 'closed' ? 'closed' : 'open';

  return {
    errors,
    data: {
      title,
      organization_id: organizationId,
      field,
      description,
      requirements,
      location,
      mode,
      duration_weeks: durationWeeks,
      paid,
      stipend,
      openings,
      deadline,
      status
    }
  };
}

function readOrganization(body) {
  const errors = [];
  const name = clean(body.name);
  if (name.length < 2 || name.length > 120) errors.push('Give the host a name.');

  const industry = clean(body.industry);
  if (industry.length < 2 || industry.length > 80) errors.push('Name the host’s field.');

  const description = block(body.description);
  if (description.length < 20 || description.length > 4000) {
    errors.push('Describe the host in a short paragraph.');
  }

  const location = clean(body.location);
  if (location.length < 2 || location.length > 120) errors.push('Say where the host is based.');

  let website = clean(body.website);
  if (website) {
    try {
      const url = new URL(website);
      if (url.protocol !== 'http:' && url.protocol !== 'https:') throw new Error('bad protocol');
      website = url.href;
    } catch (err) {
      errors.push('The website should start with http:// or https://.');
    }
  }

  const contactEmail = clean(body.contact_email).toLowerCase();
  if (contactEmail && !isEmail(contactEmail)) errors.push('The contact email does not look right.');

  return {
    errors,
    data: {
      name,
      industry,
      description,
      location,
      website,
      contact_email: contactEmail
    }
  };
}

function readStudent(body) {
  const errors = [];
  const fullName = clean(body.full_name);
  if (fullName.length < 2 || fullName.length > 80) errors.push('Enter your full name.');

  const email = clean(body.email).toLowerCase();
  if (!isEmail(email)) errors.push('Enter a valid email address.');

  const studentId = clean(body.student_id).toUpperCase();
  if (!/^[A-Z0-9-]{4,16}$/.test(studentId)) {
    errors.push('Student number should be 4–16 letters, digits, or hyphens.');
  }

  const faculty = FIELDS.includes(body.faculty) ? body.faculty : '';
  if (!faculty) errors.push('Choose your faculty.');

  const yearOfStudy = YEARS.includes(body.year_of_study) ? body.year_of_study : '';
  if (!yearOfStudy) errors.push('Choose your year of study.');

  const password = String(body.password || '');
  const confirm = String(body.confirm_password || '');
  if (password.length < 6 || password.length > 100) errors.push('Use a password of at least 6 characters.');
  if (password !== confirm) errors.push('The two passwords do not match.');

  return {
    errors,
    data: {
      full_name: fullName,
      email,
      student_id: studentId,
      faculty,
      year_of_study: yearOfStudy,
      password
    }
  };
}

function readProfile(body, role) {
  const errors = [];
  const fullName = clean(body.full_name);
  if (fullName.length < 2 || fullName.length > 80) errors.push('Enter your full name.');

  const email = clean(body.email).toLowerCase();
  if (!isEmail(email)) errors.push('Enter a valid email address.');

  const phone = clean(body.phone);
  if (phone && !/^[0-9+() \-]{6,30}$/.test(phone)) {
    errors.push('Enter a phone number the office can use, or leave it blank.');
  }

  let faculty = null;
  let yearOfStudy = null;
  if (role === 'student') {
    faculty = FIELDS.includes(body.faculty) ? body.faculty : '';
    yearOfStudy = YEARS.includes(body.year_of_study) ? body.year_of_study : '';
    if (!faculty) errors.push('Choose your faculty.');
    if (!yearOfStudy) errors.push('Choose your year of study.');
  }

  const password = String(body.password || '');
  const confirm = String(body.confirm_password || '');
  if (password || confirm) {
    if (password.length < 6 || password.length > 100) errors.push('Use a password of at least 6 characters.');
    if (password !== confirm) errors.push('The two passwords do not match.');
  }

  return {
    errors,
    data: {
      full_name: fullName,
      email,
      phone,
      faculty,
      year_of_study: yearOfStudy,
      password
    }
  };
}

function readMessage(body) {
  const errors = [];
  const name = clean(body.name);
  if (name.length < 2 || name.length > 80) errors.push('Enter your name.');

  const email = clean(body.email).toLowerCase();
  if (!isEmail(email)) errors.push('Enter a valid email address.');

  const subject = clean(body.subject);
  if (subject.length < 3 || subject.length > 140) errors.push('Give the message a subject.');

  const message = block(body.message);
  if (message.length < 10 || message.length > 4000) errors.push('Write a short message for the office.');

  return { errors, data: { name, email, subject, message } };
}

function readCoverLetter(value) {
  const letter = block(value);
  if (letter.length < 30 || letter.length > 5000) {
    return { error: 'Write a short letter of at least a few sentences.', letter };
  }
  return { error: '', letter };
}

module.exports = {
  clean,
  isEmail,
  readInternship,
  readOrganization,
  readStudent,
  readProfile,
  readMessage,
  readCoverLetter
};
