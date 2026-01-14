<?php
/**
 * Brand Logo Configuration
 * Uses high-quality SVG logos from reliable CDNs
 * Simple Icons (JSDelivr) + Wikimedia CDN for brands not in simple-icons
 */

$brand_logos = [
    'Apple' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/apple.svg',
        'alt' => 'Apple Logo'
    ],
    'Samsung' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/samsung.svg',
        'alt' => 'Samsung Logo'
    ],
    'Xiaomi' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/xiaomi.svg',
        'alt' => 'Xiaomi Logo'
    ],
    'OPPO' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/oppo.svg',
        'alt' => 'OPPO Logo'
    ],
    'Vivo' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/vivo.svg',
        'alt' => 'Vivo Logo'
    ],
    'Realme' => [
        // Using Wikimedia Commons - high quality Realme logo
        'image_url' => '../uploads/logo/realme-logo.png',
        'alt' => 'Realme Logo',
        'source' => 'local'
    ]
];

/**
 * Get brand logo URL with case-insensitive lookup
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
 * Simple brand logo HTML
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
?>