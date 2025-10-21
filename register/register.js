// pms/register/register.js (safe version)
// Goal: keep this file for future interactions,
// and make sure clicks on cards navigate normally (no popups, no blocking).

document.addEventListener("DOMContentLoaded", () => {
  // 1) Remove any legacy inline onclick handlers that might still be present
  document.querySelectorAll("[onclick]").forEach(el => el.removeAttribute("onclick"));

  // 2) Ensure clicks on the card navigate. If some script tries to preventDefault,
  //    we override and navigate anyway.
  document.addEventListener("click", (e) => {
    const card = e.target.closest("a.register-card");
    if (!card) return;

    // If any handler already called preventDefault on this event,
    // we'll force navigation.
    if (e.defaultPrevented) {
      window.location.assign(card.href);
      return;
    }

    // Otherwise, allow the browser's native link navigation to proceed.
    // (No need to call preventDefault at all.)
  }, true); // capture phase so we run before old bubble handlers

  // 3) IMPORTANT: Do NOT attach any alert()s or custom intercepts here.
  //    If you need future behavior, add it without blocking navigation.
});
