# Nutrition Monitoring System (NMS)

A digitized nutrition monitoring system built with Laravel 11+ to replace physical meal cards and manual registers. Uses QR codes and alphanumeric shortcodes for beneficiary identification and real-time meal tracking.

## Quick Start

### Prerequisites
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Run with Docker

```bash
# Clone or download this project
cd nutrition-monitoring-system

# Build and start
docker compose up --build

# Access the application
open http://localhost:8000
```

### Default Login Credentials

| Email | Password | Role |
|-------|----------|------|
| alice@nms.local | password | Head of Programmes |
| bob@nms.local | password | System Manager |
| cook.a@nms.local | password | Cook |

## Architecture

### Database Schema
- **users** - System actors (Head of Programmes, System Manager, Cook)
- **projects** - Funding sources / operational streams
- **beneficiaries** - Individual recipients with QR tokens and shortcodes
- **meal_logs** - Transactional ledger of every meal distributed
- **anomaly_logs** - Duplicate/rejected meal attempts

### Roles & Permissions

| Feature | HoP | SysMgr | Cook |
|---------|:---:|:---:|:---:|
| View global metrics | ✅ | ❌ | ❌ |
| Export data | ✅ | ✅ | ❌ |
| Manage projects | ✅ | ✅ | ❌ |
| Manage beneficiaries | ❌ | ✅ | ❌ |
| Generate card sheets | ❌ | ✅ | ❌ |
| Access scan terminal | ❌ | ❌ | ✅ |
| Record meals | ❌ | ❌ | ✅ |

## Tech Stack

- **Framework**: Laravel 11+ (PHP 8.3)
- **Database**: SQLite (default) / MySQL
- **Admin Panel**: Filament PHP v3
- **Frontend**: Blade + Livewire v3 + Tailwind CSS
- **QR Generation**: BaconQrCode
- **QR Scanning**: Html5-QRCode
- **PDF Export**: DOMPDF
- **Charts**: Filament Charts

## Manual Installation (without Docker)

```bash
# 1. Install dependencies
composer install

# 2. Set environment
cp .env.example .env
php artisan key:generate

# 3. Create SQLite database
touch database/database.sqlite

# 4. Run migrations and seeders
php artisan migrate --seed

# 5. Publish Filament assets
php artisan filament:assets

# 6. Start the server
php artisan serve
```

## Cook Terminal

The Cook Terminal is a mobile-first interface at `/terminal` that provides:
- **QR Code Scanning**: Continuous camera-based scanning with auto-detection
- **Manual Search**: Type the 5-character shortcode for fallback entry
- **Visual Feedback**: Full-screen green/red flash alerts
- **Audio Feedback**: Success/error chime sounds
- **Real-time Stats**: Live count of meals served today

## Card Generation

System Managers can generate printable PDF card sheets from the admin panel:
- A4 format with 8 cards per page (2 columns × 4 rows)
- Each card includes: beneficiary name, project, large shortcode, QR code
- Select by project for batch printing

## License

MIT
