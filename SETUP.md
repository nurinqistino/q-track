# Panduan Setup Q-TRACK (KWSP Queue Tracking System)

Ikut langkah ini untuk dapatkan sistem web berjalan selepas dapat semua fail.

---

## 1. Keperluan

- **Laragon** (sudah install PHP, MySQL, Apache/Nginx)
- **Composer** (biasanya ada dalam Laragon)
- **Browser** (Chrome, Edge, dll.)

Pastikan Laragon **Running** (Apache & MySQL hijau).

---

## 2. Letakkan folder projek

- Pastikan folder `q-track` ada dalam folder Laragon, contoh:
  - `C:\laragon\www\q-track`
  - ATAU kekal di `C:\Users\Dell\q-track` (tak perlu pindah jika Laragon boleh akses)

---

## 3. Install dependency PHP (Composer)

Buka **Terminal** (PowerShell / CMD) atau **Laragon Terminal**, lalu:

```bash
cd C:\Users\Dell\q-track
composer install
```

Tunggu sehingga selesai. Jika ada ralat “PHP extension missing”, hidupkan extension tersebut dalam `php.ini` Laragon.

---

## 4. Fail persekitaran (.env)

```bash
copy .env.example .env
```

Kemudian generate application key:

```bash
php artisan key:generate
```

---

## 5. Database MySQL

**5.1 Buat database**

- Buka **Laragon** → **Menu** → **MySQL** → **phpMyAdmin**
- Login (user: `root`, password kosong jika default)
- Cipta database baru, nama: `qtrack` (atau nama lain ikut suka)

**5.2 Sambung dalam .env**

Edit fail `q-track\.env`, cari baris database dan set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qtrack
DB_USERNAME=root
DB_PASSWORD=
```

Simpan fail.

---

## 6. Migrasi & data asas

Jalankan migrasi (cipta jadual):

```bash
cd C:\Users\Dell\q-track
php artisan migrate
```

Jawab `yes` jika diminta.

Kemudian seed 4 perkhidmatan tetap KWSP:

```bash
php artisan db:seed
```

---

## 7. Jalankan pelayan web

```bash
cd C:\Users\Dell\q-track
php artisan serve
```

Anda akan nampak mesej seperti:

```
Starting Laravel development server: http://127.0.0.1:8000
```

Buka browser dan pergi ke: **http://127.0.0.1:8000**

---

## 8. User pertama (Admin / Staff)

Sistem guna login **email @kwsp.gov.my** sahaja. User perlu dicipta dalam pangkalan data atau melalui tinker.

**Cara 1 – Tinker (cipta admin pertama)**

```bash
php artisan tinker
```

Dalam tinker, taip (ganti password ikut keselamatan anda):

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@kwsp.gov.my',
    'password' => bcrypt('password123'),
    'role' => 'admin'
]);
```

Taip `exit` untuk keluar tinker.

Selepas itu anda boleh login dengan:
- **Email:** admin@kwsp.gov.my  
- **Password:** password123  

(Selepas login, tukar password melalui Profile jika guna sistem sebenar.)

**Cara 2 – Cipta Staff (selepas ada Admin)**

1. Login sebagai admin.
2. Pergi **Admin** → **Staff** → **Add Staff**.
3. Isi nama, email @kwsp.gov.my, password, pilih role (Staff/Admin), assign counter jika perlu.

---

## 9. Ringkasan URL utama

| Fungsi              | URL (jika guna `php artisan serve`) |
|---------------------|--------------------------------------|
| Laman utama         | http://127.0.0.1:8000               |
| Ambil nombor giliran| http://127.0.0.1:8000/services      |
| Papan paparan       | http://127.0.0.1:8000/board          |
| Login staff         | http://127.0.0.1:8000/login         |
| Dashboard staff     | http://127.0.0.1:8000/staff          |
| Dashboard admin     | http://127.0.0.1:8000/admin          |

---

## 10. Jika guna Laragon Virtual Host (optional)

Jika mahu guna URL seperti `http://q-track.test`:

1. Dalam Laragon: **Menu** → **Apache** → **Virtual Hosts** → **Auto**.
2. Laragon akan cipta `http://q-track.test` jika folder projek bernama `q-track` dalam `C:\laragon\www\`.
3. Pastikan `C:\laragon\www\q-track\public` diset sebagai document root untuk vhost tersebut (biasanya Laragon auto set mengikut nama folder).

Kemudian akses sistem melalui `http://q-track.test` (atau URL yang Laragon tunjuk).

---

## Masalah biasa

- **500 error:** Pastikan `.env` wujud, `php artisan key:generate` sudah jalan, dan permission folder `storage` & `bootstrap/cache` boleh tulis.
- **Database connection error:** Semak nama database, user, password dalam `.env` dan pastikan MySQL dalam Laragon running.
- **Login tak boleh:** Pastikan email berakhir dengan `@kwsp.gov.my` dan password minimum 8 aksara.

Jika ikut langkah di atas, sistem web Q-TRACK sepatutnya sudah boleh digunakan.
