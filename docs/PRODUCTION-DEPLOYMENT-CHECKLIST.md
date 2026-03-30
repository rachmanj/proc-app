# Production Server Deployment Checklist - SAP B1 Sync Feature (v2.2)

## Server Information
- **OS**: Windows 10 Pro
- **Web Server**: XAMPP
- **PHP Version**: 8.2+ (required for Laravel 11)

---

## 1. PHP SQL Server Extensions Installation

### Step 1: Download Microsoft ODBC Driver for SQL Server
1. Download **Microsoft ODBC Driver 18 for SQL Server** (or 17) from:
   - https://learn.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server
   - Choose: `msodbcsql.msi` (x64 for 64-bit PHP, x86 for 32-bit PHP)

2. Install the ODBC driver on the production server

### Step 2: Download PHP SQL Server Extensions
1. Go to: https://pecl.php.net/package/sqlsrv
2. Or use pre-compiled DLLs from: https://github.com/Microsoft/msphpsql/releases
3. Download the appropriate version for your PHP:
   - `php_sqlsrv_82_ts_x64.dll` (Thread Safe, 64-bit, PHP 8.2)
   - `php_pdo_sqlsrv_82_ts_x64.dll` (Thread Safe, 64-bit, PHP 8.2)
   - **Note**: Match your PHP version (8.2, 8.3, etc.) and architecture (x64 or x86)
   - **Note**: Match Thread Safe (TS) or Non-Thread Safe (NTS) with your PHP build

### Step 3: Install Extensions
1. Copy the DLL files to your PHP extensions directory:
   ```
   C:\xampp\php\ext\
   ```

2. Edit `php.ini` file (usually at `C:\xampp\php\php.ini`):
   ```ini
   ; Add these lines
   extension=php_sqlsrv_82_ts_x64.dll
   extension=php_pdo_sqlsrv_82_ts_x64.dll
   ```
   **Important**: Replace `82_ts_x64` with your actual PHP version and architecture

3. Restart Apache in XAMPP Control Panel

### Step 4: Verify Installation
1. Create a test file `test_sqlsrv.php` in your web root:
   ```php
   <?php
   if (extension_loaded('sqlsrv')) {
       echo "sqlsrv extension is loaded ✓\n";
   } else {
       echo "sqlsrv extension is NOT loaded ✗\n";
   }
   
   if (extension_loaded('pdo_sqlsrv')) {
       echo "pdo_sqlsrv extension is loaded ✓\n";
   } else {
       echo "pdo_sqlsrv extension is NOT loaded ✗\n";
   }
   
   phpinfo();
   ?>
   ```

2. Access via browser: `http://localhost/test_sqlsrv.php`
3. Verify both extensions show as "loaded"
4. **Delete this test file after verification** for security

---

## 2. Environment Configuration

### Update `.env` File
Add or update these variables in your production `.env` file:

```env
# SAP SQL Server Direct Access Configuration
SAP_SQL_HOST=your_sap_server_hostname_or_ip
SAP_SQL_PORT=1433
SAP_SQL_DATABASE=your_sap_database_name
SAP_SQL_USERNAME=your_sql_username
SAP_SQL_PASSWORD=your_sql_password

# Alternative: If you already have SAP configuration, these will fallback to:
# SAP_SERVER_URL (if SAP_SQL_HOST not set)
# SAP_DB_NAME (if SAP_SQL_DATABASE not set)
# SAP_USER (if SAP_SQL_USERNAME not set)
# SAP_PASSWORD (if SAP_SQL_PASSWORD not set)
```

**Security Note**: 
- Never commit `.env` file to version control
- Use strong passwords for SQL Server access
- Consider using read-only SQL user if possible

---

## 3. Database Migration

### Run Migration for sync_logs Table
1. Open Command Prompt or PowerShell
2. Navigate to your Laravel project directory:
   ```cmd
   cd C:\xampp\htdocs\your-project-name
   ```

3. Run the migration:
   ```cmd
   php artisan migrate
   ```

4. Verify the table was created:
   ```cmd
   php artisan tinker
   ```
   Then in tinker:
   ```php
   DB::table('sync_logs')->count();
   exit
   ```

---

## 4. Database Seeding (Permissions)

### Seed New Permissions
1. Run the permission seeder:
   ```cmd
   php artisan db:seed --class=RolePermissionSeeder
   ```

2. Clear permission cache:
   ```cmd
   php artisan permission:cache-reset
   ```

3. Verify permissions were created:
   ```cmd
   php artisan tinker
   ```
   Then in tinker:
   ```php
   \Spatie\Permission\Models\Permission::whereIn('name', ['sync-custom-date', 'impor-sap-data'])->get();
   exit
   ```

---

## 5. Required Files Verification

### Ensure SQL Query Files Are Present
Verify these files exist in your project:
- `database/list_po.sql` ✓
- `database/list_pr_generated.sql` ✓

These files are required for the sync functionality.

---

## 6. Network Connectivity Test

### Test Connection to SAP B1 SQL Server
1. Test from Command Prompt:
   ```cmd
   telnet your_sap_server_hostname 1433
   ```
   Or use PowerShell:
   ```powershell
   Test-NetConnection -ComputerName your_sap_server_hostname -Port 1433
   ```

2. Test from Laravel Tinker:
   ```cmd
   php artisan tinker
   ```
   Then:
   ```php
   try {
       $result = DB::connection('sap_sql')->select('SELECT TOP 1 * FROM OPOR');
       echo "Connection successful! ✓\n";
       print_r($result);
   } catch (\Exception $e) {
       echo "Connection failed: " . $e->getMessage() . "\n";
   }
   exit
   ```

**Troubleshooting**:
- If connection fails, check firewall rules
- Verify SQL Server allows remote connections
- Check SQL Server Browser service is running (if using named instances)
- Verify credentials are correct

---

## 7. File Permissions

### Set Proper Permissions
1. Ensure Laravel can write to storage and cache:
   - `storage/` directory: writable
   - `bootstrap/cache/` directory: writable

2. On Windows, typically these directories should have:
   - Full control for the web server user (usually `SYSTEM` or `IIS_IUSRS`)
   - Or give `Users` group write access

3. Verify with:
   ```cmd
   icacls storage /grant Users:F /T
   icacls bootstrap\cache /grant Users:F /T
   ```

---

## 8. Composer Dependencies

### Install/Update Dependencies
1. Navigate to project directory
2. Run:
   ```cmd
   composer install --no-dev --optimize-autoloader
   ```

3. If you get errors about missing extensions, install them first

---

## 9. Laravel Optimization

### Optimize for Production
1. Clear and cache configuration:
   ```cmd
   php artisan config:clear
   php artisan config:cache
   ```

2. Clear and cache routes:
   ```cmd
   php artisan route:clear
   php artisan route:cache
   ```

3. Clear and cache views:
   ```cmd
   php artisan view:clear
   php artisan view:cache
   ```

4. Optimize autoloader:
   ```cmd
   composer dump-autoload --optimize
   ```

---

## 10. Application Key and Environment

### Verify Application Key
1. Check if `.env` has `APP_KEY`:
   ```cmd
   php artisan key:generate
   ```
   (Only if key is missing)

### Set Environment to Production
In `.env`:
```env
APP_ENV=production
APP_DEBUG=false
```

---

## 11. Testing the Sync Feature

### Test PR Sync
1. Log in to the application
2. Navigate to: **Sync Data > Sync With SAP**
3. Go to **PR** tab
4. Click **TODAY** or **YESTERDAY** button
5. Click **Sync PR Data**
6. Verify:
   - Data syncs successfully
   - Records appear in `pr_temps` table
   - Auto-conversion to main PR table works
   - Sync log entry is created

### Test PO Sync
1. Stay on the same page
2. Go to **PO** tab
3. Click **TODAY** or **YESTERDAY** button
4. Click **Sync PO Data**
5. Verify:
   - Data syncs successfully
   - Records appear in `po_temps` table
   - Auto-conversion to main PO table works
   - Sync log entry is created

### Verify Sync Logs
1. Check `sync_logs` table:
   ```cmd
   php artisan tinker
   ```
   ```php
   \App\Models\SyncLog::latest()->take(5)->get();
   exit
   ```

---

## 12. Permission Assignment

### Assign Permissions to Users/Roles
1. Log in as superadmin
2. Navigate to **Admin > Roles** or **Admin > Users**
3. Assign permissions:
   - `sync-custom-date`: For users who can use CUSTOM date range
   - `impor-sap-data`: For users who can see PR Import and PO Import menu items

**Default Assignments** (from seeder):
- `sync-custom-date`: superadmin, admin
- `impor-sap-data`: superadmin, admin, adminproc

---

## 13. Security Checklist

- [ ] `.env` file is not accessible via web (check `.htaccess` or web server config)
- [ ] SQL Server credentials are strong and stored securely
- [ ] Consider using read-only SQL user for sync operations
- [ ] Firewall rules allow connection to SAP SQL Server
- [ ] Test files (like `test_sqlsrv.php`) are removed
- [ ] `APP_DEBUG=false` in production
- [ ] Error logging is configured properly
- [ ] Regular backups of `sync_logs` table

---

## 14. Troubleshooting Common Issues

### Issue: "Class 'PDO' not found" or SQL Server extension not loaded
**Solution**: 
- Verify PHP extensions are correctly installed
- Check `php.ini` has the extension lines uncommented
- Restart Apache
- Check PHP version matches extension version

### Issue: "SQLSTATE[08001]: [Microsoft][ODBC Driver 18 for SQL Server]SSL Provider: No credentials are available in the security package"
**Solution**: 
- Add `'TrustServerCertificate' => true` in `config/database.php` (already configured)
- Or update ODBC driver to version 17

### Issue: "Connection timeout" or "Network-related error"
**Solution**:
- Check network connectivity to SAP SQL Server
- Verify firewall allows port 1433
- Check SQL Server allows remote connections
- Verify hostname/IP and port are correct

### Issue: "Permission denied" errors
**Solution**:
- Verify SQL user has SELECT permissions on required SAP tables
- Check user has access to the database
- Test connection with SQL Server Management Studio first

### Issue: Sync works but conversion fails
**Solution**:
- Check error message in `sync_logs.error_message` field
- Verify main PR/PO tables exist and have correct structure
- Check for duplicate records that might cause constraint violations

---

## 15. Post-Deployment Monitoring

### Monitor Sync Operations
1. Regularly check `sync_logs` table for failed syncs
2. Monitor application logs: `storage/logs/laravel.log`
3. Set up alerts for sync failures if possible
4. Review sync performance and optimize if needed

### Regular Maintenance
- Clear old sync logs periodically (keep last 3-6 months)
- Monitor database size growth
- Review and optimize SQL queries if performance degrades

---

## Quick Reference Commands

```cmd
# Test SQL Server connection
php artisan tinker
DB::connection('sap_sql')->select('SELECT TOP 1 * FROM OPOR');

# Check sync logs
php artisan tinker
\App\Models\SyncLog::latest()->take(10)->get();

# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize

# Check permissions
php artisan tinker
\Spatie\Permission\Models\Permission::all();
```

---

## Support Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Microsoft SQL Server PHP Driver**: https://github.com/Microsoft/msphpsql
- **Spatie Permission Package**: https://spatie.be/docs/laravel-permission
- **Project Documentation**: See `docs/SAP-SQL-DIRECT-ACCESS.md`

---

**Last Updated**: 2025-11-17  
**Version**: 2.2

