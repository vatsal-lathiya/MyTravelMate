<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php

/* === INSERT === */
if (isset($_POST['add_vehicle'])) {
    $vehicle_type = $_POST['vehicle_type'];
    $stmt = $conn->prepare("INSERT INTO tbl_vehicletype(vehicle_type) VALUES(?)");
    $stmt->bind_param("s", $vehicle_type);
    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/vehicles");
        exit();
    } else {
        $error = $stmt->error;
    }
}

/* === UPDATE === */
if (isset($_POST['update_vehicle'])) {
    $id = $_POST['vehicletype_id'];
    $vehicle_type = $_POST['vehicle_type'];
    $stmt = $conn->prepare("UPDATE tbl_vehicletype SET vehicle_type=? WHERE vehicletype_id=?");
    $stmt->bind_param("si", $vehicle_type, $id);
    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/vehicles");
        exit();
    } else {
        $error = $stmt->error;
    }
}

/* === DELETE === */
if (isset($_POST['delete_vehicle'])) {
    $id = $_POST['vehicletype_id'];
    $stmt = $conn->prepare("DELETE FROM tbl_vehicletype WHERE vehicletype_id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/vehicles");
        exit();
    } else {
        $error = $stmt->error;
    }
}

/* === FETCH FOR EDIT === */
$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM tbl_vehicletype WHERE vehicletype_id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
}

/* === FETCH ALL === */
$result = mysqli_query($conn, "SELECT * FROM tbl_vehicletype ORDER BY vehicletype_id ASC");
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Vehicle Types</h1>
    </header>

    <div class="content-body">

        <?php if (isset($error)): ?>
            <div style="color: var(--danger); margin-bottom: 1rem; padding: 0.75rem; background: rgba(239,68,68,0.1); border-radius: var(--radius-md);">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">

            <!-- Add / Edit Form -->
            <div class="card">
                <h2 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--text-main);">
                    <?php echo $edit_row ? 'Edit Vehicle Type' : 'Add Vehicle Type'; ?>
                </h2>
                <form method="POST">
                    <?php if ($edit_row): ?>
                        <input type="hidden" name="vehicletype_id" value="<?php echo $edit_row['vehicletype_id']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="form-label" for="vehicle_type">Vehicle Type</label>
                        <select class="form-control" name="vehicle_type" id="vehicle_type">
                            <?php
                            $types = ['Car', 'Bus', 'Train', 'Airplane', 'Bike', 'Boat'];
                            foreach ($types as $t) {
                                $sel = ($edit_row && $edit_row['vehicle_type'] === $t) ? 'selected' : '';
                                echo "<option value='$t' $sel>$t</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div style="display:flex; gap:0.75rem; margin-top:0.5rem;">
                        <?php if ($edit_row): ?>
                            <button type="submit" name="update_vehicle" class="btn btn-primary">Update</button>
                            <a href="<?php echo BASE_URL; ?>/vehicles" class="btn btn-secondary" style="background:#6B7280; text-decoration:none;">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_vehicle" class="btn btn-primary">Add Vehicle</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Vehicle Types Table -->
            <div class="card">
                <h2 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--text-main);">Vehicle Types List</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vehicle Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['vehicletype_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="<?php echo BASE_URL; ?>/vehicles?edit=<?php echo $row['vehicletype_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this vehicle type?');">
                                                    <input type="hidden" name="vehicletype_id" value="<?php echo $row['vehicletype_id']; ?>">
                                                    <button type="submit" name="delete_vehicle" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align:center; color:var(--text-muted);">No Vehicle Types Found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>