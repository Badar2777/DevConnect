document.addEventListener('DOMContentLoaded', () => {
  const messageInput = document.getElementById('messageInput');
  const fileInput = document.getElementById('fileInput');
  const attachBtn = document.getElementById('attachBtn');
  const typingStatus = document.getElementById('typingStatus');
  const form = document.getElementById('msgForm');

  // Theme restore
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }

  // Toggle theme
  window.toggleTheme = function () {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  };

  // Typing indicator
  if (messageInput) {
    messageInput.addEventListener('input', () => {
      if (typingStatus) {
        typingStatus.style.display = 'inline';
        clearTimeout(window.typingTimeout);
        window.typingTimeout = setTimeout(() => {
          typingStatus.style.display = 'none';
        }, 1000);
      }
    });
  }

  // File button behavior
  if (attachBtn && fileInput) {
    attachBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length > 0 && messageInput.value.trim() === '') {
        messageInput.value = fileInput.files[0].name;
      }
    });
  }

  // Submit message
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(form);
      const receiver = form.querySelector('input[name="receiver_id"]');
      if (!receiver) {
        alert("Recipient not found.");
        return;
      }

      fetch('send_message.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            messageInput.value = '';
            fileInput.value = '';
            fetchMessages();
          } else {
            alert("Failed to send message.");
          }
        })
        .catch(err => console.error("Send failed:", err));
    });
  }

  // Load chat messages
  function fetchMessages() {
    const chatBox = document.getElementById('chatBox');
    const receiverInput = document.querySelector('input[name="receiver_id"]');

    if (!chatBox || !receiverInput) return;

    const url = `fetch_messages.php?chat=${receiverInput.value}`;

    fetch(url)
      .then(res => res.text())
      .then(html => {
        chatBox.innerHTML = html;
        chatBox.scrollTop = chatBox.scrollHeight;
        setupActions();
      });
  }

  // Edit message
  window.editMessage = function (id, msg) {
    const newMsg = prompt("Edit your message:", msg);
    if (newMsg && newMsg.trim()) {
      fetch('edit_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&message=${encodeURIComponent(newMsg)}`
      }).then(() => fetchMessages());
    }
  };

  // Delete message
  window.deleteMessage = function (id) {
    if (confirm("Are you sure you want to delete this message?")) {
      fetch('delete_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
      }).then(() => fetchMessages());
    }
  };

  // Attach actions to icons
  function setupActions() {
    document.querySelectorAll('.edit-icon').forEach(icon => {
      icon.addEventListener('click', () => editMessage(icon.dataset.id, icon.dataset.message));
    });
    document.querySelectorAll('.delete-icon').forEach(icon => {
      icon.addEventListener('click', () => deleteMessage(icon.dataset.id));
    });
  }

  // Handle conversation click → mark read + redirect
  document.querySelectorAll('.conversation').forEach(conv => {
    conv.addEventListener('click', function (e) {
      e.preventDefault();
      const userId = this.getAttribute('data-user-id');

      if (userId) {
        fetch('mark_read.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `sender_id=${userId}`
        }).then(res => res.json()).then(data => {
          if (data.status === 'success') {
            this.querySelector('.unread-count')?.remove();
            window.location.href = '?chat=' + userId;
          }
        });
      }
    });
  });

  // Auto-refresh every 5s
  fetchMessages();
  setInterval(fetchMessages, 5000);
});