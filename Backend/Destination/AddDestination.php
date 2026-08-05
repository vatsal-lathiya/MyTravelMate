<?php require("../DB//dbconn.php") ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM - Add Destination </title>
</head>

<body>
    <?php

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["add_destination"])) {
            $dest_name = $_POST["dest_name"];
            $dest_state = $_POST["dest_state"];
            $dest_desc = $_POST["dest_desc"];
            $dest_time = $_POST["dest_besttime"];
            $dest_status = $_POST["dest_status"];
            $dest_img = $_FILES["dest_img"]['name'];
            $temp_name = $_FILES['dest_img']['tmp_name'];

            if (empty($dest_name) || empty($dest_desc) || empty($dest_state) || empty($dest_time) || empty($dest_status) || empty($dest_img)) {
                echo "<script> alert('All Fields Required')</script>";
            } else {
                move_uploaded_file($temp_name, "../Uploads/dest_img/$dest_img");
                $stmt = $conn->prepare("INSERT INTO tbl_destination (dest_name,state_id,dest_desc,dest_besttime,dest_img,dest_status) VALUES (?,?,?,?,?,?)");
                $stmt->bind_param(
                    "sissss",
                    $dest_name,
                    $dest_state,
                    $dest_desc,
                    $dest_time,
                    $dest_img,
                    $dest_status
                );

                if ($stmt->execute()) {
                    echo "Data Inserted Successfully";
                    header("Location:../Destination");
                    exit();
                } else {
                    echo $stmt->error;
                }

                $stmt->close();
            }
        } else {
            echo mysqli_error($conn);
        }
    }

    ?>
    <h1> Add Destination </h1>
    <form action="" enctype="multipart/form-data" method="POST">
        <label for="dest_name">Destination Name</label><br>
        <input
            type="text"
            name="dest_name"
            id="dest_name"
            placeholder="Enter destination name">

        <br><br>

        <label for="dest_desc">Destination Description</label><br>
        <textarea
            name="dest_desc"
            id="dest_desc"
            rows="6"
            cols="50"
            placeholder="Enter destination description"></textarea>

        <br><br>

        <label for="dest_img"> Destination Image </label>
        <input type="file" name="dest_img" id="dest_img">

        <label for="dest_state"> Destination State </label>
        <select name="dest_state" id="dest_state">
            <option value="1"> Gujarat </option>
            <option value="2"> Maharashtra </option>
        </select>

        <br><br>

        <label for="dest_besttime">Destination Best Time </label><br>
        <input
            type="text"
            name="dest_besttime"
            id="dest_besttime">

        <br><br>


        <label for="dest_status"> Destination State </label>
        <select name="dest_status" id="dest_status">
            <option value="Open"> Open </option>
            <option value="Close"> Close </option>
        </select>

        <br><br>

        <input type="submit" value="Add" name="add_destination">
        <input type="reset" value="Cancel">
    </form>
</body>

</html>