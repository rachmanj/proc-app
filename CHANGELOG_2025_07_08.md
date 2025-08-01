# Changelog - 8 Juli 2025

## Ringkasan Perubahan

Implementasi fitur kolom "Day" dan sistem otomatis untuk update status Purchase Request berdasarkan attachment dan approval Purchase Order.

---

## 🆕 Fitur Baru

### 1. Kolom "Day" di Tabel Purchase Requests

#### Database Changes
- **Menambahkan kolom `day`** ke tabel `purchase_requests`
- **Tipe**: Integer (nullable)
- **Posisi**: Setelah kolom `generated_date`

#### Migration Files
- `2025_07_08_015801_add_day_column_to_purchase_requests_table.php`
- `2025_07_08_020049_modify_day_column_type_in_purchase_requests_table.php`

#### Model Changes
- **File**: `app/Models/PurchaseRequest.php`
- Menambahkan kolom `day` ke `fillable` array
- Menambahkan `protected $casts` untuk date fields
- Menambahkan accessor `getDayDifferenceAttribute()` untuk menghitung selisih hari
- Menambahkan `protected $appends` untuk include accessor dalam JSON response

### 2. Tampilan Kolom "Day" di UI

#### View Changes
- **File**: `resources/views/procurement/pr/list.blade.php`
- Menambahkan kolom "Day" ke header tabel
- Posisi: Setelah kolom "PR Number"
- Alignment: Center (`text-center`)
- Konfigurasi DataTables untuk kolom "day"

#### Controller Changes
- **File**: `app/Http/Controllers/Procurement/PRController.php`
- Update method `data()` untuk menambahkan kolom day
- Update method `search()` untuk konsistensi
- Update validation rule untuk include "progress" dan "approved"

### 3. Sistem Otomatis Update Status PR

#### 3.1 Status "Progress" - Berdasarkan Attachment

**Observer**: `app/Observers/PrAttachmentObserver.php`
- **Event `created`**: Auto-update `pr_status` menjadi "progress" saat attachment diupload
- **Event `deleted`**: Auto-revert `pr_status` ke "OPEN" jika tidak ada attachment lagi

**Registration**: `app/Providers/AppServiceProvider.php`

#### 3.2 Status "Approved" - Berdasarkan Purchase Order

**Observer**: `app/Observers/PurchaseOrderObserver.php`
- **Event `updated`**: Auto-update `pr_status` menjadi "approved" saat semua PO dengan pr_no yang sama sudah approved
- Logging untuk tracking perubahan status

**Observer**: `app/Observers/PurchaseRequestObserver.php`
- **Event `updating`**: Menyimpan nilai day terakhir saat status berubah menjadi "approved"
- Mencegah kehilangan data historis

### 4. Penghentian Perhitungan Day untuk Status Approved

**Logic**: Ketika `pr_status` = "approved", kolom day akan menampilkan nilai tersimpan dari database, bukan perhitungan real-time.

**Manfaat**:
- Histori terpelihara
- Performa lebih baik
- Data konsisten

---

## 🎨 Perubahan UI/UX

### 1. Badge Status dengan Warna Baru

#### List View (`resources/views/procurement/pr/list.blade.php`)
- **OPEN**: Hijau (`badge-success`)
- **progress**: Biru (`badge-info`)
- **approved**: Biru tua (`badge-primary`)
- **CLOSED**: Abu-abu (`badge-secondary`)

#### Edit View (`resources/views/procurement/pr/edit.blade.php`)
- Konsisten dengan styling list view

### 2. Urutan Tabel Default

**Perubahan**: Tabel diurutkan berdasarkan kolom "Day" (descending)
- PR dengan hari terbesar (paling lama) ditampilkan di atas
- PR dengan hari terkecil (paling baru) ditampilkan di bawah

---

## 🔧 Command Line Tools

### 1. Update Status untuk Attachment yang Sudah Ada

**Command**: `php artisan pr:update-status-for-attachments`
**File**: `app/Console/Commands/UpdatePrStatusForExistingAttachments.php`

**Fungsi**: Mengupdate status PR yang sudah memiliki attachment sebelum Observer diimplementasikan.

**Hasil**: 4 PR berhasil diupdate ke status "progress"

### 2. Update Status untuk PO yang Sudah Approved

**Command**: `php artisan pr:update-status-for-approved-pos`
**File**: `app/Console/Commands/UpdatePrStatusForApprovedPOs.php`

**Fungsi**: Mengupdate status PR yang semua PO-nya sudah approved.

### 3. Simpan Nilai Day untuk PR yang Sudah Approved

**Command**: `php artisan pr:save-day-value-for-approved`
**File**: `app/Console/Commands/SaveDayValueForApprovedPRs.php`

**Fungsi**: Menyimpan nilai day terakhir untuk PR yang sudah approved.

**Hasil**: 1 PR (250170111) berhasil disimpan dengan day value: 4

---

## 📊 Status Workflow

### Alur Status Purchase Request

```
OPEN → progress → approved
  ↑        ↑         ↑
  |        |         |
  |        |         └─ Semua PO approved
  |        └─ Ada attachment
  └─ Default status
```

### Observer Chain

1. **PrAttachmentObserver** → Status: OPEN → progress
2. **PurchaseOrderObserver** → Status: progress → approved  
3. **PurchaseRequestObserver** → Simpan day value terakhir

---

## 🔍 Validation Rules

### Purchase Request Controller

```php
'pr_status' => 'required|string|in:OPEN,CLOSED,progress,approved'
```

---

## 📝 Database Schema Changes

### Tabel `purchase_requests`

```sql
ALTER TABLE purchase_requests 
ADD COLUMN day INT(11) NULL 
AFTER generated_date;
```

### Relasi dengan Tabel Lain

- **purchase_requests.pr_no** ↔ **purchase_orders.pr_no** (One-to-Many)
- **purchase_requests.id** ↔ **pr_attachments** (Many-to-Many via pivot)

---

## 🚀 Cara Penggunaan

### 1. Menambahkan Attachment
1. Buka halaman PR detail
2. Upload attachment
3. Status otomatis berubah menjadi "progress"
4. Kolom "Day" tetap menghitung selisih hari

### 2. Approve Purchase Order
1. Approve semua PO yang terkait dengan PR
2. Status PR otomatis berubah menjadi "approved"
3. Kolom "Day" berhenti menghitung dan menampilkan nilai terakhir

### 3. Monitoring via List View
- Tabel otomatis diurutkan berdasarkan kolom "Day" (terbesar dulu)
- Badge warna menunjukkan status:
  - Hijau: OPEN
  - Biru muda: progress
  - Biru tua: approved
  - Abu-abu: CLOSED

---

## 🐛 Bug Fixes

### 1. Kolom "Day" Kosong Setelah Status Approved

**Issue**: Kolom "Day" menjadi kosong setelah `pr_status` berubah menjadi "approved"

**Root Cause**: 
- `PurchaseOrderObserver` menggunakan mass update dengan query builder
- Mass update tidak memicu model events (`updating` event)
- `PurchaseRequestObserver::updating()` tidak terpanggil
- Nilai `day` tidak tersimpan saat status berubah

**Solution**: 
- **File**: `app/Observers/PurchaseOrderObserver.php`
- **Method**: `checkAndUpdatePrStatus()`
- **Perubahan**: Mengganti mass update dengan individual model updates

**Sebelum**:
```php
$updated = PurchaseRequest::where('pr_no', $purchaseOrder->pr_no)
    ->update(['pr_status' => 'approved']);
```

**Sesudah**:
```php
// Get individual PurchaseRequest models to trigger Observer events
$purchaseRequests = PurchaseRequest::where('pr_no', $purchaseOrder->pr_no)->get();

$updated = 0;
foreach ($purchaseRequests as $pr) {
    if ($pr->pr_status !== 'approved') {
        $pr->update(['pr_status' => 'approved']);
        $updated++;
    }
}
```

**Hasil**: 
- Individual model updates memicu `updating` event
- `PurchaseRequestObserver::updating()` terpanggil
- Nilai `day` tersimpan sebelum status berubah
- Kolom "Day" tetap menampilkan nilai terakhir yang valid

**Command untuk Data Existing**:
```bash
php artisan pr:save-day-value-for-approved
```

---

## 🔒 Security & Performance

### Performance Optimization
- Kolom "Day" untuk status approved tidak dihitung ulang
- Menggunakan accessor untuk perhitungan real-time
- Observer pattern untuk efficiency

### Data Integrity
- Validation rules untuk memastikan status valid
- Observer untuk menjaga konsistensi data
- Logging untuk tracking perubahan

---

## 📋 Testing Checklist

- [x] Kolom "Day" muncul di tabel list
- [x] Perhitungan selisih hari bekerja dengan benar
- [x] Status berubah otomatis saat upload attachment
- [x] Status berubah otomatis saat semua PO approved
- [x] Perhitungan day berhenti untuk status approved
- [x] Badge warna sesuai dengan status
- [x] Urutan tabel berdasarkan kolom day
- [x] Command line tools berfungsi dengan baik
- [x] **Bug Fix**: Kolom "Day" tetap terisi setelah status approved

---

## 🐛 Known Issues

- ~~Kolom "Day" menjadi kosong setelah status approved~~ ✅ **FIXED**

Tidak ada issue yang diketahui saat ini.

---

## 📚 File yang Dimodifikasi

### Models
- `app/Models/PurchaseRequest.php`

### Controllers
- `app/Http/Controllers/Procurement/PRController.php`

### Views
- `resources/views/procurement/pr/list.blade.php`
- `resources/views/procurement/pr/edit.blade.php`

### Observers
- `app/Observers/PrAttachmentObserver.php`
- `app/Observers/PurchaseOrderObserver.php`
- `app/Observers/PurchaseRequestObserver.php`

### Providers
- `app/Providers/AppServiceProvider.php`

### Commands
- `app/Console/Commands/UpdatePrStatusForExistingAttachments.php`
- `app/Console/Commands/UpdatePrStatusForApprovedPOs.php`
- `app/Console/Commands/SaveDayValueForApprovedPRs.php`

### Migrations
- `database/migrations/2025_07_08_015801_add_day_column_to_purchase_requests_table.php`
- `database/migrations/2025_07_08_020049_modify_day_column_type_in_purchase_requests_table.php`

---

## 🎯 Next Steps

1. **Monitoring**: Pantau performa sistem dengan fitur baru
2. **Testing**: Lakukan testing lebih mendalam di environment production
3. **Documentation**: Update user manual untuk fitur baru
4. **Training**: Berikan training kepada user tentang fitur baru

---

**Dibuat oleh**: AI Assistant  
**Tanggal**: 8 Juli 2025  
**Versi**: 1.0 