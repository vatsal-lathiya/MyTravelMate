<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $state_id = $_POST['state_id'];
    $state_name = $_POST['state_name'];
    $state_code = $_POST['state_code'];
    $state_status = $_POST['state_status'];

    if (empty($state_id) || empty($state_name) || empty($state_code) || empty($state_status)) {
        $msg = "<div style='color:var(--danger);'>Please fill all the required fields!</div>";
    } else {
        $sql_query = "INSERT INTO tbl_states (state_id, state_name, state_code, state_status) VALUES ('$state_id', '$state_name', '$state_code', '$state_status')";
        $result = mysqli_query($conn, $sql_query);

        if ($result) {
            header("Location: " . BASE_URL . "/states");
            exit();
        } else {
            $msg = "<div style='color:var(--danger);'>Data Not Added Successfully: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Add State</h1>
        <div>
            <a href="States.php" class="btn btn-secondary">Back to List</a>
        </div>
    </header>

    <div class="content-body">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <?php if ($msg != "") echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label" for="state_id">State ID</label>
                    <input type="text" class="form-control" name="state_id" id="state_id" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="state_name">State Name</label>
                    <input type="text" class="form-control" name="state_name" id="state_name" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="state_code">State Code</label>
                    <input type="text" class="form-control" name="state_code" id="state_code" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="state_status">Status</label>
                    <select class="form-control" name="state_status" id="state_status" required>
                        <option value="Active">Active</option>
                        <option value="Unactive">Unactive</option>
                    </select>
                </div>
                <div style="display:flex; gap:1rem;">
                    <button type="submit" class="btn btn-primary">Add State</button>
                    <button type="reset" class="btn btn-secondary" style="background:#6B7280;">Reset</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>