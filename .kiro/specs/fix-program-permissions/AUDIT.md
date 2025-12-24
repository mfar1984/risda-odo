# Permission System Audit

## Audit Date
December 23, 2025

## Modules Audited

### 1. Program Module
**File**: `resources/views/program/index.blade.php`

**Current State**:
- ❌ "Tambah Program" button - NO permission check (line 16-21)
- ✅ View icon - Has permission check (implicit through route)
- ✅ Edit icon - Has permission check: `adaKebenaran('program', 'kemaskini')`
- ✅ Delete icon - Has permission check: `adaKebenaran('program', 'padam')`
- ✅ Approve icon - Has permission check: `adaKebenaran('program', 'terima')`
- ✅ Reject icon - Has permission check: `adaKebenaran('program', 'tolak')`

**Permission Matrix** (from UserGroup model):
```php
'program' => [
    'tambah' => false,      // ✅ USED - but not checked in view
    'lihat' => false,       // ✅ USED
    'kemaskini' => false,   // ✅ USED
    'padam' => false,       // ✅ USED
    'terima' => false,      // ✅ USED
    'tolak' => false,       // ✅ USED
    'gantung' => false,     // ❌ UNUSED - no functionality
    'aktifkan' => false,    // ❌ UNUSED - no functionality
    'eksport' => false,     // ❌ UNUSED - no functionality
],
```

**Actions Required**:
1. Add permission check to "Tambah Program" button
2. Remove unused permissions: gantung, aktifkan, eksport

---

### 2. Senarai Kumpulan Module
**File**: `resources/views/pengurusan/senarai-kumpulan.blade.php`

**Current State**:
- ❌ "Tambah Kumpulan" button - NO permission check (line 16-21)
- Need to check action buttons (Edit, Delete, View)

**Permission Matrix**:
```php
'senarai_kumpulan' => [
    'tambah' => false,      // ✅ USED - but not checked
    'lihat' => false,       // ✅ USED
    'kemaskini' => false,   // ✅ USED
    'padam' => false,       // ✅ USED
    'terima' => false,      // ❓ NEED TO CHECK
    'tolak' => false,       // ❓ NEED TO CHECK
    'gantung' => false,     // ❓ NEED TO CHECK
    'aktifkan' => false,    // ❓ NEED TO CHECK
],
```

**Actions Required**:
1. Add permission check to "Tambah Kumpulan" button
2. Audit action buttons for permission checks
3. Determine which permissions are actually used

---

### 3. Senarai Pengguna Module
**File**: `resources/views/pengurusan/senarai-pengguna.blade.php`

**Current State**:
- ❌ "Tambah Pengguna" button - NO permission check (line 16-21)
- Need to check action buttons (Edit, Delete, View, Gantung, Aktifkan)

**Permission Matrix**:
```php
'senarai_pengguna' => [
    'tambah' => false,      // ✅ USED - but not checked
    'lihat' => false,       // ✅ USED
    'kemaskini' => false,   // ✅ USED
    'padam' => false,       // ✅ USED
    'terima' => false,      // ❓ NEED TO CHECK
    'tolak' => false,       // ❓ NEED TO CHECK
    'gantung' => false,     // ✅ USED (suspend user)
    'aktifkan' => false,    // ✅ USED (activate user)
],
```

**Actions Required**:
1. Add permission check to "Tambah Pengguna" button
2. Verify Gantung/Aktifkan buttons have permission checks
3. Determine if terima/tolak are used

---

### 4. Senarai Kenderaan Module
**File**: `resources/views/pengurusan/senarai-kenderaan.blade.php`

**Current State**:
- ✅ "Tambah Kenderaan" button - HAS permission check: `adaKebenaran('senarai_kenderaan', 'tambah')` (line 29-35)
- ✅ "Selenggara" button - HAS permission check: `adaKebenaran('selenggara_kenderaan', 'lihat')` (line 21-27)

**Permission Matrix**:
```php
'senarai_kenderaan' => [
    'tambah' => false,      // ✅ USED - checked
    'lihat' => false,       // ✅ USED
    'kemaskini' => false,   // ✅ USED
    'padam' => false,       // ✅ USED
    'terima' => false,      // ❓ NEED TO CHECK
    'tolak' => false,       // ❓ NEED TO CHECK
    'gantung' => false,     // ❓ NEED TO CHECK
    'aktifkan' => false,    // ❓ NEED TO CHECK
],
```

**Actions Required**:
1. ✅ No changes needed for Tambah button
2. Verify action buttons have permission checks
3. Determine which permissions are actually used

---

### 5. Selenggara Kenderaan Module
**File**: `resources/views/pengurusan/senarai-selenggara.blade.php`

**Current State**:
- ✅ "Tambah Penyelenggaraan" button - HAS permission check: `adaKebenaran('selenggara_kenderaan', 'tambah')` (line 17-23)

**Permission Matrix**:
```php
'selenggara_kenderaan' => [
    'tambah' => false,      // ✅ USED - checked
    'lihat' => false,       // ✅ USED
    'kemaskini' => false,   // ✅ USED
    'padam' => false,       // ✅ USED
],
```

**Actions Required**:
1. ✅ No changes needed - already correct

---

### 6. Log Pemandu Module
**Files**: Need to check log pemandu views

**Permission Matrix**:
```php
'log_pemandu' => [
    'lihat' => false,       // ✅ USED
    'kemaskini' => false,   // ✅ USED
    'padam' => false,       // ✅ USED
],
```

**Note**: Log Pemandu doesn't have 'tambah' because logs are created automatically through program assignments.

**Actions Required**:
1. Verify action buttons have permission checks

---

### 7. Laporan Modules

**Laporan Senarai Program**:
```php
'laporan_senarai_program' => [
    'lihat' => false,       // ✅ USED
    'eksport' => false,     // ✅ USED (PDF export)
],
```

**Laporan Kenderaan**:
```php
'laporan_kenderaan' => [
    'lihat' => false,       // ✅ USED
    'eksport' => false,     // ✅ USED (PDF export)
],
```

**Laporan Kilometer**:
```php
'laporan_kilometer' => [
    'lihat' => false,       // ✅ USED
    'eksport' => false,     // ✅ USED (PDF export)
],
```

**Laporan Kos**:
```php
'laporan_kos' => [
    'lihat' => false,       // ✅ USED
    'eksport' => false,     // ✅ USED (PDF export)
],
```

**Laporan Pemandu**:
```php
'laporan_pemandu' => [
    'lihat' => false,       // ✅ USED
    'eksport' => false,     // ✅ USED (PDF export)
],
```

**Laporan Tuntutan**:
```php
'laporan_tuntutan' => [
    'lihat' => false,       // ✅ USED
    'padam' => false,       // ✅ USED
    'terima' => false,      // ✅ USED (approve claim)
    'tolak' => false,       // ✅ USED (reject claim)
    'gantung' => false,     // ✅ USED (suspend claim)
],
```

**Actions Required**:
1. Verify "Eksport" buttons have permission checks
2. Verify action buttons in Laporan Tuntutan have permission checks

---

### 8. Tetapan Modules

**Tetapan Umum**:
```php
'tetapan_umum' => [
    'lihat' => false,       // ✅ USED
    'kemaskini' => false,   // ✅ USED
],
```

**Integrasi**:
```php
'integrasi' => [
    'lihat' => false,       // ✅ USED
    'kemaskini' => false,   // ✅ USED
],
```

**Aktiviti Log**:
```php
'aktiviti_log' => [
    'lihat' => false,       // ✅ USED
],
```

**Actions Required**:
1. Verify "Kemaskini" buttons have permission checks

---

## Summary of Findings

### Missing Permission Checks (HIGH PRIORITY)
1. ❌ Program - "Tambah Program" button
2. ❌ Senarai Kumpulan - "Tambah Kumpulan" button
3. ❌ Senarai Pengguna - "Tambah Pengguna" button

### Unused Permissions to Remove
1. ❌ Program module: gantung, aktifkan, eksport

### Modules with Correct Implementation (REFERENCE)
1. ✅ Senarai Kenderaan - Good example
2. ✅ Selenggara Kenderaan - Good example

### Modules Needing Further Investigation
1. ❓ Senarai Kumpulan - Check if terima, tolak, gantung, aktifkan are used
2. ❓ Senarai Pengguna - Check if terima, tolak are used
3. ❓ Senarai Kenderaan - Check if terima, tolak, gantung, aktifkan are used
4. ❓ All Laporan modules - Verify eksport button permission checks
5. ❓ Tetapan modules - Verify kemaskini button permission checks

## Recommended Implementation Order

1. **Phase 1**: Fix critical missing permission checks
   - Program module
   - Senarai Kumpulan module
   - Senarai Pengguna module

2. **Phase 2**: Remove unused permissions
   - Program module (gantung, aktifkan, eksport)

3. **Phase 3**: Audit and verify other modules
   - Check all action buttons
   - Verify eksport buttons in Laporan modules
   - Verify kemaskini buttons in Tetapan modules

4. **Phase 4**: Clean up unused permissions in other modules
   - Based on findings from Phase 3
