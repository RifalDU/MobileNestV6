<?php
/**
 * Brand Logo Configuration
 * Supports both:
 * 1. CDN logos (via JSDelivr/Wikimedia)
 * 2. Local custom logos (stored in uploads/logo/)
 * 
 * For local logos: simply set 'image_url' to relative path like '../uploads/logo/realme-logo.svg'
 */

$brand_logos = [
    'Apple' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/apple.svg',
        'alt' => 'Apple Logo',
        'source' => 'cdn'
    ],
    'Samsung' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/samsung.svg',
        'alt' => 'Samsung Logo',
        'source' => 'cdn'
    ],
    'Xiaomi' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/xiaomi.svg',
        'alt' => 'Xiaomi Logo',
        'source' => 'cdn'
    ],
    'OPPO' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/oppo.svg',
        'alt' => 'OPPO Logo',
        'source' => 'cdn'
    ],
    'Vivo' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/vivo.svg',
        'alt' => 'Vivo Logo',
        'source' => 'cdn'
    ],
    'Realme' => [
        // LOCAL CUSTOM LOGO - copy your logo to MobileNest/uploads/logo/realme-logo.svg
        // Then change image_url to: '../uploads/logo/realme-logo.svg'
        // Example: 'image_url' => '../uploads/logo/realme-logo.svg',
        'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9b/Realme_logo.svg/1024px-Realme_logo.svg.png',
        'alt' => 'Realme Logo',
        'source' => 'cdn'  // Change to 'local' when using local logo
    ]
];

/**
 * Get brand logo URL with case-insensitive lookup
 * Supports both CDN and local logos
 * 
 * Supports: Apple, apple, APPLE, aPPle, etc.
 */
function get_brand_logo_url($brand_name) {
    global $brand_logos;
    
    // First try exact match
    if (isset($brand_logos[$brand_name])) {
        return $brand_logos[$brand_name]['image_url'];
    }
    
    // Then try case-insensitive match
    foreach ($brand_logos as $key => $data) {
        if (strtolower($key) === strtolower($brand_name)) {
            return $data['image_url'];
        }
    }
    
    // Default smartphone icon jika brand tidak ditemukan
    return 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/smartphone.svg';
}

/**
 * Get brand logo alt text with case-insensitive lookup
 */
function get_brand_logo_alt($brand_name) {
    global $brand_logos;
    
    // First try exact match
    if (isset($brand_logos[$brand_name])) {
        return $brand_logos[$brand_name]['alt'];
    }
    
    // Then try case-insensitive match
    foreach ($brand_logos as $key => $data) {
        if (strtolower($key) === strtolower($brand_name)) {
            return $data['alt'];
        }
    }
    
    return 'Brand Logo';
}

/**
 * Get brand logo source (cdn or local)
 */
function get_brand_logo_source($brand_name) {
    global $brand_logos;
    
    // First try exact match
    if (isset($brand_logos[$brand_name])) {
        return isset($brand_logos[$brand_name]['source']) ? $brand_logos[$brand_name]['source'] : 'cdn';
    }
    
    // Then try case-insensitive match
    foreach ($brand_logos as $key => $data) {
        if (strtolower($key) === strtolower($brand_name)) {
            return isset($data['source']) ? $data['source'] : 'cdn';
        }
    }
    
    return 'cdn';
}

/**
 * Get all available brands
 */
function get_all_brands() {
    global $brand_logos;
    return array_keys($brand_logos);
}

/**
 * Get brand logo data with case-insensitive lookup
 */
function get_brand_logo_data($brand_name) {
    global $brand_logos;
    
    // First try exact match
    if (isset($brand_logos[$brand_name])) {
        return $brand_logos[$brand_name];
    }
    
    // Then try case-insensitive match
    foreach ($brand_logos as $key => $data) {
        if (strtolower($key) === strtolower($brand_name)) {
            return $data;
        }
    }
    
    return null;
}

/**
 * Get embedded SVG (DEPRECATED - kept for backward compatibility)
 * Returns null - no longer using embedded SVG
 */
function get_brand_embedded_svg($brand_name) {
    return null;
}

/**
 * Simple brand logo HTML with support for both CDN and local logos
 */
function get_brand_logo_html($brand_name, $attributes = []) {
    $logo_url = get_brand_logo_url($brand_name);
    $alt_text = htmlspecialchars(get_brand_logo_alt($brand_name));
    $class = isset($attributes['class']) ? $attributes['class'] : 'brand-logo';
    $style = isset($attributes['style']) ? $attributes['style'] : 'width: 50px; height: 50px;';
    
    return sprintf(
        '<img src="%s" alt="%s" class="%s" style="%s;object-fit:contain;" loading="lazy">',
        htmlspecialchars($logo_url),
        $alt_text,
        htmlspecialchars($class),
        htmlspecialchars($style)
    );
}

/**
 * Brand logo with visual fallback (initials in circle)
 * Works with both CDN and local logos
 */
function get_brand_logo_with_visual_fallback($brand_name, $fallback_color = '#f0f0f0') {
    $logo_url = get_brand_logo_url($brand_name);
    $alt_text = htmlspecialchars(get_brand_logo_alt($brand_name));
    $initials = htmlspecialchars(substr($brand_name, 0, 2));
    $fallback_id = 'logo-fallback-' . strtolower(str_replace(' ', '-', $brand_name));
    
    return sprintf(
        '<div style="position:relative;width:50px;height:50px;flex-shrink:0;">
            <img src="%s" alt="%s" 
                 style="width:100%%;height:100%%;object-fit:contain;display:block;" 
                 loading="lazy" 
                 onerror="this.style.display=\'none\';document.getElementById(\'%s\').style.display=\'flex\'">
            <div id="%s" style="position:absolute;top:0;left:0;width:100%%;height:100%%;background-color:%s;border-radius:50%%;display:none;align-items:center;justify-content:center;font-weight:bold;color:#666;font-size:12px;">
                %s
            </div>
        </div>',
        htmlspecialchars($logo_url),
        $alt_text,
        $fallback_id,
        $fallback_id,
        htmlspecialchars($fallback_color),
        $initials
    );
}

/**
 * HELPER: Format logo path for local uploads
 * Usage: get_logo_path('realme-logo.svg') returns '../uploads/logo/realme-logo.svg'
 */
function get_logo_path($filename) {
    return '../uploads/logo/' . basename($filename);
}
?>