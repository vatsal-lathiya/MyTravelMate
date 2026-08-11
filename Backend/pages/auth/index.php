<?php
if (isset($_SESSION['sess_name'])) {
    header("Location: " . BASE_URL . "/dashboard");
    exit();
}
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['adm_email'];
    $password = $_POST['adm_psw'];

    if (empty($email) || empty($password)) {
        $error = "All Fields Required!";
    } else {
        $sql = "SELECT * FROM tbl_admauth WHERE adm_email='$email' AND adm_psw='$password'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("SQL Error: " . mysqli_error($conn));
        }
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['sess_name'] = $row['adm_email'];
            header("Location: " . BASE_URL . "/dashboard");
            exit();
        } else {
            $error = "Invalid Credentials";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MTM</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/admin.css">
</head>

<body class="login-body">
    <div class="login-card">
        <h1>MTM Admin</h1>
        <?php if ($error != ""): ?>
            <div style="color: var(--danger); margin-bottom: 1rem; padding: 0.75rem; background: rgba(239, 68, 68, 0.1); border-radius: var(--radius-md);">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group" style="text-align: left;">
                <label class="form-label" for="adm_email">Email Address</label>
                <input type="email" class="form-control" name="adm_email" id="adm_email" placeholder="admin@example.com" required />
            </div>
            <div class="form-group" style="text-align: left;">
                <label class="form-label" for="adm_psw">Password</label>
                <input type="password" class="form-control" name="adm_psw" id="adm_psw" placeholder="••••••••" required />
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width: 100%; padding: 0.875rem; font-size: 1rem;">Log In</button>
        </form>
    </div>
</body>

</html>