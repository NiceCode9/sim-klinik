# DATABASE SCHEMA (ERD Ringkas) — SIMKLINIK

Dokumen ini berisi rancangan tabel level tinggi (bukan migration final) sebagai acuan AI agent membangun migration & model secara konsisten. Nama kolom bisa disesuaikan minor saat implementasi, tapi **relasi dan tujuan tabel harus tetap seperti ini**.

---

## 1. Modul Akses & Menu Dinamis

**roles, permissions, model_has_roles, model_has_permissions, role_has_permissions**
→ bawaan `spatie/laravel-permission`, tidak perlu dibuat manual.

**menus**
- id, parent_id (nullable, self-reference), name, icon, route_name (nullable), permission_name (FK ke nama permission), order, is_active

**activity_log**
→ bawaan `spatie/laravel-activitylog`.

---

## 2. Master Data Pasien & Kepegawaian

**patients**
- id, medical_record_number (unik, auto-generate), nik (nullable, unik jika diisi), name, gender, birth_date, phone, address, blood_type (nullable), allergies (text, nullable — penting untuk keselamatan pasien), created_at, updated_at

**employees** (dokter, perawat, apoteker, dst — data kepegawaian umum)
- id, user_id (FK users, nullable jika staff belum punya akun login), name, employee_type (dokter/perawat/apoteker/kasir/analis/radiografer/resepsionis), specialization_id (FK ke `specializations`, nullable — untuk dokter), str_number (nomor izin praktik, nullable, ditampilkan di halaman verifikasi QR & cetakan dokumen), phone, is_active

**specializations**
- id, name (Penyakit Dalam, Saraf, Gigi, Radiologi, Laboratorium, Umum)

---

## 3. Pendaftaran & Antrian

**queues**
- id, visit_id (FK), queue_number (integer, reset harian per poli), poly/specialization_id, status (enum: `waiting_online_confirmation`, `waiting`, `called`, `in_progress`, `done`, `skipped`), source (`online`/`offline`), called_at, checked_in_at

**visits** (satu kunjungan pasien ke klinik pada satu tanggal)
- id, patient_id (FK), specialization_id (FK, poli tujuan), employee_id (FK dokter yang menangani, nullable saat baru daftar), visit_date, status (enum: `registered`, `vital_check`, `waiting_doctor`, `in_examination`, `waiting_pharmacy`, `waiting_payment`, `completed`, `cancelled`), registration_channel (`online`/`offline`)

**visit_vitals** (TTV oleh perawat)
- id, visit_id (FK), employee_id (perawat yang input), blood_pressure, pulse, temperature, respiration_rate, height_cm, weight_kg, chief_complaint (keluhan awal), recorded_at

---

## 4. Rekam Medis Elektronik

**medical_records**
- id, visit_id (FK, unik per visit), employee_id (dokter), subjective (text), objective (text), assessment (text), plan (text), status (`draft`/`signed`), signed_at (nullable), created_at, updated_at

**medical_record_forms** (formulir dinamis per spesialis, JSON per skema spesialis)
- id, medical_record_id (FK), specialization_id (FK), form_type (`internal_medicine`/`neurology`/`dental`/`radiology`), data (JSON — struktur berbeda tiap `form_type`, contoh: neurology berisi {gcs: {eye, verbal, motor, total}, cranial_nerves: [...]})

**odontogram_records** (khusus gigi, dipisah dari JSON generik karena butuh query per gigi)
- id, medical_record_id (FK), tooth_number (1-32 atau notasi FDI), surface (nullable — mesial/distal/oklusal/dst), condition (karies/tambalan/dicabut/mahkota/normal/dst), notes

**icd10_codes**
- id, code (unik), description

**icd9cm_codes**
- id, code (unik), description

**medical_record_diagnoses**
- id, medical_record_id (FK), icd10_code_id (FK, nullable jika diagnosis tanpa kode standar), diagnosis_type (`primary`/`secondary`), notes

**medical_record_procedures** (tindakan dengan kode ICD-9-CM)
- id, medical_record_id (FK), icd9cm_code_id (FK), tariff_id (FK ke `procedure_tariffs`), notes

**document_signatures** (audit integritas & QR verifikasi tanda tangan elektronik)
- id, signable_type, signable_id (polymorphic — bisa untuk medical_records, prescriptions, dst), employee_id, content_hash (SHA-256 dari snapshot konten dokumen), verification_token (string acak unik, panjang ±40 karakter, dipakai di URL QR `/verify/{token}` — **bukan ID auto-increment**), signed_at

> Catatan: `signature_image_path` (gambar tanda tangan hasil canvas) **tidak lagi dipakai** — digantikan sepenuhnya oleh mekanisme QR verifikasi (lihat `TECH_STACK.md` bagian 9). QR digenerate on-the-fly dari `verification_token`, tidak perlu kolom/file tambahan untuk menyimpan gambar QR.

**medical_record_addendums** (perubahan setelah RME signed — bukan overwrite)
- id, medical_record_id (FK), employee_id, content (text), created_at

---

## 5. E-Resep

**prescriptions**
- id, medical_record_id (FK), employee_id (dokter), status (`pending`/`processed`/`dispensed`), created_at

**prescription_items**
- id, prescription_id (FK), item_type (`single`/`compound_component`), drug_id (FK ke `drugs`), dosage_instruction (text, misal "3x1 tablet sesudah makan"), quantity, compound_group_id (nullable — mengelompokkan item-item penyusun satu racikan; jika null berarti obat tunggal)

**compound_prescriptions** (grup racikan — 1 baris per resep racikan)
- id, prescription_id (FK), compound_name (misal "Puyer batuk"), total_units (jumlah bungkus/kapsul yang dibuat), embalase_tariff_id (FK ke `tariffs`)

> Catatan implementasi: saat memproses resep racikan, sistem menghitung total bahan tiap obat penyusun = (dosis per unit × total_units), lalu mengurangi stok obat dasar tersebut via `stock_movements`, bukan membuat item stok baru bernama racikan.

---

## 6. Laboratorium & Radiologi

**lab_test_masters**
- id, name, category (`lab`/`radiology`), unit (nullable, untuk hasil numerik lab), normal_range_min (nullable), normal_range_max (nullable), tariff_id (FK)

**lab_radiology_orders**
- id, visit_id (FK), ordered_by_employee_id (dokter pemesan, nullable jika pasien datang langsung), order_source (`referral`/`walk_in`), status (`ordered`/`in_process`/`completed`)

**lab_radiology_order_items**
- id, order_id (FK), lab_test_master_id (FK), status (`pending`/`done`)

**lab_results**
- id, order_item_id (FK), performed_by_employee_id (analis/dokter radiologi), result_value (nullable, untuk lab numerik), result_text (nullable, untuk ekspertise radiologi/lab kualitatif), flag (`normal`/`abnormal`/`critical`, nullable), verified_at

**lab_result_attachments** (via spatie/medialibrary bisa juga dipakai langsung, tabel ini opsional jika ingin custom)
- id, lab_result_id (FK), file_path, file_type (`image`/`pdf`)

---

## 7. Farmasi & Inventory

**drugs**
- id, code, name, category, unit (tablet/botol/ml/dst), is_fractional (boolean), pricing_type (`margin_percentage`/`flat`), price_value (decimal — persen atau nominal flat tergantung pricing_type), minimum_stock, is_active

**drug_batches**
- id, drug_id (FK), batch_number, expired_date, purchase_price (DECIMAL 15,2), quantity_in, quantity_remaining, distributor_name, received_at

**stock_movements**
- id, drug_id (FK), drug_batch_id (FK, nullable untuk movement yang tidak spesifik batch seperti adjustment awal), movement_type (`in`/`out`/`adjustment`), quantity, reference_type (polymorphic — bisa `prescription_item`, `pharmacy_transaction_item`, `stock_opname_item`), reference_id, notes, created_by_employee_id, created_at

**stock_opnames**
- id, employee_id (pelaksana), opname_date, status (`draft`/`finalized`), notes

**stock_opname_items**
- id, stock_opname_id (FK), drug_batch_id (FK), system_quantity, physical_quantity, difference (computed: physical - system)

---

## 8. Apotek / Transaksi Farmasi

**pharmacy_transactions**
- id, transaction_type (`prescription`/`general_sale`), prescription_id (FK, nullable jika general_sale), visit_id (FK, nullable jika general_sale), employee_id (apoteker), total_amount (DECIMAL 15,2), status (`draft`/`completed`), created_at

**pharmacy_transaction_items**
- id, pharmacy_transaction_id (FK), drug_id (FK), drug_batch_id (FK, hasil pilihan FEFO), quantity, unit_price, subtotal

**tariffs** (master tarif serbaguna: tuslah, embalase, tindakan, jasa dokter, dsb)
- id, tariff_type (`tuslah`/`embalase`/`procedure`/`doctor_fee`/`other`), name, amount (DECIMAL 15,2), is_active

---

## 9. Billing / Kasir

**visit_bills** (satu per visit, dibuat otomatis saat registrasi)
- id, visit_id (FK, unik), status (`open`/`paid`/`cancelled`), total_amount (DECIMAL 15,2, computed dari sum items), created_at

**visit_bill_items**
- id, visit_bill_id (FK), item_type (`doctor_fee`/`procedure`/`lab_test`/`radiology`/`drug`/`tuslah`/`embalase`), reference_type, reference_id (polymorphic ke sumber biaya), description, amount (DECIMAL 15,2)

**payments**
- id, visit_bill_id (FK), employee_id (kasir), payment_method (`cash`/`debit`/`transfer`/`qris`), amount_paid (DECIMAL 15,2), change_amount (DECIMAL 15,2, nullable), paid_at

**doctor_tariffs**
- id, employee_id (FK dokter), specialization_id (nullable jika flat semua poli), amount (DECIMAL 15,2)

**procedure_tariffs**
- id, icd9cm_code_id (FK, nullable jika tindakan custom tanpa kode), name, amount (DECIMAL 15,2)

---

## 10. Relasi Kunci (ringkasan)

```
patients 1—* visits
visits 1—1 queues
visits 1—1 visit_vitals
visits 1—1 medical_records
medical_records 1—* medical_record_forms
medical_records 1—* medical_record_diagnoses —* icd10_codes
medical_records 1—* medical_record_procedures —* icd9cm_codes
medical_records 1—1 prescriptions
prescriptions 1—* prescription_items —* drugs
prescriptions 1—* compound_prescriptions
visits 1—* lab_radiology_orders 1—* lab_radiology_order_items —1 lab_results
drugs 1—* drug_batches
drugs/drug_batches 1—* stock_movements
prescriptions 1—1 pharmacy_transactions 1—* pharmacy_transaction_items —* drug_batches
visits 1—1 visit_bills 1—* visit_bill_items
visit_bills 1—* payments
```

---

## 11. Catatan untuk AI Agent Saat Membuat Migration

- Semua kolom uang: `decimal(15,2)`, **jangan `float`/`double`**.
- Semua FK wajib pakai `foreignId()->constrained()` dengan `onDelete` yang eksplisit dipikirkan (biasanya `restrict` untuk data transaksi & master, `cascade` hanya untuk child murni seperti `*_items`).
- Kolom `status` gunakan Enum PHP (`app/Enums`) yang dipetakan ke kolom `string`/`enum` DB — jangan magic string tersebar di banyak file.
- Tabel dengan kebutuhan audit (medical_records, prescriptions, payments, stock_movements) wajib menggunakan trait `LogsActivity` dari `spatie/laravel-activitylog`.
- Seeder wajib untuk: `permissions`, `menus`, `icd10_codes`, `icd9cm_codes`, role default (Superadmin, Resepsionis, Perawat, Dokter, Apoteker, Kasir).
