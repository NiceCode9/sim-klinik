# TASK BREAKDOWN — SIMKLINIK

Urutan pengerjaan disusun agar fondasi (auth, permission, menu dinamis) selesai dulu sebelum modul bisnis, dan agar setiap modul bisa diuji berdiri sendiri sebelum diintegrasikan ke modul berikutnya.

**Cara pakai:** berikan satu task (atau beberapa task dalam satu fase) ke opencode per sesi. Jangan minta seluruh fase dikerjakan sekaligus di awal.

---

## Fase 0 — Fondasi Project

- [ ] Setup project Laravel 13, konfigurasi `.env`, koneksi MySQL.
- [ ] Install semua package inti (lihat `TECH_STACK.md` bagian 2).
- [ ] **Bangun layout utama AdminLTE 4** dari acuan `public/assets/starter.html` — pecah jadi `layouts/app.blade.php` + partial navbar/sidebar/footer, ikuti urutan kerja & aturan persis di `TECH_STACK.md` bagian 8 (jangan ubah/hapus `starter.html`, jangan menulis wrapper markup versi sendiri).
- [ ] Buat halaman dashboard kosong yang extend layout ini, verifikasi tampilan identik dengan `starter.html` sebelum lanjut.
- [ ] Setup autentikasi (Breeze, restyle ke AdminLTE 4 sesuai bagian 5 `TECH_STACK.md`).
- [ ] Migration & seeder: `roles`, `permissions` dasar, user Superadmin default.
- [ ] Migration & model: `menus`, dengan relasi ke permission.
- [ ] Build komponen sidebar dinamis (`components/sidebar.blade.php`, baca menu dari DB sesuai permission user login, markup mengikuti struktur `starter.html`).
- [ ] Halaman Settings → CRUD menu & assign permission ke role/user (panel Superadmin).
- [ ] Setup `spatie/laravel-activitylog`, halaman log activity untuk Superadmin.

**Definisi selesai Fase 0:** Superadmin bisa login, membuat role baru, membuat menu baru, assign permission, dan menu tersebut langsung muncul/hilang di sidebar sesuai user yang login — **tanpa mengubah kode**.

---

## Fase 1 — Master Data

- [ ] Migration & CRUD: `specializations`, `employees` (dengan link ke `users`).
- [ ] Migration & CRUD: `patients`.
- [ ] Seeder: import master `icd10_codes` dan `icd9cm_codes` (siapkan dataset dari sumber resmi, atau minta user menyediakan file Excel/CSV untuk diimpor via Laravel Excel).
- [ ] Migration & CRUD: `drugs`, `tariffs`, `lab_test_masters`.
- [ ] Migration & CRUD: `doctor_tariffs`, `procedure_tariffs`.

**Definisi selesai Fase 1:** Semua master data bisa di-CRUD oleh Superadmin, siap dipakai modul transaksional.

---

## Fase 2 — Pendaftaran & Antrian

- [ ] Migration: `visits`, `queues`, `visit_vitals`.
- [ ] Form pendaftaran pasien baru (cek existing by NIK dulu sebelum create baru — hindari duplikasi pasien).
- [ ] Logic penomoran antrian harian (`QueueService`), reset per hari, per poli.
- [ ] Form registrasi online sederhana (public form, tanpa login) — status awal `waiting_online_confirmation`.
- [ ] Fitur check-in oleh Resepsionis untuk pasien online.
- [ ] Fitur pemanggilan antrian (Resepsionis/Perawat) dengan logic: skip pasien online yang belum check-in, lanjut ke pasien berikutnya.
- [ ] Form input TTV oleh Perawat (permission perawat ke modul ini).
- [ ] Halaman **Display Antrian** (public, polling AJAX, menampilkan nomor yang dipanggil per poli).
- [ ] **Trigger otomatis**: saat visit dibuat, buat juga record `visit_bills` kosong (status `open`).

**Definisi selesai Fase 2:** Alur pendaftaran → antrian → TTV bisa dijalankan end-to-end, baik jalur online maupun offline, teruji manual.

---

## Fase 3 — Rekam Medis Elektronik & E-Resep

- [ ] Migration: `medical_records`, `medical_record_forms`, `medical_record_diagnoses`, `medical_record_procedures`, `medical_record_addendums`, `document_signatures`, `odontogram_records`.
- [ ] Form SOAP dasar + pencarian ICD-10/ICD-9-CM (search-as-you-type).
- [ ] Formulir dinamis per spesialis (satu per satu, bukan sekaligus — **ikuti skema JSON persis di `TECHNICAL_SPECS.md` bagian A**, jangan improvisasi struktur sendiri):
  - [ ] 3a. Penyakit Dalam (ceklis anamnesis + riwayat obat kronis)
  - [ ] 3b. Saraf (kalkulator GCS + 12 saraf kranial)
  - [ ] 3c. Gigi (odontogram interaktif 32 gigi)
  - [ ] 3d. Radiologi (form order + ekspertise — lanjut ke Fase 4 karena beririsan dengan modul Lab/Radiologi)
- [ ] Migration: `document_signatures` (dengan `verification_token`, tanpa `signature_image_path`).
- [ ] Route publik `verify/{token}` + view terbatas (hanya metadata non-pasien, lihat `AGENTS.md` poin 11).
- [ ] `SignatureService`: generate hash konten + token acak saat dokumen dikunci, generate QR (`simplesoftwareio/simple-qrcode`) untuk ditempel di cetakan dokumen.
- [ ] Logic penguncian RME (field read-only setelah signed) + alur addendum.
- [ ] Migration: `prescriptions`, `prescription_items`, `compound_prescriptions`.
- [ ] Form e-resep dari layar RME, termasuk fitur "salin resep sebelumnya" untuk obat kronis.
- [ ] Form input resep racikan (compound) dengan komponen obat penyusun.
- [ ] Trigger: e-resep tersimpan → tambah baris ke `visit_bill_items`? **(Klarifikasi: sebaiknya tagihan obat baru ditambahkan setelah apotek acc harga — lihat Fase 5, bukan saat resep baru dibuat dokter.)**

**Definisi selesai Fase 3:** Dokter bisa input SOAP + form spesialis + diagnosis ICD + resep (biasa/racikan), lalu mengunci (sign) RME.

---

## Fase 4 — Laboratorium & Radiologi

- [ ] Migration: `lab_radiology_orders`, `lab_radiology_order_items`, `lab_results`, `lab_result_attachments`.
- [ ] Form order lab/radiologi dari RME (dokter).
- [ ] Form pendaftaran langsung untuk pasien yang mau periksa lab/radiologi tanpa lewat poli.
- [ ] Halaman kerja petugas Lab/Radiologi: daftar order masuk, input hasil (numerik untuk lab dengan flag normal/abnormal otomatis berdasar rentang referensi; teks ekspertise untuk radiologi).
- [ ] Upload lampiran hasil (gambar/PDF) via `spatie/laravel-medialibrary`, otomatis tampil di RME pasien terkait.
- [ ] Trigger: hasil lab/radiologi selesai → tambah baris tagihan ke `visit_bill_items`.

**Definisi selesai Fase 4:** Order dari dokter sampai ke petugas lab/radiologi, hasil bisa diinput dan muncul kembali di RME pasien, tagihan otomatis masuk ke billing.

---

## Fase 5 — Farmasi, Inventory & Apotek

- [ ] Migration: `drug_batches`, `stock_movements`, `stock_opnames`, `stock_opname_items`.
- [ ] Fitur penerimaan stok obat (input batch baru).
- [ ] `FefoStockService`: logic pemilihan batch berdasarkan expired date terdekat (**ikuti algoritma persis di `TECHNICAL_SPECS.md` bagian B**, termasuk row locking, all-or-nothing deduction, dan unit test wajib di B.4).
- [ ] Notifikasi/alert stok minimum & stok mendekati kedaluwarsa (dashboard atau halaman khusus dulu, notifikasi real-time bisa menyusul).
- [ ] Fitur stok opname (input fisik vs sistem, generate selisih).
- [ ] Migration: `pharmacy_transactions`, `pharmacy_transaction_items`.
- [ ] Halaman kerja Apoteker: antrian e-resep masuk real-time (biasa & racikan).
- [ ] `PricingService`: kalkulasi harga obat otomatis (margin % atau flat) sesuai master `drugs`.
- [ ] Kalkulasi otomatis tuslah & embalase saat resep diproses.
- [ ] Proses resep racikan: pecah pengurangan stok ke tiap obat komponen via `FefoStockService`.
- [ ] Fitur transaksi penjualan obat umum (non-resep, non-pasien klinik) — tetap pakai `FefoStockService`.
- [ ] Cetak: struk pembayaran obat, kwitansi tindakan, etiket obat (dompdf).
- [ ] Trigger: transaksi resep selesai diproses apotek → tambah baris tagihan obat ke `visit_bill_items` pasien terkait.

**Definisi selesai Fase 5:** Apotek bisa memproses resep (termasuk racikan) dengan stok berkurang sesuai FEFO dan harga terhitung otomatis, serta melayani penjualan umum.

---

## Fase 6 — Kasir / Billing

- [ ] Migration: `visit_bills` (sudah dibuat sejak Fase 2), `visit_bill_items`, `payments`.
- [ ] Halaman Kasir: daftar pasien dengan status `waiting_payment`, klik nama → detail rincian tagihan.
- [ ] Fitur input pembayaran (metode: tunai/debit/transfer/QRIS), hitung kembalian jika tunai.
- [ ] Cetak struk pembayaran final.
- [ ] Update status `visits` menjadi `completed` setelah pembayaran lunas.

**Definisi selesai Fase 6:** Alur end-to-end dari pendaftaran sampai pembayaran bisa dijalankan penuh untuk satu pasien.

---

## Fase 7 — Laporan

- [ ] Laporan Operasional & Kunjungan (grafik Chart.js + tabel).
- [ ] Laporan Medis Top 10 ICD-10.
- [ ] Laporan Keuangan (rekap pendapatan per metode pembayaran, HPP obat).
- [ ] Laporan Logistik Farmasi (fast/slow moving, mendekati kedaluwarsa, riwayat stok opname).
- [ ] Fitur ekspor semua laporan ke Excel & PDF.

**Definisi selesai Fase 7:** Semua laporan di PRD bagian 4.7 tersedia dan bisa diekspor.

---

## Fase 8 — Polish & Hardening

- [ ] Review ulang seluruh permission & middleware (pastikan tidak ada route bocor).
- [ ] Review ulang UX (validasi form, pesan error, loading state).
- [ ] Setup backup database terjadwal (`spatie/laravel-backup`).
- [ ] Review performa query (N+1) di halaman dengan data besar (daftar pasien, daftar stok).
- [ ] Uji regresi alur end-to-end lintas modul.

---

## Fase Masa Depan (Disetujui, Dikerjakan Setelah MVP Stabil)

Tidak dikerjakan dulu sekarang — dicatat di sini supaya arsitektur MVP tidak menutup kemungkinan berikut ditambahkan tanpa refactor besar:

- [ ] **Integrasi WA Gateway** untuk pendaftaran online (menggantikan/melengkapi form web manual). Saat mendesain form pendaftaran online & tabel `visits`/`queues` di Fase 2, pastikan kolom `registration_channel` bersifat generik (bukan hardcode `web`), supaya channel `whatsapp` bisa ditambahkan belakangan tanpa mengubah struktur tabel.
- [ ] **Modul appointment/booking jadwal dokter** — pasien bisa memilih slot waktu dokter tertentu, bukan murni walk-in + antrian. Akan menambah tabel baru (misal `doctor_schedules`, `appointments`) yang terhubung ke `visits` yang sudah ada, tanpa mengubah skema inti.
- [ ] Fase lanjutan dari PRD bagian 6 (Out of Scope) — SATUSEHAT, BPJS, PACS/DICOM, tanda tangan elektronik tersertifikasi BSrE — tetap menunggu keputusan eksplisit, belum termasuk dalam roadmap dekat ini kecuali disebutkan lain oleh user.

---

## Prioritas Jika Waktu Terbatas

Jika perlu MVP secepatnya untuk demo/klien, urutan minimum yang harus jalan penuh: **Fase 0 → 1 → 2 → 3 (SOAP dasar dulu, form spesialis bisa menyusul) → 6 (skip dulu ke kasir langsung tanpa farmasi jika sementara tidak jual obat) → 5 → 4 → 7**. Diskusikan dengan saya urutan ini jika prioritas bisnis Anda berbeda.
