# TECHNICAL SPECS — Detail Implementasi Kritis

Dokumen ini melengkapi `TASK_BREAKDOWN.md` untuk dua bagian yang paling rawan salah implementasi jika hanya mengandalkan deskripsi umum: **skema JSON form spesialis** dan **algoritma FEFO**. AI agent wajib mengikuti spesifikasi ini persis, tidak mengarang struktur sendiri.

---

## BAGIAN A — Skema JSON `medical_record_forms.data`

Kolom `data` di tabel `medical_record_forms` adalah JSON dengan struktur **berbeda per `form_type`**. Berikut skema wajib untuk masing-masing.

### A.1 `form_type = internal_medicine` (Penyakit Dalam)

```json
{
  "anamnesis_sistem_organ": {
    "kardiovaskular": { "checked": true, "notes": "nyeri dada saat aktivitas" },
    "respirasi": { "checked": false, "notes": "" },
    "gastrointestinal": { "checked": true, "notes": "mual, tidak muntah" },
    "muskuloskeletal": { "checked": false, "notes": "" },
    "genitourinari": { "checked": false, "notes": "" },
    "endokrin": { "checked": false, "notes": "" },
    "hematologi_limfatik": { "checked": false, "notes": "" },
    "kulit_integumen": { "checked": false, "notes": "" }
  },
  "riwayat_obat_kronis": [
    {
      "nama_obat": "Amlodipine",
      "dosis": "10mg",
      "frekuensi": "1x sehari",
      "sejak": "2023-01-15",
      "keterangan": "rutin, kontrol tekanan darah"
    }
  ]
}
```

**Aturan validasi:**
- `anamnesis_sistem_organ`: object dengan 8 key tetap di atas (jangan menambah/mengurangi key secara dinamis dari UI — daftar sistem organ ini fixed, kalau mau menambah kategori baru harus lewat perubahan skema yang disengaja, bukan otomatis dari input user).
- `riwayat_obat_kronis`: array, boleh kosong `[]`. Setiap item wajib punya `nama_obat` dan `dosis` (required), field lain opsional (nullable).
- Field `notes` selalu string, default `""` bukan `null` (memudahkan render di Blade tanpa null-check berulang).

### A.2 `form_type = neurology` (Saraf)

```json
{
  "gcs": {
    "eye": 4,
    "verbal": 5,
    "motor": 6,
    "total": 15
  },
  "cranial_nerves": [
    { "nerve_number": 1, "nerve_name": "N. Olfaktorius", "status": "normal", "notes": "" },
    { "nerve_number": 2, "nerve_name": "N. Optikus", "status": "normal", "notes": "" },
    { "nerve_number": 3, "nerve_name": "N. Okulomotorius", "status": "normal", "notes": "" },
    { "nerve_number": 4, "nerve_name": "N. Troklearis", "status": "normal", "notes": "" },
    { "nerve_number": 5, "nerve_name": "N. Trigeminus", "status": "normal", "notes": "" },
    { "nerve_number": 6, "nerve_name": "N. Abducens", "status": "normal", "notes": "" },
    { "nerve_number": 7, "nerve_name": "N. Fasialis", "status": "normal", "notes": "" },
    { "nerve_number": 8, "nerve_name": "N. Vestibulokoklearis", "status": "normal", "notes": "" },
    { "nerve_number": 9, "nerve_name": "N. Glosofaringeus", "status": "normal", "notes": "" },
    { "nerve_number": 10, "nerve_name": "N. Vagus", "status": "normal", "notes": "" },
    { "nerve_number": 11, "nerve_name": "N. Aksesorius", "status": "normal", "notes": "" },
    { "nerve_number": 12, "nerve_name": "N. Hipoglosus", "status": "normal", "notes": "" }
  ]
}
```

**Aturan validasi & kalkulasi:**
- `gcs.eye`: integer 1-4. `gcs.verbal`: integer 1-5. `gcs.motor`: integer 1-6.
- `gcs.total` **wajib dihitung otomatis oleh backend** (`eye + verbal + motor`), **jangan menerima nilai `total` dari input user mentah-mentah** — hitung ulang di Service/Form Request sebelum simpan, untuk mencegah data tidak konsisten. Range hasil: 3-15.
- Interpretasi (tampilkan sebagai info, bukan disimpan sebagai field terpisah): 13-15 = ringan/compos mentis, 9-12 = sedang, ≤8 = berat/koma — hitung on-the-fly di view, tidak perlu kolom tambahan.
- `cranial_nerves`: array **selalu 12 item, urutan tetap sesuai `nerve_number` 1-12** (jangan biarkan array ini dinamis panjangnya). `status` adalah enum string: `"normal"` atau `"abnormal"` — jika `"abnormal"`, field `notes` sebaiknya divalidasi wajib diisi (di level Form Request, bukan constraint DB).

### A.3 `form_type = dental` (Gigi)

Data per-gigi **tidak disimpan di JSON ini**, melainkan di tabel terpisah `odontogram_records` (lihat `DATABASE_SCHEMA.md` bagian 4) karena butuh query per gigi untuk riwayat. Kolom `data` di `medical_record_forms` untuk `form_type = dental` hanya menyimpan catatan umum:

```json
{
  "keluhan_gigi": "nyeri gigi geraham kanan bawah sejak 3 hari",
  "oklusi": "normal",
  "kebersihan_mulut": "sedang",
  "catatan_umum": ""
}
```

**Skema tabel `odontogram_records` (per baris = satu temuan pada satu gigi/permukaan):**

| Field | Tipe | Keterangan |
|---|---|---|
| tooth_number | integer | Notasi FDI 2 digit (11-18, 21-28, 31-38, 41-48 untuk dewasa; 51-55 dst untuk anak jika diperlukan nanti) |
| surface | string, nullable | `mesial`/`distal`/`oklusal`/`bukal`/`lingual`/`palatal`, null jika kondisi mencakup seluruh gigi |
| condition | enum string | `sehat`, `karies`, `tambalan`, `dicabut`, `mahkota`, `impaksi`, `nekrosis`, `lainnya` |
| notes | text, nullable | |

**Aturan render odontogram visual:** frontend menggambar 32 kotak gigi (posisi tetap sesuai notasi FDI), lalu mewarnai/menandai tiap gigi berdasarkan `condition` terakhir yang tercatat untuk gigi tersebut pada kunjungan itu. Riwayat gigi yang sama di kunjungan berbeda **tidak menimpa** baris lama — setiap kunjungan punya baris odontogram sendiri (terhubung ke `medical_record_id` yang berbeda), sehingga riwayat per gigi dari waktu ke waktu tetap bisa ditelusuri.

### A.4 `form_type = radiology` (Radiologi)

```json
{
  "order": {
    "jenis_pemeriksaan": "Rontgen Thorax PA",
    "klinis_pengantar": "batuk kronis 2 minggu, curiga TB paru"
  },
  "ekspertise": {
    "deskripsi_temuan": "Cor tidak membesar, sinus costophrenicus tajam, tidak tampak infiltrat...",
    "kesimpulan": "Tidak tampak kelainan radiologis pada foto thorax saat ini"
  }
}
```

**Aturan:**
- `order` diisi oleh dokter poli saat membuat rujukan (lewat `lab_radiology_orders`, bukan langsung ke `medical_record_forms` — field `order` di sini adalah salinan ringkas untuk ditampilkan kembali di RME, sumber utama tetap tabel `lab_radiology_orders`).
- `ekspertise` diisi oleh dokter radiologi/analis setelah pemeriksaan (dari `lab_results.result_text`, disalin/ditautkan kembali ke sini agar muncul utuh di riwayat RME pasien). **Jangan duplikasi logic penyimpanan** — `lab_results` tetap single source of truth, field ini hanya representasi tampilan.
- File citra (gambar/PDF) **tidak** disimpan di JSON ini — tetap lewat `spatie/laravel-medialibrary` terhubung ke `lab_results` (lihat `lab_result_attachments`).

---

## BAGIAN B — Algoritma FEFO (`FefoStockService`)

### B.1 Prinsip Dasar

Setiap pengurangan stok obat (resep biasa, komponen racikan, atau penjualan umum) **wajib** memanggil satu method terpusat, jangan menulis ulang logic FEFO di controller/service lain.

```php
FefoStockService::deduct(
    int $drugId,
    float $quantity,
    string $referenceType,   // contoh: PrescriptionItem::class
    int $referenceId,
    int $createdByEmployeeId
): array // return: array of ['drug_batch_id' => ..., 'quantity_taken' => ...]
```

### B.2 Pseudocode

```
FUNGSI deduct(drugId, quantity, referenceType, referenceId, employeeId):

    MULAI DB TRANSACTION

    // Kunci baris batch agar tidak terjadi race condition saat 2 transaksi
    // (misal 2 apoteker memproses resep berbeda untuk obat yang sama) berjalan bersamaan
    batches = QUERY drug_batches
              WHERE drug_id = drugId
                AND quantity_remaining > 0
                AND expired_date >= TODAY   -- batch yang SUDAH kedaluwarsa tidak boleh dipakai
              ORDER BY expired_date ASC     -- inti FEFO: expired paling dekat diambil duluan
              LOCK FOR UPDATE

    totalAvailable = SUM(batches.quantity_remaining)

    JIKA totalAvailable < quantity:
        ROLLBACK TRANSACTION
        THROW InsufficientStockException(
            "Stok obat tidak cukup. Tersedia: {totalAvailable}, dibutuhkan: {quantity}"
        )
        // PENTING: tidak boleh mengambil sebagian lalu gagal di tengah (partial deduction).
        // Kegagalan harus all-or-nothing per pemanggilan deduct().

    remaining = quantity
    hasil = []

    UNTUK SETIAP batch DALAM batches:
        JIKA remaining <= 0:
            BERHENTI

        diambil = MIN(batch.quantity_remaining, remaining)

        UPDATE batch.quantity_remaining -= diambil

        INSERT INTO stock_movements (
            drug_id: drugId,
            drug_batch_id: batch.id,
            movement_type: 'out',
            quantity: diambil,
            reference_type: referenceType,
            reference_id: referenceId,
            created_by_employee_id: employeeId
        )

        TAMBAHKAN {drug_batch_id: batch.id, quantity_taken: diambil} KE hasil
        remaining -= diambil

    KOMIT TRANSACTION
    KEMBALIKAN hasil
```

### B.3 Kasus Khusus yang Wajib Ditangani

1. **Batch kedaluwarsa tidak ikut dihitung sebagai stok tersedia.** Batch dengan `expired_date < TODAY` dikeluarkan dari kandidat FEFO meskipun `quantity_remaining > 0` — batch ini seharusnya sudah ditangani lewat alur stok opname/write-off (bukan dijual/dipakai).
2. **Stok tidak cukup = transaksi gagal total, bukan sebagian.** Jangan pernah mengeluarkan obat sebagian lalu membiarkan resep "setengah terpenuhi" — ini bisa membingungkan pasien & tidak sesuai standar keselamatan farmasi. Tampilkan pesan error yang jelas ke Apoteker agar bisa mengambil keputusan (misal substitusi obat atau minta pasien tunggu re-stock).
3. **Row locking wajib** (`lockForUpdate()` di Eloquent / `LOCK FOR UPDATE` di query builder) untuk mencegah dua transaksi bersamaan sama-sama membaca `quantity_remaining` yang sama sebelum salah satu commit (race condition klasik yang menyebabkan stok minus).
4. **Racikan (compound prescription):** untuk setiap komponen racikan, hitung dulu total kebutuhan = `dosis_per_unit × total_units` (lihat `DATABASE_SCHEMA.md` bagian 5), baru panggil `deduct()` **per komponen obat**, semuanya di dalam **satu DB transaction besar** yang membungkus seluruh proses resep — jika salah satu komponen gagal (stok kurang), seluruh proses resep (bukan cuma komponen itu) di-rollback, supaya tidak ada racikan yang setengah jadi.
5. **Idempotency saat retry:** jika proses resep gagal di tengah jalan (misal error jaringan) dan Apoteker klik "proses" lagi, pastikan tidak terjadi pengurangan stok dobel — cek dulu apakah `pharmacy_transactions` untuk resep tersebut sudah `completed` sebelum menjalankan `deduct()` lagi.
6. **Penjualan umum (non-resep)** memanggil `deduct()` yang sama persis — jangan buat jalur FEFO terpisah untuk penjualan umum vs resep pasien, supaya konsistensi logic terjamin di satu tempat.

### B.4 Unit Test Wajib untuk `FefoStockService`

AI agent wajib membuat test untuk skenario berikut sebelum menandai fitur farmasi selesai:

- Stok cukup di satu batch saja → berhasil, batch yang benar (expired terdekat) yang berkurang.
- Stok perlu diambil dari 2+ batch sekaligus (batch pertama tidak cukup) → berhasil, kombinasi batch benar sesuai urutan FEFO.
- Stok total tidak cukup → exception dilempar, **tidak ada** perubahan `quantity_remaining` sama sekali (rollback sempurna, cek via assertion setelah exception).
- Batch kedaluwarsa diabaikan meski stoknya banyak.
- Dua pemanggilan `deduct()` untuk obat yang sama dijalankan "bersamaan" (simulasi race condition di test, jika memungkinkan) tidak menghasilkan stok minus.

---

## Referensi Silang

- Struktur tabel terkait → `DATABASE_SCHEMA.md` bagian 4 (RME), 6 (Lab/Radiologi), 7 (Inventory).
- Task terkait → `TASK_BREAKDOWN.md` Fase 3 (form spesialis) dan Fase 5 (FEFO).
- Aturan umum coding → `AGENTS.md`.
