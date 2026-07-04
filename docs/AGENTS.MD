# AGENTS.md — Panduan Wajib untuk AI Coding Agent (opencode)

File ini adalah instruksi permanen untuk AI agent yang mengerjakan project SIMKLINIK. Baca `PRD.md`, `TECH_STACK.md`, dan `DATABASE_SCHEMA.md` sebelum menulis kode apa pun. Jika ada instruksi user yang bertentangan dengan file ini, **tanyakan konfirmasi dulu**, jangan langsung menimpa aturan di sini.

---

## 1. Cara Kerja Umum

- Kerjakan **satu task kecil dalam satu waktu**, sesuai daftar di `TASK_BREAKDOWN.md`. Jangan membuat banyak modul sekaligus dalam satu sesi tanpa diminta — ini menyebabkan kode sulit direview.
- Sebelum membuat fitur baru, **cek dulu apakah model/migration/service terkait sudah ada** di project. Jangan membuat file duplikat dengan nama mirip (misal `PatientController` dan `PatientsController` sekaligus).
- Setelah selesai satu task, **jalankan `php artisan migrate` (di environment lokal/dev) dan pastikan tidak error**, serta jalankan test jika ada, sebelum melaporkan task selesai.
- Selalu jelaskan secara singkat file apa saja yang dibuat/diubah dan kenapa, di akhir setiap task.

## 2. Larangan Keras (Jangan Dilakukan Tanpa Persetujuan Eksplisit)

1. **Jangan mengedit migration yang sudah pernah dijalankan** (sudah ada di `migrations` table / sudah dipakai di data nyata). Jika skema perlu berubah, buat migration baru (`add_column_x_to_table_y`) — jangan edit file migration lama.
2. **Jangan mengubah struktur tabel inti** (`patients`, `medical_records`, `visits`, `drugs`, `drug_batches`, `visit_bills`) tanpa konfirmasi eksplisit dari user, karena berdampak ke banyak modul lain.
3. **Jangan menghapus data** (`DELETE`) untuk entitas transaksi medis/keuangan (medical_records, prescriptions, payments, stock_movements). Gunakan **soft delete** (`SoftDeletes` trait) atau status `cancelled`/`void`, bukan hard delete — ini data yang punya nilai hukum & audit.
4. **Jangan hardcode kredensial, API key, atau URL environment** di kode — selalu lewat `.env` dan `config/`.
5. **Jangan menulis logic bisnis kompleks langsung di Controller atau Blade view** (lihat aturan Service Layer di `TECH_STACK.md`). Kalkulasi FEFO, kalkulasi tagihan, penomoran antrian, dan logic penguncian RME **wajib** ada di Service class yang bisa di-unit-test terpisah.
6. **Jangan overwrite entri RME yang sudah `signed`.** Perubahan setelah signed harus lewat tabel `medical_record_addendums`, bukan update langsung ke `medical_records`.
7. **Jangan membuat menu/permission secara hardcode di Blade** (misal `@if(auth()->user()->role == 'dokter')`). Gunakan permission check (`@can('...')`) yang sumbernya dari database sesuai arsitektur dynamic menu di PRD bagian 3.
8. **Jangan gunakan tipe data `float`/`double` untuk nilai uang atau stok obat.** Selalu `decimal(15,2)`.
9. **Jangan membuat fitur di luar scope yang diminta** (lihat "Out of Scope" di PRD: integrasi SATUSEHAT, BPJS, PACS/DICOM, bot WhatsApp, BSrE) kecuali user secara eksplisit meminta untuk mulai mengerjakannya.
10. **Jangan mengubah file `AGENTS.md`, `PRD.md`, `TECH_STACK.md`, `DATABASE_SCHEMA.md`, atau `TASK_BREAKDOWN.md` ini sendiri** kecuali diminta langsung oleh user.
11. **Jangan pernah menampilkan data pasien, isi klinis, atau hash penuh dokumen di halaman verifikasi QR publik (`/verify/{token}`).** Halaman ini boleh diakses tanpa login, tapi field yang di-render **wajib dibatasi eksplisit** hanya: nama & status dokter penandatangan, jenis dokumen (generik, misal "Resep"), waktu tanda tangan, dan status valid/tidak. Ini berlaku juga saat debugging — jangan sisipkan data pasien "sementara untuk testing" di view ini.

## 3. Wajib Dilakukan

1. Setiap tabel transaksi/medis penting (lihat `DATABASE_SCHEMA.md` bagian 11) **wajib** pakai trait `LogsActivity` dari `spatie/laravel-activitylog`.
2. Setiap perubahan stok obat **wajib** melalui `stock_movements` (single source of truth), jangan pernah mengubah `quantity_remaining` di `drug_batches` secara langsung tanpa mencatat movement-nya.
3. Setiap perhitungan FEFO **wajib** diambil dari service `FefoStockService` — semua tempat yang butuh mengurangi stok (resep, penjualan umum, racikan) memanggil service yang sama, jangan duplikasi logic FEFO di banyak tempat.
4. Setiap route yang mengakses data sensitif (RME, kasir, farmasi) **wajib** dilindungi middleware permission, bukan hanya disembunyikan dari menu.
5. Validasi input **wajib** menggunakan Form Request class (`app/Http/Requests`), bukan validasi inline di Controller, untuk semua form dengan lebih dari 3 field.
6. Setiap membuat migration baru, sertakan juga **factory** (untuk keperluan testing/seeding) jika model tersebut akan dites.
7. Gunakan **DB transaction** (`DB::transaction()`) untuk operasi yang menyentuh lebih dari satu tabel sekaligus dan harus konsisten (contoh: proses resep racikan yang mengurangi banyak batch obat sekaligus + membuat tagihan).
8. Ikuti konvensi penamaan di `TECH_STACK.md` bagian 4 dengan konsisten.

## 4. Ketika Instruksi Tidak Jelas

- Jika sebuah requirement dari PRD masih berupa "Open Question" (lihat PRD bagian 7) dan diperlukan untuk task yang sedang dikerjakan, **tanyakan dulu ke user**, jangan mengasumsikan sendiri lalu membangun fitur besar di atas asumsi yang salah.
- Jika ambigu antara dua pendekatan implementasi yang sama-sama valid, pilih pendekatan yang **paling sederhana dan konsisten dengan pola yang sudah ada di project**, lalu sebutkan asumsi itu secara eksplisit ke user.

## 5. Definisi "Selesai" untuk Sebuah Task

Sebuah task dianggap selesai jika:
- Migration berjalan tanpa error.
- Fitur bisa diuji secara manual sesuai alur di PRD (bukan hanya "kode tidak error").
- Permission/menu terkait fitur baru sudah didaftarkan (bila relevan).
- Tidak ada logic bisnis penting yang bocor ke Controller/Blade.
- Ada minimal 1 test (unit/feature) untuk logic kritis (FEFO, kalkulasi tagihan, penguncian RME, penomoran antrian).

## 6. Referensi Silang

- Kebutuhan fitur & alur bisnis → `PRD.md`
- Struktur teknis, package, konvensi → `TECH_STACK.md`
- Struktur tabel & relasi → `DATABASE_SCHEMA.md`
- Urutan pengerjaan & prioritas → `TASK_BREAKDOWN.md`
- **Skema JSON form spesialis & algoritma FEFO detail → `TECHNICAL_SPECS.md`** — wajib dibaca sebelum mengerjakan form RME per spesialis (Fase 3) dan modul Farmasi/FEFO (Fase 5). Jangan mengarang struktur JSON atau logic FEFO sendiri jika sudah ada spesifikasinya di file ini.
