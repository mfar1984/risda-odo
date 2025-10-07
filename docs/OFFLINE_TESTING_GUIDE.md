# OFFLINE MODE TESTING GUIDE

**Purpose**: Test offline/online detection, auto-sync trigger, and data cleanup  
**Version**: 1.0.0  
**Date**: October 2025

---

## 🎯 WHAT WE'RE TESTING (Phase 1 - Foundation)

| Feature | Status | What to Test |
|---------|--------|--------------|
| ✅ Auto-detect offline | Ready | Indicator turns red automatically |
| ✅ Auto-detect online | Ready | Indicator turns green + pulse |
| ✅ Stop API calls offline | Ready | No connection errors in log |
| ✅ Resume API calls online | Ready | API calls work again |
| ✅ Auto-sync trigger | Ready | Sync runs when back online |
| ✅ Data cleanup (60 days) | Ready | Old data deleted weekly |
| ✅ FIFO enforcement (150 limit) | Ready | Oldest deleted if exceeded |
| ✅ Smart logout | Ready | User data cleared, master kept |
| ⏳ Journey sync | Not yet | Skip for now |
| ⏳ Claim sync | Not yet | Skip for now |

---

## 🧪 TEST SCENARIOS

### TEST 1: Auto-Detect Offline/Online

**Steps:**
1. Start app (ensure server is running)
2. Check indicator di AppBar (top right, sebelah bell icon)
   - ✅ Should be **GREEN** 🟢 with pulse animation
3. Turn OFF WiFi on device
4. Wait 5-10 seconds
5. Check indicator
   - ✅ Should turn **RED** 🔴 (static, no animation)
6. Check logs
   - ✅ Should see: `📶 Status changed: OFFLINE`
   - ✅ Should see: `⚠️ Skipping notification load - offline`
   - ✅ Should see: `⚠️ Skipping dashboard stats - offline`

**Expected Result:**
- ✅ Indicator changes automatically (no manual recheck needed)
- ✅ NO connection error spam in logs
- ✅ App continues working (doesn't crash)

---

### TEST 2: Auto-Detect Back Online

**Steps:**
1. Ensure device is OFFLINE (from Test 1)
2. Indicator should be RED 🔴
3. Turn ON WiFi
4. Wait 5-10 seconds
5. Check indicator
   - ✅ Should turn **GREEN** 🟢 with pulse
6. Check logs
   - ✅ Should see: `📶 Status changed: ONLINE`
   - ✅ Should see: `✅ Server reachable`
   - ✅ Should see: `🟢 Dashboard: Back online - resuming notification polling`
   - ✅ Should see: `🔄 SyncService: Back online - triggering auto-sync`

**Expected Result:**
- ✅ Indicator changes automatically
- ✅ Auto-sync triggered
- ✅ API calls resume
- ✅ Notification polling restarted

---

### TEST 3: Offline Indicator Dialog

**Steps:**
1. Tap on offline indicator (green or red badge)
2. Dialog should appear showing:
   - Connection status (Online/Offline)
   - Last checked timestamp
   - If offline: offline duration
3. Click "Recheck" button
4. Dialog closes
5. Check indicator updates

**Expected Result:**
- ✅ Dialog shows correct status
- ✅ Manual recheck works
- ✅ Status updates in indicator

---

### TEST 4: Stop API Calls When Offline

**Steps:**
1. Start app (online)
2. Navigate to Overview tab
   - ✅ Dashboard stats load
   - ✅ Chart data loads
3. Navigate to Do tab
   - ✅ Programs load
   - ✅ Chart loads
4. Turn OFF WiFi
5. Pull to refresh on any tab
6. Check logs

**Expected Result:**
- ✅ Logs show: `⚠️ Skipping ... - offline`
- ✅ NO connection error exceptions
- ✅ NO SocketException errors
- ✅ App doesn't hang/freeze

---

### TEST 5: Data Cleanup (60-Day Policy)

**Setup:**
```sql
-- In your MySQL database, create test data
INSERT INTO log_pemandu (
  pemandu_id, kenderaan_id, program_id, 
  tarikh_perjalanan, masa_keluar, 
  odometer_keluar, destinasi, status,
  dicipta_oleh, created_at
) VALUES (
  10, 1, 9,
  '2024-07-01', '08:00',
  10000, 'Test Old Journey', 'selesai',
  10, '2024-07-01 08:00:00'  -- 90 days old!
);
```

**Steps:**
1. Login to app
2. Sync data (will download old journey to Hive)
3. Force cleanup by:
   ```dart
   // In Dart DevTools console:
   HiveService.settingsBox.delete('last_cleanup');  // Reset
   ```
4. Restart app
5. Check logs during "Loading Local Data" step

**Expected Result:**
```
[log] 🧹 Cleanup: X journeys, Y claims deleted
[log] 🧹 Enforced limit: Deleted Z old journeys (if >150)
```

---

### TEST 6: Smart Logout

**Steps:**
1. Login as User A
2. View journeys & claims (should have data)
3. Logout
4. Check logs:
   - ✅ Should see: `🗑️ Cleared: auth, journeys, claims, sync queue`
   - ✅ Should see: `💾 Retained: X programs, Y vehicles`
5. Login as User B (different account)
6. View journeys & claims
   - ✅ Should be EMPTY (User A data cleared!)

**Expected Result:**
- ✅ User-specific data cleared
- ✅ Master data kept (faster login)
- ✅ No data leakage between users

---

### TEST 7: Storage Statistics

**Steps:**
1. Open Dart DevTools Console
2. Run:
   ```dart
   final stats = HiveService.getStorageStats();
   print(stats);
   ```

**Expected Output:**
```json
{
  "boxes": {
    "auth": 1,
    "journeys": 42,
    "programs": 12,
    "vehicles": 8,
    "claims": 15,
    "sync_queue": 0
  },
  "pending_sync": {
    "journeys": 0,
    "claims": 0,
    "queue": 0,
    "total": 0
  },
  "estimated_size_kb": 75,
  "last_cleanup": "2025-10-07T06:30:00.000Z"
}
```

---

### TEST 8: Offline → Online Auto-Sync Flow

**Steps:**
1. Start app OFFLINE (WiFi OFF)
2. Indicator should be RED 🔴
3. Logs should show:
   ```
   [log] ⚠️ Offline - skip sync on startup
   ```
4. Turn ON WiFi
5. Wait for auto-detection
6. Check logs for complete flow:

**Expected Log Sequence:**
```
[log] 📶 Connectivity changed: wifi
[log] ✅ Connection restored - verifying server access...
[log] ✅ Server reachable
[log] 📶 Status changed: ONLINE
[log] 🟢 SyncService: Back online - triggering auto-sync
[log] 🔄 ========================================
[log] 🔄 STARTING PENDING DATA SYNC
[log] 🔄 ========================================
[log] 📦 Step 1: Syncing pending journeys...
[log]    Found 0 pending journeys
[log] 💰 Step 2: Syncing pending claims...
[log]    Found 0 pending claims
[log] 📋 Step 3: Processing sync queue...
[log]    Found 0 queue items
[log] 🧹 Step 4: Cleaning up old data...
[log]    Deleted X old journeys
[log]    Deleted Y old claims
[log] 📊 Step 5: Enforcing storage limits...
[log] ✅ SYNC COMPLETED SUCCESSFULLY
[log] 🟢 Dashboard: Back online - resuming notification polling
```

---

## 🐛 TROUBLESHOOTING

### Issue 1: Indicator stays RED even when online

**Diagnosis:**
```bash
# Check if server is running
curl http://10.0.2.2:8000/api/ping

# Should return:
{"success":true,"message":"pong",...}
```

**Solution:**
- Ensure Laravel server running: Check di terminal
- Check emulator can reach `10.0.2.2`
- Manual recheck: Tap indicator → Recheck button

---

### Issue 2: Connection errors still appearing

**Check:**
1. Pastikan all API calls check connectivity first:
   ```dart
   if (!connectivity.isOnline) return;
   ```

2. Search for API calls without checks:
   ```bash
   grep -r "await _apiService\." lib/screens/
   ```

---

### Issue 3: Cleanup not running

**Force cleanup:**
```dart
// In Dart DevTools:
await HiveService.settingsBox.delete('last_cleanup');
// Restart app
```

---

## ✅ SUCCESS CRITERIA

### Offline Mode:
- [x] Indicator turns RED automatically
- [x] No connection error spam
- [x] App continues working
- [x] Data viewable from Hive cache

### Online Mode:
- [x] Indicator turns GREEN with pulse
- [x] Auto-sync triggered
- [x] API calls resume
- [x] Fresh data loads

### Data Management:
- [x] Cleanup runs weekly
- [x] Old data (>60 days) deleted
- [x] Max 150 records enforced
- [x] Storage stats accurate

### Security:
- [x] Logout clears user data
- [x] Master data retained
- [x] No data leakage between users

---

## 🚀 READY TO TEST!

**Run app:**
```bash
cd risda_driver_app
flutter run
```

**Watch logs:**
- Look for `📶` connectivity changes
- Look for `🔄` sync operations
- Look for `🧹` cleanup operations
- Look for `⚠️` offline skips

**Test each scenario above** and report any issues! 🎯

**Selepas test OK, kita proceed dengan Journey & Claim sync!**

