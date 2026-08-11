    <?php require BASE_PATH . "/components/auth_check.php" ?>
    <?php
    if (!isset($_GET['edit']) || empty($_GET['edit'])) {
        header("Location: States.php");
        exit();
    }

    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $sql = "SELECT * FROM tbl_states WHERE state_id=$id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        die("State not found");
    }

    $msg = "";
    if (isset($_POST['update_data'])) {
        $name = $_POST['update_name'];
        $code = $_POST['update_code'];
        $status = $_POST['update_status'];

        if (empty($name) || empty($code) || empty($status)) {
            $msg = "<div style='color:var(--danger);'>Please fill all the fields</div>";
        } else {
            $sql_query = "UPDATE tbl_states SET state_name='$name',state_code='$code',state_status='$status' WHERE state_id=$id";
            $update_res = mysqli_query($conn, $sql_query);
            if ($update_res) {
                header("Location: States.php");
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
            <h1 class="page-title">Edit State</h1>
            <div>
                <a href="States.php" class="btn btn-secondary">Back to List</a>
            </div>
        </header>

        <div class="content-body">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <?php if ($msg != "") echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label" for="update_id">State ID</label>
                        <input type="text" class="form-control" name="update_id" id="update_id" value="<?php echo $row['state_id']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="update_name">State Name</label>
                        <input type="text" class="form-control" name="update_name" id="update_name" value="<?php echo $row['state_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="update_code">State Code</label>
                        <input type="text" class="form-control" name="update_code" id="update_code" value="<?php echo $row['state_code']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="update_status">Status</label>
                        <select class="form-control" name="update_status" id="update_status" required>
                            <option value="Active" <?php if ($row['state_status'] == 'Active') echo 'selected'; ?>>Active</option>
                            <option value="Unactive" <?php if ($row['state_status'] == 'Unactive') echo 'selected'; ?>>Unactive</option>
                        </select>
                    </div>
                    <div style="display:flex; gap:1rem;">
                        <button type="submit" class="btn btn-primary" name="update_data">Update State</button>
                        <a href="States.php" class="btn btn-secondary" style="background:#6B7280; text-decoration:none;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require BASE_PATH . "/components/footer.php" ?>