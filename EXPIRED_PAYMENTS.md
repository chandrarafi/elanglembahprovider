# Sistem Pembayaran Kedaluwarsa Otomatis

Sistem ini dirancang untuk secara otomatis membatalkan pemesanan yang waktu pembayarannya telah habis, bahkan jika pengguna tidak aktif di halaman pembayaran.

## Cara Kerja

Sistem ini bekerja dengan beberapa mekanisme:

1. **WebSocket Server** - Server WebSocket yang mengelola timer pembayaran secara real-time dan juga melakukan pemeriksaan periodik.
2. **API Endpoint** - Endpoint API yang dapat dipanggil untuk memeriksa dan membatalkan pembayaran yang kedaluwarsa.
3. **Cronjob/Scheduled Task** - Tugas terjadwal yang berjalan secara periodik untuk memeriksa pembayaran yang kedaluwarsa.

## Setup

### 1. Menjalankan WebSocket Server

WebSocket Server harus berjalan terus menerus di background untuk mengelola timer pembayaran secara real-time. Di Windows, gunakan batch file yang telah disediakan:

```bash
# Di Windows
start_websocket_server.bat

# Di Linux/Mac
php server.php
```

Untuk memastikan server berjalan terus menerus, Anda dapat menggunakan tools seperti Supervisor di Linux atau Windows Service:

**Contoh konfigurasi Supervisor**:

```
[program:payment_websocket]
command=php /path/to/your/project/server.php
directory=/path/to/your/project
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/project/logs/websocket.log
```

**Contoh membuat Windows Service**:

```
sc create PaymentWebSocketService binPath= "C:\path\to\php\php.exe C:\path\to\your\project\server.php" DisplayName= "Payment WebSocket Service" start= auto
```

### 2. Mengatur Cronjob atau Scheduled Task

Untuk memastikan pembayaran kedaluwarsa dibatalkan bahkan ketika WebSocket server tidak berjalan:

**Linux Cronjob**:

```
# Jalankan setiap menit untuk memeriksa pembayaran kedaluwarsa
* * * * * php /path/to/your/project/check_expired.php >> /path/to/your/project/logs/cron.log 2>&1
```

**Windows Task Scheduler**:

```
schtasks /create /sc minute /tn "CheckExpiredPayments" /tr "C:\path\to\php\php.exe C:\path\to\your\project\check_expired.php" /ru SYSTEM
```

### 3. Konfigurasi Token Keamanan

Untuk keamanan, gunakan token yang sama di semua komponen sistem:

1. Secara default, token adalah `elanglembahsecret123`
2. Anda dapat mengubah token ini di:
   - `app/Controllers/Api.php` (variabel `$validToken`)
   - `app/WebSocket/PaymentServer.php` (variabel `$token`)
   - `check_expired.php` (variabel `$token`)
   - `server.php` (variabel `$token`)
   - `test_expired_payments.php` (variabel `$token`)

## Pengujian

Untuk menguji sistem pembayaran kedaluwarsa:

1. Jalankan script pengujian:

   ```
   php test_expired_payments.php
   ```

2. Untuk pengujian manual:
   - Buat pemesanan baru
   - Buka halaman pembayaran
   - Tunggu hingga timer mendekati 0
   - Tutup halaman pembayaran atau browser
   - Tunggu beberapa menit
   - Periksa status pemesanan - seharusnya berubah menjadi "cancelled"

## Troubleshooting

Jika sistem tidak berfungsi sebagaimana mestinya:

1. **Masalah WebSocket**:

   - Pastikan port 8090 (atau port yang dikonfigurasi) tidak terblokir oleh firewall
   - Periksa bahwa WebSocket server berjalan dengan menjalankan `tasklist | findstr php` di Windows atau `ps aux | grep server.php` di Linux
   - Coba akses WebSocket server dari browser dengan JavaScript console: `new WebSocket('ws://localhost:8090')`

2. **Masalah API**:

   - Pastikan token keamanan konsisten di seluruh sistem
   - Uji API secara langsung: `curl http://localhost:8080/api/check-expired-payments?token=elanglembahsecret123`
   - Periksa log error di `app/logs/log-*.php`

3. **Masalah Cronjob**:
   - Verifikasi cronjob atau scheduled task berjalan dengan benar
   - Periksa log cronjob di lokasi yang ditentukan

## Struktur File

- `app/Controllers/Api.php` - Controller yang menangani API endpoint
- `app/Controllers/Booking.php` - Controller dengan metode `checkAllExpiredPayments()`
- `app/WebSocket/PaymentServer.php` - WebSocket server untuk pembayaran
- `server.php` - Script untuk menjalankan WebSocket server
- `check_expired.php` - Script untuk cronjob
- `test_expired_payments.php` - Script untuk pengujian
- `start_websocket_server.bat` - Batch file untuk menjalankan server di Windows

## Diagram Alur

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│  WebSocket      │     │  Cronjob        │     │  Browser        │
│  Server         │     │  check_expired  │     │  Client         │
│                 │     │                 │     │                 │
└────────┬────────┘     └────────┬────────┘     └────────┬────────┘
         │                       │                       │
         │  Memeriksa setiap     │  Memeriksa setiap     │  Menampilkan
         │  30 detik             │  1 menit              │  countdown timer
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                 API: /api/check-expired-payments                │
│                                                                 │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                                │  Membatalkan pemesanan
                                │  yang kedaluwarsa
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                          Database                               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```
