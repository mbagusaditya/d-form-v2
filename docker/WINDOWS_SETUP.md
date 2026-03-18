# Setup Docker untuk Windows

## Perubahan yang Dilakukan

### 1. Dockerfile.dev
- ✅ Menambahkan Node.js dan npm untuk build frontend
- ✅ Menambahkan Composer installer
- ✅ Memperbaiki user/group creation untuk kompatibilitas Windows
- ✅ Menghapus COPY command untuk kode aplikasi (menggunakan volume mount)
- ✅ Menambahkan error handling yang lebih baik

### 2. docker-compose.yml
- ✅ Mengubah build context dari `./docker` ke `.` (root project)
- ✅ Menambahkan `:cached` pada volume mount utama untuk performa Windows
- ✅ Menambahkan `:ro` (read-only) pada config files
- ✅ Memperbaiki format port mapping dengan quotes
- ✅ Menambahkan environment variables untuk OPcache development
- ✅ Menambahkan build args untuk UID/GID

### 3. Caddyfile
- ✅ Membuat Caddyfile baru di `docker/caddy/Caddyfile`
- ✅ Konfigurasi untuk development server pada port 8000
- ✅ Mendukung Laravel routing dan PHP server

## Cara Menggunakan di Windows

### Prasyarat
1. Install Docker Desktop untuk Windows
2. Pastikan WSL2 sudah terinstall dan aktif
3. Pastikan Docker Desktop menggunakan WSL2 backend

### Langkah-langkah Setup

1. **Clone atau buka project**
   ```powershell
   cd D:\Doscom\d-form-v2
   ```

2. **Buat file .env** (jika belum ada)
   ```powershell
   copy .env.example .env
   ```

3. **Edit file .env** dan isi variabel berikut:
   ```
   DB_PASSWORD=your_mysql_password
   DB_DATABASE=d_form
   DB_PORT=3306
   REDIS_PASSWORD=your_redis_password
   REDIS_PORT=6379
   ```

4. **Build dan jalankan container**
   ```powershell
   docker compose up -d --build
   ```

5. **Install dependencies di dalam container**
   ```powershell
   # Install Composer dependencies
   docker compose exec app composer install
   
   # Install npm dependencies
   docker compose exec app npm install
   ```

6. **Generate application key**
   ```powershell
   docker compose exec app php artisan key:generate
   ```

7. **Jalankan migrations**
   ```powershell
   docker compose exec app php artisan migrate
   ```

8. **Build assets (jika diperlukan)**
   ```powershell
   docker compose exec app npm run build
   ```

### Perintah Berguna

**Masuk ke container:**
```powershell
docker compose exec app sh
```

**Jalankan artisan commands:**
```powershell
docker compose exec app php artisan [command]
```

**Lihat logs:**
```powershell
docker compose logs -f app
```

**Restart container:**
```powershell
docker compose restart app
```

**Stop semua container:**
```powershell
docker compose down
```

**Stop dan hapus volumes:**
```powershell
docker compose down -v
```

## Troubleshooting

### Issue: Permission denied pada storage atau bootstrap/cache
**Solusi:** Set permissions di dalam container:
```powershell
docker compose exec app sh -c "chmod -R 775 storage bootstrap/cache"
```

### Issue: Port sudah digunakan
**Solusi:** Ubah port di docker-compose.yml atau stop service yang menggunakan port tersebut

### Issue: Volume mount tidak sync
**Solusi:** 
- Pastikan Docker Desktop menggunakan WSL2 backend
- Restart Docker Desktop
- Gunakan `:cached` flag pada volume mount (sudah ditambahkan)

### Issue: Node modules tidak terdeteksi
**Solusi:** Install ulang di dalam container:
```powershell
docker compose exec app npm install
```

## Catatan Penting untuk Windows

1. **File Permissions**: Windows tidak mendukung Linux file permissions secara native. Docker Desktop akan handle ini melalui WSL2.

2. **Line Endings**: Pastikan Git config untuk handle line endings:
   ```powershell
   git config --global core.autocrlf input
   ```

3. **Performance**: Volume mounts di Windows bisa lebih lambat. Gunakan `:cached` flag untuk improve performance.

4. **Path Separators**: Docker Compose di Windows akan otomatis handle path conversion dari Windows path ke Linux path.

## Struktur Services

- **app**: Laravel application dengan FrankenPHP (port 80, 443)
- **db**: MySQL 9.0 database (port dari .env)
- **redis**: Redis 8.0 cache (port dari .env)
- **phpmyadmin**: Database management (port 8080)

## Development Workflow

1. Edit file di Windows (akan otomatis sync ke container)
2. Laravel Octane akan auto-reload dengan `--watch` flag
3. Untuk perubahan frontend, jalankan `npm run dev` di container atau di host
4. Logs bisa dilihat dengan `docker compose logs -f app`

