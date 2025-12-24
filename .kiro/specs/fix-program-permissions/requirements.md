# Requirements Document

## Introduction

This document outlines the requirements for fixing the permission system across all modules in the RISDA Odometer system. Currently, many "Tambah" (Add) buttons and action buttons are visible to all users regardless of their permission settings, and some permission matrices include unused permissions that do not have corresponding functionality in their modules.

The modules to be audited and fixed include:
- Program
- Log Pemandu (and sub-modules: Semua, Aktif, Selesai, Tertunda)
- Laporan (Senarai Program, Kenderaan, Kilometer, Kos, Pemandu, Tuntutan)
- Pengurusan (Senarai Kumpulan, Senarai Pengguna, Senarai Kenderaan, Selenggara Kenderaan)
- Tetapan (Tetapan Umum, Integrasi, Aktiviti Log)

## Glossary

- **Program Module**: The system module that manages programs and activities
- **Permission Matrix**: A data structure that defines what actions (tambah, lihat, kemaskini, padam, etc.) a user group can perform on each module
- **User Group**: A collection of users with shared permissions defined by the permission matrix
- **Tambah**: Add/Create action permission
- **Lihat**: View/Read action permission
- **Kemaskini**: Update/Edit action permission
- **Padam**: Delete action permission
- **Terima**: Approve action permission
- **Tolak**: Reject action permission
- **Gantung**: Suspend action permission
- **Aktifkan**: Activate action permission
- **Eksport**: Export action permission

## Requirements

### Requirement 1

**User Story:** As a system administrator, I want all "Tambah" (Add) buttons across all modules to only be visible to users with the 'tambah' permission for that module, so that unauthorized users cannot access creation functionality.

#### Acceptance Criteria

1. WHEN a user without 'tambah' permission for any module views that module's index page THEN the system SHALL hide the "Tambah" button for that module
2. WHEN a user with 'tambah' permission for any module views that module's index page THEN the system SHALL display the "Tambah" button for that module
3. WHEN the permission check is applied THEN the system SHALL use the `auth()->user()->adaKebenaran($module, 'tambah')` pattern consistently across all modules
4. WHEN checking permissions THEN the system SHALL apply the same checks to both desktop and mobile views

### Requirement 2

**User Story:** As a system administrator, I want the permission matrix for all modules to only show permissions that have corresponding functionality, so that I can accurately configure user group permissions without confusion.

#### Acceptance Criteria

1. WHEN viewing the permission matrix for any module THEN the system SHALL display only permissions that have corresponding functionality in that module
2. WHEN viewing the permission matrix THEN the system SHALL NOT display unused permissions (e.g., gantung, aktifkan, eksport for modules that don't use them)
3. WHEN the UserGroup model returns the default permission matrix THEN each module SHALL only include permissions that have corresponding functionality in the system
4. WHEN existing user groups are loaded THEN the system SHALL ignore any unused permissions that may exist in the database

### Requirement 3

**User Story:** As a developer, I want the permission system to be consistent across all modules, so that permission checks follow the same pattern and are easy to maintain.

#### Acceptance Criteria

1. WHEN implementing permission checks THEN the system SHALL use the `auth()->user()->adaKebenaran($module, $action)` helper method
2. WHEN a permission check fails THEN the system SHALL hide the corresponding UI element (button, link, or action icon)
3. WHEN permission checks are applied THEN the system SHALL check permissions for both desktop and mobile views
4. WHEN the permission matrix is updated THEN the system SHALL maintain backward compatibility with existing user group data

### Requirement 4

**User Story:** As a user, I want consistent permission enforcement across desktop and mobile views, so that I have the same access controls regardless of which device I use.

#### Acceptance Criteria

1. WHEN a user views the program index on desktop THEN the system SHALL apply the same permission checks as the mobile view
2. WHEN the "Tambah Program" button is hidden due to permissions on desktop THEN the system SHALL also hide it on mobile view
3. WHEN action buttons (edit, delete, approve, reject) are displayed THEN the system SHALL apply the same permission checks on both desktop and mobile views


### Requirement 5

**User Story:** As a system administrator, I want a comprehensive audit of all modules to identify which permissions are actually used, so that I can ensure the permission matrix accurately reflects system functionality.

#### Acceptance Criteria

1. WHEN auditing each module THEN the system SHALL document which CRUD operations (tambah, lihat, kemaskini, padam) are actually implemented
2. WHEN auditing each module THEN the system SHALL document which workflow operations (terima, tolak, gantung, aktifkan) are actually implemented
3. WHEN auditing each module THEN the system SHALL document which export operations are actually implemented
4. WHEN the audit is complete THEN the system SHALL provide a mapping of module → implemented permissions
5. WHEN unused permissions are identified THEN the system SHALL remove them from the permission matrix for that module
