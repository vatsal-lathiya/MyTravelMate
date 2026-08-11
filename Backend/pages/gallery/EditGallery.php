<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
if (!isset($_GET["edit"]) || $_GET["edit"] === "") {
    header("Location: " . BASE_URL . "/gallery");
    exit();
}

$sql = 'SELECT * FROM tbl_gallery where g_id = ' . intval($_GET["edit"]);
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Gallery image not found");
}

$msg = "";
if (isset($_POST['edit_data'])) {
    $edit_dest_name = $_POST['edit_dest_name'];
    if (empty($edit_dest_name)) {
        $msg = "<div style='color:var(--danger);'>Please choose a destination.</div>";
    } else {
        if (!empty($_FILES["edit_img"]["name"])) {
            $g_img = $_FILES["edit_img"]["name"];
            $tmp = $_FILES["edit_img"]["tmp_name"];
            move_uploaded_file($tmp, BASE_PATH . "/public/Uploads/gallery_img/" . $g_img);
        } else {
            $g_img = $_POST["old_image"];
        }

        $stmt = $conn->prepare('UPDATE tbl_gallery SET dest_id=?, g_img=? WHERE g_id=?');
        $stmt->bind_param('isi', $edit_dest_name, $g_img, $row["g_id"]);

        if ($stmt->execute() === TRUE) {
            header("Location: " . BASE_URL . "/gallery");
            exit();
        } else {
            $msg = "<div style='color:var(--danger);'>" . $stmt->error . "</div>";
        }
    }
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Edit Gallery Image</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/gallery" class="btn btn-secondary">Back to List</a>
        </div>
    </header>

    <div class="content-body">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <?php if ($msg != "") echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>
            <form action="" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                    <label class="form-label" for="g_id">Gallery ID</label>
                    <input type="text" class="form-control" name="g_id" id="g_id" value="<?php echo $row["g_id"]; ?>" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_dest_name">Destination Name</label>
                    <select class="form-control" name="edit_dest_name" id="edit_dest_name" required>
                        <option value="">Choose Destination</option>
                        <?php
                        $sql = "SELECT dest_id,dest_name from tbl_destination";
                        $result = mysqli_query($conn, $sql);
                        while ($rowdata = mysqli_fetch_assoc($result)) {
                            $selected = ($rowdata["dest_id"] == $row["dest_id"]) ? 'selected' : '';
                            echo "<option value='{$rowdata["dest_id"]}' $selected>{$rowdata["dest_name"]}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Current Image</label>
                    <div style="margin-bottom: 0.5rem;">
                        <img
                            src="<?php echo BASE_URL; ?>/public/Uploads/gallery_img/<?php echo htmlspecialchars($row['g_img']); ?>"
                            style="border-radius:var(--radius-md); max-width:200px;"
                            alt="Current Image">
                        <small style="color:var(--text-muted); display:block; margin-bottom:0.5rem;">Current file: <?php echo $row['g_img']; ?></small>
                        <input type="hidden" name="old_image" value="<?php echo $row['g_img']; ?>">
                        <input type="file" class="form-control" name="edit_img" id="edit_img">
                        <small style="color:var(--text-muted);">Leave empty to keep current image.</small>
                    </div>

                    <div style="display:flex; gap:1rem;">
                        <button type="submit" class="btn btn-primary" name="edit_data">Update Image</button>
                        <a href="<?php echo BASE_URL; ?>/gallery" class="btn btn-secondary" style="background:#6B7280; text-decoration:none;">Cancel</a>
                    </div>
            </form>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>