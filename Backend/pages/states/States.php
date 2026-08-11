<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
// Delete Data
$msg = "";
if (isset($_POST['remove_state'])) {
    $id = $_POST['target_id'];
    $sql_query = "DELETE FROM tbl_states WHERE state_id='$id'";
    $result = mysqli_query($conn, $sql_query);
    if ($result) {
        header("Location: " . BASE_URL . "/states");
        exit();
    } else {
        $msg = "<div style='color:var(--danger);'>State Has Destinations. You Cannot Delete It.</div>";
    }
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">States</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/states/add" class="btn btn-primary">Add State</a>
        </div>
    </header>

    <div class="content-body">
        <?php if ($msg != "") echo "<div style='margin-bottom:1rem;'>$msg</div>"; ?>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>State Name</th>
                            <th>State Code</th>
                            <th>Destinations Count</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = "SELECT tbl_states.state_id, tbl_states.state_name, tbl_states.state_code, COUNT(tbl_destination.dest_id) AS No_of_Destination, tbl_states.state_status
                                      FROM tbl_states
                                      LEFT JOIN tbl_destination ON tbl_states.state_id = tbl_destination.state_id
                                      GROUP BY tbl_states.state_id";
                        $result = mysqli_query($conn, $sql_query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td>{$row['state_id']}</td>
                                        <td>{$row['state_name']}</td>
                                        <td>{$row['state_code']}</td>
                                        <td>{$row['No_of_Destination']}</td>
                                        <td><span class='btn-sm btn-secondary' style='background: " . ($row['state_status'] == 'Active' ? 'var(--secondary)' : 'var(--danger)') . "'>{$row['state_status']}</span></td>
                                        <td>
                                            <div class='action-btns'>
                                                <a href='" . BASE_URL . "/states/edit?edit={$row["state_id"]}' class='btn btn-sm btn-primary'>Edit</a>
                                                <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this state?\");'>
                                                    <input type='hidden' name='target_id' value='{$row['state_id']}'>
                                                    <button type='submit' name='remove_state' class='btn btn-sm btn-danger'>Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;'>No States Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>