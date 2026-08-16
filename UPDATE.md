# Memasang Pembaruan Modern Tanpa Menghapus Data

Panduan ini untuk folder V2 yang sudah berjalan di `D:\Work\Project\immanuel-production-v2`.

## Sebelum memperbarui

1. Jangan hapus folder lama sebelum menyimpan `.env`.
2. Jangan menjalankan `docker compose down -v`.
3. Sebaiknya export database melalui `http://localhost:8081` terlebih dahulu.

## Langkah pembaruan

1. Matikan layanan tanpa menghapus volume:

   ```powershell
   cd D:\Work\Project\immanuel-production-v2
   docker compose down
   ```

2. Simpan `.env` yang sekarang. ZIP pembaruan memang tidak berisi `.env`, jadi password database dan Application Key milikmu tidak akan ditimpa.

3. Ekstrak isi ZIP pembaruan ke `D:\Work\Project` dan izinkan Windows mengganti file aplikasi yang memiliki nama sama.

4. Bangun ulang dan jalankan:

   ```powershell
   cd D:\Work\Project\immanuel-production-v2
   docker compose up -d --build
   ```

5. Jalankan migration pembaruan dan bersihkan cache:

   ```powershell
   docker compose exec app php artisan migrate --force
   docker compose exec app php artisan field-jobs:sync
   docker compose exec app php artisan expenses:secure-attachments
   docker compose exec app php artisan optimize:clear
   ```

6. Periksa layanan:

   ```powershell
   docker compose ps
   ```

7. Buka aplikasi di `http://localhost:8000` dan database UI di `http://localhost:8081`.

## Penting

- Jangan menjalankan `php artisan migrate:fresh`; perintah itu menghapus seluruh tabel.
- Jangan menjalankan `php artisan db:seed` pada database aktif hanya untuk memasang update; akun contoh dapat diperbarui ulang oleh seeder.
- Migration pembaruan otomatis memberi izin jadwal kepada role Mandor dan User. Setelah itu Master tetap dapat mengubahnya dari menu Hak Akses.
- Fitur **Jadwal Event** otomatis membuat pekerjaan untuk invoice lama yang sudah diterbitkan. Tipe pekerjaan kini berada pada level invoice/event dan berlaku untuk seluruh item; data lama yang pernah berbeda antar-item dimigrasikan secara aman ke **Pasang & Bongkar**. Tampilan kalender lama sudah dihapus agar jadwal, penugasan, progres, dan foto berada dalam satu alur.
- Foto pekerjaan tersimpan pada volume `storage_data`; pastikan volume ini ikut dibackup ketika pindah komputer.
- Bila perlu menyinkronkan ulang pekerjaan dari invoice tanpa menghapus foto/penugasan, jalankan `docker compose exec app php artisan field-jobs:sync`.
- Lampiran pengeluaran lama otomatis dipindahkan dari area publik ke storage privat oleh `expenses:secure-attachments` tanpa mengubah data transaksinya.
- Apabila tampilan lama masih tersimpan, tekan `Ctrl+F5` pada browser.
