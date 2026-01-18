# ⚡ QUICK START (CORRECTED): Gambar Produk MobileNest

---

## 📁 Struktur Folder yang BENAR

```
MobileNest/
├── admin/
│   └── uploads/
│       └── produk/              ← GAMBAR PRODUK DI SINI
│
├── uploads/
│   └── pembayaran/              ← BUKTI PEMBAYARAN DI SINI (terpisah)
│
├── produk/
│   ├── list-produk.php
│   └── detail-produk.php
└── ...
```

---

## 🎯 Implementation

### Step 1: Create Folder Structure
```bash
# Windows Command Prompt
cd C:\xampp\htdocs\MobileNest
mkdir admin\uploads\produk
mkdir uploads\pembayaran

# Linux/Mac Terminal
cd ~/public_html/MobileNest
mkdir -p admin/uploads/produk
mkdir -p uploads/pembayaran
chmod 755 admin/uploads/produk
chmod 755 uploads/pembayaran
```

### Step 2: Create .gitkeep Files
```bash
# Windows
type nul > admin\uploads\produk\.gitkeep
type nul > uploads\pembayaran\.gitkeep

# Linux/Mac
touch admin/uploads/produk/.gitkeep
touch uploads/pembayaran/.gitkeep
```

### Step 3: Update .gitignore
File: `MobileNest/.gitignore`

Add:
```gitignore
/admin/uploads/produk/*
!/admin/uploads/produk/.gitkeep

/uploads/pembayaran/*
!/uploads/pembayaran/.gitkeep
```

### Step 4: Test Upload
1. Open: `http://localhost/MobileNest/admin/kelola-produk.php`
2. Login dengan admin
3. Click "Tambah Produk"
4. Upload image
5. Click "Simpan"
6. **Verify:** File di `admin/uploads/produk/`

### Step 5: Test Display
1. Open: `http://localhost/MobileNest/produk/list-produk.php`
2. **Verify:** Gambar tampil (200x200px)
3. Click "Lihat Detail"
4. **Verify:** Gambar tampil di detail

---

## ✅ Verification Checklist

- [ ] Folder `admin/uploads/produk/` created
- [ ] Folder `uploads/pembayaran/` created
- [ ] `.gitkeep` files ada
- [ ] `.gitignore` updated
- [ ] Can upload image
- [ ] File saved di folder
- [ ] Image displays in list
- [ ] Image displays in detail
- [ ] Database updated

---

## 🔧 Troubleshooting

| Problem | Solution |
|---------|----------|
| Upload gagal | Check folder exists & permissions |
| Gambar tidak tampil | Check path in HTML src |
| 404 error | Verify file tersimpan |
| Permission denied | Run `chmod 755` (Linux/Mac) |

---

**Done!** ✅