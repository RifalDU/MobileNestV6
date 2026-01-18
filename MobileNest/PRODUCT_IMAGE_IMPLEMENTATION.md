# Panduan Implementasi Fitur Gambar Produk MobileNest

## 📋 Daftar Isi
1. [Status Saat Ini](#status-saat-ini)
2. [Arsitektur Gambar Produk](#arsitektur-gambar-produk)
3. [File yang Terlibat](#file-yang-terlibat)
4. [Fitur Lengkap](#fitur-lengkap)
5. [Cara Menggunakan](#cara-menggunakan)
6. [Troubleshooting](#troubleshooting)

---

## Status Saat Ini

✅ **Sudah Implementasi:**
- Upload gambar produk di admin panel (admin/kelola-produk.php)
- Penyimpanan gambar di folder `uploads/produk/`
- Validasi file (MIME type, ukuran, extension)
- Drag & drop upload
- Tab URL alternatif untuk input gambar dari URL
- Tampilan thumbnail di admin list
- Tampilan gambar di detail produk (user side)

✨ **Enhancement Baru:**
- `upload-handler-enhanced.php` - Upload handler dengan fitur:
  - Auto-resize gambar ke ukuran optimal (max 1200x1200px)
  - Kualitas JPEG terjaga di 85%
  - Automatic thumbnail generation (300x300px)
  - Support untuk PNG, JPEG, dan WebP
  - Preservasi transparency untuk PNG

---

## Arsitektur Gambar Produk

### Struktur Folder
```
MobileNest/
├── uploads/
│   ├── produk/
│   │   ├── produk_1_1705219200_abc123.jpg      (Gambar utama)
│   │   ├── produk_1_1705219200_abc123.png      (Format lain)
│   │   └── thumbs/
│   │       └── produk_1_thumb_1705219200_abc123.jpg (Thumbnail)
│   └── pembayaran/
│       └── pembayaran_1_1705219200_abc123.jpg
├── includes/
│   ├── upload-handler.php          (Handler original)
│   └── upload-handler-enhanced.php (Handler dengan optimasi)
├── admin/
│   └── kelola-produk.php           (Admin management)
└── produk/
    ├── list-produk.php             (Daftar produk user)
    ├── detail-produk.php           (Detail produk user)
    └── cari-produk.php             (Pencarian produk)
```

### Database
Field `gambar` di tabel `produk`:
```sql
ALTER TABLE produk ADD COLUMN gambar VARCHAR(255);
ALTER TABLE produk ADD COLUMN gambar_thumbnail VARCHAR(255);
```

---

## File yang Terlibat

### 1. **upload-handler.php** (Existing)
- Location: `/MobileNest/includes/upload-handler.php`
- Fungsi:
  - Upload file produk dan pembayaran
  - Validasi MIME type dan extension
  - Delete file
  - Get file URL

### 2. **upload-handler-enhanced.php** (New)
- Location: `/MobileNest/includes/upload-handler-enhanced.php`
- Fitur Tambahan:
  - Auto-resize gambar
  - Generate thumbnail
  - Image optimization
  - Support multiple image formats

### 3. **kelola-produk.php** (Admin)
- Location: `/MobileNest/admin/kelola-produk.php`
- Fungsi:
  - Add/Edit/Delete produk
  - Upload gambar
  - Preview gambar
  - Tampilkan thumbnail

### 4. **detail-produk.php** (User Side)
- Location: `/MobileNest/produk/detail-produk.php`
- Menampilkan:
  - Gambar produk besar (untuk detail)
  - Thumbnail atau placeholder
  - Image gallery (jika ada multiple images)

### 5. **list-produk.php** (User Side)
- Location: `/MobileNest/produk/list-produk.php`
- Menampilkan:
  - Thumbnail produk di grid
  - Fallback placeholder jika tidak ada gambar

---

## Fitur Lengkap

### A. Upload & Storage

**Upload Method:**
1. **Drag & Drop** - Tarik file langsung ke area upload
2. **Click to Browse** - Klik untuk membuka file picker
3. **URL Input** - Paste URL gambar dari sumber lain

**Format Suportasi:**
- JPEG (*.jpg, *.jpeg) ✓
- PNG (*.png) ✓
- WebP (*.webp) ✓

**Validasi:**
- Max ukuran: 5MB
- MIME type checking
- Extension validation
- Nama file aman (prevent injection)

**Output:**
- Gambar utama: `uploads/produk/produk_ID_timestamp_random.ext`
- Thumbnail: `uploads/produk/thumbs/produk_ID_thumb_timestamp_random.ext`

### B. Image Optimization

**Resize Produk:**
- Max width: 1200px
- Max height: 1200px
- Maintain aspect ratio
- Quality: 85% JPEG

**Thumbnail:**
- Size: 300x300px
- Square crop dari center
- Gunakan untuk list & admin

### C. Display & Rendering

**Di Admin Panel:**
```html
<!-- Table preview (60x60px) -->
<img src="uploads/produk/produk_1_1705219200_abc123.jpg" 
     style="max-width:60px;max-height:60px;object-fit:cover;border-radius:5px;">

<!-- Modal preview (300x300px) -->
<img src="uploads/produk/produk_1_1705219200_abc123.jpg" 
     class="image-preview" alt="Preview">
```

**Di User Side (Detail):**
```html
<!-- Full image -->
<?php 
  $image_url = 'uploads/produk/' . $produk['gambar'];
  if ($produk['gambar'] && file_exists($image_url)) {
?>
  <img src="<?= htmlspecialchars($image_url) ?>" 
       alt="<?= htmlspecialchars($produk['nama_produk']) ?>" 
       class="img-fluid">
<?php } else { ?>
  <div class="placeholder-image">No Image</div>
<?php } ?>
```

**Di User Side (List):**
```html
<!-- Thumbnail -->
<?php 
  $thumb_url = 'uploads/produk/thumbs/' . $produk['gambar_thumbnail'];
  $fallback = 'assets/placeholder.jpg';
  $image = (file_exists($thumb_url)) ? $thumb_url : $fallback;
?>
<img src="<?= htmlspecialchars($image) ?>" 
     alt="<?= htmlspecialchars($produk['nama_produk']) ?>" 
     class="product-thumbnail" style="width:300px;height:300px;object-fit:cover;">
```

---

## Cara Menggunakan

### 1. Setup Database (Jika diperlukan)

```sql
-- Tambah kolom untuk thumbnail (optional)
ALTER TABLE produk ADD COLUMN gambar_thumbnail VARCHAR(255) DEFAULT NULL;

-- Jika ingin track upload info
ALTER TABLE produk ADD COLUMN upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

### 2. Setup Folder Permissions

```bash
# Create directories
mkdir -p uploads/produk/thumbs
mkdir -p uploads/pembayaran

# Set permissions (Linux/Mac)
chmod 755 uploads
chmod 755 uploads/produk
chmod 755 uploads/produk/thumbs
chmod 755 uploads/pembayaran
```

### 3. Update Admin Panel (kelola-produk.php)

**Current Implementation** menggunakan `UploadHandler` class:

```php
require_once __DIR__ . '/../includes/upload-handler.php';

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $upload_result = UploadHandler::uploadProductImage($_FILES['gambar'], $temp_id);
    if ($upload_result['success']) {
        $gambar = $upload_result['filename'];
    }
}
```

**Untuk upgrade ke Enhanced Version:**

```php
require_once __DIR__ . '/../includes/upload-handler-enhanced.php';

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $upload_result = UploadHandlerEnhanced::uploadProductImage($_FILES['gambar'], $id_produk);
    if ($upload_result['success']) {
        $gambar = $upload_result['filename'];
        $gambar_thumbnail = isset($upload_result['thumbnail']) ? $upload_result['thumbnail'] : null;
        // Simpan kedua ke database
    }
}
```

### 4. Update Detail Produk User Side

**File:** `produk/detail-produk.php`

Tambahkan kode untuk menampilkan gambar:

```php
<?php
  // Fetch produk dari database
  $id_produk = (int)$_GET['id'] ?? 0;
  $result = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = $id_produk");
  $produk = mysqli_fetch_assoc($result);
  
  if (!$produk) {
      header('Location: list-produk.php');
      exit;
  }
?>

<div class="product-image-container">
    <?php if (!empty($produk['gambar'])): ?>
        <img src="<?= htmlspecialchars('uploads/produk/' . $produk['gambar']) ?>" 
             alt="<?= htmlspecialchars($produk['nama_produk']) ?>" 
             class="img-fluid product-detail-image">
    <?php else: ?>
        <div class="placeholder-image" style="width:100%;height:400px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
            <span class="text-muted">Tidak ada gambar produk</span>
        </div>
    <?php endif; ?>
</div>

<div class="product-info">
    <h2><?= htmlspecialchars($produk['nama_produk']) ?></h2>
    <p class="price">Rp <?= number_format((float)$produk['harga'], 0, ',', '.') ?></p>
    <p><?= htmlspecialchars($produk['deskripsi']) ?></p>
    <!-- ... rest of product details -->
</div>
```

### 5. Update List Produk

**File:** `produk/list-produk.php`

```php
<?php
  $result = mysqli_query($conn, "SELECT id_produk, nama_produk, harga, gambar FROM produk WHERE status_produk = 'Tersedia'");
  
  while ($row = mysqli_fetch_assoc($result)) {
?>
    <div class="product-card">
        <div class="product-image">
            <?php if (!empty($row['gambar'])): ?>
                <img src="<?= htmlspecialchars('uploads/produk/' . $row['gambar']) ?>" 
                     alt="<?= htmlspecialchars($row['nama_produk']) ?>" 
                     class="product-thumbnail"
                     style="width:100%;height:250px;object-fit:cover;border-radius:8px;">
            <?php else: ?>
                <div class="placeholder" style="width:100%;height:250px;background:#f0f0f0;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-image" style="font-size:48px;color:#ccc;"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="product-info">
            <h5><?= htmlspecialchars($row['nama_produk']) ?></h5>
            <p class="price">Rp <?= number_format((float)$row['harga'], 0, ',', '.') ?></p>
            <a href="detail-produk.php?id=<?= (int)$row['id_produk'] ?>" class="btn btn-primary btn-sm">Lihat Detail</a>
        </div>
    </div>
<?php 
  }
?>
```

---

## Implementasi Step-by-Step

### Step 1: Prepare Folders
```bash
mkdir -p uploads/produk/thumbs
chmod 755 uploads/produk
chmod 755 uploads/produk/thumbs
```

### Step 2: Copy Enhanced Handler (Optional)
Copy `upload-handler-enhanced.php` ke `includes/` folder untuk mendapat fitur image optimization.

### Step 3: Update Admin Panel
Modify `admin/kelola-produk.php` untuk:
- Gunakan enhanced handler (optional)
- Simpan thumbnail filename ke database (optional)
- Show image preview lebih besar

### Step 4: Update User Pages
Modify:
- `produk/detail-produk.php` - Tampilkan gambar besar
- `produk/list-produk.php` - Tampilkan thumbnail

### Step 5: Testing
1. Upload gambar dari admin panel
2. Verify di `uploads/produk/` folder
3. Check thumbnail di `uploads/produk/thumbs/`
4. Verify tampilan di user side

---

## Security Considerations

✅ **Implementasi:**
- MIME type validation (tidak hanya extension)
- File size limit (max 5MB)
- Random filename generation (prevent enumeration)
- Proper permissions (644)
- Directory traversal prevention

⚠️ **Best Practices:**
- Store files outside webroot jika possible
- Use CDN untuk image delivery
- Implement image optimization di server
- Regular backup gambar

---

## Performance Tips

1. **Gunakan Thumbnail di List**
   - Load thumbnail (300x300) di list view
   - Load full image hanya di detail page

2. **Image Optimization**
   - Auto-resize gambar besar ke max 1200x1200
   - Gunakan format WebP jika browser support
   - Compress JPEG dengan quality 85%

3. **Caching**
   - Browser cache: Set cache headers
   - Server cache: Cache image URL
   - CDN: Deploy gambar ke CDN untuk speed

4. **Lazy Loading**
```html
<!-- Modern browsers support native lazy loading -->
<img src="uploads/produk/..." alt="..." loading="lazy">
```

---

## Troubleshooting

### Upload Gagal
```
Gagal mengupload file / Error uploading file
```
**Solutions:**
1. Check folder permissions: `chmod 755 uploads/produk/`
2. Check max file size: `php.ini` settings
3. Check disk space
4. Check file type is valid image

### Gambar Tidak Muncul
```
Image broken / 404 not found
```
**Solutions:**
1. Check file path di database
2. Verify file exists di server
3. Check file permissions (chmod 644)
4. Check HTML path construction

### Thumbnail Tidak Generate
```
Thumbnail tidak ada di database
```
**Solutions:**
1. Check GD library enabled: `phpinfo()` or `gd` module
2. Check `thumbs/` folder writable
3. Check image format suportasi GD library

---

## Fitur Masa Depan

🔄 **Planned Features:**
- [ ] Multiple image upload per produk
- [ ] Image gallery dengan lightbox
- [ ] Drag & drop untuk reorder gambar
- [ ] Image cropping tool di admin
- [ ] WebP format automatic conversion
- [ ] CDN integration
- [ ] Image optimization queue (background job)

---

## Kontak & Support

Untuk pertanyaan atau issue:
- Check GitHub Issues
- Review code comments
- Test dengan sample images

---

**Last Updated:** January 14, 2026
**Version:** 2.0 (With Enhancement)
