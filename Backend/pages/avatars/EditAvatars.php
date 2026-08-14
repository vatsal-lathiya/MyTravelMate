<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php

if (!isset($_GET["edit"]) || $_GET["edit"] === "") {
    header("Location: " . BASE_URL . "/avatars");
    exit();
}

$id = $_GET['edit'];

$query = "SELECT * FROM tbl_avatar WHERE a_id = $id";
$result = mysqli_query($conn, $query);

$edit_row = mysqli_fetch_assoc($result);

if (isset($_POST['update_avatar'])) {

    $id       = $_POST['a_id'];
    $name     = $_POST['a_name'];
    $category = $_POST['a_category'];
    $status   = $_POST['a_status'];

    // Check if new image is selected
    if (!empty($_FILES['a_img']['name'])) {

        $extension = pathinfo($_FILES['a_img']['name'], PATHINFO_EXTENSION);

        // Example: Profile_1.png
        $image_name = $name . '.' . $extension;

        $upload_path = BASE_PATH . '/public/Uploads/avatar/' . $image_name . '.jpg';

        move_uploaded_file(
            $_FILES['a_img']['tmp_name'],
            $upload_path
        );

        $query = "UPDATE tbl_avatar SET
                    a_name = '$name',
                    a_img = '$image_name',
                    a_category = '$category',
                    a_status = '$status'
                  WHERE a_id = $id";
    } else {

        // No new image
        $query = "UPDATE tbl_avatar SET
                    a_name = '$name',
                    a_category = '$category',
                    a_status = '$status'
                  WHERE a_id = $id";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: " . BASE_URL . "/avatars");
        exit();
    } else {

        echo 'Update Failed: ' . mysqli_error($conn);
    }
}

?>

?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>
<main class="main-content">
    <header class="top-header">
        <h1> Edit Avatars </h1>
    </header>
    <div class="content-body">
        <form method='POST' enctype='multipart/form-data' class='form-grid'>

            <input type='hidden' name='a_id' value='<?php echo $edit_row["a_id"]; ?>'>

            <div class='form-group'>
                <label>Avatar Name</label>
                <input type='text' name='a_name'
                    value='<?php echo htmlspecialchars($edit_row["a_name"]); ?>'>
            </div>

            <div class='form-group'>
                <label>Avatar Category</label>
                <input type='text' name='a_category'
                    value='<?php echo htmlspecialchars($edit_row["a_category"]); ?>'>
            </div>

            <div class='form-group'>
                <label>Profile Image</label>
                <input type='file' name='a_img' accept='image/*'>
            </div>

            <div class='form-group'>
                <label>Status</label>
                <select name='a_status'>
                    <option value='Active'>Active</option>
                    <option value='Inactive'>Inactive</option>
                </select>
            </div>

            <div class='form-group full-width'>
                <button type='submit' name='update_avatar'>Update Avatar</button>
            </div>

        </form>
    </div>
</main>