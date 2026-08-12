<?php
require BASE_PATH . "/components/auth_check.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = mysqli_real_escape_string($conn, $_POST['pkg_title']);
    $desc = mysqli_real_escape_string($conn, $_POST['pkg_desc']);
    $duration = mysqli_real_escape_string($conn, $_POST['pkg_duration']);
    $state_id = intval($_POST['state_id']);
    $price = floatval($_POST['pkg_price']);
    $status = mysqli_real_escape_string($conn, $_POST['pkg_status']);

    $image_name = "";

    if (isset($_FILES['pkg_img']) && $_FILES['pkg_img']['error'] == 0) {

        $image_name = time() . "_" . basename($_FILES['pkg_img']['name']);

        $upload_path = BASE_PATH . "/public/Uploads/Package_img/";

        move_uploaded_file(
            $_FILES['pkg_img']['tmp_name'],
            $upload_path . $image_name
        );
    }

    $sql = "INSERT INTO tbl_package
            (
                pkg_title,
                pkg_img,
                pkg_desc,
                pkg_duration,
                state_id,
                pkg_price,
                pkg_status
            )
            VALUES
            (
                '$title',
                '$image_name',
                '$desc',
                '$duration',
                $state_id,
                $price,
                '$status'
            )";

    if (mysqli_query($conn, $sql)) {

        header("Location: " . BASE_URL . "/package");
        exit();
    } else {

        die("Insert Error: " . mysqli_error($conn));
    }
}
?>

<?php require BASE_PATH . "/components/header.php"; ?>
<?php require BASE_PATH . "/components/sidebar.php"; ?>

<main class="main-content">

    <header class="top-header">
        <h2>Add Package</h2>
    </header>

    <div class="content-body">

        <form method="POST"
            enctype="multipart/form-data">

            <label>Package Title</label>
            <input
                type="text"
                name="pkg_title"
                required>

            <br><br>

            <label>Package Image</label>
            <input
                type="file"
                name="pkg_img"
                accept="image/*"
                required>

            <br><br>

            <label>Description</label>
            <textarea
                name="pkg_desc"
                required></textarea>

            <br><br>

            <label>Duration</label>
            <input
                type="text"
                name="pkg_duration"
                placeholder="6 Days / 5 Nights"
                required>

            <br><br>

            <label>State</label>

            <select class="form-control" name="state_id" id="state_id" required>
                <?php
                $state_res = mysqli_query($conn, "SELECT * FROM tbl_states");
                while ($s = mysqli_fetch_assoc($state_res)) {
                    echo "<option value='{$s['state_id']}'>{$s['state_name']}</option>";
                }
                ?>
            </select>

            <br><br>

            <label>Price</label>

            <input
                type="number"
                name="pkg_price"
                min="0"
                step="0.01"
                required>

            <br><br>

            <label>Status</label>

            <select name="pkg_status">

                <option value="Active">
                    Active
                </option>

                <option value="Inactive">
                    Inactive
                </option>

            </select>

            <br><br>

            <button type="submit">
                Add Package
            </button>

        </form>

    </div>

</main>