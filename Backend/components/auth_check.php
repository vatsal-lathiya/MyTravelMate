<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['sess_name'])) {
    header("Location: " . BASE_URL . "/login");
    exit();
}
