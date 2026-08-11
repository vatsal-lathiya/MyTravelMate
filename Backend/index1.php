<?php
/*
require_once __DIR__ . "/core/dbconn.php";
require_once __DIR__ . "/core/config.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['adm_email'];
    $password = $_POST['adm_psw'];

    if (empty($email) || empty($password)) {
        echo "<script>
            alert('All fields are required');
            window.location.href='index.php';
        </script>";
        exit();
    }

    $sql = "SELECT * FROM tbl_admauth WHERE adm_email='$email' AND adm_psw='$password'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("SQL Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['sess_name'] = $row['name'];

        echo "<script>alert('Login Successful')</script>";
        header("Location: ./Dashboard");
    } else {
        echo "<script>alert('Admin Not Exist')</script>";
    }
}

echo "Hello";
*/
?>

<!--
<form action="index.php" method="post">
    Email:
    <input type="email" name="adm_email" id="adm_email">

    <br><br>

    Password:
    <input type="password" name="adm_psw" id="adm_psw">

    <br>

    <input type="submit" name="login" value="Log In">
</form>
-->
