<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add_destination"])) {
        $dest_name = $_POST["dest_name"];
        $dest_state = $_POST["dest_state"];
        $dest_desc = $_POST["dest_desc"];
        $dest_time = $_POST["dest_besttime"];
        $dest_status = $_POST["dest_status"];
        $dest_img = $_FILES["dest_img"]['name'];
        $temp_name = $_FILES['dest_img']['tmp_name'];

        if (empty($dest_name) || empty($dest_desc) || empty($dest_state) || empty($dest_time) || empty($dest_status) || empty($dest_img)) {
            $msg = "<div style='color:var(--danger);'>All Fields Required</div>";
        } else {
            move_uploaded_file($temp_name, BASE_PATH . "/public/Uploads/dest_img/$dest_img");
            $stmt = $conn->prepare("INSERT INTO tbl_destination (dest_name,state_id,dest_desc,dest_besttime,dest_img,dest_status) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("sissss", $dest_name, $dest_state, $dest_desc, $dest_time, $dest_img, $dest_status);

            if ($stmt->execute()) {
                header("Location: " . BASE_URL . "/destination");
                exit();
            } else {
                $msg = "<div style='color:var(--danger);'>" . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title"> Add Destination </h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/destination" class="btn btn-secondary">Back to List</a>
        </div>
    </header>

    <div class="content-body">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <?php if ($msg != "") echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>
            <form action="" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                    <label class="form-label" for="dest_name">Destination Name</label>
                    <input type="text" class="form-control" name="dest_name" id="dest_name" placeholder="Enter destination name" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="dest_desc">Destination Description</label>
                    <textarea class="form-control" name="dest_desc" id="dest_desc" rows="6" placeholder="Enter destination description" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="dest_img">Destination Image</label>
                    <input type="file" class="form-control" name="dest_img" id="dest_img" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="dest_state">Destination State</label>
                    <select class="form-control" name="dest_state" id="dest_state" required>
                        <?php
                        $state_res = mysqli_query($conn, "SELECT * FROM tbl_states");
                        while ($s = mysqli_fetch_assoc($state_res)) {
                            echo "<option value='{$s['state_id']}'>{$s['state_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="dest_besttime">Destination Best Time</label>
                    <input type="text" class="form-control" name="dest_besttime" id="dest_besttime" placeholder="e.g. October to March" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="dest_status">Destination Status</label>
                    <select class="form-control" name="dest_status" id="dest_status">
                        <option value="Open">Open</option>
                        <option value="Close">Close</option>
                    </select>
                </div>
                <div style="display:flex; gap:1rem;">
                    <button type="submit" class="btn btn-primary" name="add_destination">Add Destination</button>
                    <button type="reset" class="btn btn-secondary" style="background:#6B7280;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>