# Implementation Plan: Settings - Data & Eksport Tab

## Overview
Implement functional Data & Eksport settings tab that affects all pages system-wide. User preferences will be stored in database and applied globally through helper functions.

**IMPORTANT:** 
- ✅ **HANYA ubah VIEW/DISPLAY layer** (Blade templates)
- ❌ **JANGAN sentuh API/Controller** yang dah berfungsi
- ✅ Helper functions untuk formatting sahaja
- ❌ Jangan ubah database queries atau business logic

---

## Phase 1: Database & Model Setup

### 1.1 Create Migration for `user_settings` Table
**File:** `database/migrations/YYYY_MM_DD_create_user_settings_table.php`

**Columns:**
- `id` - Primary key
- `user_id` - Foreign key to users table (unique)
- `format_eksport` - Default export format (pdf/excel/csv)
- `format_tarikh` - Date format preference (DD/MM/YYYY, DD-MM-YYYY, YYYY-MM-DD, DD MMM YYYY)
- `format_masa` - Time format (24/12)
- `format_nombor` - Number format (1,234.56 / 1.234,56 / 1 234.56)
- `mata_wang` - Currency (MYR)
- `timestamps`

**Remark:** Run migration after creation

---

### 1.2 Create `UserSetting` Model
**File:** `app/Models/UserSetting.php`

**Features:**
- Belongs to User relationship
- Default values as constants
- Accessor methods for each setting
- Static method to get or create settings for user

**Remark:** Add relationship in User model as well

---

## Phase 2: Helper Functions & Service Provider

### 2.1 Create `UserSettingsHelper` Class
**File:** `app/Support/UserSettingsHelper.php`

**Methods:**
```php
// Get user settings (cached)
public static function getUserSettings($userId = null)

// Format date according to user preference
public static function formatTarikh($date, $userId = null)

// Format time according to user preference  
public static function formatMasa($time, $userId = null)

// Format datetime according to user preference
public static function formatTarikhMasa($datetime, $userId = null)

// Format number according to user preference
public static function formatNombor($number, $decimals = 0, $userId = null)

// Format currency according to user preference
public static function formatWang($amount, $userId = null)

// Get export format preference
public static function getFormatEksport($userId = null)
```

**Remark:** Use Laravel's cache to avoid repeated DB queries

---

### 2.2 Register Helper in Service Provider
**File:** `app/Providers/AppServiceProvider.php`

**Action:** 
- Import UserSettingsHelper
- Make available globally via View::share or helper function
- Register in boot() method

**Remark:** Clear cache after changes

---

## Phase 3: Controller Implementation

### 3.1 Create/Update `SettingsController`
**File:** `app/Http/Controllers/SettingsController.php`

**Methods:**
- `index()` - Show settings page with current user settings
- `updateDataEksport()` - Save Data & Eksport preferences
- `resetDataEksport()` - Reset to default values

**Remark:** Add validation rules for each field

---

### 3.2 Add Routes
**File:** `routes/web.php`

**Routes:**
```php
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings/data-eksport', [SettingsController::class, 'updateDataEksport'])->name('settings.update-data-eksport');
Route::post('/settings/data-eksport/reset', [SettingsController::class, 'resetDataEksport'])->name('settings.reset-data-eksport');
```

**Remark:** Ensure middleware auth is applied

---

## Phase 4: Update Settings View

### 4.1 Update Tab 3: Data & Eksport
**File:** `resources/views/settings/index.blade.php`

**Changes:**
- Replace hardcoded values with actual user settings from database
- Add form with Alpine.js for saving preferences
- Add save button at bottom of tab
- Add reset to default button
- Show success/error messages after save

**Remark:** Use same styling as other tabs, maintain consistency

---

## Phase 5: Apply Settings to All Pages

### 5.1 Dashboard Page
**File:** `resources/views/dashboard.blade.php`

**Apply to:**
- ✅ Tarikh display in report headers
- ✅ Masa display (masa_keluar, masa_masuk)
- ✅ Number formatting for odometer, jarak, liter
- ✅ Currency formatting for kos_minyak, tuntutan amounts

**Remark:** Replace all `format()`, `number_format()` with helper functions

---

### 5.2 Program Module
**Files:**
- `resources/views/program/index.blade.php` (List)
- `resources/views/program/show-program.blade.php` (Show)
- `resources/views/program/edit-program.blade.php` (Edit)
- `resources/views/program/tambah-program.blade.php` (Add)

**Apply to:**
- ✅ Tarikh Mula & Tarikh Selesai display
- ✅ Created_at, Updated_at timestamps
- ✅ Any budget/cost fields (if exists)

**Remark:** Check all date/time/number fields in forms and displays

---

### 5.3 Log Pemandu Module
**Files:**
- `resources/views/log-pemandu/index.blade.php` (List)
- `resources/views/log-pemandu/show.blade.php` (Show)
- `resources/views/log-pemandu/edit.blade.php` (Edit)

**Apply to:**
- ✅ Tarikh Perjalanan display
- ✅ Masa Keluar & Masa Masuk
- ✅ Odometer (keluar/masuk) numbers
- ✅ Jarak (km) numbers
- ✅ Liter Minyak numbers
- ✅ Kos Minyak currency
- ✅ Updated_at timestamps

**Remark:** This module has LOTS of date/time/number displays

---

### 5.4 Pengurusan - Senarai Kenderaan
**Files:**
- `resources/views/pengurusan/senarai-kenderaan.blade.php` (List)
- `resources/views/pengurusan/show-kenderaan.blade.php` (Show)
- `resources/views/pengurusan/edit-kenderaan.blade.php` (Edit)
- `resources/views/pengurusan/tambah-kenderaan.blade.php` (Add)

**Apply to:**
- ✅ Tarikh Daftar
- ✅ Tarikh Cukai Tamat
- ✅ Odometer Semasa (number)
- ✅ Created_at, Updated_at

**Remark:** Check for any cost/price fields

---

### 5.5 Pengurusan - Senarai Selenggara
**Files:**
- `resources/views/pengurusan/senarai-selenggara.blade.php` (List)
- `resources/views/pengurusan/show-selenggara.blade.php` (Show)
- `resources/views/pengurusan/edit-selenggara.blade.php` (Edit)
- `resources/views/pengurusan/tambah-selenggara.blade.php` (Add)

**Apply to:**
- ✅ Tarikh Selenggara
- ✅ Tarikh Selesai
- ✅ Kos Selenggara (currency)
- ✅ Odometer reading (number)
- ✅ Created_at, Updated_at

**Remark:** Important module with cost data

---

### 5.6 Pengurusan - Senarai Kumpulan
**Files:**
- `resources/views/pengurusan/senarai-kumpulan.blade.php` (List)
- `resources/views/pengurusan/show-kumpulan.blade.php` (Show)
- `resources/views/pengurusan/edit-kumpulan.blade.php` (Edit)
- `resources/views/pengurusan/tambah-kumpulan.blade.php` (Add)

**Apply to:**
- ✅ Created_at, Updated_at timestamps

**Remark:** Minimal date/time fields

---

### 5.7 Pengurusan - Senarai Pengguna
**Files:**
- `resources/views/pengurusan/senarai-pengguna.blade.php` (List)
- `resources/views/pengurusan/show-pengguna.blade.php` (Show)
- `resources/views/pengurusan/edit-pengguna.blade.php` (Edit)
- `resources/views/pengurusan/tambah-pengguna.blade.php` (Add)

**Apply to:**
- ✅ Created_at, Updated_at timestamps
- ✅ Last login timestamp

**Remark:** Check for any date fields in user profile

---

### 5.8 Pengurusan - Senarai RISDA (Bahagian/Stesen/Staf)
**Files:**
- `resources/views/pengurusan/senarai-risda.blade.php` (List with tabs)
- `resources/views/pengurusan/show-bahagian.blade.php` (Show Bahagian)
- `resources/views/pengurusan/edit-bahagian.blade.php` (Edit Bahagian)
- `resources/views/pengurusan/tambah-bahagian.blade.php` (Add Bahagian)
- `resources/views/pengurusan/show-stesen.blade.php` (Show Stesen)
- `resources/views/pengurusan/edit-stesen.blade.php` (Edit Stesen)
- `resources/views/pengurusan/tambah-stesen.blade.php` (Add Stesen)
- `resources/views/pengurusan/show-staf.blade.php` (Show Staf)
- `resources/views/pengurusan/edit-staf.blade.php` (Edit Staf)
- `resources/views/pengurusan/tambah-staf.blade.php` (Add Staf)

**Apply to:**
- ✅ Tarikh Lahir (Staf)
- ✅ Tarikh Mula Kerja (Staf)
- ✅ Created_at, Updated_at timestamps

**Remark:** Staf module has personal date fields

---

### 5.9 Pengurusan - Aktiviti Log
**Files:**
- `resources/views/pengurusan/aktiviti-log.blade.php` (List)
- `resources/views/pengurusan/aktiviti-log-show.blade.php` (Show)
- `resources/views/pengurusan/aktiviti-log-keselamatan.blade.php` (Security Log)

**Apply to:**
- ✅ Timestamp of activities
- ✅ Created_at display

**Remark:** Lots of timestamp displays

---

### 5.10 Laporan Module
**Files:**
- `resources/views/laporan/senarai-program.blade.php` (List)
- `resources/views/laporan/senarai-program-show.blade.php` (Show)
- `resources/views/laporan/laporan-kenderaan.blade.php` (List)
- `resources/views/laporan/laporan-kenderaan-show.blade.php` (Show)
- `resources/views/laporan/laporan-kilometer.blade.php` (List)
- `resources/views/laporan/laporan-kilometer-show.blade.php` (Show)
- `resources/views/laporan/laporan-kos.blade.php` (List)
- `resources/views/laporan/laporan-kos-show.blade.php` (Show)
- `resources/views/laporan/laporan-pemandu.blade.php` (List)
- `resources/views/laporan/laporan-pemandu-show.blade.php` (Show)
- `resources/views/laporan/laporan-tuntutan.blade.php` (List)
- `resources/views/laporan/laporan-tuntutan-show.blade.php` (Show)
- `resources/views/laporan/laporan-tuntutan-pdf.blade.php` (PDF)

**Apply to:**
- ✅ All date ranges in reports
- ✅ All currency amounts
- ✅ All distance/km numbers
- ✅ All timestamps

**Remark:** CRITICAL - Reports have lots of formatted data

---

### 5.11 PDF Reports
**Files:**
- `resources/views/reports/vehicle_usage_pdf.blade.php`
- `resources/views/laporan/pdf/*.blade.php` (if exists)

**Apply to:**
- ✅ All dates in PDF headers
- ✅ All currency in PDF tables
- ✅ All numbers in PDF tables

**Remark:** PDF formatting is critical for professional output

---

### 5.12 Profile Page
**File:** `resources/views/profile/partials/update-profile-information-form.blade.php`

**Apply to:**
- ✅ Display of created_at, updated_at if shown
- ✅ Any date fields in profile

**Remark:** Minimal changes expected

---

## Phase 6: Export Format Implementation

### 6.1 ~~Update Export Controllers~~ **SKIP - JANGAN GANGGU API**
**Files to check:**
- ~~`app/Http/Controllers/Laporan/*.php`~~
- ~~Any controller with export functionality~~

**Changes:**
- ~~Check user's preferred export format from settings~~
- ~~Default to user preference when generating exports~~
- ~~Add format parameter override if needed~~

**Remark:** **JANGAN SENTUH API/CONTROLLER YANG DAH BERFUNGSI. Export format setting hanya untuk UI preference sahaja (show default button), API tetap sama.**

---

### 6.2 Update Export Buttons (UI Only)
**Files:** All pages with export buttons

**Changes:**
- Show user's default format in button text or tooltip
- Example: "Eksport (PDF)" if user prefers PDF
- **TIDAK ubah API call atau controller logic**

**Remark:** Visual feedback sahaja, API tidak berubah

---

## Phase 7: Testing & Validation

### 7.1 Unit Tests
**File:** `tests/Unit/UserSettingsHelperTest.php`

**Test cases:**
- Format tarikh with different preferences
- Format masa 24h vs 12h
- Format nombor with different separators
- Format wang with currency symbol
- Cache functionality

**Remark:** Ensure all helper methods work correctly

---

### 7.2 Feature Tests
**File:** `tests/Feature/SettingsDataEksportTest.php`

**Test cases:**
- Save settings successfully
- Validation errors for invalid formats
- Reset to defaults
- Settings persist across sessions
- Settings apply to pages

**Remark:** Test full user flow

---

### 7.3 Manual Testing Checklist

**Test each module:**
- [ ] Dashboard - Check all date/time/number displays
- [ ] Program - List, Show, Edit, Add pages
- [ ] Log Pemandu - List, Show, Edit pages
- [ ] Senarai Kenderaan - List, Show, Edit, Add pages
- [ ] Senarai Selenggara - List, Show, Edit, Add pages
- [ ] Senarai Kumpulan - List, Show, Edit, Add pages
- [ ] Senarai Pengguna - List, Show, Edit, Add pages
- [ ] Senarai RISDA - All tabs and pages
- [ ] Aktiviti Log - All pages
- [ ] Laporan - All report types
- [ ] PDF Exports - Check formatting
- [ ] Profile - Check date displays

**Test different settings combinations:**
- [ ] DD/MM/YYYY vs YYYY-MM-DD vs DD MMM YYYY
- [ ] 24h vs 12h time format
- [ ] Different number separators
- [ ] PDF vs Excel vs CSV default

**Remark:** Test with multiple user accounts

---

## Phase 8: Documentation & Cleanup

### 8.1 Update User Documentation
**File:** `docs/USER_GUIDE.md` (if exists)

**Add section:**
- How to access Settings
- Explanation of Data & Eksport options
- How settings affect the system
- How to reset to defaults

**Remark:** User-friendly language

---

### 8.2 Code Cleanup
**Actions:**
- Remove any hardcoded date/time/number formats
- Ensure consistent use of helper functions
- Remove debug code
- Clear all caches

**Remark:** Clean code is maintainable code

---

## Summary of Files to Modify

### New Files (7):
1. `database/migrations/YYYY_MM_DD_create_user_settings_table.php`
2. `app/Models/UserSetting.php`
3. `app/Support/UserSettingsHelper.php`
4. `app/Http/Controllers/SettingsController.php` (if not exists)
5. `tests/Unit/UserSettingsHelperTest.php`
6. `tests/Feature/SettingsDataEksportTest.php`
7. `SETTINGS_DATA_EKSPORT_IMPLEMENTATION.md` (this file)

### Modified Files (60+):
1. `app/Providers/AppServiceProvider.php`
2. `app/Models/User.php`
3. `routes/web.php`
4. `resources/views/settings/index.blade.php`
5. `resources/views/dashboard.blade.php`
6-9. Program module (4 files)
10-12. Log Pemandu module (3 files)
13-16. Senarai Kenderaan (4 files)
17-20. Senarai Selenggara (4 files)
21-24. Senarai Kumpulan (4 files)
25-28. Senarai Pengguna (4 files)
29-37. Senarai RISDA (9 files)
38-40. Aktiviti Log (3 files)
41-47. Laporan module (7 files)
48. PDF Reports (1+ files)
49. Profile page (1 file)
50+. Export controllers (multiple files)

**Total estimated files: 67+ files**

---

## Implementation Order (Recommended)

1. ✅ **Phase 1** - Database & Model (Foundation)
2. ✅ **Phase 2** - Helper Functions (Core functionality)
3. ✅ **Phase 3** - Controller & Routes (Backend logic)
4. ✅ **Phase 4** - Update Settings View (User interface)
5. ✅ **Phase 5.1-5.3** - Apply to Dashboard, Program, Log Pemandu (High traffic pages)
6. ✅ **Test Phase 5.1-5.3** - Ensure working before continuing
7. ✅ **Phase 5.4-5.9** - Apply to Pengurusan modules
8. ✅ **Phase 5.10-5.11** - Apply to Laporan & PDF (Critical for reports)
9. ✅ **Phase 6** - Export Format Implementation
10. ✅ **Phase 7** - Testing & Validation
11. ✅ **Phase 8** - Documentation & Cleanup

---

## Estimated Time

- Phase 1: 30 minutes
- Phase 2: 1 hour
- Phase 3: 45 minutes
- Phase 4: 30 minutes
- Phase 5: 4-6 hours (many files)
- Phase 6: 1 hour
- Phase 7: 2 hours
- Phase 8: 30 minutes

**Total: 10-12 hours of development work**

---

## Notes

- Use caching to avoid performance issues
- Test thoroughly with different user preferences
- Ensure backward compatibility (default values)
- Consider mobile responsive display of formatted data
- PDF formatting may need special attention
- Export functionality should respect user preference but allow override
- **CRITICAL: JANGAN GANGGU API/CONTROLLER YANG DAH BERFUNGSI**
- **Hanya ubah display formatting dalam Blade views**
- **Helper functions untuk formatting output sahaja, bukan untuk business logic**

---

## Risk Mitigation

**Risk:** Breaking existing date/time displays
**Mitigation:** Test each page after changes, use default format if settings not found

**Risk:** Performance issues from repeated DB queries
**Mitigation:** Use Laravel cache for user settings

**Risk:** PDF formatting issues
**Mitigation:** Test PDF exports thoroughly with different settings

**Risk:** Missing pages in implementation
**Mitigation:** This comprehensive checklist ensures all pages covered

---

**Created:** 2025-12-24
**Status:** Planning Phase
**Next Step:** Review with user, then start Phase 1
