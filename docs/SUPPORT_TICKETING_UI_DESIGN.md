# Support Ticketing System - UI/UX Design Mockup

> **Design System:** Poppins Font, 32px inputs/dropdowns, Clean & Professional  
> **Date:** 2025-10-03  
> **Status:** Design Approval Pending

---

## 🎨 **DESIGN SPECIFICATIONS**

### **Typography**
- **Font Family:** Poppins (Google Fonts)
- **Sizes:**
  - Page Title: 14px (font-semibold)
  - Section Heading: 12px (font-semibold)
  - Body Text: 11px (font-normal)
  - Small Text: 10px (font-normal)

### **Input Elements**
- **Height:** 32px (h-8)
- **Font Size:** 11px (text-[11px])
- **Border Radius:** 2px (rounded-sm)
- **Border:** 1px solid #E5E7EB (border-gray-200)
- **Focus:** Ring 0, border-blue-500

### **Spacing**
- **Container Padding:** 24px (p-6)
- **Card Padding:** 16px (p-4)
- **Element Gap:** 12px (gap-3)
- **Section Margin:** 24px (mb-6)

### **Colors**
- **Primary:** Blue-600 (#2563EB)
- **Success:** Green-600 (#16A34A)
- **Warning:** Yellow-600 (#CA8A04)
- **Danger:** Red-600 (#DC2626)
- **Gray:** Gray-600 (#4B5563)
- **Background:** White (#FFFFFF)
- **Border:** Gray-200 (#E5E7EB)

---

## 📱 **1. STAFF VIEW (Pengurus) - Main List**

### **URL:** `/help/hubungi-sokongan`

```
┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ JARA - Sistem Pengurusan Perjalanan                                      ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┌──────────────────────────────────────────────────────────────────────────┐
│ Hubungi Sokongan > Senarai Tiket                                        │
└──────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│ Container: bg-white, rounded-sm, shadow-sm, border border-gray-200, p-6 │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ 🎫 Tiket Sokongan                           [+ Buat Tiket Baru] → │ │
│  │ Font: Poppins 14px semibold                 Button: h-8, text-11px │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Tab Navigation (h-10, border-b border-gray-200)                    │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ [Dari Pemandu] [Tiket Saya] [Semua]                               │ │
│  │  Active: border-b-2 border-blue-600, text-blue-600                 │ │
│  │  Inactive: text-gray-600, hover:text-gray-900                      │ │
│  │  Font: Poppins 11px medium                                         │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ 📊 Ringkasan (Grid: 4 columns, gap-3, mb-6)                       │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐             │ │
│  │ │ 🟢 Baru  │ │ 🟡 Proses│ │ 🔴 Urgent│ │ ✅ Selesai│             │ │
│  │ │   3      │ │    5     │ │    2     │ │    45    │             │ │
│  │ │ text-12px│ │ text-12px│ │ text-12px│ │ text-12px│             │ │
│  │ │ semibold │ │ semibold │ │ semibold │ │ semibold │             │ │
│  │ └──────────┘ └──────────┘ └──────────┘ └──────────┘             │ │
│  │ Each card: bg-gray-50, p-4, rounded-sm, border border-gray-200    │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Filter & Search (flex gap-3, mb-6)                                 │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ ┌────────────────────────────────────────────────────────────────┐│ │
│  │ │ 🔍 [Cari tiket...]                                              ││ │
│  │ │ Input: h-8 (32px), text-11px, px-3, rounded-sm, border          ││ │
│  │ │ border-gray-200, flex-1                                         ││ │
│  │ └────────────────────────────────────────────────────────────────┘│ │
│  │                                                                     │ │
│  │ ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────┐   │ │
│  │ │ Status ▼         │ │ Keutamaan ▼      │ │ Kategori ▼       │   │ │
│  │ │ h-8, text-11px   │ │ h-8, text-11px   │ │ h-8, text-11px   │   │ │
│  │ └──────────────────┘ └──────────────────┘ └──────────────────┘   │ │
│  │ Select: h-8 (32px), px-3, rounded-sm, border border-gray-200      │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Ticket List (space-y-3)                                            │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ Ticket Card #1                                                  │ │ │
│  │ │ Container: bg-white, p-4, rounded-sm, border border-gray-200   │ │ │
│  │ │ hover:border-blue-300, hover:shadow-sm, transition             │ │ │
│  │ ├────────────────────────────────────────────────────────────────┤ │ │
│  │ │ ┌──────────────────────────────────────────────────────────┐   │ │ │
│  │ │ │ Header (flex justify-between items-center, mb-2)         │   │ │ │
│  │ │ ├──────────────────────────────────────────────────────────┤   │ │ │
│  │ │ │ 📋 TICKET-0001                    🔴 Tinggi    Baru      │   │ │ │
│  │ │ │ Font: 11px medium                 Badges: h-5, text-10px  │   │ │ │
│  │ │ │ text-gray-900                     px-2, rounded-sm       │   │ │ │
│  │ │ └──────────────────────────────────────────────────────────┘   │ │ │
│  │ │                                                                 │ │ │
│  │ │ ┌──────────────────────────────────────────────────────────┐   │ │ │
│  │ │ │ Subject (text-12px semibold, text-gray-900, mb-2)        │   │ │ │
│  │ │ ├──────────────────────────────────────────────────────────┤   │ │ │
│  │ │ │ Tak boleh login di aplikasi mobile                       │   │ │ │
│  │ │ └──────────────────────────────────────────────────────────┘   │ │ │
│  │ │                                                                 │ │ │
│  │ │ ┌──────────────────────────────────────────────────────────┐   │ │ │
│  │ │ │ Meta Info (flex gap-4, text-10px, text-gray-600, mb-3)   │   │ │ │
│  │ │ ├──────────────────────────────────────────────────────────┤   │ │ │
│  │ │ │ 👤 fairiz@jara.my (Pemandu, Stesen A)                    │   │ │ │
│  │ │ │ ⏰ 2 jam lalu                                             │   │ │ │
│  │ │ │ 💬 3 mesej                                                │   │ │ │
│  │ │ │ 🏷️ Teknikal                                               │   │ │ │
│  │ │ └──────────────────────────────────────────────────────────┘   │ │ │
│  │ │                                                                 │ │ │
│  │ │ ┌──────────────────────────────────────────────────────────┐   │ │ │
│  │ │ │ Actions (flex gap-2)                                     │   │ │ │
│  │ │ ├──────────────────────────────────────────────────────────┤   │ │ │
│  │ │ │ [Lihat]  [Balas]  [⬆️ Escalate ke Admin]                │   │ │ │
│  │ │ │ Buttons: h-7, text-10px, px-3, rounded-sm                │   │ │ │
│  │ │ │ Primary: bg-blue-600, text-white                         │   │ │ │
│  │ │ │ Secondary: border border-gray-300, text-gray-700         │   │ │ │
│  │ │ │ Danger: bg-red-600, text-white                           │   │ │ │
│  │ │ └──────────────────────────────────────────────────────────┘   │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ Ticket Card #2 (same structure)                                 │ │ │
│  │ │ ...                                                             │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ Pagination (flex justify-center, mt-6)                          │ │ │
│  │ │ Buttons: h-8, text-10px, min-w-8                                │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## 👨‍💼 **2. ADMINISTRATOR VIEW - Main List**

### **URL:** `/help/hubungi-sokongan`

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Container: bg-white, rounded-sm, shadow-sm, border border-gray-200, p-6 │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ 🎫 Tiket Sokongan - Administrator                                  │ │
│  │ Font: Poppins 14px semibold, text-gray-900                         │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Tab Navigation (h-10, border-b border-gray-200)                    │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ [🔺 Escalated] [📬 Tiket Staff] [👥 Dari Pemandu] [✅ Selesai]    │ │
│  │  Active: border-b-2 border-blue-600, text-blue-600                 │ │
│  │  Font: Poppins 11px medium                                         │ │
│  │  With badge count: bg-red-600, text-white, text-9px, px-1.5        │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ 📊 Dashboard Keseluruhan (Grid: 4 columns, gap-4, mb-6)           │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────┐  │ │
│  │ │ 🔺 Escalated │ │ 📬 Staff     │ │ 👥 Driver    │ │ ✅ Hari  │  │ │
│  │ │              │ │              │ │              │ │    Ini   │  │ │
│  │ │     8        │ │     3        │ │    156       │ │    23    │  │ │
│  │ │  tiket       │ │  tiket       │ │  tiket       │ │  selesai │  │ │
│  │ │              │ │              │ │              │ │          │  │ │
│  │ │ Number:      │ │ Number:      │ │ Number:      │ │ Number:  │  │ │
│  │ │ text-20px    │ │ text-20px    │ │ text-20px    │ │ text-20px│  │ │
│  │ │ font-bold    │ │ font-bold    │ │ font-bold    │ │ font-bold│  │ │
│  │ │              │ │              │ │              │ │          │  │ │
│  │ │ Label:       │ │ Label:       │ │ Label:       │ │ Label:   │  │ │
│  │ │ text-10px    │ │ text-10px    │ │ text-10px    │ │ text-10px│  │ │
│  │ │ text-gray-   │ │ text-gray-   │ │ text-gray-   │ │ text-gray│  │ │
│  │ │ 600          │ │ 600          │ │ 600          │ │ -600     │  │ │
│  │ └──────────────┘ └──────────────┘ └──────────────┘ └──────────┘  │ │
│  │ Card: bg-gradient-to-br from-blue-50 to-white, p-4, rounded-sm   │ │
│  │ border border-blue-200                                             │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ ⚠️ Perlu Perhatian (bg-red-50, border-l-4 border-red-500, p-3)   │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ • 2 tiket escalated lebih 24 jam                                   │ │
│  │ • 1 critical staff request                                         │ │
│  │ Font: text-10px, text-red-800                                      │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Advanced Filters (grid grid-cols-5 gap-3, mb-6)                    │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ 🔍 [Cari tiket...]                                            │  │ │
│  │ │ col-span-2, h-8 (32px), text-11px                             │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐  │ │
│  │ │ Organisasi▼ │ │ Status ▼    │ │ Keutamaan ▼ │ │ Kategori ▼  │  │ │
│  │ │ h-8         │ │ h-8         │ │ h-8         │ │ h-8         │  │ │
│  │ │ text-11px   │ │ text-11px   │ │ text-11px   │ │ text-11px   │  │ │
│  │ └─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘  │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Ticket List - Enhanced for Admin                                   │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ 🔺 Escalated Ticket Card                                        │ │ │
│  │ │ border-l-4 border-red-500 (escalated indicator)                │ │ │
│  │ ├────────────────────────────────────────────────────────────────┤ │ │
│  │ │ 📋 TICKET-0001                    🔴 Tinggi    Escalated       │ │ │
│  │ │                                                                 │ │ │
│  │ │ Tak boleh login di aplikasi mobile                             │ │ │
│  │ │                                                                 │ │ │
│  │ │ 👤 fairiz@jara.my (Pemandu, Stesen A, Bahagian Utara)          │ │ │
│  │ │ ⬆️ Escalated by: faizan@jara.my (2 jam lalu)                  │ │ │
│  │ │ ⏰ Created: 5 jam lalu | 💬 5 mesej | 🏷️ Teknikal              │ │ │
│  │ │                                                                 │ │ │
│  │ │ [Lihat & Resolve]  [Assign to...]  [Mark Priority]            │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ 📬 Staff Ticket Card                                            │ │ │
│  │ │ border-l-4 border-blue-500 (staff indicator)                   │ │ │
│  │ ├────────────────────────────────────────────────────────────────┤ │ │
│  │ │ 📋 TICKET-0010                    🟡 Sederhana    Staff Request │ │ │
│  │ │                                                                 │ │ │
│  │ │ Perlukan akses ke sistem billing                               │ │ │
│  │ │                                                                 │ │ │
│  │ │ 👨‍💼 faizan@jara.my (Staff, Stesen A)                           │ │ │
│  │ │ ⏰ Created: 1 hari lalu | 💬 2 mesej | 🏷️ Pentadbiran          │ │ │
│  │ │                                                                 │ │ │
│  │ │ [Lihat & Respond]  [Assign to...]                              │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## 💬 **3. TICKET THREAD VIEW (Same for All Users)**

### **URL:** `/help/hubungi-sokongan/{ticket}`

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Container: bg-white, rounded-sm, shadow-sm, border border-gray-200, p-6 │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Header Section                                                      │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ 🎫 TICKET-0001: Tak boleh login di aplikasi mobile                 │ │
│  │ Font: Poppins 14px semibold, text-gray-900                         │ │
│  │                                                                     │ │
│  │ Status: [Baru]  Priority: [🔴 Tinggi]  Category: [Teknikal]       │ │
│  │ Badges: h-6, text-10px, px-2, rounded-sm, inline-flex items-center│ │
│  │                                                                     │ │
│  │ Created: 2 jam lalu | Last activity: 30 min lalu                   │ │
│  │ Font: text-10px, text-gray-600                                     │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Participants Info (bg-gray-50, p-3, rounded-sm, mb-6)              │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ 👤 Pemohon: fairiz@jara.my (Pemandu, Stesen A)                     │ │
│  │ 👨‍💼 Assigned: faizan@jara.my (Pengurus Stesen A)                   │ │
│  │ Font: text-10px, text-gray-700                                     │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ 💬 Conversation Thread (space-y-4)                                 │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ Message #1 (Initial Message)                                    │ │ │
│  │ │ bg-blue-50, p-4, rounded-sm, border-l-4 border-blue-500        │ │ │
│  │ ├────────────────────────────────────────────────────────────────┤ │ │
│  │ │ ┌──────────────────────────────────────────────────────────┐   │ │ │
│  │ │ │ 👤 fairiz@jara.my (Pemandu)            2 jam lalu        │   │ │ │
│  │ │ │ flex justify-between, text-10px, text-gray-600, mb-2     │   │ │ │
│  │ │ └──────────────────────────────────────────────────────────┘   │ │ │
│  │ │                                                                 │ │ │
│  │ │ ┌──────────────────────────────────────────────────────────┐   │ │ │
│  │ │ │ Message Content                                          │   │ │ │
│  │ │ │ Font: Poppins 11px, text-gray-900, leading-relaxed       │   │ │ │
│  │ │ ├──────────────────────────────────────────────────────────┤   │ │ │
│  │ │ │ Assalamualaikum. Saya tak boleh login ke dalam          │   │ │ │
│  │ │ │ aplikasi. Password saya salah katanya. Sila bantu.       │   │ │ │
│  │ │ │ Terima kasih.                                            │   │ │ │
│  │ │ └──────────────────────────────────────────────────────────┘   │ │ │
│  │ │                                                                 │ │ │
│  │ │ ┌──────────────────────────────────────────────────────────┐   │ │ │
│  │ │ │ Attachments (if any) - mt-2                              │   │ │ │
│  │ │ ├──────────────────────────────────────────────────────────┤   │ │ │
│  │ │ │ 📎 screenshot-error.png (125 KB)                         │   │ │ │
│  │ │ │ Attachment: flex items-center gap-2, bg-white, p-2       │   │ │ │
│  │ │ │ rounded-sm, border, text-10px, hover:bg-gray-50          │   │ │ │
│  │ │ └──────────────────────────────────────────────────────────┘   │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ Message #2 (Staff Reply)                                        │ │ │
│  │ │ bg-white, p-4, rounded-sm, border border-gray-200              │ │ │
│  │ ├────────────────────────────────────────────────────────────────┤ │ │
│  │ │ 👨‍💼 faizan@jara.my (Pengurus)             1 jam lalu           │ │ │
│  │ │                                                                 │ │ │
│  │ │ Waalaikumussalam. Saya akan cuba reset password anda           │ │ │
│  │ │ sekarang. Sila tunggu sebentar...                              │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ Message #3 (Admin Reply - if escalated)                         │ │ │
│  │ │ bg-green-50, p-4, rounded-sm, border-l-4 border-green-500      │ │ │
│  │ ├────────────────────────────────────────────────────────────────┤ │ │
│  │ │ 👨‍💼 admin@jara.my (Administrator)         30 min lalu          │ │ │
│  │ │                                                                 │ │ │
│  │ │ Saya dah reset password anda. Sila cuba login semula dengan:   │ │ │
│  │ │ Email: fairiz@jara.my                                           │ │ │
│  │ │ Password: TempPass123 (sila tukar selepas login)               │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  │                                                                     │ │
│  │ ┌────────────────────────────────────────────────────────────────┐ │ │
│  │ │ System Message (Escalation)                                     │ │ │
│  │ │ bg-yellow-50, p-3, rounded-sm, text-center, border-dashed      │ │ │
│  │ │ border border-yellow-400                                        │ │ │
│  │ ├────────────────────────────────────────────────────────────────┤ │ │
│  │ │ 🔺 Tiket ini telah dieskalasi kepada Administrator             │ │ │
│  │ │ by faizan@jara.my pada 1 jam lalu                              │ │ │
│  │ │ Font: text-10px, text-yellow-800, italic                       │ │ │
│  │ └────────────────────────────────────────────────────────────────┘ │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ 📝 Reply Box (bg-gray-50, p-4, rounded-sm, mt-6)                   │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ Balas Tiket                                                         │ │
│  │ Font: Poppins 11px semibold, text-gray-900, mb-3                   │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Mesej: *                                                      │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ Textarea                                                │   │  │ │
│  │ │ │ rows: 4                                                 │   │  │ │
│  │ │ │ Font: Poppins 11px                                      │   │  │ │
│  │ │ │ p-3, rounded-sm, border border-gray-200                 │   │  │ │
│  │ │ │ focus:ring-0, focus:border-blue-500                     │   │  │ │
│  │ │ │ resize-none                                             │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Lampiran (Optional)                                           │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ [📎 Pilih Fail]                                         │   │  │
│  │ │ │ Input file: h-8, text-10px                              │   │  │ │
│  │ │ │ Max 3 files, 10MB each                                  │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Actions (flex justify-end gap-2, mt-4)                        │  │ │
│  │ ├──────────────────────────────────────────────────────────────┤  │ │
│  │ │ [Batal]  [Hantar Mesej] →                                    │  │ │
│  │ │ Buttons: h-8, text-11px, px-4, rounded-sm                     │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ 🔧 Actions (Staff/Admin) - flex gap-2, mt-6, pt-6, border-t       │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ [⬆️ Escalate ke Admin]  [✅ Mark as Resolved]  [🔒 Close]         │ │
│  │ Buttons: h-8, text-11px, px-4, rounded-sm                          │ │
│  │ Conditional based on user role & ticket status                     │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## ➕ **4. CREATE TICKET FORM (Staff to Admin)**

### **URL:** `/help/hubungi-sokongan/create`

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Container: bg-white, rounded-sm, shadow-sm, border border-gray-200, p-6 │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ 🎫 Buat Tiket Baru untuk Administrator                             │ │
│  │ Font: Poppins 14px semibold, text-gray-900                         │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Info Box (bg-blue-50, border-l-4 border-blue-500, p-3, mb-6)      │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ ℹ️ Tiket ini akan terus dihantar kepada Administrator             │ │
│  │ Font: text-10px, text-blue-800                                     │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Form Fields (space-y-4)                                            │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Kepada:                                                       │  │ │
│  │ │ Label: text-11px, font-medium, text-gray-700, mb-1           │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ 👨‍💼 Administrator (admin@jara.my) ✓                    │   │  │ │
│  │ │ │ Display: h-8, px-3, bg-gray-100, rounded-sm, border    │   │  │ │
│  │ │ │ border-gray-200, text-11px, text-gray-600              │   │  │ │
│  │ │ │ (Read-only, auto-set)                                  │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Tajuk / Subject: *                                            │  │ │
│  │ │ Label: text-11px, font-medium, text-gray-700, mb-1           │  │ │
│  │ │ Required indicator: text-red-600                             │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ [Input text]                                            │   │  │ │
│  │ │ │ h-8 (32px), px-3, text-11px                             │   │  │ │
│  │ │ │ rounded-sm, border border-gray-200                      │   │  │ │
│  │ │ │ focus:ring-0, focus:border-blue-500                     │   │  │ │
│  │ │ │ placeholder: "Contoh: Perlukan akses sistem..."         │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Kategori: *                                                   │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ [Pilih Kategori ▼]                                      │   │  │ │
│  │ │ │ Select: h-8 (32px), px-3, text-11px                     │   │  │ │
│  │ │ │ rounded-sm, border border-gray-200                      │   │  │ │
│  │ │ │                                                          │   │  │ │
│  │ │ │ Options:                                                │   │  │ │
│  │ │ │ • Teknikal - Sistem & teknologi                         │   │  │ │
│  │ │ │ • Akaun - User access & permissions                     │   │  │ │
│  │ │ │ • Perjalanan - Program & journey issues                 │   │  │ │
│  │ │ │ • Tuntutan - Claims & approvals                         │   │  │ │
│  │ │ │ • Pentadbiran - Admin tasks                             │   │  │ │
│  │ │ │ • Lain-lain                                             │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Keutamaan / Priority: *                                       │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ Radio buttons (flex gap-4)                              │   │  │ │
│  │ │ │                                                          │   │  │ │
│  │ │ │ ○ 🟢 Rendah    ○ 🟡 Sederhana    ○ 🔴 Tinggi           │   │  │ │
│  │ │ │                                                          │   │  │ │
│  │ │ │ Radio: h-4, w-4, text-blue-600                          │   │  │ │
│  │ │ │ Label: text-11px, ml-2                                  │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Mesej / Message: *                                            │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ Textarea                                                │   │  │ │
│  │ │ │ rows: 6                                                 │   │  │ │
│  │ │ │ Font: Poppins 11px                                      │   │  │ │
│  │ │ │ p-3, rounded-sm, border border-gray-200                 │   │  │ │
│  │ │ │ focus:ring-0, focus:border-blue-500                     │   │  │ │
│  │ │ │ placeholder: "Terangkan masalah dengan terperinci..."   │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ │ Character count: text-9px, text-gray-500, text-right        │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Lampiran (Optional):                                          │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ [📎 Pilih Fail]                                         │   │  │ │
│  │ │ │ Input file: text-10px, file:mr-4 file:py-1 file:px-3   │   │  │ │
│  │ │ │ file:rounded-sm file:border-0 file:bg-blue-50           │   │  │ │
│  │ │ │ file:text-blue-700 file:cursor-pointer                  │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ │ Help text: text-9px, text-gray-500, mt-1                    │  │ │
│  │ │ "Max 3 files, 10MB each (PDF, JPG, PNG)"                    │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  │                                                                     │ │
│  │ ┌──────────────────────────────────────────────────────────────┐  │ │
│  │ │ Selected Files Preview (if any) - mt-2                        │  │ │
│  │ │ ┌────────────────────────────────────────────────────────┐   │  │ │
│  │ │ │ 📄 document.pdf (2.5 MB) [×]                            │   │  │ │
│  │ │ │ 📷 screenshot.png (500 KB) [×]                          │   │  │ │
│  │ │ │ Items: flex items-center gap-2, bg-gray-50, p-2         │   │  │ │
│  │ │ │ rounded-sm, text-10px                                   │   │  │ │
│  │ │ └────────────────────────────────────────────────────────┘   │  │ │
│  │ └──────────────────────────────────────────────────────────────┘  │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ Form Actions (flex justify-end gap-3, mt-6, pt-6, border-t)       │ │
│  ├────────────────────────────────────────────────────────────────────┤ │
│  │ [Batal]                              [Hantar Tiket] →             │ │
│  │                                                                     │ │
│  │ Batal: h-8, px-4, text-11px, rounded-sm                            │ │
│  │ border border-gray-300, text-gray-700, hover:bg-gray-50            │ │
│  │                                                                     │ │
│  │ Hantar: h-8, px-6, text-11px, rounded-sm, font-medium              │ │
│  │ bg-blue-600, text-white, hover:bg-blue-700                         │ │
│  └────────────────────────────────────────────────────────────────────┘ │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## 🎨 **TAILWIND CSS CLASSES REFERENCE**

### **Input Elements (h-8 / 32px)**
```css
/* Text Input */
.input-standard {
  @apply h-8 px-3 text-[11px] rounded-sm border border-gray-200;
  @apply focus:ring-0 focus:border-blue-500 focus:outline-none;
  @apply placeholder:text-gray-400;
}

/* Select Dropdown */
.select-standard {
  @apply h-8 px-3 text-[11px] rounded-sm border border-gray-200;
  @apply focus:ring-0 focus:border-blue-500 focus:outline-none;
  @apply bg-white cursor-pointer;
}

/* Textarea */
.textarea-standard {
  @apply p-3 text-[11px] rounded-sm border border-gray-200;
  @apply focus:ring-0 focus:border-blue-500 focus:outline-none;
  @apply resize-none;
}
```

### **Buttons**
```css
/* Primary Button */
.btn-primary {
  @apply h-8 px-4 text-[11px] font-medium rounded-sm;
  @apply bg-blue-600 text-white;
  @apply hover:bg-blue-700 transition-colors;
  @apply inline-flex items-center justify-center;
}

/* Secondary Button */
.btn-secondary {
  @apply h-8 px-4 text-[11px] rounded-sm;
  @apply border border-gray-300 text-gray-700;
  @apply hover:bg-gray-50 transition-colors;
  @apply inline-flex items-center justify-center;
}

/* Danger Button */
.btn-danger {
  @apply h-8 px-4 text-[11px] font-medium rounded-sm;
  @apply bg-red-600 text-white;
  @apply hover:bg-red-700 transition-colors;
  @apply inline-flex items-center justify-center;
}
```

### **Badges**
```css
/* Status Badge */
.badge {
  @apply h-5 px-2 text-[10px] rounded-sm;
  @apply inline-flex items-center font-medium;
}

.badge-red {
  @apply badge bg-red-100 text-red-800 border border-red-200;
}

.badge-yellow {
  @apply badge bg-yellow-100 text-yellow-800 border border-yellow-200;
}

.badge-green {
  @apply badge bg-green-100 text-green-800 border border-green-200;
}

.badge-blue {
  @apply badge bg-blue-100 text-blue-800 border border-blue-200;
}
```

### **Cards**
```css
/* Ticket Card */
.ticket-card {
  @apply bg-white p-4 rounded-sm border border-gray-200;
  @apply hover:border-blue-300 hover:shadow-sm;
  @apply transition-all duration-200 cursor-pointer;
}

/* Escalated Indicator */
.ticket-card-escalated {
  @apply ticket-card border-l-4 border-l-red-500;
}

/* Staff Ticket Indicator */
.ticket-card-staff {
  @apply ticket-card border-l-4 border-l-blue-500;
}
```

---

## 📱 **RESPONSIVE DESIGN**

### **Mobile Breakpoints**
```css
/* Mobile First - Base styles for mobile */

/* Tablet (md: 768px) */
@media (min-width: 768px) {
  .stats-grid {
    @apply grid-cols-2;
  }
  
  .filter-grid {
    @apply grid-cols-3;
  }
}

/* Desktop (lg: 1024px) */
@media (min-width: 1024px) {
  .stats-grid {
    @apply grid-cols-4;
  }
  
  .filter-grid {
    @apply grid-cols-5;
  }
}
```

---

## ✅ **DESIGN CHECKLIST**

- ✅ Font: Poppins (all sizes: 9px - 14px)
- ✅ Input height: 32px (h-8)
- ✅ Dropdown height: 32px (h-8)
- ✅ Border radius: 2px (rounded-sm)
- ✅ Consistent spacing (p-6, p-4, gap-3)
- ✅ Color scheme (blue primary, semantic colors)
- ✅ Button heights: 32px (h-8) for primary actions
- ✅ Badge heights: 20px (h-5)
- ✅ Clean & professional layout
- ✅ Proper contrast ratios (WCAG AA)
- ✅ Hover states for interactivity
- ✅ Focus states for accessibility

---

**Last Updated:** 2025-10-03  
**Status:** 🟡 Awaiting Approval  
**Next Step:** Implementation after design approval

