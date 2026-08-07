<?php require "../DB/dbconn.php" ?>
<?php require("../session.php") ?>
<?php

// /* ===========================
// INSERT
// =========================== */

if (isset($_POST['add_vehicle'])) {

    $vehicletype_id = $_POST['vt_id'];
    $vehicle_type = $_POST['vehicle_type'];

    $stmt = $conn->prepare("INSERT INTO tbl_vehicletype(vehicletype_id,vehicle_type) VALUES(?,?)");
    $stmt->bind_param("is", $vehicletype_id, $vehicle_type);

    if ($stmt->execute()) {
        header("Location:vehicletype.php");
        exit();
    } else {
        die($stmt->error);
    }
}


/* ===========================
UPDATE
=========================== */

// if (isset($_POST['update_vehicle'])) {

//     $id = $_POST['vehicletype_id'];
//     $vehicle_type = $_POST['vehicle_type'];

//     $stmt = $conn->prepare("UPDATE tbl_vehicletype
// SET vehicle_type=?
// WHERE vehicletype_id=?");

//     $stmt->bind_param("si", $vehicle_type, $id);

//     if ($stmt->execute()) {
//         header("Location:../Vehicles");
//         exit();
//     } else {
//         die($stmt->error);
//     }
// }


/* ===========================
FETCH ALL DATA
=========================== */

$stmt = $conn->prepare("SELECT * FROM tbl_vehicletype ORDER BY vehicletype_id ASC");
$stmt->execute();

$result = $stmt->get_result();
// 
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
        <label for="vt_id"> Vehicle Type ID </label>
        <input type="text" name="vt_id" id="vt_id">
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
    <h1> Vehicle Types List : </h1>
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
                        <form action="" method="POST">
                            <button name="update_data_form" id="update_data_form">
                                Edit
                            </button>
                            <input type="hidden" name="delete_id" value="<?php echo $row["vehicletype_id"] ?>">
                            <button class="delete"
                                id="delete_vehicle"
                                name="delete_vehicle"
                                onclick="return confirm('Delete this vehicle type?');">
                                Delete
                            </button>
                        </form>
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

</html>