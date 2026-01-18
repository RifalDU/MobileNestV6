# 📁 Struktur Folder ACTUAL MobileNest V6

## Klarifikasi Struktur yang Benar

Berdasarkan klarifikasi dari Anda, struktur folder yang **SEBENARNYA** adalah:

```
MobileNest/
│
├── admin/
│   ├── uploads/
│   │   └── produk/
│   │       ├── .gitkeep
│   │       └── (gambar produk)
│   │
│   ├── kelola-produk.php
│   └── ...
│
├── uploads/                    ← ROOT LEVEL
│   └── pembayaran/            ← BUKTI PEMBAYARAN DI SINI
│       ├── .gitkeep
│       └── (bukti pembayaran)
│
├── produk/
│   ├── list-produk.php
│   └── detail-produk.php
│
└── ...
```

---

## 📌 Upload Handler Constants

```php
class UploadHandler {
    // PRODUK
    const ADMIN_UPLOAD_DIR_PRODUK = 'admin/uploads/produk/';  // ← DIGUNAKAN
    
    // PEMBAYARAN
    const UPLOAD_DIR_PEMBAYARAN = 'uploads/pembayaran/';      // ← DIGUNAKAN
}
```

---

## 🎯 Upload Behavior

### Produk Gambar
```
Upload → admin/kelola-produk.php
    ↓
Save → admin/uploads/produk/produk_15_xxx.jpg
    ↓
DB → produk.gambar = 'produk_15_xxx.jpg'
```

### Bukti Pembayaran
```
Upload → admin/verifikasi-pembayaran.php
    ↓
Save → uploads/pembayaran/pembayaran_5_xxx.jpg
    ↓
DB → transaksi.bukti_pembayaran = 'pembayaran_5_xxx.jpg'
```

---

## 📐 Path Resolution

### Dari produk/list-produk.php
```
Current: /MobileNest/produk/list-produk.php
Target: /MobileNest/admin/uploads/produk/produk_15_xxx.jpg
Path: ../admin/uploads/produk/produk_15_xxx.jpg ✅
```

### Dari admin/verifikasi-pembayaran.php
```
Current: /MobileNest/admin/verifikasi-pembayaran.php
Target: /MobileNest/uploads/pembayaran/pembayaran_5_xxx.jpg
Path: ../uploads/pembayaran/pembayaran_5_xxx.jpg ✅
```

---

## .gitignore Configuration

```gitignore
# Produk images
/admin/uploads/produk/*
!/admin/uploads/produk/.gitkeep

# Pembayaran bukti
/uploads/pembayaran/*
!/uploads/pembayaran/.gitkeep
```

---

**Key Difference:**
- Produk: di `admin/uploads/produk/` (admin asset)
- Pembayaran: di `uploads/pembayaran/` (terpisah, user-generated)