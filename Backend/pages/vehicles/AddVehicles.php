<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
// =========================
// // ADD DATA
// =========================
if (isset($_POST['add_vehicle'])) {
    $type = $_POST['vehicletype_id'];
    $name = $_POST['vehicle_name'];
    $capacity = $_POST['capacity'];
    $status = $_POST['vehicle_status'];

    if (empty($type) || empty($name) || empty($capacity) || empty($status)) {
        echo "<script>alert('Please fill all fields');</script>";
    } else {

        $stmt = $conn->prepare("INSERT INTO tbl_vehicle
        (vehicletype_id, vehicle_name, capacity, vehicle_status)
        VALUES (?, ?, ?, ?)");

        $stmt->bind_param("isis", $type, $name, $capacity, $status);

        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "/vehicles/VehicleData.php");
            exit();
        } else {
            die($stmt->error);
        }

        $stmt->close();
    }
}

?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM - Vehicle Models </title>
</head>

<body>
    <main class="main-content">
        <header class="top-header">
            Add Vehicles
        </header>
        <div class="content-body">
            <form method="POST">
                <div>
                    <label>Vehicle Type</label><br>

                    <select name="vehicletype_id" required>

                        <option value="">-- Select Vehicle Type --</option>

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
                        name="vehicle_name"
                        placeholder="Enter Vehicle Name"
                        required>
                </div>

                <br>

                <div>
                    <label>Capacity</label><br>

                    <input
                        type="number"
                        name="capacity"
                        placeholder="Enter Capacity"
                        required>
                </div>

                <br>

                <div>
                    <label>Status</label><br>

                    <select name="vehicle_status" required>

                        <option value="">-- Select Status --</option>

                        <option value="Available">Available</option>

                        <option value="Unavailable">Unavailable</option>

                    </select>
                </div>

                <br>

                <button type="submit" name="add_vehicle">
                    Add Vehicle
                </button>

                <button type="reset">
                    Reset
                </button>

            </form>
        </div>
    </main>
</body>

</html>