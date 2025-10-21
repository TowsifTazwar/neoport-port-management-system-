// pms/register/register-operations.js — conditional fields + password show/hide + basic check
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('opsForm');
  const success = document.getElementById('success');
  const roleSelect = document.getElementById('role');

  // Map keywords to conditional elements
  const conds = {
    harbor: document.querySelector('.cond-harbor'),
    customs: document.querySelector('.cond-customs'),
    finance: document.querySelector('.cond-finance'),
    it: document.querySelector('.cond-it'),
  };

  function updateConditionalFields() {
    Object.values(conds).forEach(el => el && (el.hidden = true));
    const val = (roleSelect.value || '').toLowerCase();

    if (val.includes('harbor')) conds.harbor.hidden = false;
    if (val.includes('customs')) conds.customs.hidden = false;
    if (val.includes('finance')) conds.finance.hidden = false;
    if (val.includes('system admin') || val.includes('(it)') || val.includes('it')) conds.it.hidden = false;
  }
  roleSelect.addEventListener('change', updateConditionalFields);
  updateConditionalFields();

  // Show/Hide password buttons
  document.querySelectorAll('.peek').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const input = document.getElementById(id);
      if (!input) return;
      const isPwd = input.type === 'password';
      input.type = isPwd ? 'text' : 'password';
      btn.textContent = isPwd ? '🙈' : '👁';
      btn.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
    });
  });

  // Server-side validation is now solely responsible for handling form submissions.
  // The client-side checks have been removed to prevent conflicts and ensure
  // that the form always submits to the server, which will provide feedback.
});
