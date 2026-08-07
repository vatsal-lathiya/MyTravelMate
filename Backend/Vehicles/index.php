<?php
require "../DB/dbconn.php"; ?>
<?php
require "../session.php";

/* ===========================
UPDATE by GET PARAM
=========================== */

if (isset($_POST['edit_vehicle'])) {
    $id = $_POST['vehicle_id'];
    header("Location:EditData.php?edit=$id");
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
        header("Location:vehiclelist.php?delete=1");
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM-Vehicles </title>
</head>

<body>
    <h2> Vehicles Data List </h2>
    <a href="../Vehicles/AddVehicles.php"> Add Vehicle </a>
    <table border="1" cellpadding="10" cellspacing="0">

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
</body>

</html>