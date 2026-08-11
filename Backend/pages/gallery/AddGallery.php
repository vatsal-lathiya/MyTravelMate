
<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add_data"])) {
        $dest_id = $_POST["dest_name"];
        if (empty($dest_id)) {
            $msg = "<div style='color:var(--danger);'>Please choose a destination.</div>";
        } else if (!empty($_FILES["gal_img"]["name"])) {
            $gal_img = $_FILES["gal_img"]["name"];
            $tmp = $_FILES["gal_img"]["tmp_name"];
            move_uploaded_file($tmp, BASE_PATH . "/public/Uploads/gallery_img/" . $gal_img);
            
            $stmt = $conn->prepare("INSERT INTO tbl_gallery (g_img, dest_id) VALUES (?, ?)");
            $stmt->bind_param("si", $gal_img, $dest_id);
            if ($stmt->execute()) {
                header("Location: " . BASE_URL . "/gallery");
                exit();
            } else {
                $msg = "<div style='color:var(--danger);'>" . $stmt->error . "</div>";
            }
        } else {
            $msg = "<div style='color:var(--danger);'>Please select an image.</div>";
        }
    }
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Add Gallery Image</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/gallery" class="btn btn-secondary">Back to List</a>
        </div>
    </header>

    <div class="content-body">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <?php if($msg != "") echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>
            <form enctype="multipart/form-data" method="POST">
                <div class="form-group">
                    <label class="form-label" for="dest_name">Destination Name</label>
                    <select class="form-control" name="dest_name" id="dest_name" required>
                        <option value="">Choose Destination</option>
                        <?php
                        $sql = "SELECT dest_id,dest_name from tbl_destination";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row["dest_id"]}'>{$row["dest_name"]}</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="gal_img">Destination Gallery Image</label>
                    <input type="file" class="form-control" name="gal_img" id="gal_img" required>
                </div>

                <div style="display:flex; gap:1rem;">
                    <button type="submit" class="btn btn-primary" name="add_data">Add Image</button>
                    <button type="reset" class="btn btn-secondary" style="background:#6B7280;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>