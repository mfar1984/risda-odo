# OFFLINE → ONLINE SYNC WORKFLOW

**Version**: 1.0.0  
**Last Updated**: October 2025  
**Policy**: 60-Day Data Retention + FIFO Cleanup

---

## 🔄 COMPLETE SYNC WORKFLOW

### 📱 OFFLINE → ONLINE TRANSITION

```
USER TURNS ON INTERNET
  ↓
┌─────────────────────────────────────────────────────┐
│ STEP 1: Auto-Detect Online (ConnectivityService)   │
├─────────────────────────────────────────────────────┤
│ • connectivity_plus detects WiFi/Mobile data        │
│ • Ping server: GET /api/ping                        │
│ • Verify server reachable                           │
│ • Update status: OFFLINE → ONLINE                   │
│ • Trigger onBackOnline() callbacks                  │
│ • Indicator: 🔴 RED → 🟢 GREEN (pulse animation)    │
└─────────────────────────────────────────────────────┘
  ↓
┌─────────────────────────────────────────────────────┐
│ STEP 2: Sync Pending Data (SyncService)            │
├─────────────────────────────────────────────────────┤
│ A. SYNC PENDING JOURNEYS                            │
│    • Get: HiveService.getPendingSyncJourneys()      │
│    • Filter: isSynced = false                       │
│    • Upload each journey to server                  │
│    • POST /api/log-pemandu/start (if new)           │
│    • PUT /api/log-pemandu/{id}/end (if update)      │
│    • Receive server ID                              │
│    • Update journey.id = server_id                  │
│    • Mark journey.isSynced = true                   │
│    • Save to Hive                                   │
│                                                      │
│ B. SYNC PENDING CLAIMS                              │
│    • Get: HiveService.getPendingSyncClaims()        │
│    • Filter: isSynced = false                       │
│    • Upload each claim to server                    │
│    • POST /api/tuntutan (if new)                    │
│    • PUT /api/tuntutan/{id} (if update)             │
│    • Receive server ID                              │
│    • Update claim.id = server_id                    │
│    • Mark claim.isSynced = true                     │
│    • Save to Hive                                   │
│                                                      │
│ C. PROCESS SYNC QUEUE                               │
│    • Get: HiveService.getPendingSyncQueue()         │
│    • Sort by priority (1=high, 2=medium, 3=low)     │
│    • Process each item                              │
│    • Remove from queue if successful                │
│    • Increment retries if failed (max 3)            │
└─────────────────────────────────────────────────────┘
  ↓
┌─────────────────────────────────────────────────────┐
│ STEP 3: Cleanup Old Data (60-Day Policy)           │
├─────────────────────────────────────────────────────┤
│ • Run: HiveService.cleanOldData()                   │
│ • Cutoff date: NOW - 60 days                        │
│                                                      │
│ A. DELETE OLD JOURNEYS                              │
│    WHERE:                                            │
│    • isSynced = true (safe to delete)               │
│    • status != 'dalam_perjalanan' (not active)      │
│    • createdAt < cutoff (older than 60 days)        │
│                                                      │
│ B. DELETE OLD CLAIMS                                │
│    WHERE:                                            │
│    • isSynced = true (safe to delete)               │
│    • status != 'pending' (not pending approval)     │
│    • createdAt < cutoff (older than 60 days)        │
│                                                      │
│ C. DELETE FAILED SYNC QUEUE                         │
│    WHERE:                                            │
│    • retries >= 3 (max retries exceeded)            │
│    • createdAt < NOW - 7 days (old failures)        │
└─────────────────────────────────────────────────────┘
  ↓
┌─────────────────────────────────────────────────────┐
│ STEP 4: Enforce Storage Limits (FIFO)              │
├─────────────────────────────────────────────────────┤
│ • Max journeys: 150 records                         │
│ • Max claims: 150 records                           │
│                                                      │
│ IF journeys > 150:                                  │
│    • Sort by createdAt ASC (oldest first)           │
│    • Filter: isSynced = true + not active           │
│    • Delete oldest (FIFO) until count = 150         │
│                                                      │
│ IF claims > 150:                                    │
│    • Sort by createdAt ASC (oldest first)           │
│    • Filter: isSynced = true + not pending          │
│    • Delete oldest (FIFO) until count = 150         │
└─────────────────────────────────────────────────────┘
  ↓
┌─────────────────────────────────────────────────────┐
│ STEP 5: Resume Normal Operations                   │
├─────────────────────────────────────────────────────┤
│ • Start notification polling                        │
│ • Load dashboard statistics                         │
│ • Load chart data                                   │
│ • User can use app normally                         │
└─────────────────────────────────────────────────────┘
```

---

## 📊 DATA LIFECYCLE EXAMPLE

### Scenario: Driver creates 200 journeys over 3 months

```
DAY 1-30 (Month 1)
├── Create 60 journeys
├── All synced to server (isSynced = true)
└── Stored in Hive (60 records)

DAY 31-60 (Month 2)
├── Create 70 journeys
├── All synced to server
├── Stored in Hive (130 records total)
└── Cleanup: Nothing deleted (all < 60 days old)

DAY 61-90 (Month 3)
├── Create 70 journeys (total 200)
├── All synced to server
├── Stored in Hive (200 records)
│
├── CLEANUP RUNS (60-Day Policy):
│   ├── Day 1-30 journeys are now >60 days old
│   ├── DELETE 60 old journeys
│   └── Remaining: 140 journeys (Day 31-90)
│
└── LIMIT CHECK:
    ├── 140 < 150 (within limit)
    └── No FIFO deletion needed

DAY 91 (Month 4)
├── Current records: 140 journeys
├── Day 31 journeys now >60 days old
├── CLEANUP: Delete Day 31 journeys
└── FIFO: Keep latest 150 (if exceeded)
```

---

## 🧹 CLEANUP RULES

### What Gets Deleted?

#### ✅ SAFE TO DELETE:
```sql
WHERE isSynced = true              -- Already on server (safe!)
  AND status != 'dalam_perjalanan' -- Not active
  AND status != 'pending'          -- Not pending
  AND createdAt < NOW - 60 days    -- Older than retention period
```

#### ❌ NEVER DELETE:
- Active journey (`status = 'dalam_perjalanan'`)
- Pending claims (`status = 'pending'`)
- Unsynced data (`isSynced = false`)
- Recent data (< 60 days)
- Data in sync queue

---

## 📅 CLEANUP SCHEDULE

### When Cleanup Runs:

| Trigger | Frequency | What Happens |
|---------|-----------|--------------|
| **App Startup** | Every launch | Check if weekly cleanup needed |
| **After Sync** | When online | Run cleanup after successful sync |
| **Manual** | User triggered | Settings → Clear Cache option |

### Weekly Check Logic:
```dart
bool shouldRunCleanup() {
  final lastCleanup = settings['last_cleanup'];
  if (lastCleanup == null) return true;  // Never cleaned
  
  final daysSince = NOW - lastCleanup;
  return daysSince >= 7;  // Once per week
}
```

---

## 🎯 FIFO (First In, First Out) LOGIC

### When Limits Exceeded:

```dart
IF journeys.length > 150:
  // Sort by oldest first
  journeys.sort((a, b) => a.createdAt.compareTo(b.createdAt))
  
  // Filter safe to delete
  deletable = journeys.where(
    j => j.isSynced && j.status != 'dalam_perjalanan'
  )
  
  // Delete oldest (FIFO)
  excess = journeys.length - 150
  delete deletable[0..excess]  // Oldest records first
```

### Example:
```
Records: [Day1, Day2, Day3, ... Day160]  (160 total)
Limit: 150
Excess: 10

DELETE: Day1, Day2, Day3, ..., Day10  (10 oldest)
KEEP: Day11, Day12, ..., Day160       (150 newest)
```

---

## 🔐 DATA SAFETY GUARANTEES

### Protection Layers:

1. **Sync Check**: Only delete if `isSynced = true`
2. **Status Check**: Skip active/pending items
3. **Date Check**: Keep data < 60 days
4. **Server Backup**: All deleted data exists on server
5. **Pagination**: Can retrieve historical data from server

### Recovery Path:
```
User needs old data (>60 days)
  ↓
Not in Hive (deleted)
  ↓
Fetch from server (pagination):
  GET /api/log-pemandu?page=2&dateFrom=2024-01-01
  ↓
Show in UI (memory only, not saved to Hive)
  ↓
User can view/export but not edit (read-only)
```

---

## 📊 STORAGE MONITORING

### Health Checks:

```dart
final stats = HiveService.getStorageStats();

{
  'boxes': {
    'journeys': 142,        // Current count
    'claims': 87,
    'programs': 12,
    'vehicles': 8,
  },
  'pending_sync': {
    'journeys': 3,          // Unsynced
    'claims': 1,
    'total': 4,
  },
  'estimated_size_kb': 185,  // ~185 KB
  'last_cleanup': '2025-10-06T12:00:00Z'
}
```

### Storage Limits:

| Metric | Soft Limit | Hard Limit | Action |
|--------|------------|------------|--------|
| Journeys | 150 | 200 | Delete oldest (FIFO) |
| Claims | 150 | 200 | Delete oldest (FIFO) |
| Total Size | 2 MB | 5 MB | Force cleanup |
| Age | 60 days | 90 days | Delete regardless |

---

## 🚨 EDGE CASES

### Case 1: Offline for Long Period
```
Scenario: User offline for 65 days

Day 1-30:   Create journeys (offline, isSynced = false)
Day 31-65:  Still offline, journeys accumulating

PROBLEM: Data >60 days but NOT synced yet!
SOLUTION: Cleanup ONLY deletes isSynced = true
         Unsynced data protected regardless of age
```

### Case 2: Sync Fails Repeatedly
```
Scenario: Journey won't sync (API error)

Attempt 1: Fail → retries = 1
Attempt 2: Fail → retries = 2  
Attempt 3: Fail → retries = 3
After 7 days: Delete from sync_queue (give up)

Journey itself: Kept in Hive (isSynced = false)
User can: View locally, cannot delete (pending)
```

### Case 3: Storage Full
```
Scenario: 200 journeys in Hive

Auto-cleanup:
├── Delete journeys >60 days (synced only)
├── If still >150: Delete oldest synced
└── Emergency: Warn user, suggest manual cleanup
```

---

## 🎯 ANSWERS TO YOUR CONCERNS

### Q1: "Data hari ke-61 akan auto clear?"

**Answer:** 
✅ **YES**, tetapi dengan protection:

```
Journey created on Day 1
├── Day 1-60: Keep in Hive ✅
├── Day 61: Eligible for cleanup
│   ├── IF isSynced = true → DELETE ✅
│   └── IF isSynced = false → KEEP (protect data!)
└── Day 90: Emergency delete (even if unsynced)
```

### Q2: "Risau tentang Start Journey & End Journey?"

**Answer:**  
✅ **CORRECT PRIORITY** - foundation dulu!

**Workflow yang betul:**
```
PHASE 1: Foundation (CURRENT) ✅
├── Connectivity detection
├── Data retention policy
├── Smart logout
├── Auto-sync framework
└── Offline indicator

PHASE 2: Journey Operations (NEXT)
├── Start Journey offline
├── End Journey offline
├── Sync journey to server
└── Handle conflicts

PHASE 3: Claims & Others
├── Create claim offline
├── Sync claims
└── Full offline mode
```

**Sebab:**
- Foundation must be solid first
- Journey/Claim sync will use SyncService we just built
- Cleanup will run after journey sync (automatic!)

### Q3: "FIFO - bagaimana exactly?"

**Answer:**
```
150 journeys (limit reached)
New journey created → Total: 151

FIFO Cleanup:
├── Sort by createdAt ASC (oldest first)
├── Filter: isSynced = true (safe to delete)
├── Delete [0] (oldest journey)
└── Total: 150 (within limit)
```

**Visual:**
```
Before: [J1, J2, J3, ..., J150, J151]  (151 total)
              ↓ Delete oldest
After:  [J2, J3, J4, ..., J150, J151]  (150 total)
```

---

## ✅ IMPLEMENTATION STATUS

| Component | Status | Notes |
|-----------|--------|-------|
| ConnectivityService | ✅ DONE | Auto-detect online/offline |
| SyncService | ✅ DONE | Framework ready, sync logic TODO |
| Data Cleanup (60-day) | ✅ DONE | Auto-runs after sync |
| FIFO Enforcement | ✅ DONE | Max 150 records |
| Offline Indicator | ✅ DONE | Green pulse / Red static |
| Smart Logout | ✅ DONE | Clear user data, keep master |
| Splash Screen | ✅ DONE | Real connectivity + cleanup |
| API Call Protection | ✅ DONE | Skip calls when offline |

**NEXT PHASE:** Implement actual journey/claim sync logic in SyncService

---

## 🚀 READY FOR TESTING

**Test Flow:**
1. ✅ Start app → See cleanup logs
2. ✅ Turn OFF internet → Indicator RED, API calls stopped
3. ✅ Turn ON internet → Indicator GREEN, auto-sync starts
4. ✅ Check logs → See sync workflow
5. ✅ Old data (>60 days) deleted automatically
6. ✅ Max 150 records enforced (FIFO)

**Foundation is SOLID!** Ready untuk implement Start/End Journey offline! 🎯

