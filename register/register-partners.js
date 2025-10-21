// pms/register/register-partners.js — conditional fields + password toggles + simple validation
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('partnerForm');
  const success = document.getElementById('success');
  const role = document.getElementById('role');

  const conds = {
    ship: document.querySelector('.cond-ship'),
    importer: document.querySelector('.cond-importer'),
    exporter: document.querySelector('.cond-exporter'),
    vendor: document.querySelector('.cond-vendor'),
  };

  function updateFields() {
    Object.values(conds).forEach(el => el && (el.hidden = true));
    const val = (role.value || '').toLowerCase();
    if (val.includes('shipping')) conds.ship.hidden = false;
    if (val.includes('importer')) conds.importer.hidden = false;
    if (val.includes('exporter')) conds.exporter.hidden = false;
    if (val.includes('supplier') || val.includes('vendor')) conds.vendor.hidden = false;
  }
  role.addEventListener('change', updateFields);
  updateFields();

  // Eye toggles
  document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const inp = document.getElementById(id);
      if (!inp) return;
      const isPwd = inp.type === 'password';
      inp.type = isPwd ? 'text' : 'password';
      btn.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
      btn.textContent = isPwd ? '🙈' : '👁';
    });
  });

});
