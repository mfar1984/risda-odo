# Design Document

## Overview

This design document outlines the solution for fixing the permission system for the Program module. The fix involves two main changes:
1. Adding permission checks to the "Tambah Program" button in the program index view
2. Removing unused permissions (gantung, aktifkan, eksport) from the Program module's permission matrix

The solution maintains consistency with existing permission patterns used throughout the application and ensures backward compatibility with existing user group data.

## Architecture

The permission system in RISDA Odometer follows a role-based access control (RBAC) pattern:

```
User → belongs to → UserGroup → has → Permission Matrix → defines → Module Permissions
```

Each UserGroup has a `kebenaran_matrix` JSON field that stores permissions for each module. The User model has an `adaKebenaran($module, $action)` helper method that checks if the user's group has a specific permission.

### Current Flow
1. User logs in and their UserGroup is loaded
2. Views check permissions using `auth()->user()->adaKebenaran($module, $action)`
3. UI elements (buttons, links) are conditionally rendered based on permission checks

### Changes Required
1. Add permission check to "Tambah Program" button in `resources/views/program/index.blade.php`
2. Update `UserGroup::getDefaultPermissionMatrix()` to remove unused Program permissions
3. Ensure both desktop and mobile views have consistent permission checks

## Components and Interfaces

### 1. UserGroup Model (`app/Models/UserGroup.php`)

**Current State:**
```php
'program' => [
    'tambah' => false,
    'lihat' => false,
    'kemaskini' => false,
    'padam' => false,
    'terima' => false,
    'tolak' => false,
    'gantung' => false,      // UNUSED - to be removed
    'aktifkan' => false,     // UNUSED - to be removed
    'eksport' => false,      // UNUSED - to be removed
],
```

**New State:**
```php
'program' => [
    'tambah' => false,
    'lihat' => false,
    'kemaskini' => false,
    'padam' => false,
    'terima' => false,
    'tolak' => false,
],
```

### 2. Program Index View (`resources/views/program/index.blade.php`)

**Current State:**
```blade
<a href="{{ route('program.create') }}">
    <x-buttons.primary-button type="button">
        <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
        Tambah Program
    </x-buttons.primary-button>
</a>
```

**New State:**
```blade
@if(auth()->user()->adaKebenaran('program', 'tambah'))
    <a href="{{ route('program.create') }}">
        <x-buttons.primary-button type="button">
            <span class="material-symbols-outlined mr-2" style="font-size: 16px;">add_circle</span>
            Tambah Program
        </x-buttons.primary-button>
    </a>
@endif
```

### 3. Permission Matrix View (`resources/views/pengurusan/edit-kumpulan.blade.php`)

No changes required. The view dynamically renders checkboxes based on the permissions defined in `UserGroup::getDefaultPermissionMatrix()`. When we remove the unused permissions from the model, they will automatically disappear from the UI.

## Data Models

### UserGroup Model

**Fields:**
- `id`: Primary key
- `nama_kumpulan`: Group name
- `kebenaran_matrix`: JSON field storing permission matrix
- `keterangan`: Description
- `status`: Status (aktif, tidak_aktif, gantung)
- `dicipta_oleh`: Foreign key to User who created the group
- `jenis_organisasi`: Organization type (semua, bahagian, stesen)
- `organisasi_id`: Organization ID

**Methods:**
- `adaKebenaran($modul, $aksi)`: Check if group has specific permission
- `getDefaultPermissionMatrix()`: Static method returning default permission structure
- `getPermissionLabels()`: Static method returning permission labels in Bahasa Melayu
- `getModuleLabels()`: Static method returning module labels in Bahasa Melayu

### User Model

**Relevant Methods:**
- `adaKebenaran($modul, $aksi)`: Delegates to UserGroup's adaKebenaran method

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: UI Element Hiding Based on Permissions

*For any* user with a permission set to false for a specific module action, when that user views the corresponding page, the UI element for that action should not be present in the rendered HTML.

**Validates: Requirements 3.2**

### Property 2: Permission Matrix Structure Correctness

*For any* call to `UserGroup::getDefaultPermissionMatrix()`, the Program module should contain exactly these keys: tambah, lihat, kemaskini, padam, terima, tolak, and should not contain: gantung, aktifkan, eksport.

**Validates: Requirements 2.1, 2.2**

## Error Handling

### Missing Permission Check
- **Scenario**: User attempts to access a protected route without permission
- **Handling**: Laravel middleware should handle route-level authorization. View-level checks only control UI visibility.

### Invalid Permission Key
- **Scenario**: Code checks for a permission that doesn't exist in the matrix
- **Handling**: The `adaKebenaran()` method returns `false` if the permission key doesn't exist

### Null User or UserGroup
- **Scenario**: User is not authenticated or doesn't have a user group
- **Handling**: The `auth()->user()` check will fail gracefully, and UI elements will be hidden

## Testing Strategy

### Unit Tests

Unit tests will verify specific permission check scenarios:

1. **Test Permission Check for Tambah Button**
   - Given a user without 'tambah' permission
   - When the program index view is rendered
   - Then the "Tambah Program" button should not be present in the HTML

2. **Test Permission Matrix Structure**
   - Given the UserGroup model
   - When `getDefaultPermissionMatrix()` is called
   - Then the Program module should only contain: tambah, lihat, kemaskini, padam, terima, tolak

3. **Test Backward Compatibility**
   - Given a user group with old permission matrix (including gantung, aktifkan, eksport)
   - When checking for a valid permission (e.g., 'tambah')
   - Then the permission check should work correctly

### Property-Based Tests

Property-based tests will verify universal properties across all inputs:

1. **Property Test: Permission Check Consistency**
   - Generate random user groups with various permission combinations
   - For each permission that is set to false, verify that the corresponding UI element is not rendered
   - For each permission that is set to true, verify that the corresponding UI element is rendered

2. **Property Test: Permission Matrix Completeness**
   - For each module in the permission matrix
   - Generate random permission combinations
   - Verify that all permissions have corresponding functionality (no orphaned permissions)

### Manual Testing Checklist

1. Login as user with 'tambah' permission → Verify "Tambah Program" button is visible
2. Login as user without 'tambah' permission → Verify "Tambah Program" button is hidden
3. Edit user group permission matrix → Verify only 6 checkboxes appear for Program module (not 9)
4. Test on mobile view → Verify same permission behavior as desktop
5. Check existing user groups → Verify they still work correctly after changes

## Implementation Notes

### File Changes Required

1. **app/Models/UserGroup.php**
   - Remove 'gantung', 'aktifkan', 'eksport' from Program module in `getDefaultPermissionMatrix()`

2. **resources/views/program/index.blade.php**
   - Add `@if(auth()->user()->adaKebenaran('program', 'tambah'))` wrapper around "Tambah Program" button (desktop view)
   - Add same permission check to mobile view section

### Migration Considerations

No database migration is required because:
- We're only changing the default permission matrix structure
- Existing user groups in the database will retain their current permissions
- The `adaKebenaran()` method will simply ignore unused permissions
- When user groups are edited and saved, they will automatically use the new structure

### Rollback Plan

If issues arise:
1. Revert changes to `UserGroup.php` to restore unused permissions
2. Revert changes to `program/index.blade.php` to remove permission check
3. No database changes needed as we didn't modify existing data
