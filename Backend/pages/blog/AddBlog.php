
<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_data"])) {
        $blog_title = $_POST["blog_title"];
        $blog_description = $_POST["blog_desc"];
        $blog_destination = $_POST["blog_dest_id"];
        if (empty($blog_title) || empty($blog_description) || empty($blog_destination)) {
            $msg = "<div style='color:var(--danger);'>All Fields Are Required</div>";
        } else if (empty($_FILES["blog_img"]["name"])) {
            $msg = "<div style='color:var(--danger);'>Please Select Image of Blog</div>";
        } else {
            $blog_img = $_FILES["blog_img"]["name"];
            $tmp = $_FILES["blog_img"]["tmp_name"];
            move_uploaded_file($tmp, BASE_PATH . "/public/Uploads/Blog_img/$blog_img");

            $stmt = $conn->prepare("INSERT INTO tbl_blogs(blog_title,blog_img,blog_desc,dest_id) VALUES (?,?,?,?)");
            $stmt->bind_param("sssi", $blog_title, $blog_img, $blog_description, $blog_destination);
            if ($stmt->execute() === TRUE) {
                $stmt->close();
                header("Location: " . BASE_URL . "/blog");
                exit();
            } else {
                $msg = "<div style='color:var(--danger);'>" . $stmt->error . "</div>";
            }
        }
    }
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Add Blog</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/blog" class="btn btn-secondary">Back to List</a>
        </div>
    </header>

    <div class="content-body">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <?php if($msg != "") echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>
            <form action="" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                    <label class="form-label" for="blog_title">Blog Title</label>
                    <input type="text" class="form-control" name="blog_title" id="blog_title" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="blog_img">Blog Image</label>
                    <input type="file" class="form-control" name="blog_img" id="blog_img" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="blog_desc">Blog Description</label>
                    <textarea class="form-control" name="blog_desc" id="blog_desc" rows="10" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="blog_dest_id">Choose Destination</label>
                    <select class="form-control" name="blog_dest_id" id="blog_dest_id" required>
                        <?php
                        $sql = "SELECT dest_id,dest_name from tbl_destination";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row["dest_id"]}'>{$row["dest_name"]}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div style="display:flex; gap:1rem;">
                    <button type="submit" class="btn btn-primary" name="add_data">Add Blog</button>
                    <button type="reset" class="btn btn-secondary" style="background:#6B7280;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>