<?php  ?>
<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
if (!isset($_GET["edit"]) || $_GET["edit"] === "") {
    header("Location: " . BASE_URL . "/blog");
    exit();
}

$sql = 'SELECT * FROM tbl_blogs where blog_id = ' . intval($_GET["edit"]);
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Blog not found");
}

$msg = "";
if (isset($_POST['edit_data'])) {
    $edit_blog_title = $_POST['blog_title'];
    $edit_blog_desc = $_POST['blog_desc'];
    $edit_dest_name = $_POST['blog_dest_id'];

    if (empty($edit_blog_title) || empty($edit_blog_desc) || empty($edit_dest_name)) {
        $msg = "<div style='color:var(--danger);'>All Fields Are Required</div>";
    } else {
        if (!empty($_FILES["blog_img"]["name"])) {
            $edit_blog_img = $_FILES["blog_img"]["name"];
            $tmp = $_FILES["blog_img"]["tmp_name"];
            move_uploaded_file($tmp, BASE_PATH . "/public/Uploads/Blog_img/" . $edit_blog_img);
        } else {
            $edit_blog_img = $_POST["old_img"];
        }

        $stmt = $conn->prepare('UPDATE tbl_blogs SET blog_title=?,blog_img=?,blog_desc=?,dest_id=? WHERE blog_id=' . $row["blog_id"]);
        $stmt->bind_param('sssi', $edit_blog_title, $edit_blog_img, $edit_blog_desc, $edit_dest_name);

        if ($stmt->execute() === TRUE) {
            header("Location: " . BASE_URL . "/blog");
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
        <h1 class="page-title">Edit Blog</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/blog" class="btn btn-secondary">Back to List</a>
        </div>
    </header>

    <div class="content-body">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <?php if ($msg != "") echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>
            <form action="" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                    <label class="form-label" for="blog_ID">Blog ID</label>
                    <input type="text" class="form-control" name="blog_id" value="<?php echo $row["blog_id"] ?>" id="blog_id" disabled>
                </div>

                <div class="form-group">
                    <label class="form-label" for="blog_title">Blog Title</label>
                    <input type="text" class="form-control" name="blog_title" value="<?php echo htmlspecialchars($row["blog_title"]); ?>" id="blog_title" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="blog_img">Blog Image</label>
                    <div style="margin-bottom: 0.5rem;">
                        <img src="<?php echo BASE_URL; ?>/public/Uploads/Blog_img/<?php echo $row['blog_img']; ?>" style="border-radius:var(--radius-md); max-width:200px;" alt="Current Image">
                    </div>
                    <small style="color:var(--text-muted); display:block; margin-bottom:0.5rem;">Current file: <?php echo $row['blog_img']; ?></small>
                    <input type="hidden" name="old_img" value="<?php echo $row['blog_img']; ?>">
                    <input type="file" class="form-control" name="blog_img" id="blog_img">
                    <small style="color:var(--text-muted);">Leave empty to keep current image.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="blog_desc">Blog Description</label>
                    <textarea class="form-control" name="blog_desc" rows="10" id="blog_desc" required><?php echo htmlspecialchars(trim($row["blog_desc"])); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="blog_dest_id">Choose Destination</label>
                    <select class="form-control" name="blog_dest_id" id="blog_dest_id" required>
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

                <div style="display:flex; gap:1rem;">
                    <button type="submit" class="btn btn-primary" name="edit_data">Update Blog</button>
                    <a href="<?php echo BASE_URL; ?>/blog" class="btn btn-secondary" style="background:#6B7280; text-decoration:none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>