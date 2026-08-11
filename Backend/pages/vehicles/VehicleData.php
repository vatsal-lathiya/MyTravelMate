<?php
require BASE_PATH . "/components/auth_check.php"; ?>
<?php
/* ===========================
UPDATE by GET PARAM
=========================== */

if (isset($_POST['edit_vehicle'])) {
    $id = $_POST['vehicle_id'];
    header("Location: " . BASE_URL . "/vehicles/EditVehicle.php?edit=$id");
}

/* ===========================
DELETE
=========================== */

if (isset($_POST['delete_vehicle'])) {

    $id = $_POST['vehicle_id'];
    echo $id;

    $stmt = $conn->prepare("DELETE FROM tbl_vehicle WHERE vehicle_id=?");

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "/vehicles/VehicleData.php");
        exit();
    } else {
        die($stmt->error);
    }
}

$sql = "SELECT
            tbl_vehicle.vehicle_id,
            tbl_vehicletype.vehicle_type,
            tbl_vehicle.vehicle_name,
            tbl_vehicle.capacity,
            tbl_vehicle.vehicle_status
        FROM tbl_vehicle
        INNER JOIN tbl_vehicletype
        ON tbl_vehicle.vehicletype_id = tbl_vehicletype.vehicletype_id
        ORDER BY tbl_vehicle.vehicle_id ASC";

$result = mysqli_query($conn, $sql);
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM-Vehicles </title>
</head>

<body>

    <main class="main-content">
        <header>
            <h2> Vehicles Data List </h2>
            <a href=" <?php echo BASE_URL; ?>/vehicles/add" style="margin-left:50px;"> Add Vehicle </a>
        </header>
        <table class="table" style="width:80%">

            <tr>
                <th>ID</th>
                <th>Vehicle Type</th>
                <th>Vehicle Model</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <tr>

                    <td><?php echo $row['vehicle_id']; ?></td>

                    <td><?php echo $row['vehicle_type']; ?></td>

                    <td><?php echo $row['vehicle_name']; ?></td>

                    <td><?php echo $row['capacity']; ?></td>

                    <td><?php echo $row['vehicle_status']; ?></td>

                    <td>

                        <!-- Edit Button -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden"
                                name="vehicle_id"
                                value="<?php echo $row['vehicle_id']; ?>">

                            <button type="submit" name="edit_vehicle">
                                Edit
                            </button>
                        </form>

                        <!-- Delete Button -->
                        <form method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this vehicle?');">

                            <input type="hidden"
                                name="vehicle_id"
                                value="<?php echo $row['vehicle_id']; ?>">
                            <button type="submit" name="delete_vehicle">
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            <?php } ?>

        </table>
    </main>
</body>

</html>
<?php require BASE_PATH . "/components/footer.php" ?>