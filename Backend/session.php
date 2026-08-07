<?php
session_start();
if (!isset($_SESSION['sess_name'])) {
    header("location:../../Backend");
    exit();
}
