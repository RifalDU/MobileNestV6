# 💻 Copy-Paste Commands: Implementasi Gambar Produk

> Semua command siap copy-paste untuk setup cepat

---

## 🪟 WINDOWS (Command Prompt / PowerShell)

### One-liner (simplest)

```batch
mkdir admin\uploads\produk & mkdir uploads\pembayaran & type nul > admin\uploads\produk\.gitkeep & type nul > uploads\pembayaran\.gitkeep
```

### Step-by-step

```batch
cd C:\xampp\htdocs\MobileNest
mkdir admin\uploads\produk
mkdir uploads\pembayaran
type nul > admin\uploads\produk\.gitkeep
type nul > uploads\pembayaran\.gitkeep
echo Setup complete!
```

---

## 🐧 LINUX / MAC (Terminal / Bash)

### One-liner (simplest)

```bash
mkdir -p admin/uploads/produk uploads/pembayaran && touch admin/uploads/produk/.gitkeep uploads/pembayaran/.gitkeep && chmod 755 admin/uploads/produk uploads/pembayaran && echo "Setup complete!"
```

### Step-by-step

```bash
cd ~/public_html/MobileNest
mkdir -p admin/uploads/produk uploads/pembayaran
chmod 755 admin/uploads/produk
chmod 755 uploads/pembayaran
touch admin/uploads/produk/.gitkeep
touch uploads/pembayaran/.gitkeep
echo "Setup complete!"
```

---

## 📝 Update .gitignore

Append ke `MobileNest/.gitignore`:

```gitignore
# Ignore uploaded files
/admin/uploads/produk/*
!/admin/uploads/produk/.gitkeep

/uploads/pembayaran/*
!/uploads/pembayaran/.gitkeep
```

---

## 🧪 Verification Commands

### Windows
```batch
dir admin\uploads\produk
dir uploads\pembayaran
```

### Linux/Mac
```bash
ls -la admin/uploads/produk/
ls -la uploads/pembayaran/
```

---

## 🗄️ Database Check

```sql
-- Check product images
SELECT id_produk, nama_produk, gambar FROM produk WHERE gambar IS NOT NULL LIMIT 5;

-- Check payment proofs  
SELECT id_transaksi, bukti_pembayaran FROM transaksi WHERE bukti_pembayaran IS NOT NULL LIMIT 5;
```

---

**Choose your OS and copy-paste command to terminal/command prompt!**