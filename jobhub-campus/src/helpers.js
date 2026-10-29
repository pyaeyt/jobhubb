const { APPLICATION_STATUSES } = require('./constants');

function parseId(value) {
  const n = Number(value);
  if (!Number.isInteger(n) || n < 1) return null;
  return n;
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function paragraphs(value) {
  const text = String(value || '').trim();
  if (!text) return '';
  return text.split(/\n{2,}/).map((part) => {
    return `<p>${escapeHtml(part).replace(/\n/g, '<br>')}</p>`;
  }).join('');
}

function todayISO() {
  const d = new Date();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${d.getFullYear()}-${month}-${day}`;
}

function deadlinePassed(deadline) {
  return String(deadline || '') < todayISO();
}

function formatDate(value) {
  if (!value) return '—';
  const raw = String(value);
  const iso = raw.length === 10 ? `${raw}T00:00:00` : raw.replace(' ', 'T') + 'Z';
  const date = new Date(iso);
  if (Number.isNaN(date.getTime())) return raw;
  return date.toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
}

function formatStipend(paid, amount) {
  if (!paid) return 'Unpaid';
  return `USD ${Number(amount || 0).toLocaleString('en-US')} a month`;
}

function statusLabel(value) {
  const found = APPLICATION_STATUSES.find((item) => item.value === value);
  return found ? found.label : String(value || '');
}

module.exports = {
  parseId,
  paragraphs,
  todayISO,
  deadlinePassed,
  formatDate,
  formatStipend,
  statusLabel
};
