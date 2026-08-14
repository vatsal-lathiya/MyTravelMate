<?php
session_start();
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/dbconn.php';

$route = isset($_GET['route']) ? $_GET['route'] : 'dashboard';
$route = rtrim($route, '/');

$routes = [
    '' => 'pages/dashboard/index.php',
    'dashboard' => 'pages/dashboard/index.php',
    'login' => 'pages/auth/index.php',
    'logout' => 'pages/auth/Logout.php',
    'landing' => 'pages/auth/landing.php',
    'booking' => 'pages/booking/index.php',
    'users' => 'pages/users/index.php',
    'feedbacks' => 'pages/feedbacks/index.php',
    'get_travel_cost.php' => 'get_travel_cost.php',
];

$file_to_load = '';

if (array_key_exists($route, $routes)) {
    $file_to_load = __DIR__ . '/' . $routes[$route];
} else {
    $parts = explode('/', $route);
    $module = strtolower($parts[0]);
    $action = isset($parts[1]) ? $parts[1] : 'index';

    $action_map = [
        'index' => 'index.php',
        'add' => 'Add' . ucfirst($module) . '.php',
        'edit' => 'Edit' . ucfirst($module) . '.php',
        'read' => 'Read' . ucfirst($module) . '.php',
    ];

    $filename = isset($action_map[$action]) ? $action_map[$action] : $action;
    if ($module === 'states' && $action === 'index') {
        $filename = 'States.php';
    }

    $file_to_load = __DIR__ . "/pages/{$module}/{$filename}";
}

if (file_exists($file_to_load)) {
    require $file_to_load;
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    
}
