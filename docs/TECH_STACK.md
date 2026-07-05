# TECH STACK & KONVENSI TEKNIS — SIMKLINIK

## 1. Core Stack

| Layer | Pilihan |
|---|---|
| Backend Framework | Laravel 13 (PHP 8.3+) |
| Database | MySQL 8 |
| Web Server | Apache |
| CSS Framework | AdminLTE 4 (dibangun di atas Bootstrap 5 — utility class Bootstrap 5 tetap berlaku), lihat bagian 8 |
| Frontend Interaktivitas | Blade + Alpine.js (ringan, cocok untuk komponen dinamis seperti odontogram/GCS calculator tanpa perlu SPA penuh) |
| Realtime (display antrian) | Laravel Reverb (WebSocket bawaan Laravel) — alternatif ringan: polling AJAX interval 3-5 detik jika ingin skip setup websocket di awal |

> Rekomendasi: **mulai dengan polling AJAX** untuk display antrian di MVP (lebih simpel, tanpa perlu queue worker tambahan), upgrade ke Reverb setelah fitur inti stabil.

## 2. Package / Library Wajib

| Kebutuhan | Package | Alasan |
|---|---|---|
| Role & Permission | `spatie/laravel-permission` | Sudah ditentukan |
| Activity/Audit Log | `spatie/laravel-activitylog` | Sudah ditentukan, dipakai untuk audit trail RME & log activity Superadmin |
| Export Excel | `maatwebsite/excel` | Sudah ditentukan |
| Export PDF | `barryvdh/laravel-dompdf` | Sudah ditentukan |
| Media/File upload | `spatie/laravel-medialibrary` | Untuk upload hasil lab/radiologi, foto profil, lampiran — lebih rapi daripada handle manual (auto generate path, konversi thumbnail untuk citra radiologi) |
| Query builder untuk datatable | `yajra/laravel-datatables` | Tabel data pasien/obat/laporan dengan server-side processing (penting karena data pasien/stok bisa besar) |
| Backup database | `spatie/laravel-backup` | Data medis kritis, wajib ada backup terjadwal |
| Nomor urut otomatis (No. RM, No. Antrian, No. Invoice) | Custom trait/service, opsional `haruncpi/laravel-id-generator` | Untuk format nomor rekam medis, invoice, dsb yang konsisten |
| QR Code untuk verifikasi tanda tangan elektronik | `simplesoftwareio/simple-qrcode` | Open source (MIT), wrapper Laravel untuk `endroid/qr-code` — dipakai untuk QR verifikasi dokumen (RME, resep, hasil lab/radiologi), lihat bagian 9 |
| Query/Reporting Excel styling | Sudah cover oleh `maatwebsite/excel` | — |
| Testing | Pest atau PHPUnit (bawaan Laravel) | Wajib ada test untuk logic kritis: FEFO, kalkulasi tagihan, antrian |
| Debugging dev | `barryvdh/laravel-debugbar` (dev only) | Membantu Anda review query N+1 saat development |

## 3. Struktur Folder (Prinsip)

Gunakan pendekatan **domain-oriented** di dalam `app/`, bukan menumpuk semua Controller/Model generik, agar AI agent (opencode) tidak salah taruh file saat scope makin besar:

```
app/
  Models/
    Patient.php
    Queue.php
    Visit.php
    MedicalRecord.php
    ...
  Http/
    Controllers/
      Registration/
      MedicalRecord/
      Pharmacy/
      LabRadiology/
      Cashier/
      Report/
      Settings/
  Services/
    Registration/QueueService.php
    Pharmacy/FefoStockService.php
    Pharmacy/PricingService.php
    MedicalRecord/SignatureService.php
    Billing/VisitBillService.php
  Http/Requests/
    <mengikuti sub-folder yang sama dengan Controllers>
  Enums/
    QueueStatus.php
    VisitStatus.php
    PaymentMethod.php
  Policies/
resources/
  views/
    registration/
    medical-record/
    pharmacy/
    lab-radiology/
    cashier/
    reports/
    settings/
    layouts/
    components/
database/
  migrations/
  seeders/
    Icd10Seeder.php
    Icd9CmSeeder.php
    PermissionSeeder.php
    MenuSeeder.php
routes/
  web.php   (atau dipecah per modul: routes/registration.php, dll, di-include dari web.php)
```

**Aturan wajib:**
- Logic bisnis kompleks (kalkulasi FEFO, kalkulasi tagihan, penomoran antrian) **tidak boleh** ditulis langsung di Controller. Wajib di **Service class**.
- Controller hanya: validasi request → panggil service → return response/view.
- Setiap modul besar (Pendaftaran, RME, Farmasi, Kasir, Lab/Radiologi, Laporan, Settings) punya sub-folder sendiri di Controllers & Views — konsisten dengan penamaan modul di PRD.

## 4. Konvensi Penamaan

- Tabel: `snake_case`, jamak (`patients`, `visit_bills`, `drug_batches`).
- Model: `PascalCase` singular (`Patient`, `VisitBill`, `DrugBatch`).
- Foreign key: `{singular_table}_id` (`patient_id`, `visit_id`).
- Migration baru selalu **menambah**, tidak mengedit migration lama yang sudah dijalankan di data nyata (lihat AGENTS.md).
- Route name: `{modul}.{aksi}`, contoh `registration.queue.call`, `pharmacy.prescription.process`.
- Permission name: `{modul}.{aksi}`, contoh `medical-record.sign`, `report.export`.
- Blade view: `kebab-case`, mengikuti path folder modul (`resources/views/pharmacy/prescriptions/index.blade.php`).

## 5. Autentikasi & Session

- Gunakan Laravel Breeze (Blade stack) sebagai starter kit auth — install manual tanpa scaffolding UI Tailwind default Breeze, ganti tampilan login/register agar konsisten dengan AdminLTE 4 (pakai asset yang sudah ada di `public/assets/`, lihat bagian 8), bukan tampilan Breeze bawaan.
- Tambahkan **2FA opsional** untuk Superadmin (opsional, bukan wajib MVP).
- Session timeout wajib untuk keamanan data medis (misal auto logout setelah idle 15-30 menit) — bisa dikonfigurasi.

## 6. Precision Angka (Uang & Stok)

- Gunakan tipe `DECIMAL(15,2)` untuk semua kolom uang (harga, tarif, total tagihan) — **jangan pakai FLOAT/DOUBLE** untuk menghindari floating point error di kalkulasi keuangan.
- Stok obat: `DECIMAL(10,2)` atau `INTEGER` tergantung apakah ada satuan pecahan (misal tablet vs cairan ml) — tabel `drugs` sebaiknya punya `unit` (tablet, botol, ml, dst) dan `is_fractional` untuk membedakan.

## 7. Cetak Thermal (Struk Kasir & Etiket Apotek)

Klinik menggunakan **printer thermal** (umumnya lebar kertas 58mm atau 80mm — konfirmasi ke user ukuran pastinya saat mulai Fase 6/5) untuk struk kasir, kwitansi kecil, dan etiket obat. Ini **terpisah** dari kebutuhan cetak laporan/dokumen A4 (yang tetap pakai dompdf).

**Rekomendasi pendekatan (dari yang paling sederhana):**
1. **Cetak via browser (paling simpel, direkomendasikan untuk mulai):** buat halaman/Blade view khusus dengan CSS `@page { size: 80mm auto; margin: 0; }` (sesuaikan lebar), lalu panggil `window.print()` dari JavaScript. Browser modern + driver printer thermal umumnya sudah bisa mencetak halaman HTML sederhana ini tanpa library tambahan. Cocok untuk MVP karena tidak perlu instalasi apa pun di sisi client selain driver printer itu sendiri.
2. **ESC/POS langsung (jika opsi 1 tidak stabil di lapangan):** gunakan library seperti `mike42/escpos-php` untuk mengirim perintah cetak langsung ke printer thermal (butuh printer terhubung ke jaringan/USB dan didukung server, biasanya melalui print server lokal karena Apache/Laravel di server tidak selalu punya akses langsung ke printer di komputer kasir).

**Keputusan:** mulai dengan **opsi 1 (cetak via browser)** di Fase 5 & 6, upgrade ke ESC/POS hanya jika di lapangan ternyata bermasalah (misal potong kertas otomatis tidak jalan, format berantakan).

## 8. Frontend Template & Layout (AdminLTE 4)

Project menggunakan template **AdminLTE 4** (berbasis Bootstrap 5). Asset (CSS, JS, font, plugin) sudah ditaruh di `public/assets/`, dan contoh halaman referensi ada di **`public/assets/starter.html`**.

**Aturan wajib untuk AI agent:**

1. **`public/assets/starter.html` adalah acuan struktur markup, bukan file yang dijalankan.** Jangan hapus, jangan pindahkan, jangan ubah isinya. File ini hanya dibaca sebagai referensi untuk melihat struktur HTML asli AdminLTE 4 (sidebar, navbar, breadcrumb, content wrapper, footer) sebelum dikonversi ke Blade.
2. **Layout utama dibuat sebagai Blade layout terpisah**, bukan menyalin `starter.html` ke satu file besar. Struktur yang direkomendasikan:
   ```
   resources/views/layouts/
     app.blade.php          -- kerangka utama (html, head, body wrapper AdminLTE, @yield/section untuk konten)
   resources/views/components/
     navbar.blade.php       -- top navbar (partial)
     sidebar.blade.php      -- sidebar menu (HARUS baca menu dari database, lihat AGENTS.md poin 7 & PRD bagian 3 — jangan hardcode <li> menu di sini)
     footer.blade.php       -- footer partial
     breadcrumb.blade.php   -- breadcrumb per halaman (menerima parameter judul & path)
   ```
3. **Path asset di layout memakai `asset('assets/...')`**, mengikuti struktur folder yang sudah ada di `public/assets/` — jangan install ulang AdminLTE via npm/composer/CDN yang berbeda, karena asset lokal sudah disiapkan.
4. **Class & struktur HTML AdminLTE 4 (`sidebar`, `app-wrapper`, `content-wrapper`, `app-main`, dsb — sesuai versi 4, yang strukturnya berbeda dari AdminLTE 3) harus diikuti persis seperti di `starter.html`**, supaya semua plugin CSS/JS bawaan (dropdown sidebar, dark mode toggle jika ada, dsb) tetap berfungsi. Jangan menulis markup wrapper versi sendiri yang "mirip-mirip".
5. **Sidebar menu tetap dinamis** (sesuai arsitektur di PRD bagian 3) — gunakan struktur `<li>`/`<a>` yang sama seperti di `starter.html`, tapi isi/loop-nya berasal dari tabel `menus` (relasi parent-child untuk submenu), bukan daftar menu statis dari `starter.html`.
6. Halaman-halaman modul (`resources/views/registration/`, `pharmacy/`, dst) **extend `layouts.app`** dan hanya mengisi bagian konten (`@section('content')`), tidak menulis ulang bagian sidebar/navbar/head di setiap halaman.
7. Jika ada komponen AdminLTE spesifik yang dipakai satu halaman saja (misal datatable plugin, date range picker), load asset tambahan itu **lokal per halaman** (`@push('scripts')` / `@push('styles')`), jangan dimasukkan ke layout utama supaya halaman lain tidak ikut memuat asset yang tidak perlu.

**Urutan kerja saat membangun layout pertama kali (Fase 0):**
1. Baca `public/assets/starter.html` untuk memahami struktur wrapper AdminLTE 4 yang dipakai (versi elemen, urutan tag, class utama).
2. Pecah jadi `layouts/app.blade.php` + partial (navbar, sidebar, footer) sesuai poin 2 di atas.
3. Buat satu halaman dashboard kosong yang extend layout ini untuk memastikan tampilan sudah identik dengan `starter.html` sebelum lanjut ke modul lain.
4. Baru setelah layout dasar dikonfirmasi cocok, sidebar diisi dinamis dari tabel `menus`.

## 9. Tanda Tangan Elektronik via QR Verifikasi (Fase 1)

Menggantikan pendekatan signature-pad. Lihat detail alur bisnis di `PRD.md` bagian 4.2. Poin teknis & keamanan yang wajib diikuti:

- **Isi QR hanya URL pendek** berisi token acak (`https://{domain}/verify/{token}`), **jangan pernah** menaruh data pasien, isi klinis, atau hash penuh langsung di dalam QR. QR yang berisi data mentah akan sulit dipindai (terlalu padat) dan berisiko membocorkan data medis jika kertas dilihat/difoto orang lain sebelum sempat di-scan ke halaman resmi.
- **Token verifikasi** dibuat dengan `Str::random(40)` atau UUID — bukan ID auto-increment biasa, supaya tidak bisa ditebak/diiterasi orang lain (`/verify/1`, `/verify/2`, dst berbahaya karena bisa di-enumerate).
- **Route verifikasi bersifat publik** (tanpa login, karena tujuannya siapa pun yang memindai QR di kertas cetak bisa mengecek keasliannya) — tapi **hanya render melalui view khusus yang membatasi field yang ditampilkan** secara eksplisit (nama dokter, jenis dokumen, waktu tanda tangan, status valid/tidak). **Jangan pernah** query dan tampilkan field pasien/klinis apa pun di controller/view ini, bahkan untuk debugging.
- Hash SHA-256 dari konten dokumen tetap disimpan di `document_signatures.content_hash` sebagai lapisan keamanan integritas tambahan (dicek ulang di background saat halaman verifikasi diakses — bila hash tidak cocok dengan snapshot konten tersimpan, tampilkan status "tidak valid"), tapi hash itu sendiri **tidak ditampilkan penuh** ke publik (cukup status cocok/tidak).
- Generate QR image on-the-fly saat dokumen dicetak (bukan disimpan sebagai file statis) menggunakan `simplesoftwareio/simple-qrcode`, contoh: `QrCode::size(150)->generate(route('verify.show', $token))`.
- Rate limit route `/verify/{token}` (misal `throttle:30,1`) untuk mencegah penyalahgunaan/brute force token, meskipun token sudah acak dan panjang.

## 10. Rekomendasi Tambahan (opsional, boleh disesuaikan)

- **Laravel Excel** untuk import master data (misal import daftar obat awal dari file Excel distributor) selain untuk export laporan.
- **Chart.js** (via CDN, ringan, cocok dengan AdminLTE 4/Bootstrap 5) untuk grafik di dashboard laporan — lebih simpel daripada library chart Vue/React karena stack Anda Blade-based.
- **DataTables (jQuery)** di sisi frontend untuk melengkapi `yajra/laravel-datatables` di backend.
