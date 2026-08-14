<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
// Delete Destination Method
if (isset($_POST["delete_data"])) {
    $id = (int)$_POST["delete_id"];
    $stmt = $conn->prepare("DELETE FROM tbl_destination WHERE dest_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/destination");
        exit();
    } else {
        $error = $stmt->error;
    }
    $stmt->close();
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>  
<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Destinations</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/destination/add" class="btn btn-primary">Add Destination</a>
        </div>
    </header>

    <div class="content-body">
        <?php if (isset($error)): ?>
            <div style="color: var(--danger); margin-bottom: 1rem;"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>State</th>
                            <th>Best Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_query = "SELECT tbl_destination.dest_id,tbl_destination.dest_img,tbl_destination.dest_name,tbl_destination.dest_desc,tbl_states.state_name,tbl_destination.dest_besttime,tbl_destination.dest_status
                                      FROM tbl_destination,tbl_states
                                      WHERE tbl_destination.state_id = tbl_states.state_id";
                        $result = mysqli_query($conn, $sql_query);
                        $count = 0;

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $count += 1;
                                echo "<tr>
                                        <td>{$count}</td>
                                        <td><img src='" . BASE_URL . "/public/Uploads/dest_img/{$row["dest_img"]}' width='80' height='60' alt=''></td>
                                        <td>{$row['dest_name']}</td>
                                        <td>" . substr($row['dest_desc'], 0, 30) . "...</td>
                                        <td>{$row['state_name']}</td>
                                        <td>{$row['dest_besttime']}</td>
                                        <td><span class='btn-sm btn-secondary' style='background: " . ($row['dest_status'] == 'Open' ? 'var(--secondary)' : 'var(--danger)') . "'>{$row['dest_status']}</span></td>
                                        <td>
                                            <div class='action-btns'>
                                                <a href='" . BASE_URL . "/destination/edit?edit={$row['dest_id']}' class='btn btn-sm btn-primary'>Edit</a>
                                                <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure?\");'>
                                                    <input type='hidden' name='delete_id' value='{$row['dest_id']}'>
                                                    <button type='submit' name='delete_data' class='btn btn-sm btn-danger'>Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center;'>No Destinations Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>