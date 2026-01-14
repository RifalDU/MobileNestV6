<?php
/**
 * Brand Logo Configuration
 * Uses high-quality logos from reliable sources
 * 
 * Updated: Jan 14, 2026
 * - Apple, Samsung, Xiaomi, OPPO, Vivo: Using CDN (Simple Icons)
 * - Realme: Using local file (assets/images/realme-logo.jpg) - manually managed
 */

$brand_logos = [
    'Apple' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/apple.svg',
        'alt' => 'Apple Logo',
        'type' => 'cdn'
    ],
    'Samsung' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/samsung.svg',
        'alt' => 'Samsung Logo',
        'type' => 'cdn'
    ],
    'Xiaomi' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/xiaomi.svg',
        'alt' => 'Xiaomi Logo',
        'type' => 'cdn'
    ],
    'OPPO' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/oppo.svg',
        'alt' => 'OPPO Logo',
        'type' => 'cdn'
    ],
    'Vivo' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/vivo.svg',
        'alt' => 'Vivo Logo',
        'type' => 'cdn'
    ],
    'Realme' => [
        // Changed: Using local file instead of CDN (Jan 14, 2026)
        // Path: /MobileNest/assets/images/realme-logo.jpg
        // This is manually managed and doesn't depend on external CDN
        'image_url' => '../assets/images/realme-logo.jpg',
        'alt' => 'Realme Logo',
        'type' => 'local',
        'note' => 'Local file at assets/images/realme-logo.jpg'
    ]
];

/**
 * Get brand logo URL with case-insensitive lookup
 * Supports: Apple, apple, APPLE, aPPle, etc.
 * 
 * @param string $brand_name The brand name
 * @return string The logo image URL
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
    
    // Default smartphone icon if brand not found
    return 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/smartphone.svg';
}

/**
 * Get brand logo alt text with case-insensitive lookup
 * 
 * @param string $brand_name The brand name
 * @return string The alt text
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
 * 
 * @return array Array of brand names
 */
function get_all_brands() {
    global $brand_logos;
    return array_keys($brand_logos);
}

/**
 * Get brand logo data with case-insensitive lookup
 * Returns complete brand logo configuration
 * 
 * @param string $brand_name The brand name
 * @return array|null Brand logo data or null if not found
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
 * 
 * @param string $brand_name The brand name
 * @return null
 */
function get_brand_embedded_svg($brand_name) {
    return null;
}

/**
 * Simple brand logo HTML with error handling
 * 
 * @param string $brand_name The brand name
 * @param array $attributes Additional HTML attributes
 * @return string HTML img tag
 */
function get_brand_logo_html($brand_name, $attributes = []) {
    $logo_url = get_brand_logo_url($brand_name);
    $alt_text = htmlspecialchars(get_brand_logo_alt($brand_name));
    $class = isset($attributes['class']) ? $attributes['class'] : 'brand-logo';
    $style = isset($attributes['style']) ? $attributes['style'] : 'width: 50px; height: 50px;';
    
    // Unique fallback ID
    $fallback_id = 'logo-fallback-' . strtolower(str_replace(' ', '-', $brand_name)) . '-' . uniqid();
    $initials = htmlspecialchars(substr($brand_name, 0, 2));
    
    // Build HTML with fallback
    return sprintf(
        '<img src="%s" alt="%s" class="%s" style="%s; object-fit: contain;" loading="lazy" '
        . 'onerror="this.style.display=\'none\'; let fallback=document.getElementById(\'%s\'); if(fallback) fallback.style.display=\'flex\';" /'
        . '><div id="%s" style="display: none; width: %s; height: %s; align-items: center; justify-content: center; '
        . 'background-color: #f0f0f0; border-radius: 50%%; font-weight: bold; color: #666; font-size: 10px; flex-shrink: 0;"'
        . ' data-initials="%s">%s</div>',
        htmlspecialchars($logo_url),
        $alt_text,
        htmlspecialchars($class),
        htmlspecialchars($style),
        $fallback_id,
        $fallback_id,
        isset($attributes['width']) ? htmlspecialchars($attributes['width']) : '50px',
        isset($attributes['height']) ? htmlspecialchars($attributes['height']) : '50px',
        $initials,
        $initials
    );
}

/**
 * Brand logo with visual fallback (initials in circle)
 * More robust fallback mechanism
 * 
 * @param string $brand_name The brand name
 * @param string $fallback_color The fallback background color
 * @return string HTML with logo and fallback div
 */
function get_brand_logo_with_visual_fallback($brand_name, $fallback_color = '#f0f0f0') {
    $logo_url = get_brand_logo_url($brand_name);
    $alt_text = htmlspecialchars(get_brand_logo_alt($brand_name));
    $initials = htmlspecialchars(substr($brand_name, 0, 2));
    $fallback_id = 'logo-fallback-' . strtolower(str_replace(' ', '-', $brand_name)) . '-' . uniqid();
    
    return sprintf(
        '<div style="position: relative; width: 50px; height: 50px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center;">' .
            '<img src="%s" alt="%s" ' .
                 'style="width: 100%%; height: 100%%; object-fit: contain; display: block;" ' .
                 'loading="lazy" ' .
                 'onerror="this.style.display=\'none\'; document.getElementById(\'%s\').style.display=\'flex\';" />' .
            '<div id="%s" style="position: absolute; top: 0; left: 0; width: 100%%; height: 100%%; ' .
                          'background-color: %s; border-radius: 50%%; display: none; align-items: center; justify-content: center; ' .
                          'font-weight: bold; color: #666; font-size: 12px; flex-shrink: 0;">' .
                '%s' .
            '</div>' .
        '</div>',
        htmlspecialchars($logo_url),
        $alt_text,
        $fallback_id,
        $fallback_id,
        htmlspecialchars($fallback_color),
        $initials
    );
}

/**
 * Get logo type (local or CDN)
 * Useful for debugging
 * 
 * @param string $brand_name The brand name
 * @return string|null The logo type or null
 */
function get_brand_logo_type($brand_name) {
    $data = get_brand_logo_data($brand_name);
    return $data ? ($data['type'] ?? 'unknown') : null;
}

/**
 * Get logo source info for debugging
 * Returns the complete logo configuration
 * 
 * @param string $brand_name The brand name
 * @return array|null Complete logo data or null
 */
function get_brand_logo_info($brand_name) {
    return get_brand_logo_data($brand_name);
}

?>