<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php

if (isset($_POST['insert_avatar'])) {

    $a_name = mysqli_real_escape_string($conn, $_POST['a_name']);
    $a_category = mysqli_real_escape_string($conn, $_POST['a_category']);
    $a_status = mysqli_real_escape_string($conn, $_POST['a_status']);

    $image_name = $_FILES['a_img']['name'];
    $image_tmp = $_FILES['a_img']['tmp_name'];

    $upload_dir = BASE_PATH . "/public/Uploads/Avatar_img/";

    $new_image_name = trim($a_name) . ".jpg";

    if (move_uploaded_file($image_tmp, $upload_dir . $new_image_name)) {

        $query = "INSERT INTO tbl_avatar 
                  (a_name, a_img, a_category, a_status)
                  VALUES 
                  ('$a_name', '$new_image_name', '$a_category', '$a_status')";

        if (mysqli_query($conn, $query)) {
            echo "Avatar added successfully.";
            header("Location:" . BASE_URL . "/avatars");
        } else {
            echo "Insert Failed: " . mysqli_error($conn);
        }
    } else {
        echo "Image upload failed.";
    }
}

?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>
<main class="main-content">
    <header class="top-header">
        <h1> Add Avatar </h1>
    </header>
    <div class="content-body">
        <div>

            <h2>Add Avatar</h2>

            <form action="" method="POST" enctype="multipart/form-data">

                <div>
                    <label for="a_name">Avatar Name</label>
                    <input
                        type="text"
                        name="a_name"
                        id="a_name"
                        required>
                </div>

                <div>
                    <label for="a_img">Avatar Profile</label>
                    <input
                        type="file"
                        name="a_img"
                        id="a_img"
                        accept="image/*"
                        required>
                </div>

                <div>
                    <label for="a_category">Avatar Category</label>
                    <select name="a_category" id="a_category" required>
                        <option value="">Select Category</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div>
                    <label for="a_status">Status</label>
                    <select name="a_status" id="a_status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit" name="insert_avatar">
                    Add Avatar
                </button>

            </form>

        </div>
    </div>
</main>