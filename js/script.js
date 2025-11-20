
// File: js/script.js

function toggleTheme() {
  const body = document.body;
  const icon = document.getElementById("themeIcon");

  const isDark = body.classList.contains("dark-mode");

  body.classList.toggle("dark-mode", !isDark);
  body.classList.toggle("light-mode", isDark);

  icon.classList.remove("bi-moon-stars-fill", "bi-sun-fill");
  icon.classList.add(!isDark ? "bi-sun-fill" : "bi-moon-stars-fill");
}

document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const icon = document.getElementById("themeIcon");

  const isDark = body.classList.contains("dark-mode");

  icon.classList.remove("bi-moon-stars-fill", "bi-sun-fill");
  icon.classList.add(isDark ? "bi-sun-fill" : "bi-moon-stars-fill");
});
