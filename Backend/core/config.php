    <?php
    define('BASE_PATH', dirname(__DIR__));
    // Dynamically determine the base URL — dirname strips /index.php from the path
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // SCRIPT_NAME is /MyTravelMate-main/index.php — take the directory
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $base_url = rtrim($protocol . "://" . $host . $script_dir, '/');
    define('BASE_URL', $base_url);
