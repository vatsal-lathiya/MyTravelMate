<?php
    session_start();
    if(!isset($_SESSION['sess_name'])){
        header("location:index.php");
        exit();
    }
    echo "Hello " . $_SESSION['sess_name'];
?>