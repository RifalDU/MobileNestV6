<?php
/**
 * Brand Logo Configuration
 * Uses high-quality PNG/SVG logos from reliable CDNs
 * With embedded SVG fallback for robustness
 */

// Embedded SVG logos (these will ALWAYS work) - LARGER and BOLDER
$embedded_svgs = [
    'Realme' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid meet"><defs><style>.realme-text{font-size:80px;font-weight:900;fill:#00a699;font-family:Arial,Helvetica,sans-serif;letter-spacing:-5px}</style></defs><text x="100" y="150" text-anchor="middle" class="realme-text">Re</text></svg>',
    'OPPO' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid meet"><defs><style>.oppo-text{font-size:80px;font-weight:900;fill:#1f1f1f;font-family:Arial,Helvetica,sans-serif;letter-spacing:-5px}</style></defs><text x="100" y="150" text-anchor="middle" class="oppo-text">OP</text></svg>',
];

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
        'alt' => 'OPPO Logo',
        'has_embedded' => true
    ],
    'Vivo' => [
        'image_url' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/vivo.svg',
        'alt' => 'Vivo Logo'
    ],
    'Realme' => [
        // Primary: PNG from multiple CDN mirrors (most reliable for Realme)
        'image_urls' => [
            'https://static.realme.com/static/images/realme_logo.png',
            'https://cdn-images-1.medium.com/max/1200/1*VSHo3EWShE6d0Hw0-_lYOA.png',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ce/Realme_logo.svg/1024px-Realme_logo.svg.png'
        ],
        'image_url' => 'https://static.realme.com/static/images/realme_logo.png', // Primary
        'alt' => 'Realme Logo',
        'has_embedded' => true
    ]
];

/**
 * Get brand logo URL
 */
function get_brand_logo_url($brand_name) {
    global $brand_logos;
    
    if (isset($brand_logos[$brand_name]['image_url'])) {
        return $brand_logos[$brand_name]['image_url'];
    }
    
    return 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/smartphone.svg';
}

/**
 * Get embedded SVG
 */
function get_brand_embedded_svg($brand_name) {
    global $embedded_svgs;
    return isset($embedded_svgs[$brand_name]) ? $embedded_svgs[$brand_name] : null;
}

/**
 * Get brand logo HTML with SVG fallback - SIMPLIFIED VERSION
 */
function get_brand_logo_html($brand_name, $attributes = []) {
    $logo_url = get_brand_logo_url($brand_name);
    $alt_text = htmlspecialchars($brand_name);
    $embedded_svg = get_brand_embedded_svg($brand_name);
    $class = isset($attributes['class']) ? $attributes['class'] : 'brand-logo';
    $style = isset($attributes['style']) ? $attributes['style'] : 'width: 50px; height: 50px;';
    
    if ($embedded_svg) {
        // Return with inline SVG fallback
        $svg_id = 'svg-' . str_replace(' ', '-', strtolower($brand_name));
        return sprintf(
            '<div style="position:relative;display:inline-block;%s" data-brand="%s">
                <img src="%s" alt="%s" class="%s" style="%s;object-fit:contain" loading="lazy" onerror="this.style.display=\'none\';document.getElementById(\'%s\').style.display=\'flex\'">
                <div id="%s" style="display:none;%s;align-items:center;justify-content:center">%s</div>
            </div>',
            $style,
            htmlspecialchars($brand_name),
            htmlspecialchars($logo_url),
            $alt_text,
            htmlspecialchars($class),
            $style,
            $svg_id,
            $svg_id,
            $style,
            $embedded_svg
        );
    } else {
        // Simple img tag
        return sprintf(
            '<img src="%s" alt="%s" class="%s" style="%s;object-fit:contain" loading="lazy">',
            htmlspecialchars($logo_url),
            $alt_text,
            htmlspecialchars($class),
            htmlspecialchars($style)
        );
    }
}

/**
 * Get all available brands
 */
function get_all_brands() {
    global $brand_logos;
    return array_keys($brand_logos);
}

/**
 * Get brand logo data
 */
function get_brand_logo_data($brand_name) {
    global $brand_logos;
    return isset($brand_logos[$brand_name]) ? $brand_logos[$brand_name] : null;
}

/**
 * Get brand logo with visual fallback (initials in circle)
 */
function get_brand_logo_with_visual_fallback($brand_name, $fallback_color = '#f0f0f0') {
    $logo_data = get_brand_logo_data($brand_name);
    
    if (!$logo_data) {
        return sprintf(
            '<div style="width:50px;height:50px;background-color:%s;border-radius:50%%;display:flex;align-items:center;justify-content:center;font-weight:bold;color:#666;font-size:12px;flex-shrink:0;">%s</div>',
            htmlspecialchars($fallback_color),
            htmlspecialchars(substr($brand_name, 0, 2))
        );
    }
    
    $logo_url = isset($logo_data['image_url']) ? $logo_data['image_url'] : 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/smartphone.svg';
    $alt_text = htmlspecialchars($brand_name);
    $embedded_svg = get_brand_embedded_svg($brand_name);
    $initials = substr($brand_name, 0, 2);
    $svg_id = 'fallback-' . str_replace(' ', '-', strtolower($brand_name));
    
    if ($embedded_svg) {
        $fallback = sprintf(
            '<div id="%s" style="position:absolute;top:0;left:0;width:100%%;height:100%%;display:none;align-items:center;justify-content:center;background:#f8f9fa;border-radius:10px;">%s</div>',
            $svg_id,
            $embedded_svg
        );
        $onerror = "this.style.display='none';document.getElementById('" . $svg_id . "').style.display='flex';";
    } else {
        $fallback = sprintf(
            '<div id="%s" style="position:absolute;top:0;left:0;width:100%%;height:100%%;background-color:%s;border-radius:50%%;display:none;align-items:center;justify-content:center;font-weight:bold;color:#666;font-size:12px;">%s</div>',
            $svg_id,
            htmlspecialchars($fallback_color),
            htmlspecialchars($initials)
        );
        $onerror = "this.style.display='none';document.getElementById('" . $svg_id . "').style.display='flex';";
    }
    
    return sprintf(
        '<div style="position:relative;width:50px;height:50px;flex-shrink:0;">
            <img src="%s" alt="%s" 
                 style="width:100%%;height:100%%;object-fit:contain;display:block;" 
                 loading="lazy" 
                 onerror="%s">
            %s
        </div>',
        htmlspecialchars($logo_url),
        $alt_text,
        $onerror,
        $fallback
    );
}
?>