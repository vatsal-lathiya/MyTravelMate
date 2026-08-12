<?php

require BASE_PATH . "/components/auth_check.php";

if (!isset($_GET['edit']) || $_GET['edit'] == "") {

    header("Location: " . BASE_URL . "/packages");
    exit();
}

$id = intval($_GET['edit']);

$sql = "SELECT * FROM tbl_package WHERE pkg_id = $id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Package not found");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = mysqli_real_escape_string(
        $conn,
        $_POST['pkg_title']
    );

    $desc = mysqli_real_escape_string(
        $conn,
        $_POST['pkg_desc']
    );

    $duration = mysqli_real_escape_string(
        $conn,
        $_POST['pkg_duration']
    );

    $state_id = intval($_POST['state_id']);

    $price = floatval($_POST['pkg_price']);

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['pkg_status']
    );


    $image_sql = "";

    if (
        isset($_FILES['pkg_img']) &&
        $_FILES['pkg_img']['error'] == 0
    ) {

        $image_name =
            time() . "_" .
            basename($_FILES['pkg_img']['name']);

        $upload_path =
            BASE_PATH .
            "/public/Uploads/Package_img/";

        move_uploaded_file(
            $_FILES['pkg_img']['tmp_name'],
            $upload_path . $image_name
        );

        $image_sql = ",
            pkg_img='$image_name'";
    }


    $update_sql = "UPDATE tbl_package SET

        pkg_title='$title',
        pkg_desc='$desc',
        pkg_duration='$duration',
        state_id=$state_id,
        pkg_price=$price,
        pkg_status='$status'
        $image_sql

        WHERE pkg_id=$id";


    if (mysqli_query($conn, $update_sql)) {

        header(
            "Location: " .
                BASE_URL .
                "/packages"
        );

        exit();
    } else {

        die("Update Error: " .
            mysqli_error($conn));
    }
}

?>

<?php require BASE_PATH . "/components/header.php"; ?>
<?php require BASE_PATH . "/components/sidebar.php"; ?>

<main class="main-content">

    <header class="top-header">
        <h2>Edit Package</h2>
    </header>

    <div class="content-body">

        <form
            method="POST"
            enctype="multipart/form-data">

            <label>Package Title</label>

            <input
                type="text"
                name="pkg_title"
                value="<?= htmlspecialchars($row['pkg_title']) ?>"
                required>

            <br><br>


            <label>Current Image</label>

            <br>

            <?php if (!empty($row['pkg_img'])) { ?>

                <img
                    src="<?= BASE_URL ?>/public/Uploads/Package_img/<?= htmlspecialchars($row['pkg_img']) ?>"
                    width="200">

            <?php } ?>

            <br><br>

            <label>Change Image</label>

            <input
                type="file"
                name="pkg_img"
                accept="image/*">

            <br><br>


            <label>Description</label>

            <textarea
                name="pkg_desc"
                required><?= htmlspecialchars($row['pkg_desc']) ?></textarea>

            <br><br>


            <label>Duration</label>

            <input
                type="text"
                name="pkg_duration"
                value="<?= htmlspecialchars($row['pkg_duration']) ?>"
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
                value="<?= $row['pkg_price'] ?>"
                min="0"
                step="0.01"
                required>

            <br><br>


            <label>Status</label>

            <select name="pkg_status">

                <option
                    value="Active"
                    <?= $row['pkg_status'] == 'Active' ? 'selected' : '' ?>>
                    Active
                </option>

                <option
                    value="Inactive"
                    <?= $row['pkg_status'] == 'Inactive' ? 'selected' : '' ?>>
                    Inactive
                </option>

            </select>

            <br><br>

            <button type="submit">
                Update Package
            </button>

            <a href="<?= BASE_URL ?>/package">
                Cancel
            </a>

        </form>

    </div>

</main>