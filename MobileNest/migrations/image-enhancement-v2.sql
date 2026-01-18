-- Product Image Enhancement Migration v2.0
-- Adds support for thumbnail storage and image metadata
-- Generated: 2026-01-14

-- Update produk table to support thumbnails and image metadata
ALTER TABLE `produk` 
ADD COLUMN `gambar_thumbnail` VARCHAR(255) COMMENT 'Thumbnail filename' AFTER `gambar`,
ADD COLUMN `image_width` INT COMMENT 'Original image width' AFTER `gambar_thumbnail`,
ADD COLUMN `image_height` INT COMMENT 'Original image height' AFTER `image_width`,
ADD COLUMN `image_size_kb` INT COMMENT 'Original image size in KB' AFTER `image_height`,
ADD COLUMN `image_uploaded_at` TIMESTAMP NULL COMMENT 'When image was uploaded' AFTER `image_size_kb`;

-- Create index untuk faster queries
ALTER TABLE `produk` 
ADD INDEX `idx_gambar` (`gambar`),
ADD INDEX `idx_gambar_thumbnail` (`gambar_thumbnail`);

-- Migration notes:
-- 1. gambar_thumbnail: Stores the thumbnail filename from uploads/produk/thumbs/
-- 2. image_width/image_height: Dimensions after optimization
-- 3. image_size_kb: Final file size in KB
-- 4. image_uploaded_at: Timestamp for tracking

-- Untuk existing data, Anda bisa generate thumbnails menggunakan:
-- Script PHP yang iterate semua produk dan call UploadHandlerEnhanced::generateThumbnail()
