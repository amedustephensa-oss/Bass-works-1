const api = {
  async request(url, options = {}) {
    const res = await fetch(url, {
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      ...options,
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Request failed');
    return data;
  },
  me() { return this.request('api/me.php'); },
  login(payload) { return this.request('api/login.php', { method: 'POST', body: JSON.stringify(payload) }); },
  register(payload) { return this.request('api/register.php', { method: 'POST', body: JSON.stringify(payload) }); },
  logout() { return this.request('api/logout.php', { method: 'POST' }); },
};

function setAlert(id, message, type = 'danger') {
  const el = document.getElementById(id);
  if (!el) return;
  el.className = `alert alert-${type}`;
  el.textContent = message;
  el.classList.remove('d-none');
}

function hideAlert(id) {
  const el = document.getElementById(id);
  if (el) el.classList.add('d-none');
}

async function guardAuth(redirect = 'login.html') {
  const info = await api.me();
  if (!info.user) {
    window.location.href = redirect;
    return null;
  }
  return info.user;
}

async function renderNav() {
  const nav = document.getElementById('mainNav');
  if (!nav) return;
  const info = await api.me();
  const user = info.user;
  nav.innerHTML = `
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
      <div class="container">
        <a class="navbar-brand font-weight-bold" href="index.html">ELeP</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navMenu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMenu">
          <ul class="navbar-nav ml-auto">
            ${user ? `
              <li class="nav-item"><a class="nav-link" href="dashboard.html">Dashboard</a></li>
              <li class="nav-item"><a class="nav-link" href="reminders.html">Reminders</a></li>
              <li class="nav-item"><a class="nav-link" href="profile.html">Profile</a></li>
              <li class="nav-item"><button id="logoutBtn" class="btn btn-link nav-link">Logout</button></li>
            ` : `
              <li class="nav-item"><a class="nav-link" href="login.html">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="register.html">Sign Up</a></li>
            `}
          </ul>
        </div>
      </div>
    </nav>`;

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      await api.logout();
      window.location.href = 'index.html';
    });
  }
}
