<?php
/**
 * API Endpoint untuk fetch produk
 * GET /produk/get-produk.php
 * 
 * Parameters:
 *   - filter (optional): terbaru, bestseller, price_low, price_high
 *   - limit (optional): jumlah produk yang di-return
 *   - offset (optional): pagination offset
 *   - search (optional): pencarian produk berdasarkan nama
 * 
 * Returns:
 *   JSON array dengan data produk
 */

header('Content-Type: application/json');

require_once '../config.php';
require_once '../includes/upload-handler.php';

// Get parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'terbaru';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Sanitize inputs
$limit = ($limit > 100) ? 100 : $limit; // Max 100 per request
$limit = ($limit < 1) ? 10 : $limit;
$offset = ($offset < 0) ? 0 : $offset;

// Build WHERE clause
$where_clauses = ["produk.status_produk = 'Tersedia'"];

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $where_clauses[] = "(produk.nama_produk LIKE '%$search_safe%' OR produk.deskripsi LIKE '%$search_safe%')";
}

$where = implode(' AND ', $where_clauses);

// Build ORDER BY clause
$order_by = "produk.tanggal_ditambahkan DESC"; // default

switch ($filter) {
    case 'bestseller':
        $order_by = "produk.terjual DESC";
        break;
    case 'price_low':
        $order_by = "produk.harga ASC";
        break;
    case 'price_high':
        $order_by = "produk.harga DESC";
        break;
    case 'rating':
        $order_by = "produk.rating DESC";
        break;
    case 'terbaru':
    default:
        $order_by = "produk.tanggal_ditambahkan DESC";
        break;
}

// Execute query
$sql = "SELECT id_produk, nama_produk, merek, deskripsi, harga, stok, gambar, kategori, rating, terjual 
        FROM produk 
        WHERE $where 
        ORDER BY $order_by 
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database query error'
    ]);
    exit;
}

// Build response
$produk_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Build image URL dengan smart path resolution
    $image_url = '';
    if (!empty($row['gambar'])) {
        if (strpos($row['gambar'], 'http') === false && strpos($row['gambar'], '/') === false) {
            // It's a filename - use UploadHandler dengan smart path untuk folder produk
            $image_url = UploadHandler::getFileUrlFromProduk($row['gambar'], 'produk');
        } else {
            // It's already a URL
            $image_url = $row['gambar'];
        }
    }
    
    $produk_list[] = [
        'id_produk' => (int)$row['id_produk'],
        'nama_produk' => htmlspecialchars($row['nama_produk']),
        'merek' => htmlspecialchars($row['merek']),
        'deskripsi' => htmlspecialchars(substr($row['deskripsi'], 0, 100)) . '...',
        'harga' => (float)$row['harga'],
        'stok' => (int)$row['stok'],
        'gambar' => htmlspecialchars($image_url),
        'kategori' => htmlspecialchars($row['kategori']),
        'rating' => (float)$row['rating'],
        'terjual' => (int)$row['terjual']
    ];
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'total' => count($produk_list),
    'filter' => $filter,
    'data' => $produk_list
]);
?>
