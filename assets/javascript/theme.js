// assets/javascript/theme.js
//
// Shared theme (and sound toggle label) behavior for pages that don't load dashboard.js.

document.addEventListener("DOMContentLoaded", () => {
  const root = document.documentElement;

  const savedTheme = localStorage.getItem("alkansya-theme") || "dark";
  root.setAttribute("data-theme", savedTheme);

  const toggleTheme = () => {
    const nextTheme = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
    root.setAttribute("data-theme", nextTheme);
    localStorage.setItem("alkansya-theme", nextTheme);
  };

  document.getElementById("theme-toggle")?.addEventListener("click", toggleTheme);
  document.getElementById("theme-toggle-mob")?.addEventListener("click", toggleTheme);

  // Sound toggle support (labels only). Real ping audio is handled elsewhere.
  let soundOn = localStorage.getItem("alkansya-sound") !== "off";

  const updateSoundButtonLabels = () => {
    const soundBtn = document.getElementById("sound-toggle");
    const soundMob = document.getElementById("sound-toggle-mob");

    const text = soundOn ? "Sound: On" : "Sound: Off";

    if (soundBtn) {
      soundBtn.textContent = text;
      soundBtn.classList.toggle("active-mute", !soundOn);
    }

    if (soundMob) {
      soundMob.textContent = text;
      soundMob.classList.toggle("active-mute", !soundOn);
    }
  };

  const toggleSound = () => {
    soundOn = !soundOn;
    localStorage.setItem("alkansya-sound", soundOn ? "on" : "off");
    updateSoundButtonLabels();
  };

  document.getElementById("sound-toggle")?.addEventListener("click", toggleSound);
  document.getElementById("sound-toggle-mob")?.addEventListener("click", toggleSound);

  updateSoundButtonLabels();
});
