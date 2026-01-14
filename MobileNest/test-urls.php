<?php
/**
 * Test URLs accessibility
 */

echo "<h1>Testing URL Accessibility</h1>";
echo "<hr>";

$urls = [
    'Wikimedia Commons PNG' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Realme_logo.svg/400px-Realme_logo.svg.png',
    'BrandLogos PNG' => 'https://brandlogos.net/wp-content/uploads/2021/04/realme-logo-brandlogos.net_.png',
    'LogoWik PNG' => 'https://logowik.com/data/file/1663/realme-1024.png',
    'Simple Icons SVG' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/smartphone.svg',
    'CDN Apple SVG' => 'https://cdn.jsdelivr.net/npm/simple-icons@latest/icons/apple.svg',
];

echo "<table border='1' cellpadding='10' style='width:100%'><tr><th>URL Name</th><th>Status</th><th>HTTP Code</th><th>Response Time</th></tr>";

foreach ($urls as $name => $url) {
    $start = microtime(true);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $time = number_format((microtime(true) - $start) * 1000, 2) . 'ms';
    
    $status = ($http_code === 200) ? '✅ OK' : '❌ FAILED';
    
    echo "<tr>
        <td>" . htmlspecialchars($name) . "</td>
        <td>" . $status . "</td>
        <td>" . $http_code . "</td>
        <td>" . $time . "</td>
    </tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>✅ WORKING URLS: Use these!";
echo "<p>If all fail, we'll use embedded SVG as fallback.";

?>
<style>
body { font-family: Arial; margin: 20px; }
table { border-collapse: collapse; margin: 20px 0; }
th { background: #f0f0f0; }
tr:nth-child(even) { background: #f9f9f9; }
</style>