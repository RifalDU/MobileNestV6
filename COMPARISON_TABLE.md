# 📊 Comparison: Gambar Produk vs Bukti Pembayaran

## Side-by-Side Comparison

| Aspek | **Gambar Produk** | **Bukti Pembayaran** |
|-------|------------------|----------------------|
| **Lokasi Folder** | `admin/uploads/produk/` | `uploads/pembayaran/` |
| **Folder Level** | Di dalam admin | Root level (terpisah) |
| **Database Table** | `produk` | `transaksi` |
| **Database Field** | `gambar` | `bukti_pembayaran` |
| **Upload By** | Admin | User / Admin |
| **Naming** | `produk_ID_TIMESTAMP_RANDOM.ext` | `pembayaran_ID_TIMESTAMP_RANDOM.ext` |
| **Max File Size** | 5MB | 5MB |
| **File Types** | JPG, PNG, WebP | JPG, PNG, WebP, PDF |

---

## Path Resolution Reference

### From Admin Panel
| Item | Path | Example |
|------|------|----------|
| Produk gambar | Sibling: `uploads/produk/` | `uploads/produk/produk_15_xxx.jpg` |
| Pembayaran | Up 1: `../uploads/pembayaran/` | `../uploads/pembayaran/pembayaran_5_xxx.jpg` |

### From Produk Folder
| Item | Path | Example |
|------|------|----------|
| Produk gambar | Up 1+admin: `../admin/uploads/produk/` | `../admin/uploads/produk/produk_15_xxx.jpg` |
| Pembayaran | Up 1: `../uploads/pembayaran/` | `../uploads/pembayaran/pembayaran_5_xxx.jpg` |

---

## Database Structure

```sql
CREATE TABLE produk (
    id_produk INT PRIMARY KEY,
    nama_produk VARCHAR(100),
    gambar VARCHAR(255),  -- File name only
    ...
);

CREATE TABLE transaksi (
    id_transaksi INT PRIMARY KEY,
    bukti_pembayaran VARCHAR(255),  -- File name only
    ...
);
```

---

## Upload Behavior

### Product Image
```php
$result = UploadHandler::uploadProductImage($_FILES['gambar'], $product_id);
// Saves to: admin/uploads/produk/produk_{product_id}_{timestamp}_{random}.jpg
```

### Payment Proof
```php
$result = UploadHandler::uploadPaymentProof($_FILES['bukti_pembayaran'], $transaction_id);
// Saves to: uploads/pembayaran/pembayaran_{transaction_id}_{timestamp}_{random}.jpg
```

---

## Display Code

### Product Image (produk/list-produk.php)
```php
$image_url = UploadHandler::getFileUrl($row['gambar'], 'produk');
// Returns: ../admin/uploads/produk/...
echo "<img src='{$image_url}' alt='...'>";
```

### Payment Proof (admin/verifikasi-pembayaran.php)
```php
$proof_url = UploadHandler::getFileUrl($row['bukti_pembayaran'], 'pembayaran');
// Returns: ../uploads/pembayaran/...
echo "<img src='{$proof_url}' alt='...'>";
```

---

## Key Difference

**Two different structures for two different purposes:**

```
🖼️  Product Images  → admin/uploads/produk/
                      (Admin-controlled, public display)

🧾  Payment Proofs  → uploads/pembayaran/
                      (User-generated, verification only)
```