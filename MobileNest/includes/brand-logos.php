<?php
/**
 * Brand Logo Configuration
 * Uses Simple Icons CDN (most reliable) + embedded SVG fallback
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
        // Primary: Simple Icons CDN (MOST RELIABLE)
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/smartphone.svg',
        'alt' => 'Realme Logo',
        // SVG Embedded as fallback (will always work)
        'embedded_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="50" height="50" style="fill: none; stroke: #00a699; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round;"><path d="M20 25 L80 25 L80 75 L20 75 Z"/><circle cx="50" cy="50" r="2" fill="#00a699"/></svg>'
    ]
];

/**
 * Get brand logo URL
 * @param string $brand_name - The name of the phone brand
 * @return string - The CDN URL of the logo
 */
function get_brand_logo_url($brand_name) {
    global $brand_logos;
    
    if (isset($brand_logos[$brand_name]['image_url'])) {
        return $brand_logos[$brand_name]['image_url'];
    }
    
    return 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/smartphone.svg';
}

/**
 * Get embedded SVG fallback
 * @param string $brand_name - The name of the phone brand
 * @return string|null - SVG HTML or null if not available
 */
function get_brand_embedded_svg($brand_name) {
    global $brand_logos;
    
    if (isset($brand_logos[$brand_name]['embedded_svg'])) {
        return $brand_logos[$brand_name]['embedded_svg'];
    }
    
    return null;
}

/**
 * Get brand logo HTML with embedded SVG fallback
 * @param string $brand_name - The name of the phone brand
 * @param array $attributes - Additional HTML attributes
 * @return string - HTML img tag with logo and SVG fallback
 */
function get_brand_logo_html($brand_name, $attributes = []) {
    global $brand_logos;
    
    $logo_url = get_brand_logo_url($brand_name);
    $alt_text = isset($brand_logos[$brand_name]['alt']) ? $brand_logos[$brand_name]['alt'] : 'Brand Logo';
    $embedded_svg = get_brand_embedded_svg($brand_name);
    
    // Default attributes
    $class = isset($attributes['class']) ? $attributes['class'] : 'brand-logo';
    $style = isset($attributes['style']) ? $attributes['style'] : 'width: 50px; height: 50px;';
    
    // Build onerror - fallback to embedded SVG or hide
    if ($embedded_svg) {
        // Create unique ID for this brand's SVG
        $svg_id = 'fallback-svg-' . sanitize_for_html($brand_name);
        $onerror = "document.getElementById('" . $svg_id . "').style.display='flex'; this.style.display='none';";
        
        return sprintf(
            '<div style="position: relative; width: 50px; height: 50px; display: inline-block;">
                <img src="%s" alt="%s" class="%s" style="%s; display: block;" loading="lazy" onerror="%s">
                <div id="%s" style="position: absolute; top: 0; left: 0; width: 100%%; height: 100%%; display: none; align-items: center; justify-content: center;">%s</div>
            </div>',
            htmlspecialchars($logo_url),
            htmlspecialchars($alt_text),
            htmlspecialchars($class),
            htmlspecialchars($style),
            $onerror,
            htmlspecialchars($svg_id),
            $embedded_svg // Already safe SVG HTML
        );
    } else {
        // No fallback, just hide on error
        return sprintf(
            '<img src="%s" alt="%s" class="%s" style="%s" loading="lazy" onerror="this.style.display=\'none\'">',
            htmlspecialchars($logo_url),
            htmlspecialchars($alt_text),
            htmlspecialchars($class),
            htmlspecialchars($style)
        );
    }
}

/**
 * Sanitize strings for HTML attributes
 * @param string $str
 * @return string
 */
function sanitize_for_html($str) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $str);
}

/**
 * Get all available brands
 * @return array - Array of brand names
 */
function get_all_brands() {
    global $brand_logos;
    return array_keys($brand_logos);
}

/**
 * Get brand logo array data
 * @param string $brand_name - The name of the phone brand
 * @return array|null - Array with logo data or null if not found
 */
function get_brand_logo_data($brand_name) {
    global $brand_logos;
    return isset($brand_logos[$brand_name]) ? $brand_logos[$brand_name] : null;
}

/**
 * Get brand logo with visual fallback (initials in circle)
 * @param string $brand_name - The name of the phone brand
 * @param string $fallback_color - Fallback background color (hex)
 * @return string - HTML with logo or styled text fallback
 */
function get_brand_logo_with_visual_fallback($brand_name, $fallback_color = '#f0f0f0') {
    global $brand_logos;
    
    $logo_data = get_brand_logo_data($brand_name);
    
    if (!$logo_data) {
        return sprintf(
            '<div class="brand-logo-fallback" style="width: 50px; height: 50px; background-color: %s; border-radius: 50%%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #666; font-size: 12px; flex-shrink: 0;">%s</div>',
            htmlspecialchars($fallback_color),
            htmlspecialchars(substr($brand_name, 0, 2))
        );
    }
    
    $logo_url = $logo_data['image_url'];
    $alt_text = $logo_data['alt'];
    $initials = substr($brand_name, 0, 2);
    $svg_id = 'fallback-svg-' . sanitize_for_html($brand_name);
    $embedded_svg = isset($logo_data['embedded_svg']) ? $logo_data['embedded_svg'] : null;
    
    // Build HTML with embedded SVG or text fallback
    if ($embedded_svg) {
        $fallback = sprintf(
            '<div id="%s" style="position: absolute; top: 0; left: 0; width: 100%%; height: 100%%; display: none; align-items: center; justify-content: center;">%s</div>',
            htmlspecialchars($svg_id),
            $embedded_svg
        );
        $onerror = "document.getElementById('" . $svg_id . "').style.display='flex'; this.style.display='none';";
    } else {
        $fallback = sprintf(
            '<div style="position: absolute; top: 0; left: 0; width: 100%%; height: 100%%; background-color: %s; border-radius: 50%%; display: none; align-items: center; justify-content: center; font-weight: bold; color: #666; font-size: 12px;">%s</div>',
            htmlspecialchars($fallback_color),
            htmlspecialchars($initials)
        );
        $onerror = "this.nextElementSibling.style.display='flex'; this.style.display='none';";
    }
    
    return sprintf(
        '<div style="position: relative; width: 50px; height: 50px; flex-shrink: 0;">
            <img src="%s" alt="%s" 
                 style="width: 100%%; height: 100%%; object-fit: contain; display: block;" 
                 loading="lazy" 
                 onerror="%s">
            %s
        </div>',
        htmlspecialchars($logo_url),
        htmlspecialchars($alt_text),
        $onerror,
        $fallback
    );
}
?>