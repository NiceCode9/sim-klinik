# PRD — Sistem Informasi Manajemen Klinik (SIMKLINIK)

Versi: 1.0
Status: Draft untuk pengembangan (vibecoding dengan opencode)

---

## 1. Latar Belakang & Tujuan

Klinik baru dengan layanan **Penyakit Dalam, Saraf, Gigi, Radiologi, dan Laboratorium** membutuhkan sistem informasi manajemen klinik (SIMKLINIK) yang mencakup alur pasien dari pendaftaran sampai pembayaran, termasuk rekam medis elektronik (RME), farmasi, dan pelaporan.

Tujuan utama:
- Mempercepat & merapikan alur pendaftaran, antrian, dan pemeriksaan.
- Menyediakan RME sesuai kaidah SOAP + kodifikasi ICD-10/ICD-9-CM, selaras dengan semangat **Permenkes No. 24 Tahun 2022 tentang Rekam Medis** (identitas pasien, anamnesis, pemeriksaan fisik & penunjang, setiap entri wajib memiliki timestamp, user ID pencatat, dan tanda tangan elektronik/verifikasi sistem).
- Mengelola farmasi dengan logika **FEFO** (First Expired First Out) dan multi-batch.
- Menyatukan tagihan pasien lintas modul (poli, tindakan, obat) ke satu titik kasir.
- Menyediakan laporan operasional, medis, keuangan, dan logistik yang bisa diekspor.

Catatan penting terkait regulasi (untuk konteks, bukan wajib di fase awal):
- Faskes di Indonesia diwajibkan memiliki RME dan idealnya terintegrasi ke **SATUSEHAT** (platform Kemenkes berbasis HL7-FHIR). Integrasi SATUSEHAT **tidak dikerjakan di fase awal**, tapi skema data (NIK, kode ICD-10, struktur observasi) dibuat agar mudah dipetakan ke FHIR resource di kemudian hari (`Patient`, `Condition`, `Observation`, `DiagnosticReport`, `MedicationStatement`).
- Radiologi standar nasional idealnya mengirim citra dalam format **DICOM** via PACS lalu dibaca sebagai *ekspertise*. Untuk klinik skala ini, cukup upload hasil citra (JPG/PNG/PDF) + input teks ekspertise oleh dokter radiologi/analis — bukan PACS/DICOM penuh (terlalu berat untuk fase awal, bisa jadi roadmap masa depan).

---

## 2. Aktor / Role

| Role | Deskripsi singkat |
|---|---|
| Superadmin | Kontrol penuh sistem, master data, konfigurasi menu & permission, log activity |
| Resepsionis | Pendaftaran pasien, antrian, check-in pasien online |
| Perawat | Pemeriksaan awal (TTV), **memiliki akses tambahan ke ranah resepsionis** (lihat 4.1) |
| Dokter | Input RME (SOAP), diagnosis ICD, e-resep, order lab/radiologi, tanda tangan elektronik |
| Apoteker | Racik & serahkan obat, kelola stok, cetak etiket, transaksi obat non-resep/umum |
| Kasir | Terima pembayaran, cetak struk/kwitansi |

Role tambahan implisit yang direkomendasikan (untuk kelengkapan modul, konfirmasi ke klien Anda):
- **Analis Lab** — input hasil pemeriksaan lab (bisa jadi sub-role dari Dokter/Perawat atau role baru).
- **Radiografer/Dokter Radiologi** — input ekspertise radiologi (role baru atau sub-role Dokter).

> Karena arsitektur menu & permission bersifat dinamis (lihat bagian 3), penambahan role baru di kemudian hari tidak memerlukan perubahan struktur database — cukup melalui panel Superadmin.

---

## 3. Prinsip Arsitektur Inti: Dynamic Menu & Permission

Ini adalah **fondasi teknis** yang harus dibangun paling awal sebelum modul lain:

- Menu **tidak di-hardcode** di Blade/route, melainkan disimpan di database (tabel `menus`), dengan struktur parent-child (untuk sidebar/submenu).
- Setiap menu terhubung ke satu **permission** (via Spatie Permission).
- Role memiliki sekumpulan permission → otomatis menentukan menu apa saja yang tampil.
- Superadmin bisa assign permission langsung ke **user tertentu** (override role), bukan hanya lewat role — ini yang memungkinkan kasus "Perawat dapat izin akses ranah Resepsionis".
- Setiap route/controller action dilindungi middleware permission, **bukan hanya menu yang disembunyikan** (agar tidak bisa diakses via URL langsung).

Gunakan pendekatan: `permission` = 1 hak akses granular (contoh: `pendaftaran.create`, `rme.edit`, `laporan.export`). `menu` = representasi UI yang di-assign ke satu atau lebih permission.

---

## 4. Modul & Alur Bisnis

### 4.1 Pendaftaran & Antrian

**Alur:**
1. Pasien dapat mendaftar **online** (melalui link/form yang terhubung ke WhatsApp — minimal berupa link web form yang dikirim via WA, bukan bot WA otomatis di fase awal, kecuali diputuskan lain) atau **datang langsung (offline)**.
2. Pasien mendapat nomor antrian berurutan sesuai urutan pendaftaran/kedatangan.
3. **Pasien online wajib check-in di admin/resepsionis saat tiba** untuk dikonfirmasi kedatangannya. Sebelum check-in, status pasien = "terdaftar online, belum datang" — nomor antriannya **dilewati sementara**.
4. Jika saat gilirannya pasien online belum check-in, sistem otomatis lanjut memanggil pasien offline dengan nomor antrian berikutnya (pasien online yang terlewat tetap dalam antrian, dipanggil ulang setelah check-in atau disisipkan sesuai kebijakan klinik — perlu dikonfirmasi: apakah masuk ke akhir antrian yang tersisa atau disisipkan di antrian saat itu juga).
5. Begitu pasien check-in (baik online maupun offline), **perawat langsung melakukan pemeriksaan awal (TTV)**: tensi, nadi, suhu, tinggi/berat badan, keluhan awal. Karena itu, **role Perawat diberi permission tambahan di modul Pendaftaran** (create kunjungan, update status antrian) — bukan role terpisah, tapi permission overlap.
6. Setelah TTV, pasien diarahkan ke poli tujuan (dokter) sesuai jadwal/pilihan dan menunggu dipanggil dokter.
7. **Display antrian**: layar publik yang menampilkan nomor antrian yang sedang dipanggil per poli (real-time, bisa pakai polling atau websocket/Laravel Reverb).

**Entitas kunci:** `patients`, `queues`, `visits` (kunjungan), `visit_vitals` (TTV).

**Keputusan final (lihat juga bagian 7):**
- Pendaftaran online untuk MVP memakai **form web sederhana** (link dibagikan manual), disiapkan agar mudah dikembangkan ke WA Gateway nanti.
- Pasien online yang telat check-in **disisipkan ke antrian setelah pasien offline yang sudah check-in lebih dulu** di poli yang sama pada momen itu — bukan hangus dan bukan menyerobot ke depan pasien yang sudah menunggu.

---

### 4.2 Rekam Medis Elektronik (RME) & E-Resep

**Standar pencatatan:**
- Format **SOAP** (Subjective, Objective, Assessment, Plan) sesuai standar Kemenkes.
- Diagnosis dikodifikasi dengan **ICD-10** (diagnosis) dan **ICD-9-CM** (tindakan/prosedur). Sistem harus punya master data ICD-10 & ICD-9-CM yang bisa dicari (search-as-you-type), diimpor dari dataset resmi (WHO/Kemenkes) via seeder.
- Setiap **create/update** entri RME dicatat sebagai **Audit Trail** (siapa, kapan, apa yang diubah) — gunakan `spatie/laravel-activitylog`.
- Setiap simpan RME final, dokter melakukan **tanda tangan elektronik**.

**Rekomendasi teknis tanda tangan elektronik (mudah, gratis, aman untuk kerahasiaan pasien, bertahap):**
- **Fase 1 (MVP, gratis, berbasis QR verifikasi — dipilih oleh Anda):**
  - Saat RME/resep dikunci (final) oleh dokter, sistem menghasilkan **hash SHA-256 dari konten dokumen** (isi SOAP, diagnosis, resep, dsb yang sudah dibekukan) + **token verifikasi acak** (bukan ID berurutan, agar tidak bisa ditebak/diakses sembarangan), disimpan di tabel `document_signatures`.
  - Sistem men-generate **QR code yang hanya berisi URL pendek** ke halaman verifikasi internal, contoh `https://klinik.example.com/verify/{token}` — **QR tidak menaruh data pasien atau isi klinis di dalamnya**, hanya referensi.
  - QR ini dicetak di dokumen (RME cetak, resep, hasil lab/radiologi, kwitansi) sebagai pengganti tanda tangan basah/gambar, biasanya didampingi teks nama & status dokter (misal "dr. Budi Santoso, Sp.PD — ditandatangani secara elektronik").
  - **Halaman verifikasi bersifat publik (bisa diakses tanpa login, cukup scan QR) tapi dibatasi informasinya secara ketat**: hanya menampilkan nama dokter penandatangan, jenis dokumen (misal "Resep" atau "Rekam Medis"), waktu tanda tangan, dan status validitas (cocok/tidak dengan hash tersimpan). **Tidak menampilkan nama pasien, diagnosis, atau isi klinis apa pun** — ini untuk menjaga kerahasiaan rekam medis sesuai prinsip kerahasiaan yang diwajibkan Permenkes 24/2022, meskipun dokumen fisiknya bisa terlihat/terjatuh di tempat umum.
  - Generate QR menggunakan package gratis & open source `simplesoftwareio/simple-qrcode` (lihat `TECH_STACK.md`).
  - Ini menggantikan pendekatan signature-pad (gambar tanda tangan di canvas) yang sebelumnya direncanakan — QR lebih sederhana secara implementasi (tidak perlu canvas & penyimpanan gambar tanda tangan) dan sudah menjadi praktik umum dokumen elektronik resmi di Indonesia (ijazah elektronik, sertifikat BPOM, dsb menggunakan QR verifikasi, bukan gambar tanda tangan basah discan).
- **Fase 2 (opsional, sesuai regulasi penuh Permenkes 24/2022, berbayar/perlu registrasi, dikerjakan belakangan):** integrasi ke **BSrE (Balai Sertifikasi Elektronik)** — PSrE resmi pemerintah di bawah BSSN, tanda tangan elektronik tersertifikasi berbasis PKI dengan kekuatan hukum penuh (UU ITE, PP 71/2019). Setiap dokter perlu registrasi sertifikat elektronik pribadi (by NIK) ke BSrE. Ini disiapkan sebagai *service layer* terpisah (`SignatureService`) agar mudah ditambahkan tanpa mengubah struktur data `document_signatures` yang sudah ada — cukup menambah field seperti `bsre_signature_payload` di kemudian hari.
- Setelah RME ditandatangani/dikunci, **field-field klinis menjadi read-only**; perubahan berikutnya wajib berupa entri **adendum baru** dengan referensi ke entri asal (jangan overwrite riwayat medis — ini prinsip wajib rekam medis).

**Formulir dinamis per spesialis:**

| Spesialis | Formulir Khusus |
|---|---|
| Penyakit Dalam | Ceklis anamnesis per sistem organ (kardiovaskular, respirasi, GI, dsb) + riwayat obat kronis (nama obat, dosis, sejak kapan) |
| Saraf | Kalkulator **GCS** (Glasgow Coma Scale: Eye 1-4, Verbal 1-5, Motor 1-6, auto-total) + pemeriksaan **12 saraf kranial** (checklist normal/abnormal per saraf + catatan) |
| Gigi | **Odontogram visual interaktif** — peta 32 gigi (dewasa) dengan status per gigi per permukaan (sehat, karies, tambalan, dicabut, mahkota, dst — gunakan kode odontogram standar), disimpan sebagai data terstruktur (bukan gambar statis) agar riwayat per gigi bisa dilacak dari waktu ke waktu |
| Radiologi | Form order berisi jenis pemeriksaan, klinis pengantar, lalu form ekspertise berisi **teks bacaan/hasil** terstruktur (deskripsi temuan + kesimpulan), plus lampiran citra. (Catatan: standar penuh Kemenkes untuk radiologi mengacu ke integrasi PACS/DICOM + SATUSEHAT `ImagingStudy` & `DiagnosticReport` — di luar scope MVP, dicatat sebagai roadmap) |
| Umum (semua poli) | SOAP dasar + vital signs dari perawat otomatis ter-load |

Formulir dinamis ini sebaiknya dibangun dengan pendekatan **template per spesialis** (bukan form builder generik dulu — terlalu kompleks untuk MVP), yaitu tabel `medical_record_forms` yang menyimpan data JSON terstruktur sesuai skema per spesialis, plus field SOAP standar yang selalu ada.

**E-Resep:**
- Dokter input resep langsung dari layar RME.
- **Obat kronis**: tombol "salin dari resep sebelumnya" — mengambil resep pasien yang sama pada kunjungan sebelumnya untuk mempercepat input (dengan opsi edit dosis/jumlah sebelum simpan).
- **Obat racikan** — rekomendasi solusi sesuai kebutuhan stok:
  - Racikan dicatat sebagai **item resep tersendiri** dengan tipe `racikan`, berisi daftar obat penyusun + takaran per obat + jumlah yang mau dibuat (misal "puyer 10 bungkus").
  - Saat resep racikan diproses di Farmasi, sistem **memecah pengurangan stok ke masing-masing obat penyusun** sesuai formula (bukan mengurangi stok "obat racikan" sebagai satu item, karena racikan bukan barang fisik di gudang).
  - Ditambahkan otomatis **biaya penggerusan/embalase** (lihat modul Farmasi) sebagai baris biaya tambahan, terpisah dari harga obat.
  - Racikan tidak disimpan sebagai master obat baru — cukup sebagai *transaksi* yang mereferensikan komponen obat asli, agar laporan stok & FEFO tetap akurat per obat dasar.

**Entitas kunci:** `medical_records`, `medical_record_forms` (JSON per spesialis), `icd10_codes`, `icd9cm_codes`, `medical_record_diagnoses`, `prescriptions`, `prescription_items`, `document_signatures`.

---

### 4.3 Laboratorium & Radiologi

**Alur:**
1. **Order internal** — dokter poli membuat rujukan pemeriksaan lab/rontgen langsung dari layar RME (tidak perlu kertas).
2. **Pasien datang langsung** juga bisa mendaftar untuk periksa lab/radiologi tanpa melalui poli dulu (jalur langsung — misal general check-up mandiri).
3. Petugas lab/radiologi menerima antrian order, melakukan pemeriksaan, lalu **input hasil**:
   - Hasil **teks/bacaan (ekspertise)** oleh Dokter Radiologi (untuk radiologi) atau Analis (untuk lab, bisa berupa nilai numerik per parameter + flag normal/abnormal berdasarkan rentang referensi).
   - **Upload berkas digital** (gambar rontgen/USG atau PDF hasil) yang otomatis tersemat/muncul di RME pasien pada kunjungan terkait.
4. Master data **jenis pemeriksaan** (nama, kategori lab/radiologi, satuan, nilai rujukan/rentang normal untuk lab, tarif) dikelola Superadmin.

**Entitas kunci:** `lab_radiology_orders`, `lab_radiology_order_items`, `lab_test_masters` (dengan rentang nilai normal), `lab_results`, `lab_result_attachments`.

---

### 4.4 Farmasi & Manajemen Stok (Inventory)

**Fitur inti:**
- **Multi-batch tracking**: setiap penerimaan obat dicatat sebagai batch tersendiri — nomor batch, tanggal kedaluwarsa, harga beli (bisa beda antar distributor/batch), jumlah, distributor.
- **FEFO (First Expired, First Out)**: saat obat keluar (baik untuk resep maupun penjualan umum), sistem otomatis memilih/menyarankan batch dengan tanggal kedaluwarsa terdekat lebih dulu — bukan berdasarkan urutan input (FIFO) atau manual.
- **Stok opname berkala**: fitur input hasil hitung fisik vs sistem, otomatis menghasilkan selisih (surplus/defisit) dan mencatat sebagai transaksi penyesuaian stok dengan alasan.
- **Notifikasi stok minimum**: setiap obat punya `minimum_stock`; sistem memberi alert (dashboard/notifikasi) saat stok total obat mendekati/di bawah ambang batas.
- **Notifikasi kedaluwarsa**: alert untuk batch yang mendekati expired (misal H-90/H-30, dapat dikonfigurasi).

**Entitas kunci:** `drugs` (master obat), `drug_batches`, `stock_movements` (in/out/adjustment dengan referensi ke sumber transaksi), `stock_opnames`, `stock_opname_items`.

---

### 4.5 Manajemen Apotek

**Alur:**
1. Menerima e-resep (biasa & racikan) dari dokter secara real-time (begitu dokter simpan resep, muncul di antrian apotek).
2. **Kalkulasi harga otomatis** berdasarkan preferensi klinik: markup bisa dikonfigurasi per obat/kategori sebagai **margin persentase** atau **harga flat** (Superadmin set di master obat/kategori).
3. Perhitungan otomatis untuk resep:
   - **Tuslah** (biaya jasa resep) — nominal per lembar resep atau per item, dikonfigurasi di master tarif.
   - **Embalase/biaya penggerusan** — khusus untuk racikan, nominal per racikan/bungkus, dikonfigurasi di master tarif.
4. Cetak: **struk pembayaran obat**, **kwitansi tindakan**, dan **etiket obat** (aturan pakai — misal "3x sehari 1 tablet sesudah makan", dengan nama pasien & tanggal).
5. **Penjualan bebas/umum** (bukan pasien klinik, tanpa resep dokter) — transaksi langsung di apotek seperti POS biasa, tetap mengikuti logika FEFO & mengurangi stok yang sama dengan yang dipakai pasien klinik.

**Entitas kunci:** `pharmacy_transactions` (bisa tipe `resep` atau `umum`), `pharmacy_transaction_items`, `tariffs` (master tarif tuslah/embalase/jasa lain).

---

### 4.6 Kasir / Billing

**Alur:**
1. Saat **registrasi**, sistem otomatis membuat record tagihan di tabel khusus **penampung tagihan per kunjungan** (`visit_bills`), lalu setiap layanan yang dikenakan biaya selama kunjungan (jasa dokter, tindakan, hasil lab/radiologi, obat dari apotek) **menambahkan baris tagihan** (`visit_bill_items`) ke tagihan kunjungan tersebut — bukan transaksi terpisah-pisah.
2. Jika pasien **tanpa resep obat**, alur langsung ke kasir setelah pemeriksaan selesai (tidak menunggu apotek).
3. Jika ada resep, tagihan obat baru ditambahkan **setelah apotek menyiapkan/acc resep** (karena harga final obat baru diketahui setelah diracik/dihitung apoteker).
4. **Master Tarif** dikelola Superadmin: tarif jasa dokter (dasar per dokter/per poli, via `doctor_tariffs`) **dan** tarif per tindakan spesifik (via `procedure_tariffs`, terhubung ke kode ICD-9-CM). Saat kalkulasi tagihan, sistem mengecek dulu apakah kunjungan tersebut punya tindakan dengan tarif spesifik; jika ada, tarif tindakan itu yang dipakai, jika tidak ada baru menggunakan tarif dasar dokter. Komponen biaya obat mengikuti kalkulasi dari modul Farmasi (poin 4.5).
5. Di layar Kasir: daftar nama pasien yang siap dibayar (status kunjungan = "menunggu pembayaran"). Klik nama pasien → tampil rincian tagihan (per item: jasa dokter, tindakan, obat, dll) dan total. Kasir menerima pembayaran (tunai/debit/transfer/QRIS — metode pembayaran dicatat untuk kebutuhan laporan keuangan), cetak struk.

**Entitas kunci:** `visit_bills`, `visit_bill_items`, `payments`, `doctor_tariffs`, `procedure_tariffs`.

---

### 4.7 Laporan

| Kategori | Isi |
|---|---|
| Operasional & Kunjungan | Grafik tren kunjungan harian/bulanan, demografi pasien (usia/gender/wilayah), statistik poli paling ramai |
| Medis | Top 10 penyakit terbanyak berdasarkan kode ICD-10 (periode dapat difilter) — untuk kebutuhan pelaporan internal & eksternal (mis. ke Dinkes) |
| Keuangan & Pendapatan | Rekap pendapatan harian/bulanan per metode pembayaran, total omset kasir, laporan HPP (Harga Pokok Penjualan) obat berdasarkan harga beli batch yang keluar |
| Logistik Farmasi | Obat fast-moving vs slow-moving, obat mendekati kedaluwarsa, riwayat transaksi stok opname |
| Ekspor | Semua laporan dapat diunduh ke **Excel** (maatwebsite/Laravel Excel) dan **PDF** (dompdf) dalam satu klik |

---

### 4.8 Settings

- Superadmin dapat melihat **log activity** seluruh sistem (siapa mengubah apa, kapan) via `spatie/laravel-activitylog`, dengan filter per user/modul/tanggal.
- Pengaturan menu dinamis, role & permission, master tarif, master obat, master pemeriksaan lab/radiologi, konfigurasi klinik (nama, alamat, logo untuk cetakan).

---

## 5. Non-Functional Requirements

- **Keamanan data medis**: enkripsi field sensitif (jika perlu), role-based access ketat di level route (bukan hanya UI), audit trail wajib untuk RME.
- **Konsistensi data keuangan**: setiap transaksi yang mengubah stok/tagihan harus dalam DB transaction (rollback-safe).
- **Cetak dokumen**: struk, kwitansi, etiket, dan laporan dicetak rapi via dompdf (atau thermal print untuk struk kasir/apotek jika diperlukan — perlu dikonfirmasi jenis printer yang dipakai klinik).
- **Auditability**: semua entri RME immutable setelah signed; perubahan lewat adendum.
- **Skalabilitas fase awal**: didesain untuk 1 klinik/1 cabang dulu (multi-cabang bukan scope MVP, tapi struktur data sebaiknya tidak menutup kemungkinan itu — beri kolom `branch_id` nullable sejak awal jika Anda mau future-proof, opsional).

---

## 6. Out of Scope (Fase MVP)

- Integrasi SATUSEHAT / BPJS.
- PACS/DICOM penuh untuk radiologi.
- Bot WhatsApp otomatis (pendaftaran online cukup via form web).
- Tanda tangan elektronik tersertifikasi BSrE (pakai signature-pad + hash dulu).
- Aplikasi mobile terpisah untuk pasien.

---

## 7. Keputusan Terkonfirmasi (menggantikan Open Questions versi sebelumnya)

1. **Kanal pendaftaran online**: MVP menggunakan **form web sederhana**, link dibagikan manual (misal via chat WA personal, bukan bot). Arsitektur dibuat agar `registration_channel`/`source` pada `visits`/`queues` sudah generik (`online`/`offline`), sehingga saat integrasi **WA Gateway** dikerjakan di kemudian hari (lihat Fase Masa Depan di `TASK_BREAKDOWN.md`), cukup menambah channel baru tanpa mengubah struktur inti.
2. **Kebijakan antrian pasien online yang telat check-in**: pasien tersebut **disisipkan ke antrian sesuai urutan pasien yang sudah hadir saat itu** (bukan hangus, bukan otomatis kembali ke posisi asal). Detail teknis: begitu pasien online check-in, sistem memberi nomor antrian baru yang disisipkan **setelah pasien-pasien offline yang sudah check-in lebih dulu di poli yang sama**, bukan mengantre dari nol di belakang semua orang dan bukan juga menyerobot ke depan. Ini akan diimplementasikan di `QueueService` sebagai bagian dari Fase 2.
3. **Printer kasir/apotek**: menggunakan **printer thermal**. Konsekuensi teknis dicatat di `TECH_STACK.md` bagian 8 — format cetak struk/etiket harus dirancang untuk lebar kertas thermal (umumnya 58mm/80mm), terpisah dari format laporan/kwitansi A4 yang tetap pakai dompdf.
4. **Tarif jasa dokter**: **fleksibel** — bisa disetel sebagai tarif dasar per dokter (di `doctor_tariffs`, sudah ada di skema) **dan** bisa juga bervariasi per tindakan tertentu (`procedure_tariffs`, sudah ada di skema). Tidak perlu perubahan skema database; perlu dipastikan saat kalkulasi tagihan (`VisitBillService`), sistem mengecek dulu apakah ada tarif tindakan spesifik yang berlaku, baru fallback ke tarif dasar dokter jika tidak ada.
5. **Modul appointment/booking jadwal dokter**: **disetujui untuk ditambahkan, tapi belakangan** (bukan MVP). Dicatat sebagai item roadmap di `TASK_BREAKDOWN.md` bagian "Fase Masa Depan", setelah alur walk-in + antrian inti stabil.
