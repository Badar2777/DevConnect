 // Theme toggle
  function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  }

  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }
function confirmDelete() {
  if (confirm("Are you sure you want to delete your account permanently?")) {
    window.location.href = "delete_developer.php";
  }
}