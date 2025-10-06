# COMPLETE HIVE DATABASE ARCHITECTURE - RISDA DRIVER APP

**Version**: 1.0.0  
**Last Updated**: October 2025  
**Author**: System Architecture Documentation

---

## 📋 TABLE OF CONTENTS

1. [System Overview](#system-overview)
2. [Database Initialization](#database-initialization)
3. [Hive Boxes Structure](#hive-boxes-structure)
4. [Data Models](#data-models)
5. [Relationships & Foreign Keys](#relationships--foreign-keys)
6. [Sync Strategies](#sync-strategies)
7. [Offline Operations](#offline-operations)
8. [Best Practices](#best-practices)

---

## 🏗️ SYSTEM OVERVIEW

### Technology Stack
- **Hive**: 2.2.3
- **Hive Flutter**: 1.1.0
- **Code Generation**: hive_generator 2.0.1 + build_runner 2.4.8

### Initialization Flow
```dart
main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();           // Firebase (FCM)
  await FirebaseService().initialize();     // FCM Setup
  await HiveService.init();                 // ✅ Hive Database Init
  runApp(MyApp());
}
```

### Storage Location
- **Android**: `/data/data/com.risda.driver/files/`
- **iOS**: `Library/Application Support/`
- **Web**: IndexedDB

---

## 📦 HIVE BOXES STRUCTURE

### Box Registry

| Box Name | TypeId | Model Class | Records | Purpose |
|----------|--------|-------------|---------|---------|
| `auth` | 5 | `AuthHive` | 1 | User authentication & session |
| `journeys` | 0 | `JourneyHive` | Many | Driver logs (trips/log_pemandu) |
| `programs` | 1 | `ProgramHive` | Many | Available programs for drivers |
| `vehicles` | 2 | `VehicleHive` | Many | Fleet vehicles |
| `claims` | 3 | `ClaimHive` | Many | Expense claims (tuntutan) |
| `sync_queue` | 4 | `SyncQueueHive` | Many | Pending sync operations |
| `settings` | N/A | Dynamic | Many | Key-value app settings |

### Box Access
```dart
// Quick access to boxes
Box<AuthHive> authBox = HiveService.authBox;
Box<JourneyHive> journeyBox = HiveService.journeyBox;
Box<ProgramHive> programBox = HiveService.programBox;
Box<VehicleHive> vehicleBox = HiveService.vehicleBox;
Box<ClaimHive> claimBox = HiveService.claimBox;
Box<SyncQueueHive> syncQueueBox = HiveService.syncQueueBox;
Box settingsBox = HiveService.settingsBox;
```

---

## 1️⃣ AUTH BOX - User Authentication

### Model: `AuthHive` (TypeId: 5)

```dart
@HiveType(typeId: 5)
class AuthHive extends HiveObject {
  @HiveField(0)  String token;              // API Bearer token
  @HiveField(1)  int userId;                // User ID
  @HiveField(2)  String name;               // Display name
  @HiveField(3)  String email;              // User email
  @HiveField(4)  String? jenisOrganisasi;   // 'semua'/'bahagian'/'stesen'
  @HiveField(5)  String? organisasiId;      // Organization ID
  @HiveField(6)  String? organisasiName;    // Organization name
  @HiveField(7)  String role;               // User role
  @HiveField(8)  DateTime loginAt;          // Login timestamp
  @HiveField(9)  DateTime? lastSync;        // Last server sync
  @HiveField(10) bool rememberMe;           // Auto-login preference
}
```

### Business Rules
- ✅ Only **ONE** auth record at a time (FIFO)
- ✅ Session expires after **7 days**
- ✅ Auto-logout on expiry
- ✅ Clear on logout
- ⚠️ Additional user data stored in `settings['full_user_data']`

### Operations
```dart
// Save auth (login)
await HiveService.authBox.clear();
await HiveService.authBox.add(authData);

// Get current auth
AuthHive? auth = HiveService.getCurrentAuth();

// Clear auth (logout)
await HiveService.clearAuth();
```

---

## 2️⃣ JOURNEY BOX - Driver Logs (Log Pemandu)

### Model: `JourneyHive` (TypeId: 0) - 37 Fields

#### Server Fields (Backend: `log_pemandu` table)
```dart
@HiveType(typeId: 0)
class JourneyHive extends HiveObject {
  // Identity
  @HiveField(0)  int? id;                   // NULL = not synced yet
  @HiveField(1)  int pemanduId;             // FK: users.id
  @HiveField(2)  int kenderaanId;           // FK: kenderaan.id
  @HiveField(3)  int? programId;            // FK: program.id (nullable)
  
  // Journey
  @HiveField(4)  DateTime tarikhPerjalanan;
  @HiveField(5)  String masaKeluar;         // "HH:mm"
  @HiveField(6)  String? masaMasuk;         // "HH:mm"
  @HiveField(7)  String destinasi;
  @HiveField(8)  String? catatan;
  
  // Odometer
  @HiveField(9)  int odometerKeluar;
  @HiveField(10) int? odometerMasuk;
  @HiveField(11) int? jarak;                // Auto: odometerMasuk - odometerKeluar
  
  // Fuel
  @HiveField(12) double? literMinyak;
  @HiveField(13) double? kosMinyak;
  @HiveField(14) String? stesenMinyak;
  @HiveField(15) String? resitMinyak;       // Server URL
  
  // Photos
  @HiveField(16) String? fotoOdometerKeluar; // Server URL
  @HiveField(17) String? fotoOdometerMasuk;  // Server URL
  
  // Status
  @HiveField(18) String status;             // 'dalam_perjalanan'/'selesai'/'tertunda'
  @HiveField(19) String? jenisOrganisasi;
  @HiveField(20) String? organisasiId;
  
  // Audit
  @HiveField(21) int diciptaOleh;
  @HiveField(22) int? dikemaskiniOleh;
  
  // GPS
  @HiveField(23) double? lokasiCheckinLat;
  @HiveField(24) double? lokasiCheckinLong;
  @HiveField(25) double? lokasiCheckoutLat;
  @HiveField(26) double? lokasiCheckoutLong;
  
  // Timestamps
  @HiveField(27) DateTime? createdAt;
  @HiveField(28) DateTime? updatedAt;
  
  // === OFFLINE-SPECIFIC FIELDS ===
  @HiveField(29) String localId;            // UUID for local tracking
  @HiveField(30) bool isSynced;             // Sync status
  @HiveField(31) DateTime? lastSyncAttempt;
  @HiveField(32) int syncRetries;
  @HiveField(33) String? syncError;
  @HiveField(34) String? fotoOdometerKeluarLocal; // Local device path
  @HiveField(35) String? fotoOdometerMasukLocal;  // Local device path
  @HiveField(36) String? resitMinyakLocal;        // Local device path
}
```

### Journey Status Flow
```
┌─────────────────┐
│  Start Journey  │ status = 'dalam_perjalanan'
│  (Check-Out)    │ isSynced = false
└────────┬────────┘
         │
         ▼
   ┌──────────┐
   │  Active  │ Only ONE active journey allowed
   │  Journey │ Check: getActiveJourney() == null
   └────┬─────┘
        │
        ▼
┌───────────────┐
│  End Journey  │ status = 'selesai'
│  (Check-In)   │ Calculate jarak
└───────────────┘
```

### Operations
```dart
// Get active journey (only one allowed)
JourneyHive? active = HiveService.getActiveJourney();
// Returns: status == 'dalam_perjalanan'

// Get all journeys
List<JourneyHive> all = HiveService.getAllJourneys();

// Get pending sync
List<JourneyHive> pending = HiveService.getPendingSyncJourneys();
// Returns: isSynced == false

// Save new journey
await HiveService.saveJourney(journey);

// Update journey (using HiveObject)
journey.odometerMasuk = 12500;
await journey.save();  // ✅ Atomic update

// Watch for changes
Stream<BoxEvent> stream = HiveService.watchJourneys();
```

---

## 3️⃣ PROGRAM BOX - Program Management

### Model: `ProgramHive` (TypeId: 1)

```dart
@HiveType(typeId: 1)
class ProgramHive extends HiveObject {
  @HiveField(0)  int id;                    // Server ID
  @HiveField(1)  String namaProgram;
  @HiveField(2)  String? kenderaanId;       // JSON: "1,2,3"
  @HiveField(3)  String? pemanduId;         // JSON: "10,20,30"
  @HiveField(4)  DateTime? tarikhMula;
  @HiveField(5)  DateTime? tarikhTamat;
  @HiveField(6)  String? lokasi;
  @HiveField(7)  double? lokasiLat;
  @HiveField(8)  double? lokasiLong;
  @HiveField(9)  String? peneranganProgram;
  @HiveField(10) int? jarakAnggaran;
  @HiveField(11) String? catatanTambahan;
  @HiveField(12) String status;             // 'belum_bermula'/'sedang_berlangsung'/'selesai'
  @HiveField(13) String? organisasiId;
  @HiveField(14) int diciptaOleh;
  @HiveField(15) int? dikemaskiniOleh;
  @HiveField(16) DateTime? createdAt;
  @HiveField(17) DateTime? updatedAt;
  @HiveField(18) DateTime lastSync;
}
```

### Program Assignment
```dart
// Get programs for specific user
List<ProgramHive> userPrograms = HiveService.getProgramsForUser(userId);

// Logic: Check if userId in comma-separated pemanduId
// Example: pemanduId = "10,20,30"
//          userId = 10
//          Result: "10,20,30".contains("10") → TRUE
```

### Operations
```dart
// Bulk sync (replace all)
await HiveService.savePrograms(programsList);

// Programs are READ-ONLY in mobile
// No offline creation/editing
```

---

## 4️⃣ VEHICLE BOX - Fleet Management

### Model: `VehicleHive` (TypeId: 2)

```dart
@HiveType(typeId: 2)
class VehicleHive extends HiveObject {
  @HiveField(0)  int id;
  @HiveField(1)  String noPendaftaran;      // License plate
  @HiveField(2)  String jenisKenderaan;     // Vehicle type
  @HiveField(3)  String? model;
  @HiveField(4)  int? tahun;
  @HiveField(5)  String? warna;
  @HiveField(6)  int? bacanOdometerSemasaTerkini;
  @HiveField(7)  String status;             // 'aktif'/'tidak_aktif'/'dalam_penyelenggaraan'
  @HiveField(8)  String? organisasiId;
  @HiveField(9)  String jenisOrganisasi;
  @HiveField(10) int diciptaOleh;
  @HiveField(11) int? dikemaskiniOleh;
  @HiveField(12) DateTime? createdAt;
  @HiveField(13) DateTime? updatedAt;
  @HiveField(14) DateTime lastSync;
}
```

### Operations
```dart
// Get available vehicles
List<VehicleHive> available = HiveService.getAvailableVehicles();
// Returns: status == 'aktif'

// Vehicles are READ-ONLY in mobile
```

---

## 5️⃣ CLAIM BOX - Expense Claims (Tuntutan)

### Model: `ClaimHive` (TypeId: 3)

```dart
@HiveType(typeId: 3)
class ClaimHive extends HiveObject {
  @HiveField(0)  int? id;                   // NULL = not synced
  @HiveField(1)  int? logPemanduId;         // FK: journey.id (optional)
  @HiveField(2)  String kategori;           // 'tol'/'parking'/'f&b'/'fuel'
  @HiveField(3)  double jumlah;             // Amount (RM)
  @HiveField(4)  String? resit;             // Server URL
  @HiveField(5)  String? catatan;           // Maps to 'keterangan' in DB
  @HiveField(6)  String status;             // 'pending'/'diluluskan'/'ditolak'
  @HiveField(7)  int diciptaOleh;
  @HiveField(8)  int? dikemaskiniOleh;
  @HiveField(9)  int? diprosesOleh;
  @HiveField(10) DateTime? tarikhDiproses;
  @HiveField(11) String? alasanTolak;
  @HiveField(12) String? alasanGantung;
  @HiveField(13) DateTime? createdAt;
  @HiveField(14) DateTime? updatedAt;
  
  // Offline
  @HiveField(15) String localId;
  @HiveField(16) bool isSynced;
  @HiveField(17) String? resitLocal;        // Local device path
  @HiveField(18) int syncRetries;
  @HiveField(19) String? syncError;
  @HiveField(20) DateTime? lastSyncAttempt;
}
```

### Operations
```dart
// Get all claims
List<ClaimHive> all = HiveService.getAllClaims();

// Get pending sync
List<ClaimHive> pending = HiveService.getPendingSyncClaims();
```

---

## 6️⃣ SYNC QUEUE BOX - Background Sync

### Model: `SyncQueueHive` (TypeId: 4)

```dart
@HiveType(typeId: 4)
class SyncQueueHive extends HiveObject {
  @HiveField(0) String id;                  // UUID
  @HiveField(1) String type;                // 'journey'/'claim'/'photo'
  @HiveField(2) String action;              // 'create'/'update'/'delete'
  @HiveField(3) String localId;             // FK: journey.localId / claim.localId
  @HiveField(4) int priority;               // 1=high, 2=medium, 3=low
  @HiveField(5) String data;                // JSON payload
  @HiveField(6) DateTime createdAt;
  @HiveField(7) DateTime? lastAttempt;
  @HiveField(8) int retries;
  @HiveField(9) String? errorMessage;
}
```

### Priority System
```
Priority 1 (HIGH):    Journeys (critical for operations)
Priority 2 (MEDIUM):  Claims (important but not blocking)
Priority 3 (LOW):     Photos (can be deferred)
```

### Operations
```dart
// Add to sync queue
await HiveService.addToSyncQueue(syncItem);

// Get pending count
int count = HiveService.getPendingSyncCount();

// Total pending across all types
int total = HiveService.getTotalPendingSyncCount();
// Returns: journeys + claims + sync_queue
```

---

## 7️⃣ SETTINGS BOX - Dynamic Storage

### Structure: Untyped Box
```dart
Box settingsBox = Hive.box('settings');
```

### Current Keys

| Key | Type | Purpose |
|-----|------|---------|
| `full_user_data` | `Map<String, dynamic>` | Complete user API response |
| `last_sync` | `DateTime` | Last successful sync |
| `offline_mode` | `bool` | Offline mode preference |
| `app_version` | `String` | App version tracking |

### Operations
```dart
// Save setting
await HiveService.saveSetting('key', value);

// Get setting
dynamic value = HiveService.getSetting('key', defaultValue: null);
```

---

## 🔗 RELATIONSHIPS & FOREIGN KEYS

### Entity Relationship Diagram

```
AuthHive (Session)
  │
  ├─── userId (1:Many) ───────> JourneyHive.pemanduId
  │                              │
  │                              ├── kenderaanId ──> VehicleHive.id
  │                              ├── programId ───> ProgramHive.id
  │                              └── localId ──────> ClaimHive.logPemanduId
  │
  └─── userId (1:Many) ───────> ClaimHive.diciptaOleh

SyncQueueHive.localId ────────> JourneyHive.localId
                         └─────> ClaimHive.localId
```

### Foreign Key Constraints

#### JourneyHive
- `pemanduId` → `AuthHive.userId` (REQUIRED)
- `kenderaanId` → `VehicleHive.id` (REQUIRED)
- `programId` → `ProgramHive.id` (OPTIONAL)

#### ClaimHive
- `diciptaOleh` → `AuthHive.userId` (REQUIRED)
- `logPemanduId` → `JourneyHive.id` (OPTIONAL - can be standalone)

#### SyncQueueHive
- `localId` → `JourneyHive.localId` OR `ClaimHive.localId` (depends on type)

---

## 🔄 SYNC STRATEGIES

### 1. Optimistic Sync (Current Implementation)
```dart
// Save locally first → Sync in background
await HiveService.saveJourney(journey);       // ✅ Immediate
await syncManager.queueForSync(journey);      // Background
```

**Pros:**
- Fast user experience
- Works offline

**Cons:**
- Potential conflicts
- Requires retry logic

### 2. Pessimistic Sync (Alternative)
```dart
// Save to server first → Save locally
final result = await apiService.startJourney(...);
if (result.success) {
  await HiveService.saveJourney(JourneyHive.fromJson(result.data));
}
```

**Pros:**
- No conflicts
- Server is source of truth

**Cons:**
- Requires internet
- Slower UX

### 3. Conflict Resolution Strategy
```
if (server.updatedAt > local.updatedAt) {
  // Server is newer → Overwrite local
  local = server;
} else if (!local.isSynced) {
  // Local has unpushed changes → Upload
  uploadToServer(local);
} else {
  // Same version → Use server (safe)
  local = server;
}
```

---

## 📱 OFFLINE OPERATIONS

### Supported Offline Actions

| Action | Offline Support | Sync Required |
|--------|----------------|---------------|
| Login | ❌ No (requires API) | N/A |
| View Journeys | ✅ Yes | ❌ No |
| Start Journey | ✅ Yes | ✅ Yes |
| End Journey | ✅ Yes | ✅ Yes |
| Create Claim | ✅ Yes | ✅ Yes |
| View Claims | ✅ Yes | ❌ No |
| View Programs | ✅ Yes (cached) | ❌ No |
| View Vehicles | ✅ Yes (cached) | ❌ No |

### Sync Flow

```
OFFLINE OPERATION
├── User performs action (e.g., Start Journey)
├── Save to Hive with isSynced = false
├── Generate localId (UUID)
├── Add to SyncQueueHive
└── Show "Pending Sync" indicator

WHEN ONLINE DETECTED
├── Check connectivity
├── Get pending items from SyncQueueHive
├── Sort by priority
├── For each item:
│   ├── Upload to server API
│   ├── If SUCCESS:
│   │   ├── Update local record (id, isSynced = true)
│   │   ├── Remove from SyncQueueHive
│   │   └── Show success notification
│   └── If FAIL:
│       ├── Increment retries
│       ├── Log error message
│       └── Retry later (max 3 attempts)
└── Update UI sync status
```

---

## 💾 STORAGE MANAGEMENT

### Size Estimates

| Data Type | Size per Record | 100 Records | 1000 Records |
|-----------|----------------|-------------|--------------|
| AuthHive | 500 bytes | 0.05 MB | 0.5 MB |
| JourneyHive | 1 KB | 0.1 MB | 1 MB |
| ProgramHive | 800 bytes | 0.08 MB | 0.8 MB |
| VehicleHive | 400 bytes | 0.04 MB | 0.4 MB |
| ClaimHive | 600 bytes | 0.06 MB | 0.6 MB |
| SyncQueueHive | 500 bytes | 0.05 MB | 0.5 MB |
| **Total** | - | **0.38 MB** | **3.8 MB** |

**Note:** Photos stored separately in device file system.

### Cleanup Strategy
```dart
// Delete old synced journeys (older than 30 days)
final oldDate = DateTime.now().subtract(Duration(days: 30));
final oldJourneys = journeyBox.values.where(
  (j) => j.isSynced && j.createdAt!.isBefore(oldDate)
);
for (var journey in oldJourneys) {
  await journey.delete();
}

// Delete failed sync queue items (older than 7 days)
final oldQueue = syncQueueBox.values.where(
  (q) => q.createdAt.isBefore(oldDate) && q.retries >= 3
);
for (var item in oldQueue) {
  await item.delete();
}
```

---

## ✅ BEST PRACTICES

### 1. Data Updates
```dart
// ✅ DO: Use HiveObject.save() for updates
journey.odometerMasuk = 12500;
await journey.save();

// ❌ DON'T: Recreate and replace
await journeyBox.put(journey.key, newJourney);
```

### 2. Queries
```dart
// ✅ DO: Use where() for filtering
final active = journeyBox.values.where((j) => j.status == 'aktif').toList();

// ❌ DON'T: Loop through all records
for (var j in journeyBox.values) {
  if (j.status == 'aktif') active.add(j);
}
```

### 3. Watching Changes
```dart
// ✅ DO: Use streams for reactive updates
StreamBuilder<BoxEvent>(
  stream: HiveService.watchJourneys(),
  builder: (context, snapshot) {
    return ListView(...);
  },
)
```

### 4. Photo Management
```dart
// ✅ DO: Compress photos before saving
final compressed = await FlutterImageCompress.compressAndGetFile(
  file.path,
  quality: 70,  // 70% quality
);

// ✅ DO: Store path only, not base64
journey.fotoOdometerKeluarLocal = compressed.path;

// ❌ DON'T: Store base64 in Hive (too large)
```

### 5. Sync Management
```dart
// ✅ DO: Use sync queue for reliability
await HiveService.addToSyncQueue(syncItem);

// ✅ DO: Show pending sync count to user
int pending = HiveService.getTotalPendingSyncCount();

// ❌ DON'T: Sync immediately (use background)
```

---

## 🔧 MAINTENANCE COMMANDS

### Generate Hive Adapters
```bash
flutter pub run build_runner build --delete-conflicting-outputs
```

### Clean Hive Cache
```dart
await HiveService.clearAllData();  // Clear all boxes
```

### Debug Hive Data
```dart
print('Auth: ${HiveService.authBox.length}');
print('Journeys: ${HiveService.journeyBox.length}');
print('Pending Sync: ${HiveService.getTotalPendingSyncCount()}');
```

---

## 📊 MONITORING & METRICS

### Key Metrics to Track

```dart
// 1. Sync Health
int pendingSync = HiveService.getTotalPendingSyncCount();
int failedSync = syncQueueBox.values.where((q) => q.retries >= 3).length;

// 2. Storage Usage
int totalRecords = journeyBox.length + claimBox.length + programBox.length + vehicleBox.length;

// 3. Offline Usage
int offlineJourneys = journeyBox.values.where((j) => !j.isSynced).length;
int offlineClaims = claimBox.values.where((c) => !c.isSynced).length;

// 4. Session Status
AuthHive? auth = HiveService.getCurrentAuth();
int sessionAge = DateTime.now().difference(auth?.loginAt ?? DateTime.now()).inDays;
```

---

## 🚨 KNOWN LIMITATIONS

1. **No Server-Side Conflict Resolution**: Last-write-wins strategy
2. **No Partial Updates**: Full record sync required
3. **Photo Size**: Large photos can impact storage (compress first)
4. **Max Sync Retries**: 3 attempts then manual intervention
5. **Single Active Journey**: Only one active journey allowed per user
6. **Program Assignment**: Stored as comma-separated string (not optimal)

---

## 📚 REFERENCES

- [Hive Documentation](https://docs.hivedb.dev/)
- [Backend Database Schema](./DETAIL_STRUCTURE_MODEL_SYSTEM_JARA.md)
- [System Architecture](./SYSTEM_ARCHITECTURE.md)
- [Data Isolation Guide](./DATA_ISOLATION_GUIDE.md)

---

**Document Status**: ✅ Complete  
**Last Review**: October 2025  
**Next Review**: When database schema changes

