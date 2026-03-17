# ELeP MVP (HTML + PHP API + MySQL + Bootstrap 4)

Clean and intuitive MVP web app that helps learners stay consistent with weekly study goals.

## What changed
- Legacy server-rendered/redirect page files were removed; only HTML frontend + PHP API backend files remain.
- Front-end pages are now pure **HTML** files.
- Backend logic is moved to **PHP API endpoints** in `/api`.
- Data is stored in **MySQL** using `database.sql`.

## Features
- Registration, login, logout, and profile update.
- Weekly goal creation + completion tracking.
- Dashboard metrics (weekly goals, completed, pending, progress %).
- Reminder creation and upcoming deadline alerts.

## Run locally
1. Create tables:
   ```bash
   mysql -u root -p < database.sql
   ```
2. Update DB credentials in `config.php`.
3. Start server:
   ```bash
   php -S 0.0.0.0:8000
   ```
4. Open `http://localhost:8000/index.html`.

## Main files
- Frontend: `index.html`, `login.html`, `register.html`, `dashboard.html`, `reminders.html`, `profile.html`
- Backend: `api/*.php`, `config.php`
