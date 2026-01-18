<?php
/**
 * Contoh Penggunaan Fitur Gambar Produk
 * File ini menunjukkan best practices untuk menggunakan gambar produk
 * 
 * Location: MobileNest/examples/image-usage-example.php
 * Date: 2026-01-14
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/upload-handler.php';
require_once __DIR__ . '/../includes/upload-handler-enhanced.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contoh Penggunaan Gambar Produk - MobileNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .example-section { margin-bottom: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        .code-block { background: #2d3436; color: #00ff00; padding: 15px; border-radius: 5px; overflow-x: auto; font-family: monospace; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .product-image { width: 100%; height: 250px; object-fit: cover; background: #f0f0f0; display: flex; align-items: center; justify-content: center; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-info { padding: 15px; }
        .thumbnail-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .thumbnail { width: 100%; height: 100px; object-fit: cover; border-radius: 5px; cursor: pointer; transition: transform 0.3s; }
        .thumbnail:hover { transform: scale(1.05); }
    </style>
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4"><i class="bi bi-image"></i> Contoh Penggunaan Gambar Produk MobileNest</h1>

    <!-- SECTION 1: Upload Gambar -->
    <div class="example-section">
        <h2>1. Upload Gambar (Admin Panel)</h2>
        <p>Cara upload gambar produk di admin panel dengan berbagai metode.</p>
        
        <h4>A. HTML Form untuk Upload</h4>
        <div class="code-block" style="font-size: 12px;">&lt;?php
// File: admin/kelola-produk.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gambar'])) {
    // Method 1: Gunakan original handler
    $upload_result = UploadHandler::uploadProductImage($_FILES['gambar'], $product_id);
    
    // Method 2: Gunakan enhanced handler (dengan optimization)
    $upload_result = UploadHandlerEnhanced::uploadProductImage($_FILES['gambar'], $product_id);
    
    if ($upload_result['success']) {
        $gambar = $upload_result['filename'];
        $gambar_thumbnail = isset($upload_result['thumbnail']) ? $upload_result['thumbnail'] : null;
        
        // Simpan ke database
        $query = "UPDATE produk SET gambar='" . mysqli_real_escape_string($conn, $gambar) . "'
                  WHERE id_produk = $product_id";
        mysqli_query($conn, $query);
    }
}
?&gt;</div>
        
        <h4 class="mt-3">B. Supported Upload Methods</h4>
        <ul>
            <li><strong>Drag & Drop:</strong> Tarik file ke area upload</li>
            <li><strong>Click to Browse:</strong> Klik untuk membuka file picker</li>
            <li><strong>URL Input:</strong> Paste URL gambar dari sumber lain</li>
        </ul>
    </div>

    <!-- SECTION 2: Menampilkan Gambar -->
    <div class="example-section">
        <h2>2. Menampilkan Gambar Produk</h2>
        
        <h4>A. Detail Produk (Gambar Besar)</h4>
        <div class="code-block" style="font-size: 12px;">&lt;?php
// File: produk/detail-produk.php
$id_produk = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = $id_produk");
$produk = mysqli_fetch_assoc($result);
?&gt;

&lt;div class="product-detail"&gt;
  &lt;?php if (!empty($produk['gambar'])): ?&gt;
    &lt;img src="uploads/produk/&lt;?= htmlspecialchars($produk['gambar']) ?&gt;" 
         alt="&lt;?= htmlspecialchars($produk['nama_produk']) ?&gt;" 
         class="img-fluid" style="max-width:500px;border-radius:10px;"&gt;
  &lt;?php else: ?&gt;
    &lt;div class="placeholder" style="width:500px;height:500px;background:#f0f0f0;border-radius:10px;display:flex;align-items:center;justify-content:center;"&gt;
      &lt;i class="bi bi-image" style="font-size:48px;color:#ccc;"&gt;&lt;/i&gt;
    &lt;/div&gt;
  &lt;?php endif; ?&gt;
&lt;/div&gt;</div>
        
        <h4 class="mt-3">B. List Produk (Thumbnail Grid)</h4>
        <div class="code-block" style="font-size: 12px;">&lt;?php
// File: produk/list-produk.php
$result = mysqli_query($conn, "SELECT id_produk, nama_produk, harga, gambar FROM produk LIMIT 12");
?&gt;

&lt;div class="product-grid"&gt;
  &lt;?php while ($row = mysqli_fetch_assoc($result)): ?&gt;
    &lt;div class="product-card"&gt;
      &lt;div class="product-image"&gt;
        &lt;?php if (!empty($row['gambar'])): ?&gt;
          &lt;img src="uploads/produk/&lt;?= htmlspecialchars($row['gambar']) ?&gt;" 
               alt="&lt;?= htmlspecialchars($row['nama_produk']) ?&gt;"&gt;
        &lt;?php else: ?&gt;
          &lt;i class="bi bi-image" style="font-size:64px;color:#ccc;"&gt;&lt;/i&gt;
        &lt;?php endif; ?&gt;
      &lt;/div&gt;
      &lt;div class="product-info"&gt;
        &lt;h5&gt;&lt;?= htmlspecialchars($row['nama_produk']) ?&gt;&lt;/h5&gt;
        &lt;p class="price"&gt;Rp &lt;?= number_format((float)$row['harga'], 0, ',', '.') ?&gt;&lt;/p&gt;
        &lt;a href="detail-produk.php?id=&lt;?= (int)$row['id_produk'] ?&gt;" class="btn btn-primary btn-sm"&gt;Lihat Detail&lt;/a&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;?php endwhile; ?&gt;
&lt;/div&gt;</div>
    </div>

    <!-- SECTION 3: Image Optimization -->
    <div class="example-section">
        <h2>3. Image Optimization (Enhanced Handler)</h2>
        
        <h4>A. Auto-Resize</h4>
        <ul>
            <li>Max width: 1200px</li>
            <li>Max height: 1200px</li>
            <li>Maintain aspect ratio</li>
            <li>Quality: 85% untuk JPEG</li>
        </ul>
        
        <h4 class="mt-3">B. Thumbnail Generation</h4>
        <ul>
            <li>Size: 300x300px</li>
            <li>Square crop dari center</li>
            <li>Stored di: uploads/produk/thumbs/</li>
            <li>Gunakan di list view untuk faster loading</li>
        </ul>
        
        <h4 class="mt-3">C. Supported Formats</h4>
        <ul>
            <li>JPEG (.jpg, .jpeg)</li>
            <li>PNG (.png) - dengan transparency preservation</li>
            <li>WebP (.webp)</li>
        </ul>
    </div>

    <!-- SECTION 4: Database Usage -->
    <div class="example-section">
        <h2>4. Database Structure</h2>
        
        <h4>Kolom Produk untuk Gambar</h4>
        <div class="code-block" style="font-size: 12px;">-- Basic fields (existing)
CREATE TABLE produk (
    id_produk INT PRIMARY KEY AUTO_INCREMENT,
    nama_produk VARCHAR(100) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    gambar VARCHAR(255) DEFAULT NULL,  -- Nama file gambar utama
    
    -- Optional: Enhanced fields (dari migration)
    gambar_thumbnail VARCHAR(255) DEFAULT NULL,
    image_width INT COMMENT 'Lebar gambar setelah optimasi',
    image_height INT COMMENT 'Tinggi gambar setelah optimasi',
    image_size_kb INT COMMENT 'Ukuran file dalam KB',
    image_uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
</div>
    </div>

    <!-- SECTION 5: Best Practices -->
    <div class="example-section">
        <h2>5. Best Practices</h2>
        
        <div class="row">
            <div class="col-md-6">
                <h5>✅ DO's</h5>
                <ul>
                    <li>Gunakan thumbnail di list view</li>
                    <li>Validate file type sebelum upload</li>
                    <li>Compress image untuk performa</li>
                    <li>Set proper file permissions (644)</li>
                    <li>Use CDN untuk image delivery</li>
                    <li>Implement lazy loading</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>❌ DON'Ts</h5>
                <ul>
                    <li>Jangan display gambar full size di list</li>
                    <li>Jangan skip MIME type validation</li>
                    <li>Jangan store gambar di webroot langsung</li>
                    <li>Jangan use sequential filenames</li>
                    <li>Jangan delete original sebelum backup</li>
                    <li>Jangan allow unlimited upload size</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SECTION 6: Troubleshooting -->
    <div class="example-section">
        <h2>6. Troubleshooting</h2>
        
        <h4>Problem: Image tidak muncul</h4>
        <div class="code-block" style="font-size: 11px;">// Cek di browser console (F12)
// Verify path di database:
SELECT id_produk, nama_produk, gambar FROM produk;

// Verify file exists di server:
ls -la uploads/produk/

// Cek file permissions:
chmod 644 uploads/produk/*
</div>
        
        <h4 class="mt-3">Problem: Upload gagal</h4>
        <div class="code-block" style="font-size: 11px;">// Cek folder permissions:
chmod 755 uploads/produk/
chmod 755 uploads/produk/thumbs/

// Cek disk space:
df -h

// Cek upload_max_filesize di php.ini:
php -i | grep upload_max_filesize
</div>
        
        <h4 class="mt-3">Problem: Thumbnail tidak generate</h4>
        <div class="code-block" style="font-size: 11px;">// Cek GD library:
php -i | grep GD

// Di PHP:
if (extension_loaded('gd')) {
    echo 'GD library enabled';
}
</div>
    </div>

    <!-- SECTION 7: API References -->
    <div class="example-section">
        <h2>7. API References</h2>
        
        <h4>UploadHandler Class (Original)</h4>
        <div class="code-block" style="font-size: 11px;">// Upload produk
$result = UploadHandler::uploadProductImage($file, $product_id);
// Return: ['success', 'filename', 'filepath', 'message']

// Delete file
UploadHandler::deleteFile($filename, 'produk');

// Get URL
$url = UploadHandler::getFileUrl($filename, 'produk');
</div>
        
        <h4 class="mt-3">UploadHandlerEnhanced Class (With Optimization)</h4>
        <div class="code-block" style="font-size: 11px;">// Upload dengan optimization & thumbnail
$result = UploadHandlerEnhanced::uploadProductImage($file, $product_id);
// Return: ['success', 'filename', 'thumbnail', 'filepath', 'message']

// Get thumbnail URL
$thumb_url = UploadHandlerEnhanced::getFileUrl($thumb_filename, 'produk_thumb');

// Delete file (juga delete thumbnail)
UploadHandlerEnhanced::deleteFile($filename, 'produk');
</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
