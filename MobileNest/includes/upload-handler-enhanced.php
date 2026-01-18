<?php
/**
 * Enhanced Upload Handler for MobileNest
 * Secure file upload handler with image optimization (resize, thumbnail)
 * for products and payment proofs
 */

class UploadHandlerEnhanced {
    
    // Configuration
    const UPLOAD_DIR_PRODUK = 'uploads/produk/';
    const UPLOAD_DIR_PRODUK_THUMB = 'uploads/produk/thumbs/';
    const UPLOAD_DIR_PEMBAYARAN = 'uploads/pembayaran/';
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    
    // Image optimization
    const PRODUCT_MAX_WIDTH = 1200;
    const PRODUCT_MAX_HEIGHT = 1200;
    const THUMB_WIDTH = 300;
    const THUMB_HEIGHT = 300;
    const JPEG_QUALITY = 85;
    
    // Allowed MIME types
    const ALLOWED_PRODUK_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    const ALLOWED_PEMBAYARAN_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
    
    // Allowed extensions
    const ALLOWED_PRODUK_EXT = ['jpg', 'jpeg', 'png', 'webp'];
    const ALLOWED_PEMBAYARAN_EXT = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
    
    /**
     * Upload file produk dengan optimasi
     * 
     * @param array $file $_FILES array
     * @param int $product_id Product ID
     * @return array ['success' => bool, 'filename' => string, 'thumbnail' => string, 'message' => string]
     */
    public static function uploadProductImage($file, $product_id) {
        $result = self::uploadFile(
            $file,
            self::UPLOAD_DIR_PRODUK,
            self::ALLOWED_PRODUK_TYPES,
            self::ALLOWED_PRODUK_EXT,
            'produk_' . $product_id
        );
        
        // Jika upload berhasil, lakukan optimasi image
        if ($result['success']) {
            // Resize image ke ukuran optimal
            self::optimizeProductImage(
                $result['filepath'],
                self::PRODUCT_MAX_WIDTH,
                self::PRODUCT_MAX_HEIGHT
            );
            
            // Generate thumbnail
            $thumb_result = self::generateThumbnail(
                $result['filepath'],
                self::UPLOAD_DIR_PRODUK_THUMB,
                self::THUMB_WIDTH,
                self::THUMB_HEIGHT,
                'produk_' . $product_id
            );
            
            if ($thumb_result['success']) {
                $result['thumbnail'] = $thumb_result['filename'];
            }
        }
        
        return $result;
    }
    
    /**
     * Upload file pembayaran
     * 
     * @param array $file $_FILES array
     * @param int $transaction_id Transaction ID
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    public static function uploadPaymentProof($file, $transaction_id) {
        return self::uploadFile(
            $file,
            self::UPLOAD_DIR_PEMBAYARAN,
            self::ALLOWED_PEMBAYARAN_TYPES,
            self::ALLOWED_PEMBAYARAN_EXT,
            'pembayaran_' . $transaction_id
        );
    }
    
    /**
     * Generic file upload handler
     * 
     * @param array $file $_FILES array
     * @param string $upload_dir Directory to upload to
     * @param array $allowed_mimes Allowed MIME types
     * @param array $allowed_ext Allowed extensions
     * @param string $prefix File prefix
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    private static function uploadFile($file, $upload_dir, $allowed_mimes, $allowed_ext, $prefix) {
        // Validate input
        if (empty($file) || !isset($file['tmp_name'])) {
            return ['success' => false, 'message' => 'File tidak ditemukan'];
        }
        
        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar (maksimal 5MB)'];
        }
        
        // Check file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            return ['success' => false, 'message' => 'Tipe file tidak diperbolehkan. Format: ' . implode(', ', $allowed_ext)];
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed_mimes)) {
            return ['success' => false, 'message' => 'MIME type file tidak valid'];
        }
        
        // Create upload directory if not exists
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                return ['success' => false, 'message' => 'Gagal membuat direktori upload'];
            }
        }
        
        // Generate unique filename
        $timestamp = time();
        $random = bin2hex(random_bytes(4));
        $filename = $prefix . '_' . $timestamp . '_' . $random . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        // Move file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'message' => 'Gagal mengupload file'];
        }
        
        // Set proper permissions
        chmod($filepath, 0644);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'File berhasil diupload'
        ];
    }
    
    /**
     * Optimize product image - resize jika lebih besar dari max dimensions
     * 
     * @param string $filepath Path to image file
     * @param int $max_width Maximum width
     * @param int $max_height Maximum height
     * @return bool Success
     */
    private static function optimizeProductImage($filepath, $max_width, $max_height) {
        try {
            $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
            
            // Load image based on extension
            switch ($ext) {
                case 'png':
                    $image = imagecreatefrompng($filepath);
                    break;
                case 'webp':
                    $image = imagecreatefromwebp($filepath);
                    break;
                case 'jpg':
                case 'jpeg':
                default:
                    $image = imagecreatefromjpeg($filepath);
                    break;
            }
            
            if ($image === false) {
                return false;
            }
            
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Calculate new dimensions
            $scale = min($max_width / $width, $max_height / $height);
            
            if ($scale >= 1) {
                // Image is smaller than max, no need to resize
                imagedestroy($image);
                return true;
            }
            
            $new_width = (int)($width * $scale);
            $new_height = (int)($height * $scale);
            
            // Create resized image
            $resized = imagecreatetruecolor($new_width, $new_height);
            
            // Preserve transparency for PNG
            if ($ext === 'png') {
                imagecolortransparent($resized, imagecolorallocatealpha($resized, 0, 0, 0, 127));
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }
            
            imagecopyresampled(
                $resized, $image,
                0, 0, 0, 0,
                $new_width, $new_height,
                $width, $height
            );
            
            // Save optimized image
            switch ($ext) {
                case 'png':
                    imagepng($resized, $filepath, 9);
                    break;
                case 'webp':
                    imagewebp($resized, $filepath, self::JPEG_QUALITY);
                    break;
                case 'jpg':
                case 'jpeg':
                default:
                    imagejpeg($resized, $filepath, self::JPEG_QUALITY);
                    break;
            }
            
            imagedestroy($image);
            imagedestroy($resized);
            
            return true;
        } catch (Exception $e) {
            error_log('Image optimization error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate thumbnail from image
     * 
     * @param string $source_path Path to source image
     * @param string $thumb_dir Thumbnail directory
     * @param int $thumb_width Thumbnail width
     * @param int $thumb_height Thumbnail height
     * @param string $prefix Filename prefix
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    private static function generateThumbnail($source_path, $thumb_dir, $thumb_width, $thumb_height, $prefix) {
        try {
            // Create thumb directory if not exists
            if (!is_dir($thumb_dir)) {
                if (!mkdir($thumb_dir, 0755, true)) {
                    return ['success' => false, 'message' => 'Gagal membuat direktori thumbnail'];
                }
            }
            
            $ext = strtolower(pathinfo($source_path, PATHINFO_EXTENSION));
            $timestamp = time();
            $random = bin2hex(random_bytes(4));
            $thumb_filename = $prefix . '_thumb_' . $timestamp . '_' . $random . '.' . $ext;
            $thumb_path = $thumb_dir . $thumb_filename;
            
            // Load source image
            switch ($ext) {
                case 'png':
                    $source = imagecreatefrompng($source_path);
                    break;
                case 'webp':
                    $source = imagecreatefromwebp($source_path);
                    break;
                case 'jpg':
                case 'jpeg':
                default:
                    $source = imagecreatefromjpeg($source_path);
                    break;
            }
            
            if ($source === false) {
                return ['success' => false, 'message' => 'Gagal membaca image source'];
            }
            
            $source_width = imagesx($source);
            $source_height = imagesy($source);
            
            // Calculate dimensions for square thumbnail (crop from center)
            $min_size = min($source_width, $source_height);
            $x = ($source_width - $min_size) / 2;
            $y = ($source_height - $min_size) / 2;
            
            // Create thumbnail
            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
            
            // Preserve transparency for PNG
            if ($ext === 'png') {
                imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
            }
            
            imagecopyresampled(
                $thumb, $source,
                0, 0, (int)$x, (int)$y,
                $thumb_width, $thumb_height,
                $min_size, $min_size
            );
            
            // Save thumbnail
            switch ($ext) {
                case 'png':
                    imagepng($thumb, $thumb_path, 9);
                    break;
                case 'webp':
                    imagewebp($thumb, $thumb_path, self::JPEG_QUALITY);
                    break;
                case 'jpg':
                case 'jpeg':
                default:
                    imagejpeg($thumb, $thumb_path, self::JPEG_QUALITY);
                    break;
            }
            
            imagedestroy($source);
            imagedestroy($thumb);
            
            chmod($thumb_path, 0644);
            
            return [
                'success' => true,
                'filename' => $thumb_filename,
                'filepath' => $thumb_path,
                'message' => 'Thumbnail berhasil dibuat'
            ];
        } catch (Exception $e) {
            error_log('Thumbnail generation error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal membuat thumbnail: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete uploaded file
     * 
     * @param string $filename Filename to delete
     * @param string $type Type of file ('produk' or 'pembayaran')
     * @return array ['success' => bool, 'message' => string]
     */
    public static function deleteFile($filename, $type = 'produk') {
        $upload_dir = ($type === 'pembayaran') ? self::UPLOAD_DIR_PEMBAYARAN : self::UPLOAD_DIR_PRODUK;
        $filepath = $upload_dir . $filename;
        
        // Security check - prevent directory traversal
        $filepath = realpath($filepath);
        $upload_dir_real = realpath($upload_dir);
        
        if ($filepath === false || strpos($filepath, $upload_dir_real) !== 0) {
            return ['success' => false, 'message' => 'Invalid file path'];
        }
        
        if (!file_exists($filepath)) {
            return ['success' => false, 'message' => 'File tidak ditemukan'];
        }
        
        if (!unlink($filepath)) {
            return ['success' => false, 'message' => 'Gagal menghapus file'];
        }
        
        // Also delete thumbnail if it's a product image
        if ($type === 'produk') {
            $thumb_pattern = str_replace('.', '_thumb.', $filename);
            $thumb_dir = self::UPLOAD_DIR_PRODUK_THUMB;
            $thumb_files = glob($thumb_dir . str_replace('_thumb', '_thumb_*', pathinfo($filename, PATHINFO_FILENAME)) . '.*');
            
            foreach ($thumb_files as $thumb_file) {
                if (file_exists($thumb_file)) {
                    @unlink($thumb_file);
                }
            }
        }
        
        return ['success' => true, 'message' => 'File berhasil dihapus'];
    }
    
    /**
     * Get file URL
     * 
     * @param string $filename Filename
     * @param string $type Type of file ('produk' or 'pembayaran', 'produk_thumb' for thumbnail)
     * @return string File URL
     */
    public static function getFileUrl($filename, $type = 'produk') {
        if ($type === 'produk_thumb') {
            return self::UPLOAD_DIR_PRODUK_THUMB . $filename;
        }
        
        $upload_dir = ($type === 'pembayaran') ? self::UPLOAD_DIR_PEMBAYARAN : self::UPLOAD_DIR_PRODUK;
        return $upload_dir . $filename;
    }
    
    /**
     * Get thumbnail filename from original filename
     * 
     * @param string $original_filename Original filename
     * @param string $upload_dir Upload directory (optional)
     * @return string|null Thumbnail filename or null if not found
     */
    public static function getThumbnailFilename($original_filename, $upload_dir = self::UPLOAD_DIR_PRODUK) {
        $base = pathinfo($original_filename, PATHINFO_FILENAME);
        $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
        
        // Search for thumbnail files with this prefix
        $thumb_dir = str::UPLOAD_DIR_PRODUK_THUMB;
        $pattern = $thumb_dir . $base . '_thumb_*.' . $ext;
        
        $files = glob($pattern);
        if (!empty($files)) {
            // Return just the filename, not full path
            return basename($files[0]);
        }
        
        return null;
    }
}
?>
