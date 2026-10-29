const FIELDS = [
  'Computer Science',
  'Design',
  'Business',
  'Engineering',
  'Communications',
  'Science',
  'Law',
  'Humanities'
];

const YEARS = [
  'First year',
  'Second year',
  'Third year',
  'Fourth year',
  'Graduate'
];

const MODES = ['On site', 'Hybrid', 'Remote'];

const APPLICATION_STATUSES = [
  { value: 'submitted', label: 'Submitted' },
  { value: 'review', label: 'Under review' },
  { value: 'offered', label: 'Offered' },
  { value: 'declined', label: 'Declined' }
];

module.exports = { FIELDS, YEARS, MODES, APPLICATION_STATUSES };
