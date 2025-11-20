window.deleteSkill = function(skill, elementId) {
  console.log("Deleting skill:", skill);

  fetch('delete_skills_ajax.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'skill=' + encodeURIComponent(skill)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const elem = document.getElementById(elementId);
      if (elem) elem.remove();

      document.getElementById('response').innerHTML =
        <div class="alert alert-success">Skill "${skill}" deleted successfully.</div>;
    } else {
      document.getElementById('response').innerHTML =
        <div class="alert alert-danger">Error: ${data.message}</div>;
    }
  })
  .catch(err => {
    console.error("Error deleting skill:", err);
    document.getElementById('response').innerHTML =
      <div class="alert alert-danger">Request failed. Please try again.</div>;
  });
};