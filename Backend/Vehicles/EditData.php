<?php require("../DB/dbconn.php") ?>
<?php require("../session.php") ?>
<?php
if (!isset($_GET["edit"])) {
    die("<script> alert('Edit Doesn't Possible') </script>");
} else {
    $id = $_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM tbl_vehicle WHERE vehicle_id=?");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $rowdata = $result->fetch_assoc();
    } else {

        die("Vehicle Not Found");
    }
}

if (isset($_POST['update_vehicle'])) {

    $id = $_GET['edit'];
    $type = $_POST['vehicletype_id'];
    $name = $_POST['vehicle_name'];
    $capacity = $_POST['capacity'];
    $status = $_POST['vehicle_status'];

    $stmt = $conn->prepare("UPDATE tbl_vehicle
                            SET vehicletype_id=?,
                                vehicle_name=?,
                                capacity=?,
                                vehicle_status=?
                            WHERE vehicle_id=?");

    $stmt->bind_param(
        "isisi",
        $type,
        $name,
        $capacity,
        $status,
        $id
    );

    if ($stmt->execute()) {
        header("Location: vehiclelist.php?update=1");
        exit();
    } else {
        die($stmt->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM - EDIT DATA </title>
</head>

<body>
    <h1> Edit Vehicle Data </h1>
    <form method="POST">

        <!-- Hidden Vehicle ID -->
        <input type="hidden" name="vehicle_id" id="vehicle_id">

        <div>
            <label>Vehicle Type</label><br>

            <select name="vehicletype_id" id="vehicletype_id" required>

                <?php
                $result = mysqli_query($conn, "SELECT * FROM tbl_vehicletype");

                while ($row = mysqli_fetch_assoc($result)) {
                ?>

                    <option value="<?= $row['vehicletype_id']; ?>">
                        <?= $row['vehicle_type']; ?>
                    </option>

                <?php } ?>

            </select>
        </div>

        <br>

        <div>
            <label>Vehicle Name</label><br>

            <input
                type="text"
                value="<?php echo $rowdata['vehicle_name'] ?>"
                name="vehicle_name"
                id="vehicle_name"
                required>
        </div>

        <br>

        <div>
            <label>Capacity</label><br>

            <input
                type="number"
                name="capacity"
                value="<?php echo $rowdata['capacity'] ?>"
                id="capacity"
                required>
        </div>

        <br>

        <div>
            <label>Status</label><br>
            <select name="vehicle_status" id="vehicle_status">
                <?php
                echo '
                <option value="Available" ' . ($rowdata["vehicle_status"] == "Available" ? "selected" : "") . '>
                    Available
                </option>

                <option value="Unavailable" ' . ($rowdata["vehicle_status"] == "Unavailable" ? "selected" : "") . '>
                    Unavailable
                </option>
                ';
                ?>
            </select>
        </div>

        <br>

        <button type="submit" name="update_vehicle">
            Update Vehicle
        </button>

        <button type="reset">
            Cancel
        </button>

    </form>
</body>

</html>