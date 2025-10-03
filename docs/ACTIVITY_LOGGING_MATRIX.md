# Activity Logging Matrix - JARA System

> **Generated:** 2025-10-03  
> **Purpose:** Complete matrix of all operations that require activity logging

---

## 🎯 Overview

This document lists ALL operations across the JARA system that require activity logging, organized by module and controller.

---

## 📋 Module Matrix

### 1. **Senarai Kumpulan** (`/pengurusan/senarai-kumpulan`)

**Controller:** `UserGroupController`  
**Model:** `UserGroup`

| Operation | Method | Route | Permission | Event Name | Status |
|-----------|--------|-------|------------|------------|--------|
| **Tambah Kumpulan** | `store()` | POST `/senarai-kumpulan/tambah-kumpulan` | `senarai_kumpulan,tambah` | `created` | ⏳ Pending |
| **Kemaskini Kumpulan** | `update()` | PUT `/senarai-kumpulan/{id}` | `senarai_kumpulan,kemaskini` | `updated` | ⏳ Pending |
| **Padam Kumpulan** | `destroy()` | DELETE `/senarai-kumpulan/{id}` | `senarai_kumpulan,padam` | `deleted` | ⏳ Pending |

**Data to Log:**
- Group name (`nama_kumpulan`)
- Status (`status`)
- Permission matrix changes (`kebenaran_matrix`)
- Description (`keterangan`)
- Creator/updater info
- IP address & user agent

---

### 2. **Senarai Pengguna** (`/pengurusan/senarai-pengguna`)

**Controller:** `PenggunaController`  
**Model:** `User`

| Operation | Method | Route | Permission | Event Name | Status |
|-----------|--------|-------|------------|------------|--------|
| **Tambah Pengguna** | `store()` | POST `/senarai-pengguna/tambah-pengguna` | `senarai_pengguna,tambah` | `created` | ⏳ Pending |
| **Kemaskini Pengguna** | `update()` | PUT `/senarai-pengguna/{id}` | `senarai_pengguna,kemaskini` | `updated` | ⏳ Pending |
| **Padam Pengguna** | `destroy()` | DELETE `/senarai-pengguna/{id}` | `senarai_pengguna,padam` | `deleted` | ⏳ Pending |

**Data to Log:**
- User name (`name`)
- Email (`email`)
- Organization type (`jenis_organisasi`)
- Organization ID (`organisasi_id`)
- User group (`kumpulan_id`)
- Status (`status`)
- Roles/permissions changes
- IP address & user agent

**Security Note:** This is HIGH PRIORITY for audit trail!

---

### 3. **Senarai Kenderaan** (`/pengurusan/senarai-kenderaan`)

**Controller:** `KenderaanController`  
**Model:** `Kenderaan`

| Operation | Method | Route | Permission | Event Name | Status |
|-----------|--------|-------|------------|------------|--------|
| **Tambah Kenderaan** | `store()` | POST `/senarai-kenderaan/tambah-kenderaan` | `senarai_kenderaan,tambah` | `created` | ⏳ Pending |
| **Kemaskini Kenderaan** | `update()` | PUT `/senarai-kenderaan/{id}` | `senarai_kenderaan,kemaskini` | `updated` | ⏳ Pending |
| **Padam Kenderaan** | `destroy()` | DELETE `/senarai-kenderaan/{id}` | `senarai_kenderaan,padam` | `deleted` | ⏳ Pending |

**Data to Log:**
- License plate (`no_plat`)
- Brand & model (`jenama`, `model`)
- Year (`tahun`)
- Fuel type (`jenis_bahan_api`)
- Status (`status`)
- Engine & chassis numbers (`no_enjin`, `no_casis`)
- Road tax expiry (`cukai_tamat_tempoh`)
- Documents uploaded/deleted
- IP address & user agent

---

### 4. **Integrasi** (`/pengurusan/integrasi`)

**Controller:** `IntegrasiController`  
**Models:** `IntegrasiConfig`, `WeatherConfig`, `EmailConfig`

| Operation | Method | Route | Permission | Event Name | Status |
|-----------|--------|-------|------------|------------|--------|
| **Jana API Token** | `generateApiToken()` | POST `/integrasi/generate-api-token` | `integrasi,tambah` | `generated_token` | ⏳ Pending |
| **Kemaskini CORS** | `updateCors()` | PUT `/integrasi/cors` | `integrasi,tambah` | `updated_cors` | ⏳ Pending |
| **Kemaskini Cuaca** | `updateWeather()` | PUT `/integrasi/cuaca` | `integrasi,kemaskini` | `updated_weather` | ⏳ Pending |
| **Kemaskini Email** | `updateEmail()` | PUT `/integrasi/email` | `integrasi,kemaskini` | `updated_email` | ⏳ Pending |

**Data to Log:**

**For API Token Generation:**
- Old token (masked for security)
- New token (masked for security)
- Generation timestamp
- IP address & user agent

**For CORS Update:**
- Allowed origins changes
- Allow all flag changes
- IP address & user agent

**For Weather Config Update:**
- API key changes (masked)
- Location changes
- Update frequency changes
- IP address & user agent

**For Email Config Update:**
- SMTP host/port changes
- Encryption changes
- From address changes
- Password changes (NEVER log actual password!)
- IP address & user agent

**Security Note:** This is CRITICAL for security audit!

---

### 5. **Profile** (`/profile`)

**Controller:** `ProfileController`  
**Model:** `User`

| Operation | Method | Route | Permission | Event Name | Status |
|-----------|--------|-------|------------|------------|--------|
| **Kemaskini Profile** | `update()` | PATCH `/profile` | N/A (Own profile) | `updated_profile` | ⏳ Pending |
| **Padam Akaun** | `destroy()` | DELETE `/profile` | N/A (Own account) | `deleted_account` | ⏳ Pending |

**Data to Log:**
- Name changes
- Email changes
- Password changes (flag only, not actual password!)
- IP address & user agent
- Account deletion (with reason if provided)

**Security Note:** Profile changes are important for user accountability!

---

## 📊 Summary Statistics

| Module | Total Operations | High Priority | Medium Priority |
|--------|------------------|---------------|-----------------|
| Senarai Kumpulan | 3 | 2 (add, delete) | 1 (update) |
| Senarai Pengguna | 3 | **3 (all!)** | 0 |
| Senarai Kenderaan | 3 | 1 (delete) | 2 (add, update) |
| Integrasi | 4 | **4 (all!)** | 0 |
| Profile | 2 | 2 (update, delete) | 0 |
| **TOTAL** | **15** | **12** | **3** |

---

## 🔐 Security Considerations

### High-Priority Logging (Security Audit)
1. **User Management** - All add/update/delete operations
2. **API Token Generation** - Critical for API security
3. **Permission Changes** - User group matrix updates
4. **Account Deletion** - Both admin deletion and self-deletion
5. **Integration Config** - CORS, API, Email, Weather settings

### Data Masking Requirements
When logging, these fields MUST be masked:
- ❌ `password` - NEVER log passwords
- ❌ `api_token` - Log masked version (e.g., "abc123...xyz789")
- ❌ `smtp_password` - Log only "changed" flag
- ❌ `weather_api_key` - Log masked version

### Multi-Tenancy Filtering
All activity logs MUST respect multi-tenancy:
- ✅ Administrator (`jenis_organisasi = 'semua'`) - See ALL logs
- ✅ Bahagian users - See only bahagian logs
- ✅ Stesen users - See only stesen logs

This is already implemented in `AktivitiLogController`.

---

## 🎯 Implementation Checklist

### Phase 1: User Management (HIGH PRIORITY) ⏳
- [ ] UserGroupController - create, update, delete
- [ ] PenggunaController - create, update, delete

### Phase 2: Asset Management ⏳
- [ ] KenderaanController - create, update, delete

### Phase 3: System Configuration (CRITICAL) ⏳
- [ ] IntegrasiController - all 4 operations

### Phase 4: Profile Management ⏳
- [ ] ProfileController - update, delete

---

## 📝 Completed Modules

### ✅ Already Implemented:
1. **ProgramController** - create, update, delete, approve, reject, export
2. **TuntutanController** - approve, reject, cancel, delete, export
3. **TetapanUmumController** - update
4. **AktivitiLogController** - Multi-tenancy filtering (FIXED)

---

## 🔍 Log Viewing

All activity logs can be viewed at:
- **URL:** `http://localhost:8000/pengurusan/aktiviti-log`
- **Permission Required:** `aktiviti_log,lihat`
- **Filters Available:**
  - Search by description/module/subject
  - Filter by event type
  - Filter by date range
  - Filter by user (causer)

---

## 🛠️ Technical Implementation

### Standard Activity Log Format:
```php
activity()
    ->performedOn($model)
    ->causedBy(auth()->user())
    ->withProperties([
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        // ... specific data ...
    ])
    ->event('event_name')
    ->log("Description of action");
```

### UUID Primary Key:
All activity logs use **UUID** as primary key for better security and distribution.

### Properties Structure:
```json
{
    "ip": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "specific_field_1": "value",
    "specific_field_2": "value",
    "changes": {
        "field_name": {
            "old": "old_value",
            "new": "new_value"
        }
    }
}
```

---

**Last Updated:** 2025-10-03  
**Maintained By:** Development Team  
**Status:** 🟡 In Progress

