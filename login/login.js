document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.pwd-toggle');
  const input  = document.getElementById('password');

  if (toggle && input) {
    toggle.addEventListener('click', () => {
      const isPwd = input.type === 'password';
      input.type  = isPwd ? 'text' : 'password';
      toggle.textContent = isPwd ? '🙈' : '👁';
      toggle.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
    });
  }
});
