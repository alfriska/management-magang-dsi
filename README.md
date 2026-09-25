# 📋 Management Magang DSI

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Sistem Manajemen Magang untuk Direktorat Sistem Informasi</strong>
</p>

<p align="center">
  <a href="#-features">Features</a> •
  <a href="#-requirements">Requirements</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-development">Development</a> •
  <a href="#-whatsapp-reminder-setup">WhatsApp Reminder</a> •
  <a href="#-testing">Testing</a>
</p>

---

## ✨ Features

- 👥 **Intern Management** - Manage intern data with import/export capabilities
- 📊 **Assessment System** - Track and evaluate intern performance
- 📅 **Attendance Tracking** - Monitor intern attendance records
- 📝 **Task Management** - Assign and track intern tasks
- 📆 **Calendar** - Unified calendar view for Attendance and Tasks, with linked Daily Report status per date
- 📓 **Daily Report** - Interns log daily activity (description, photo attachment, target completion date, progress status); Admin/Pembimbing get a monitoring dashboard with donut-chart summary and per-instansi filtering
- 📑 **Report Generation** - Generate PDF reports and certificates
- 🔔 **Notifications** - Email notifications for task assignments and reminders
- 📲 **WhatsApp Reminder** - Automated WhatsApp reminders (via Wablas) for interns who haven't filled their Daily Report by the cutoff time
- 👨‍💼 **Supervisor Management** - Manage supervisors and their assigned interns

---

## 📋 Requirements

Before you begin, ensure you have the following installed:

| Requirement | Version |
|-------------|---------|
| PHP | ^8.3 |
| Composer | Latest |
| Node.js | ^18.x or ^20.x |
| npm | ^9.x or ^10.x |
| MySQL | ^8.0 |

### PHP Extensions Required

- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO (MySQL)
- Tokenizer
- XML
- GD / Imagick (for PDF generation)
- **curl** (required for WhatsApp Reminder integration via Wablas)

> ⚠️ **Windows note:** if `WhatsAppService` throws `cURL error 60: SSL certificate ... unable to get local issuer certificate`, download [`cacert.pem`](https://curl.se/ca/cacert.pem) and set `curl.cainfo` / `openssl.cafile` in your `php.ini` to point to it, then restart your PHP process.

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/alfriska/management-magang-dsi.git
cd management-magang-dsi
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

Open `.env` and update the following configurations:

```env
# Application
APP_NAME="Management Magang DSI"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=management_magang
DB_USERNAME=root
DB_PASSWORD=your_password

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# WhatsApp Reminder (Wablas) - see "WhatsApp Reminder Setup" section below
WHATSAPP_API_URL=https://your-region.wablas.com/api/send-message
WHATSAPP_API_TOKEN=your_wablas_token
WHATSAPP_API_SECRET=your_wablas_secret_key
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

Create a new MySQL database named `management_magang`, then run:

```bash
php artisan migrate
```

### 7. Seed Database (Optional)

To populate the database with sample data:

```bash
php artisan db:seed
```

### 8. Create Storage Link

```bash
php artisan storage:link
```

---

## 💻 Development

### Quick Start (Recommended)

Use the built-in composer script to run all services concurrently:

```bash
composer dev
```

This will start:
- 🌐 **Laravel Server** at `http://localhost:8000`
- ⚡ **Vite Dev Server** for hot module replacement
- 📨 **Queue Worker** for background jobs (email notifications, WhatsApp reminder jobs)

> Note: `composer dev` does **not** start the Laravel Scheduler. See [WhatsApp Reminder Setup](#-whatsapp-reminder-setup) below if you need scheduled reminders running locally.

### Manual Start

If you prefer to run services separately in different terminals:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

**Terminal 3 - Queue Worker (for email notifications & WhatsApp reminder jobs):**
```bash
php artisan queue:listen
```

**Terminal 4 - Scheduler (only needed to trigger the WhatsApp reminder automatically, see below):**
```bash
php artisan schedule:work
```

### Building for Production

```bash
npm run build
```

---

## 📲 WhatsApp Reminder Setup

Interns who haven't filled their Daily Report get an automatic WhatsApp reminder via **[Wablas](https://wablas.com)** (an unofficial WhatsApp Web gateway).

### 1. Create a Wablas Device

1. Register at [wablas.com](https://wablas.com)
2. Dashboard → **Add Device** → scan the QR code with the WhatsApp number you want to send reminders from
3. Leave the auto-reply / chatbot-related settings (Get Incoming Message, Get Auto Reply From Webhook, Greeting Message, Buku Kas, etc.) **untouched / disabled** — this integration only sends outbound messages via API, no inbound handling is needed
4. Once the device status shows **connected**, open **Device → Settings** and note down:
   - **Domain / Base URL** — Wablas assigns each device to a specific regional server (e.g. `jkt.wablas.com`, `sby.wablas.com`). Use the exact domain shown for **your** device, not a generic one.
   - **API Keys / Token**
   - **Secret Key** — ⚠️ this is shown **only once**. Copy it immediately; if missed, you'll need to regenerate it.

### 2. Configure `.env`

```env
WHATSAPP_API_URL=https://your-region.wablas.com/api/send-message
WHATSAPP_API_TOKEN=paste_your_token_here
WHATSAPP_API_SECRET=paste_your_secret_key_here
```

Replace `your-region.wablas.com` with the exact **Domain / Base URL** shown on your device's settings page (e.g. `jkt.wablas.com`).

### 3. Reminder Schedule

Reminders run automatically via `daily-report:send-reminders`, scheduled in `routes/console.php`:

| Day | Time (Asia/Jakarta) |
|---|---|
| Monday – Friday | 16:00 |
| Saturday | 12:30 |
| Sunday | No reminder sent |

Only **active** interns without a Daily Report for the current date receive a reminder. Once a reminder is successfully sent for a given intern+date, it will not be sent again (tracked in `daily_report_reminders`).

### 4. Running the Scheduler

**In local development**, nothing triggers the schedule automatically — run this in a dedicated terminal while developing:

```bash
php artisan schedule:work
```

**In production**, register a single cron entry instead (do **not** use `schedule:work` in production):

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

A queue worker (`php artisan queue:work`, ideally managed by Supervisor) must also be running at all times for reminders to actually be dispatched, since sending happens asynchronously via the `SendDailyReportReminder` job.

### 5. Manually Triggering a Reminder Run (Testing)

```bash
php artisan daily-report:send-reminders
```

### 6. Provider Notes / Troubleshooting

- Wablas (and similar unofficial gateways like Fonnte) work by hijacking a WhatsApp Web session, **not** the official Meta Business API — occasional device **disconnects** are a known characteristic of this category of service, not a bug in this codebase.
- Wablas requires the `Authorization` header to be sent as `{token}.{secret_key}` (token and secret key joined with a dot) — this is already handled inside `WhatsAppService`.
- Each Wablas device is hosted on a specific regional server (its **Domain / Base URL**, e.g. `jkt.wablas.com`) — sending requests to the wrong domain will fail even with a valid token.
- Failed sends are logged with a clear reason in `daily_report_reminders.error_message` (e.g. empty phone number, device disconnected) and are automatically retried the next time the reminder job runs — the scheduler and command never crash on a failed send.
- If you need to switch providers again in the future, all provider-specific logic is isolated in `app/Services/WhatsAppService.php` — only that file and the `.env` values need to change.

---

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Or directly with Artisan:

```bash
php artisan test
```

---

## 📁 Project Structure

├── app/
│ ├── Console/
│ │ └── Commands/ # Artisan Commands (incl. daily-report:send-reminders)
│ ├── Http/
│ │ └── Controllers/ # HTTP Controllers (incl. DailyReportController)
│ ├── Jobs/ # Queued Jobs (incl. SendDailyReportReminder)
│ ├── Livewire/ # Livewire Components (incl. Calendar)
│ ├── Models/ # Eloquent Models (incl. DailyReport, DailyReportReminder)
│ ├── Mail/ # Mailable Classes
│ └── Services/ # Business Logic Services (incl. WhatsAppService)
├── database/
│ ├── factories/ # Model Factories
│ ├── migrations/ # Database Migrations
│ └── seeders/ # Database Seeders
├── public/
│ └── templates/ # Import Templates (Excel)
├── resources/
│ ├── css/ # Stylesheets
│ ├── js/ # JavaScript Files
│ └── views/ # Blade Templates
│ ├── components/ # Blade Components
│ ├── daily-reports/ # Daily Report views (Intern log, detail, Admin/Pembimbing monitoring)
│ ├── livewire/ # Livewire Views (incl. Calendar)
│ └── layouts/ # Layout Templates
├── routes/
│ ├── web.php # Web Routes
│ ├── console.php # Scheduler definitions (incl. WhatsApp reminder schedule)
│ └── api.php # API Routes
└── storage/
└── app/public/ # Public File Storage (incl. daily_report_images/)


---

## 🔧 Configuration

### Queue Configuration

The application uses database queue driver. Make sure to run migrations and start the queue worker:

```bash
php artisan queue:listen
```

### PDF Generation

The application uses multiple PDF libraries:
- **DomPDF** - For basic PDF generation
- **Snappy** - For advanced PDF features
- **mPDF** - For certificate generation

### Excel Import/Export

Uses **Maatwebsite/Excel** for importing/exporting intern data. Templates are located in:

public/templates/


### WhatsApp Reminder

See the dedicated [WhatsApp Reminder Setup](#-whatsapp-reminder-setup) section above.

---

## 📓 Daily Report — Feature Overview

| Role | Capability |
|---|---|
| **Intern** | Create one Daily Report per day (description, optional photo, optional target completion date, progress status: *Belum Dikerjakan / On Progress / Selesai*), view/edit today's own report, browse a filterable log of past reports |
| **Pembimbing** | View a monitoring dashboard scoped to their own assigned interns — filter by instansi/date, "Sudah Lapor" vs "Belum Lapor" summary, report detail |
| **Admin** | Same monitoring dashboard as Pembimbing but across **all** instansi, plus the ability to delete a report |

Daily Report status is also surfaced on the intern's own **Kalender → Tugas** view — any date with a submitted report shows a "Report terkirim" badge (independent of whether a task exists on that date), linking straight to the report detail.

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Development Team

**Direktorat Sistem Informasi (DSI)**

---

<p align="center">
  Made with ❤️ using Laravel, Livewire, and TailwindCSS
</p>