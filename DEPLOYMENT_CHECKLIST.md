# Deployment Checklist - Server 192.168.32.13

## 📋 Pre-Deployment Checklist

### 1. Environment Configuration
- [ ] Update `.env` file untuk environment production/staging
- [ ] Set `APP_ENV=production` atau `APP_ENV=staging`
- [ ] Set `APP_DEBUG=false` untuk production
- [ ] Update `APP_URL=http://192.168.32.13`
- [ ] Konfigurasi database credentials
- [ ] Set `APP_KEY` yang secure

### 2. Database Preparation
- [ ] Backup database existing (jika ada)
- [ ] Run migration baru:
  ```bash
  php artisan migrate
  ```
- [ ] Run seeder jika diperlukan:
  ```bash
  php artisan db:seed
  ```

### 3. Observer Registration
- [ ] Pastikan Observer sudah registered di `AppServiceProvider`:
  ```php
  // app/Providers/AppServiceProvider.php
  use App\Models\PrAttachment;
  use App\Models\PurchaseOrder;
  use App\Models\PurchaseRequest;
  use App\Observers\PrAttachmentObserver;
  use App\Observers\PurchaseOrderObserver;
  use App\Observers\PurchaseRequestObserver;

  public function boot()
  {
      PrAttachment::observe(PrAttachmentObserver::class);
      PurchaseOrder::observe(PurchaseOrderObserver::class);
      PurchaseRequest::observe(PurchaseRequestObserver::class);
  }
  ```

### 4. Data Migration Commands
Jalankan commands berikut untuk data existing:

```bash
# Update status PR yang sudah punya attachment
php artisan pr:update-status-for-attachments

# Update status PR yang semua PO-nya sudah approved
php artisan pr:update-status-for-approved-pos

# Simpan nilai day untuk PR yang sudah approved
php artisan pr:save-day-value-for-approved
```

### 5. File Permissions
- [ ] Set permission untuk storage directory:
  ```bash
  chmod -R 775 storage/
  chmod -R 775 bootstrap/cache/
  ```
- [ ] Set ownership jika diperlukan:
  ```bash
  chown -R www-data:www-data storage/
  chown -R www-data:www-data bootstrap/cache/
  ```

### 6. Storage Link
- [ ] Create storage link untuk file uploads:
  ```bash
  php artisan storage:link
  ```

### 7. Cache & Optimization
- [ ] Clear all caches:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```
- [ ] Cache configuration untuk production:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

### 8. Composer & Dependencies
- [ ] Install production dependencies:
  ```bash
  composer install --no-dev --optimize-autoloader
  ```

### 9. Assets & Frontend
- [ ] Compile assets jika menggunakan build tools:
  ```bash
  npm run build
  # atau
  npm run production
  ```

---

## 🚀 Deployment Steps

### Step 1: Upload Files
1. Upload semua file kecuali:
   - `node_modules/`
   - `.env` (buat baru di server)
   - `storage/logs/`
   - `storage/framework/cache/`
   - `storage/framework/sessions/`
   - `storage/framework/views/`

### Step 2: Server Configuration
1. Set up `.env` file di server
2. Set proper file permissions
3. Configure web server (Apache/Nginx)

### Step 3: Database Setup
1. Run migrations:
   ```bash
   php artisan migrate --force
   ```
2. Run data migration commands
3. Verify database structure

### Step 4: Final Checks
1. Test login functionality
2. Test PR list dengan kolom "Day"
3. Test attachment upload (status change to "progress")
4. Test PO approval (status change to "approved")
5. Verify kolom "Day" tidak kosong setelah approved

---

## 🔧 Server-Specific Commands

### After Upload to 192.168.32.13:

```bash
# 1. Install dependencies
composer install --no-dev --optimize-autoloader

# 2. Run migrations
php artisan migrate --force

# 3. Data migration commands
php artisan pr:update-status-for-attachments
php artisan pr:update-status-for-approved-pos
php artisan pr:save-day-value-for-approved

# 4. Create storage link
php artisan storage:link

# 5. Set permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# 6. Clear and cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7. Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔍 Testing After Deployment

### 1. Basic Functionality
- [ ] Application loads without errors
- [ ] Login works
- [ ] Navigation works

### 2. PR List Features
- [ ] Kolom "Day" muncul di tabel
- [ ] Sorting by "Day" column works
- [ ] Badge colors correct (OPEN=green, progress=blue, approved=primary)

### 3. Status Changes
- [ ] Upload attachment → status changes to "progress"
- [ ] Approve all POs → status changes to "approved"
- [ ] Kolom "Day" shows stored value for approved PRs

### 4. PO Features
- [ ] PO show page attachments table format
- [ ] View attachment button works
- [ ] PO approval workflow

---

## 🚨 Rollback Plan

Jika terjadi masalah:

1. **Database Rollback**:
   ```bash
   php artisan migrate:rollback --step=2
   ```

2. **Restore Previous Version**:
   - Restore file backup
   - Restore database backup
   - Clear caches

3. **Emergency Fixes**:
   - Disable Observer di `AppServiceProvider`
   - Set `APP_DEBUG=true` untuk debugging
   - Check logs di `storage/logs/`

---

## 📝 Notes

- Semua perubahan sudah backward compatible
- Observer hanya aktif untuk perubahan baru
- Command migration aman untuk data existing
- Kolom "Day" tidak akan break existing functionality

---

## 🎯 Success Criteria

✅ **Deployment berhasil jika**:
- [ ] Aplikasi berjalan normal
- [ ] Kolom "Day" muncul dan berfungsi
- [ ] Status otomatis berubah saat upload/approve
- [ ] Tidak ada error di logs
- [ ] Performance tetap optimal

---

**Prepared by**: AI Assistant  
**Date**: {{ date }}  
**Target Server**: 192.168.32.13  
**Version**: 1.0 