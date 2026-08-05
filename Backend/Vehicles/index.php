<?php require "../DB/dbconn.php" ?>
<?php

/* ===========================
INSERT
=========================== */

if (isset($_POST['add_vehicle'])) {

    $vehicle_type = $_POST['vehicle_type'];

    $stmt = $conn->prepare("INSERT INTO tbl_vehicletype(vehicle_type) VALUES(?)");
    $stmt->bind_param("s", $vehicle_type);

    if ($stmt->execute()) {
        header("Location: VehicleType.php");
        exit();
    } else {
        die($stmt->error);
    }
}


/* ===========================
UPDATE
=========================== */

if (isset($_POST['update_vehicle'])) {

    $id = $_POST['vehicletype_id'];
    $vehicle_type = $_POST['vehicle_type'];

    $stmt = $conn->prepare("UPDATE tbl_vehicletype
SET vehicle_type=?
WHERE vehicletype_id=?");

    $stmt->bind_param("si", $vehicle_type, $id);

    if ($stmt->execute()) {
        header("Location: VehicleType.php");
        exit();
    } else {
        die($stmt->error);
    }
}


/* ===========================
DELETE
=========================== */

if (isset($_POST['delete_vehicle'])) {

    $id = $_POST['vehicletype_id'];

    $stmt = $conn->prepare("DELETE FROM tbl_vehicletype
WHERE vehicletype_id=?");

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: VehicleType.php");
        exit();
    } else {
        die($stmt->error);
    }
}


/* ===========================
FETCH ALL DATA
=========================== */

$stmt = $conn->prepare("SELECT * FROM tbl_vehicletype ORDER BY vehicletype_id ASC");
$stmt->execute();

$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM - Vehicletype </title>
</head>

<body>
    <h1> Add Vehicle Types </h1>
    <!-- Add Vehicles    -->
    <form method="POST">

        <select name="vehicle_type">

            <option value="Car">Car</option>

            <option value="Bus">Bus</option>

            <option value="Train">Train</option>

            <option value="Airplane">Airplane</option>

        </select>

        <button type="submit" name="add_vehicle">
            Add
        </button>

    </form>
    <br>
    <h2> Update Data</h2>
    <form method="POST">

        <input type="hidden" name="vehicletype_id" value="1">

        <select name="vehicle_type">

            <option value="101">Car</option>

            <option value="Bus">Bus</option>

            <option value="Train">Train</option>

            <option value="Airplane">Airplane</option>

        </select>

        <button type="submit" name="update_vehicle">
            Update
        </button>

        <h3> Vehicle Types </h3>
        <?php
        $sql = "SELECT * FROM tbl_vehicletype ORDER BY vehicletype_id ASC";
        $result = mysqli_query($conn, $sql);
        ?>

        <!DOCTYPE html>
        <html>

        <head>
            <title>Vehicle Type List</title>
            <style>
                table {
                    border-collapse: collapse;
                    width: 50%;
                    margin: auto;
                    text-align: center;
                }

                table,
                th,
                td {
                    border: 1px solid black;
                    padding: 10px;
                }

                th {
                    background: #f2f2f2;
                }

                a {
                    text-decoration: none;
                    padding: 5px 10px;
                    background: blue;
                    color: white;
                }

                .delete {
                    background: red;
                }
            </style>
        </head>

        <body>

            <h2 align="center">Vehicle Type List</h2>

            <table>

                <tr>
                    <th>ID</th>
                    <th>Vehicle Type</th>
                    <th>Action</th>
                </tr>

                <?php

                if (mysqli_num_rows($result) > 0) {

                    while ($row = mysqli_fetch_assoc($result)) {

                ?>

                        <tr>

                            <td><?php echo $row['vehicletype_id']; ?></td>

                            <td><?php echo $row['vehicle_type']; ?></td>

                            <td>
                                <a href="EditVehicleType.php?id=<?php echo $row['vehicletype_id']; ?>">
                                    Edit
                                </a>

                                <a class="delete"
                                    href="DeleteVehicleType.php?id=<?php echo $row['vehicletype_id']; ?>"
                                    onclick="return confirm('Delete this vehicle type?');">
                                    Delete
                                </a>
                            </td>
                        </tr>

                    <?php

                    }
                } else {

                    ?>

                    <tr>
                        <td colspan="3">No Vehicle Types Found</td>
                    </tr>

                <?php
                }
                ?>

            </table>
    </form>
</body>

</html>