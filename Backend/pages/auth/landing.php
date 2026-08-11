<?php
    session_start();
    if(!isset($_SESSION['sess_name'])){
        header("Location: " . BASE_URL . "/auth");
        exit();
    }
    echo "Hello " . $_SESSION['sess_name'];
?>