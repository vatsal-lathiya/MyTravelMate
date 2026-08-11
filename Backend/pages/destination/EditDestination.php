<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
if (!(isset($_GET["edit"]))) {
    die("ID Not Provided");
}

$sql_query = "SELECT * from tbl_destination WHERE dest_id=" . intval($_GET["edit"]);
$result = mysqli_query($conn, $sql_query);
if (mysqli_num_rows($result) <= 0) {
    die("Data Not Found");
}
$row = mysqli_fetch_assoc($result);
$msg = "";

if (isset($_POST["update_data"])) {
    $dest_name = mysqli_real_escape_string($conn, $_POST["edit_dest_name"]);
    $dest_desc = mysqli_real_escape_string($conn, $_POST["edit_dest_desc"]);
    $best_time = mysqli_real_escape_string($conn, $_POST["best_time"]);
    $status = mysqli_real_escape_string($conn, $_POST["dest_status"]);

    if (!empty($_FILES["dest_img"]["name"])) {
        $dest_img = $_FILES["dest_img"]["name"];
        $tmp = $_FILES["dest_img"]["tmp_name"];
        move_uploaded_file($tmp, BASE_PATH . "/public/Uploads/dest_img/" . $dest_img);
    } else {
        $dest_img = $_POST["old_image"];
    }

    if (empty($dest_name) || empty($dest_desc) || empty($best_time) || empty($status)) {
        $msg = "<div style='color:var(--danger);'>Fields Must Not Be Empty</div>";
    } else {
        $sql_query = "UPDATE tbl_destination SET dest_img='$dest_img',dest_name='$dest_name',dest_desc='$dest_desc',dest_besttime='$best_time',dest_status='$status' WHERE dest_id='" . intval($_GET["edit"]) . "'";
        $update_res = mysqli_query($conn, $sql_query);

        if ($update_res) {
            header("Location: " . BASE_URL . "/destination");
            exit();
        } else {
            $msg = "<div style='color:var(--danger);'>" . mysqli_error($conn) . "</div>";
        }
    }
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Edit Destination</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/destination" class="btn btn-secondary">Back to List</a>
        </div>
    </header>

    <div class="content-body">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <?php if ($msg != "")
                echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Destination ID</label>
                    <input type="text" class="form-control" name="dest_id" value="<?php echo $row["dest_id"]; ?>"
                        disabled>
                </div>

                <div class="form-group">
                    <label class="form-label">Current Image</label>
                    <div style="margin-bottom: 0.5rem;">
                        <img src="<?php echo BASE_URL; ?>/public/Uploads/dest_img/<?php echo $row['dest_img']; ?>"
                            style="border-radius:var(--radius-md); max-width:200px;" alt="Current Image">
                    </div>
                    <small style="color:var(--text-muted); display:block; margin-bottom:0.5rem;">Current file:
                        <?php echo $row['dest_img']; ?>
                    </small>
                    <input type="hidden" name="old_image" value="<?php echo $row['dest_img']; ?>">
                    <input type="file" class="form-control" name="dest_img">
                    <small style="color:var(--text-muted);">Leave empty to keep current image.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_dest_name">Destination Name</label>
                    <input type="text" class="form-control" name="edit_dest_name"
                        value="<?php echo $row["dest_name"]; ?>" id="edit_dest_name" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_dest_desc">Destination Description</label>
                    <textarea class="form-control" name="edit_dest_desc" id="edit_dest_desc" rows="8"
                        required><?php echo htmlspecialchars(trim($row["dest_desc"])); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="best_time">Best Time</label>
                    <input type="text" class="form-control" name="best_time" value="<?php echo $row["dest_besttime"] ?>"
                        id="best_time" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="dest_status">Destination Status</label>
                    <select class="form-control" name="dest_status" id="dest_status">
                        <option value="Open" <?php if ($row["dest_status"] == "Open")
                            echo "selected"; ?>>Open</option>
                        <option value="Close" <?php if ($row["dest_status"] == "Close")
                            echo "selected"; ?>>Close
                        </option>
                    </select>
                </div>

                <div style="display:flex; gap:1rem;">
                    <button type="submit" class="btn btn-primary" name="update_data">Update Destination</button>
                    <a href="<?php echo BASE_URL; ?>/destination" class="btn btn-secondary"
                        style="background:#6B7280; text-decoration:none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>