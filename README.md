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
  <a href="#-testing">Testing</a>
</p>

---

## ✨ Features

- 👥 **Intern Management** - Manage intern data with import/export capabilities
- 📊 **Assessment System** - Track and evaluate intern performance
- 📅 **Attendance Tracking** - Monitor intern attendance records
- 📝 **Task Management** - Assign and track intern tasks
- 📑 **Report Generation** - Generate PDF reports and certificates
- 🔔 **Notifications** - Email notifications for task assignments and reminders
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

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/Management-Magang-DSI.git
cd Management-Magang-DSI
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
- 📨 **Queue Worker** for background jobs

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

**Terminal 3 - Queue Worker (for email notifications):**
```bash
php artisan queue:listen
```

### Building for Production

```bash
npm run build
```

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

```
├── app/
│   ├── Http/
│   │   ├── Controllers/     # HTTP Controllers
│   │   └── Livewire/        # Livewire Components
│   ├── Models/              # Eloquent Models
│   ├── Mail/                # Mailable Classes
│   └── Services/            # Business Logic Services
├── database/
│   ├── factories/           # Model Factories
│   ├── migrations/          # Database Migrations
│   └── seeders/             # Database Seeders
├── public/
│   └── templates/           # Import Templates (Excel)
├── resources/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript Files
│   └── views/               # Blade Templates
│       ├── components/      # Blade Components
│       ├── livewire/        # Livewire Views
│       └── layouts/         # Layout Templates
├── routes/
│   ├── web.php              # Web Routes
│   └── api.php              # API Routes
└── storage/
    └── app/public/          # Public File Storage
```

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
```
public/templates/
```

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
