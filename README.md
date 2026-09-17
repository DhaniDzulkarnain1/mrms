# Material Request Management System (MRMS)

Sistem manajemen permintaan material untuk perusahaan dengan 2 role: Production dan Warehouse.

## Tech Stack
- PHP 7.4
- CodeIgniter 3.1.13
- MySQL 5.7
- Docker & Docker Compose
- Apache

## Fitur Utama

### Production Role
- Login ke sistem
- Membuat RFI (Request for Item/Material)
- Menambahkan multiple items dalam satu RFI
- Mengubah/menghapus item sebelum submit
- Submit RFI ke Warehouse
- Melihat status dan hasil pengecekan

### Warehouse Role
- Login ke sistem
- Melihat daftar RFI yang sudah disubmit
- Melihat detail material dalam RFI
- Memberikan status READY/NOT READY untuk setiap item
- Memberikan remark jika material tidak tersedia
- Track siapa yang melakukan pengecekan

## Database Schema

### users
- Menyimpan data user (Production & Warehouse)
- Role-based access control

### rfi
- Header RFI dengan nomor unik (RFI-YYYY-NNNN)
- Link ke requester
- Status: draft, submitted, checked

### rfi_items
- Item material dalam setiap RFI
- Status: pending, ready, not_ready
- Remark untuk catatan Warehouse

## Setup & Installation

### Prerequisites
- Docker Desktop terinstall dan running

### Quick Start

1. Clone atau extract project ke folder

2. Start Docker containers:
```bash
docker-compose up -d --build
```

3. Tunggu sampai containers fully running (sekitar 1-2 menit)

4. Akses aplikasi:
   - Web App: http://localhost:8080
   - PHPMyAdmin: http://localhost:8081

### Default Login Credentials

**Production:**
- Username: `production1`
- Password: `password123`

**Warehouse:**
- Username: `warehouse1`
- Password: `password123`

### Database Access (PHPMyAdmin)
- URL: http://localhost:8081
- Server: `db`
- Username: `root`
- Password: `root`

## Project Structure

```
mrms/
├── application/          # CodeIgniter application
│   ├── controllers/     # Controllers
│   ├── models/          # Models
│   ├── views/           # Views
│   └── config/          # Configuration files
├── system/              # CodeIgniter core files
├── assets/              # CSS, JS, images
│   ├── css/
│   └── js/
├── database/            # Database schema
│   └── schema.sql
├── docker-compose.yml   # Docker services configuration
├── Dockerfile           # PHP/Apache container setup
└── README.md
```

## Development

### Stop containers:
```bash
docker-compose down
```

### View logs:
```bash
docker-compose logs -f web
```

### Rebuild containers:
```bash
docker-compose up -d --build
```

### Access MySQL directly:
```bash
docker exec -it mrms_db mysql -u root -proot mrms_db
```

## Notes

- Data MySQL tersimpan di Docker volume `mysql_data`
- Database schema otomatis diimport saat pertama kali container dibuat
- Sample data sudah include 2 user dan 1 RFI dengan 4 items
