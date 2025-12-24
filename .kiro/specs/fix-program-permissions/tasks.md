# Implementation Plan

## Phase 1: Audit and Documentation

- [x] 1. Audit all modules to identify missing permission checks
  - Document which modules have "Tambah" buttons without permission checks
  - Document which modules have unused permissions in the matrix
  - Create a mapping of module → actual functionality → required permissions
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

## Phase 2: Fix Permission Matrix (UserGroup Model)

- [ ] 2. Update UserGroup model permission matrix
- [x] 2.1 Remove unused permissions from Program module
  - Remove 'gantung', 'aktifkan', 'eksport' from Program module in `getDefaultPermissionMatrix()`
  - Keep only: tambah, lihat, kemaskini, padam, terima, tolak
  - _Requirements: 2.1, 2.2, 2.3_

- [ ] 2.2 Audit and fix other modules' permission matrices
  - Review each module in the permission matrix
  - Remove any permissions that don't have corresponding functionality
  - Document changes made
  - _Requirements: 2.1, 2.2, 2.3_

## Phase 3: Fix Program Module

- [ ] 3. Add permission checks to Program module
- [x] 3.1 Fix "Tambah Program" button in desktop view
  - Add `@if(auth()->user()->adaKebenaran('program', 'tambah'))` wrapper
  - File: `resources/views/program/index.blade.php` (around line 16-21)
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 3.2 Fix "Tambah Program" button in mobile view
  - Add same permission check to mobile card section
  - Ensure consistency with desktop view
  - _Requirements: 1.4, 4.1, 4.2_

## Phase 4: Fix Senarai Kumpulan Module

- [ ] 4. Add permission checks to Senarai Kumpulan module
- [x] 4.1 Fix "Tambah Kumpulan" button
  - Add `@if(auth()->user()->adaKebenaran('senarai_kumpulan', 'tambah'))` wrapper
  - File: `resources/views/pengurusan/senarai-kumpulan.blade.php`
  - Apply to both desktop and mobile views
  - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [ ] 4.2 Verify action buttons have permission checks
  - Check Edit icon has 'kemaskini' permission
  - Check Delete icon has 'padam' permission
  - Check View icon has 'lihat' permission
  - _Requirements: 3.2_

## Phase 5: Fix Senarai Pengguna Module

- [ ] 5. Add permission checks to Senarai Pengguna module
- [x] 5.1 Fix "Tambah Pengguna" button
  - Add `@if(auth()->user()->adaKebenaran('senarai_pengguna', 'tambah'))` wrapper
  - File: `resources/views/pengurusan/senarai-pengguna.blade.php`
  - Apply to both desktop and mobile views
  - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [ ] 5.2 Verify action buttons have permission checks
  - Check Edit icon has 'kemaskini' permission
  - Check Delete icon has 'padam' permission
  - Check View icon has 'lihat' permission
  - Check Gantung/Aktifkan buttons have correct permissions
  - _Requirements: 3.2_

## Phase 6: Verify Other Modules

- [ ] 6. Verify permission checks in other modules
- [ ] 6.1 Verify Log Pemandu module
  - Check if "Tambah" functionality exists (it may not, as logs are auto-created)
  - Verify action buttons have permission checks
  - _Requirements: 1.1, 1.2, 3.2_

- [ ] 6.2 Verify Laporan modules
  - Check Laporan Senarai Program
  - Check Laporan Kenderaan
  - Check Laporan Kilometer
  - Check Laporan Kos
  - Check Laporan Pemandu
  - Check Laporan Tuntutan
  - Verify "Eksport" buttons have permission checks
  - _Requirements: 1.1, 1.2, 3.2_

- [ ] 6.3 Verify Tetapan modules
  - Check Tetapan Umum
  - Check Integrasi
  - Check Aktiviti Log
  - Verify "Kemaskini" buttons have permission checks
  - _Requirements: 1.1, 1.2, 3.2_

## Phase 7: Testing and Validation

- [ ] 7. Test permission system
- [ ] 7.1 Create test user groups
  - Create group with all permissions
  - Create group with no permissions
  - Create group with selective permissions
  - _Requirements: 3.4_

- [ ] 7.2 Test each module with different permission combinations
  - Test Program module
  - Test Senarai Kumpulan module
  - Test Senarai Pengguna module
  - Test Senarai Kenderaan module
  - Test Selenggara module
  - _Requirements: 1.1, 1.2, 3.2_

- [ ] 7.3 Test desktop and mobile views
  - Verify permission checks work on desktop
  - Verify permission checks work on mobile
  - Verify consistency between views
  - _Requirements: 4.1, 4.2, 4.3_

- [ ] 7.4 Test backward compatibility
  - Load existing user groups
  - Verify they still work correctly
  - Verify unused permissions are ignored
  - _Requirements: 2.4, 3.4_

## Phase 8: Documentation and Cleanup

- [ ] 8. Final documentation
- [ ] 8.1 Document all changes made
  - List all files modified
  - List all permissions removed
  - List all permission checks added
  - _Requirements: 5.4_

- [ ] 8.2 Update user documentation if needed
  - Update permission matrix documentation
  - Update admin guide if necessary
  - _Requirements: 5.4_

- [ ] 8.3 Clear view cache
  - Run `php artisan view:clear`
  - Verify changes are visible
  - _Requirements: 3.4_
