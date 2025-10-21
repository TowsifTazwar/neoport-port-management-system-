document.addEventListener('DOMContentLoaded', () => {
  // Toggle show/hide for password fields
  document.querySelectorAll('.toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.previousElementSibling;
      const isPwd = input.getAttribute('type') === 'password';
      input.setAttribute('type', isPwd ? 'text' : 'password');
      btn.textContent = isPwd ? '🙈' : '👁';
      btn.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
    });
  });

  const pass1 = document.getElementById('pass');
  const pass2 = document.getElementById('pass2');
  const msg   = document.getElementById('matchMsg');

  function checkMatch(){
    if (!pass1.value && !pass2.value){ msg.textContent=''; return; }
    if (pass1.value === pass2.value){
      msg.textContent = 'Passwords match.';
      msg.style.color = '#0a6b4c';
    } else {
      msg.textContent = 'Passwords do not match.';
      msg.style.color = '#9a1d1d';
    }
  }
  pass1.addEventListener('input', checkMatch);
  pass2.addEventListener('input', checkMatch);

  document.querySelector('form').addEventListener('submit', (e) => {
    if (pass1.value.length < 8){
      e.preventDefault();
      alert('Password must be at least 8 characters.');
      pass1.focus();
      return;
    }
    if (pass1.value !== pass2.value){
      e.preventDefault();
      alert('Passwords do not match.');
      pass2.focus();
    }
  });
});
