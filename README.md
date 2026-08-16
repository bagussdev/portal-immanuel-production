# Portal Immanuel Production

Salinan baru aplikasi Immanuel Production. Proyek original tidak diubah. V2 berjalan dengan Laravel 12, PHP 8.3 FPM, Nginx, MySQL 8.4, queue worker, scheduler, Node/Vite, serta phpMyAdmin melalui Docker Compose.

## Yang sudah dirapikan

- Light mode biru muda yang cerah menjadi tampilan bawaan, dengan dark mode hitam-merah yang dapat diganti dari tombol kanan atas dan tersimpan otomatis di perangkat.
- Tabel lebar memiliki tombol geser kiri/kanan saat ruang layar terbatas, selain tetap mendukung swipe pada ponsel.
- Halaman error 403, 404, 419, 500, dan 503 memakai pesan yang jelas tanpa membocorkan detail teknis.
- Sidebar hanya menampilkan fitur yang benar-benar dimiliki role; grup menu kosong tidak lagi muncul.
- Status quotation, invoice, pembayaran, payroll, armada, dan Samsat memakai badge yang konsisten serta mudah dibaca.
- Nama client dapat langsung diketik pada quotation/invoice. Data relasinya dibuat otomatis di belakang layar dan menu Client tidak ditampilkan di Data Operasional.
- Mandor dan User dapat melihat kalender serta detail jadwal operasional tanpa mendapat akses ke nilai invoice.
- Invoice terbit otomatis membuat **Jadwal Event** operasional dengan nomor pekerjaan terpisah (`JOB/mm/yy/xxx`).
- Setiap invoice/event memakai satu tipe pekerjaan: **Pasang & Bongkar**, **Pasang saja**, atau **Sekali jalan**. Pilihan ini berlaku untuk seluruh item di dalam invoice.
- Master, Admin, dan Mandor dapat menugaskan anggota per tahap. User hanya dapat membuka pekerjaan yang ditugaskan kepadanya.
- Setiap tahap mendukung beberapa foto hasil dari kamera HP. Foto dikompres, disimpan privat, dan minimal satu foto diwajibkan sebelum tahap diselesaikan.
- Quotation yang disetujui otomatis membuat invoice Draft yang masih fleksibel untuk diedit.
- Diskon dan potongan pajak sama-sama mengurangi total tagihan. Keduanya mendukung persen atau nominal.
- Satu invoice dapat menerima pembayaran/DP berkali-kali. Setiap pembayaran langsung mengurangi sisa tagihan, dan persentasenya otomatis dihitung ulang ketika total invoice diedit.
- Pembayaran yang salah di-void dengan alasan, bukan dihapus, agar jejak audit tetap aman.
- Payroll mempertahankan alur Draft -> Sudah Dibayar dan closing periode yang sudah disukai.
- Samsat disederhanakan menjadi status pengingat serta riwayat perpanjangan.

## Hak akses bawaan

| Role | Akses utama |
|---|---|
| Master | Seluruh fitur, akun, role/izin, pembatalan, dan pengarsipan sensitif |
| Admin | Operasional bisnis lengkap, invoice/pembayaran, penugasan tim, dan menandai gaji dibayar |
| Mandor | Seluruh pekerjaan tim dan penugasan anggota serta membuat/mengedit seluruh slip gaji Draft; tanpa akses keuangan |
| User | Pekerjaan yang ditugaskan, upload foto hasil, dan slip gajinya sendiri; tanpa akses keuangan |

Master dapat mengubah izin role dari menu **Hak Akses**. Setelah izin menu dicabut, menu terkait juga tidak akan terlihat. Izin keuangan untuk Mandor/User dikunci di backend dan tidak dapat diberikan secara tidak sengaja.

## Alur pekerjaan lapangan

1. Admin/Master memilih satu tipe pekerjaan untuk seluruh invoice/event.
2. Saat **Terbitkan invoice** ditekan, sistem otomatis membuat pekerjaan dan tahap yang diperlukan.
3. Buka menu **Jadwal Event**, lalu Admin/Mandor memilih anggota untuk setiap tahap. Tim Pasang dapat langsung disalin ke Bongkar dan tetap dapat dibedakan. Kalender lama sudah digabung ke alur ini agar tidak ada dua menu jadwal.
4. Anggota membuka tugasnya dari HP, menekan **Mulai pekerjaan**, lalu mengambil atau memilih beberapa foto hasil.
5. Setelah minimal satu foto tersedia, anggota dapat menekan **Tandai selesai**. Admin/Mandor dapat membuka kembali tahap jika perlu diperbaiki.

Catatan invoice dan catatan tim dipisahkan. Hanya kolom **Catatan khusus tim lapangan** yang dikirim ke halaman Mandor/User; nilai, harga, diskon, pajak, pembayaran, rekening, dan status pembayaran tidak ikut disalin.

## Instalasi baru dengan Docker

Kebutuhan: Docker Desktop sudah aktif.

1. Salin konfigurasi:

   ```powershell
   Copy-Item .env.example .env
   ```

2. Buka `.env`, lalu ganti nilai berikut dengan password kuat dan berbeda:

   - `DB_PASSWORD`
   - `MYSQL_ROOT_PASSWORD`
   - `SEED_DEFAULT_PASSWORD`

3. Buat application key:

   ```powershell
   docker compose run --rm app php artisan key:generate --show
   ```

   Salin hasil `base64:...` ke `APP_KEY=` dalam `.env`.

4. Bangun dan jalankan layanan:

   ```powershell
   docker compose up -d --build
   ```

5. Untuk instalasi pertama saja, buat tabel dan akun awal:

   ```powershell
   docker compose exec app php artisan migrate --seed
   ```

6. Buka:

   - Aplikasi: `http://localhost:8000`
   - phpMyAdmin: `http://localhost:8081`

Untuk masuk phpMyAdmin, gunakan `DB_USERNAME` dan `DB_PASSWORD` dari `.env`. Host database internalnya adalah `db`.

## Akun awal

Password seluruh akun awal mengikuti `SEED_DEFAULT_PASSWORD` pada saat seeder dijalankan.

| Role | Email |
|---|---|
| Master | `master@immanuel.test` |
| Admin | `admin@immanuel.test` |
| Mandor | `mandor@immanuel.test` |
| User | `user@immanuel.test` |

Masuk sebagai Master lalu ubah password akun contoh sebelum aplikasi digunakan untuk data sungguhan.

## Memasang pembaruan pada V2 yang sudah berjalan

Baca [UPDATE.md](UPDATE.md). Jangan menyalin `.env` dari paket dan jangan menjalankan seeder ulang pada database yang sudah dipakai.

## Deploy ke VPS

VPS hanya membutuhkan Docker Engine dan plugin Docker Compose. PHP, Composer, Node.js, Nginx, MySQL, queue worker, dan scheduler sudah berada di image/container sehingga tidak perlu dipasang langsung di VPS.

1. Salin proyek dan buat `.env` dari `.env.example`.
2. Isi domain HTTPS, `APP_KEY`, serta seluruh password contoh dengan nilai production yang kuat dan berbeda.
3. Arahkan reverse proxy HTTPS ke `127.0.0.1:8000`. phpMyAdmin tetap hanya dapat diakses dari server/SSH tunnel melalui `127.0.0.1:8081`.
4. Jalankan dari root proyek:

   ```sh
   sh deploy-vps.sh
   ```

Skrip akan menolak konfigurasi yang masih development/tidak aman, memvalidasi Compose, membangun image terbaru, menunggu database, membuat backup pre-deploy, menjalankan migration dan sinkronisasi, mengamankan lampiran lama, mengoptimalkan cache, lalu melakukan health check.

## Perintah harian

Di Windows, aplikasi pada drive D dapat dijalankan cukup dengan klik dua kali [start-immanuel.cmd](start-immanuel.cmd). File tersebut mencari Docker Desktop di drive C, menyalakannya bila perlu, membangun versi kode terbaru, menjalankan Compose dari folder project ini, lalu membuka aplikasi. Gunakan [stop-immanuel.cmd](stop-immanuel.cmd) untuk menghentikan container tanpa menghapus database atau upload.

```powershell
docker compose ps
docker compose logs -f web app queue scheduler
docker compose exec app php artisan optimize:clear
docker compose stop
docker compose start
```

`docker compose down` hanya mematikan dan menghapus container; volume database tetap ada. Jangan menjalankan `docker compose down -v` pada data penting karena opsi `-v` menghapus volume MySQL dan storage.

## Backup

Sebelum pembaruan besar, backup database melalui phpMyAdmin atau perintah berikut:

```powershell
docker compose exec db mysqldump -u root -p immanuel_production > backup-immanuel.sql
```

Simpan juga salinan volume/file unggahan. Bukti pembayaran dan dokumen STNK berada di volume `storage_data`.

## Keamanan

- Aplikasi dan phpMyAdmin hanya dipublikasikan ke `127.0.0.1` pada konfigurasi lokal bawaan.
- `.env`, database lokal, dependency, log, dan bukti dokumen tidak masuk ke build context/ZIP distribusi.
- Bukti pembayaran, STNK, dan foto pekerjaan disimpan secara private dan hanya dibuka melalui rute berizin.
- Login dibatasi percobaannya; akun nonaktif ditolak dan sesi yang masih terbuka langsung dihentikan.
- Lampiran pengeluaran, bukti pembayaran, STNK, dan foto pekerjaan disimpan privat serta hanya dikirim melalui rute yang sudah memeriksa login dan hak akses.
- Container aplikasi, queue, dan scheduler menjalankan PHP sebagai user non-root; database dan phpMyAdmin tidak dipublikasikan ke jaringan luar.
- Perubahan penting dicatat pada audit log.
- Untuk server publik, pasang HTTPS, gunakan password unik, ubah `APP_URL`, aktifkan secure cookie, batasi phpMyAdmin, dan lakukan backup rutin.

## Verifikasi

```powershell
docker build --target test -t immanuel-production-test .
docker run --rm -e APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= immanuel-production-test
docker compose exec app php artisan route:list
```

Frontend otomatis dibangun saat `docker compose up -d --build` dijalankan.

Setelah perubahan tampilan atau kode, cukup jalankan `docker compose up -d --build`. Tidak perlu `down`, tidak perlu seeder ulang, dan volume database tetap digunakan.
