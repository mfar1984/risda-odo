# TESTING OFFLINE CLAIMS - STEP BY STEP

**CRITICAL:** You MUST login while ONLINE first to populate Hive!

---

## 🔴 ISSUE YOU'RE FACING

```
Problem: "Tiada Tuntutan" and empty dropdown

Root Cause: Hive database is EMPTY!
  ├── Claims: 0
  ├── Journeys: 0
  └── Programs: 0

Why Empty?
  ├── You might have logged in while OFFLINE
  └── Splash screen skipped sync (no data downloaded)
```

---

## ✅ CORRECT TESTING PROCEDURE

### STEP 1: RESET & START FRESH

**Clear Hive data:**
```dart
// In Dart DevTools Console:
await HiveService.clearAllData();

// Or uninstall app:
adb uninstall com.risda.driver
```

---

### STEP 2: ENSURE SERVER RUNNING

```bash
# Terminal 1: Start Laravel
cd /path/to/RISDA-ODOMETER
php artisan serve

# Should show:
# Server running on [http://127.0.0.1:8000]
```

**Verify server reachable:**
```bash
curl http://10.0.2.2:8000/api/ping

# Should return:
# {"success":true,"message":"pong",...}
```

---

### STEP 3: FIRST LOGIN (MUST BE ONLINE!)

**Run app:**
```bash
flutter run
```

**Expected Splash Screen Logs:**
```
[log] 📡 Connectivity monitoring started
[log] ✅ Server reachable
[log] 📶 Connection status: ONLINE  ← MUST SEE THIS!
[log] 🔄 Online detected - syncing master data to Hive...
[log] 🔄 SYNCING ALL MASTER DATA TO HIVE
[log] 📋 Syncing programs from server...
[log] ✅ Programs synced to Hive: X programs
[log] 🚙 Syncing vehicles from server...
[log] ✅ Vehicles synced to Hive: X vehicles
[log] 🚗 Syncing journeys from server...
[log] ✅ Journeys synced to Hive: X journeys
[log] 💰 Syncing claims from server...
[log] ✅ Claims synced to Hive: X claims
```

**Login:**
- Email: fairiz@jara.my (or your test user)
- Password: (your password)

**Expected After Login:**
```
[log] ✅ Login successful: Fairiz Bin Rahman
[log] 🔄 Triggering post-login data sync...
[log] 🔄 SYNCING ALL MASTER DATA TO HIVE
[log] ✅ Programs synced to Hive: X programs
[log] ✅ Vehicles synced to Hive: X vehicles
[log] ✅ Journeys synced to Hive: X journeys
[log] ✅ Claims synced to Hive: X claims
```

**✅ Indicator should be GREEN 🟢 with pulse**

---

### STEP 4: VERIFY HIVE HAS DATA

**In Dart DevTools Console:**
```dart
final stats = HiveService.getStorageStats();
print(stats);

// Should show:
// {
//   boxes: {
//     auth: 1,
//     journeys: 45,    ← NOT 0!
//     programs: 12,    ← NOT 0!
//     vehicles: 8,     ← NOT 0!
//     claims: 10       ← NOT 0!
//   }
// }
```

**If still 0:**
```dart
// Check if user is logged in
final auth = HiveService.getCurrentAuth();
print('User: ${auth?.name}');

// Force sync manually
await syncService.syncAllMasterData();
```

---

### STEP 5: TEST CLAIMS TAB (ONLINE)

**Navigate to Claim tab**

**Expected:**
- ✅ See claims list (from Hive)
- ✅ Tab counts updated (Total, Pending, etc)
- ✅ NO "Error loading claims" message

**Click "Create Claim":**
- ✅ Dropdown shows "Pilih Log Perjalanan"
- ✅ Click dropdown
- ✅ See list of completed journeys
- ✅ Can select one

---

### STEP 6: TEST OFFLINE MODE

**Turn OFF WiFi OR Stop Laravel server**

**Wait 15-30 seconds** (for periodic check to detect)

**Expected:**
```
[log] 🔍 Periodic server check...
[log] ⚠️ First ping failed, retrying...
[log] ❌ Connection check failed (after retry)
[log] 📶 Status changed: OFFLINE
[log] 🔴 Dashboard: Offline - stopping notification polling
```

**✅ Indicator should turn RED 🔴**

---

### STEP 7: CREATE CLAIM OFFLINE

**Navigate: Claim → Create Claim**

**Expected:**
- ✅ Dropdown STILL shows journeys (from Hive!)
- ✅ Can fill form

**Fill form:**
- Log Perjalanan: (select one)
- Kategori: Fuel
- Jumlah: 50.00
- Keterangan: Test offline claim
- Resit: (upload photo)

**Submit**

**Expected:**
```
[log] 📴 Offline mode - saving claim to Hive
[log] 📷 Receipt saved locally: /data/.../claim_uuid_timestamp.jpg
[log] 💾 Claim saved to Hive (offline): uuid-xxxxx
```

**UI:**
- ✅ Orange notification: "Tuntutan disimpan offline. Akan sync bila online."
- ✅ Navigate back to claim list
- ✅ See new claim in list

---

### STEP 8: AUTO-SYNC WHEN BACK ONLINE

**Turn ON WiFi OR Start server**

**Within 15-30 seconds:**

**Expected:**
```
[log] 🔍 Periodic server check...
[log] ✅ Server reachable
[log] 📶 Status changed: OFFLINE → ONLINE
[log] 🟢 Back online - triggering callbacks...
[log] 🟢 SyncService: Back online - triggering auto-sync
[log] 🔄 STARTING PENDING DATA SYNC
[log] 💰 Step 2: Syncing pending claims...
[log]    Found 1 pending claims
[log]    ⬆️ Creating claim uuid-xxxxx...
```

**UI:**
- ✅ Indicator turns GREEN 🟢
- ✅ Claim syncs to server
- ✅ Can see in backend

---

## 🚨 TROUBLESHOOTING

### Problem: "Tiada Tuntutan" (Empty)

**Cause:** Hive is empty

**Solution:**
1. Logout
2. Ensure server running
3. Login again (will trigger sync)
4. Check logs for sync messages

---

### Problem: Dropdown "Tiada log perjalanan selesai"

**Cause:** No completed journeys in Hive

**Check:**
```dart
// DevTools:
final journeys = HiveService.getAllJourneys();
print('Total: ${journeys.length}');
print('Completed: ${journeys.where((j) => j.status == 'selesai').length}');
```

**Solution:**
1. Create some journeys di backend first
2. Logout & login again (trigger sync)

---

### Problem: Indicator stuck RED even when online

**Check:**
```bash
# Verify server reachable
curl http://10.0.2.2:8000/api/ping
```

**Force recheck:**
- Tap indicator
- Click "Recheck" button

---

## ✅ SUCCESS CRITERIA

After proper setup:
- [ ] Login while ONLINE
- [ ] Hive populated with data
- [ ] Claim tab shows data offline
- [ ] Dropdown works offline
- [ ] Can create claim offline
- [ ] Auto-sync when back online

**TRY NOW!** 🚀

