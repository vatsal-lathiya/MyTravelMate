<?php require "../DB/dbconn.php" ?>
<?php require("../session.php") ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM - Edit Destination </title>
</head>

<body>
    <?php

    if (!(isset($_GET["edit"]))) {
        die("ID Not Provided");
    }

    // Fetch Data By Query Param Edit ID
    $sql_query = "SELECT * from tbl_destination WHERE dest_id=" . $_GET["edit"] . "";
    $result = mysqli_query($conn, $sql_query);
    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) <= 0) {
        die("Data Not Found");
    }

    // Edit Data
    if (isset($_POST["update_data"])) {
        $dest_name = mysqli_real_escape_string($conn, $_POST["edit_dest_name"]);
        $dest_desc = mysqli_real_escape_string($conn, $_POST["edit_dest_desc"]);
        $best_time = mysqli_real_escape_string($conn, $_POST["best_time"]);
        $status = mysqli_real_escape_string($conn, $_POST["dest_status"]);

        // Image Upload
        if (!empty($_FILES["dest_img"]["name"])) {

            $dest_img = $_FILES["dest_img"]["name"];
            $tmp = $_FILES["dest_img"]["tmp_name"];

            move_uploaded_file($tmp, "../Uploads/dest_img/" . $dest_img);
        } else {

            // Keep old image
            $dest_img = $_POST["old_image"];
        }

        if (empty($dest_name) || empty($dest_desc) || empty($best_time) || empty($status)) {
            echo "Fields Not Must Be Empty";
        } else {
            $sql_query = "UPDATE tbl_destination SET dest_img='$dest_img',dest_name='$dest_name',dest_desc='$dest_desc',dest_besttime='$best_time',dest_status='$status'   WHERE dest_id='" . $_GET["edit"] . "'";
            $result = mysqli_query($conn, $sql_query);

            if ($result) {
                header("Location:../Destination/");
                exit();
            } else {
                echo mysqli_error($conn);
            }
        }
    }


    ?>
    <h1> Update Data of Destination </h1>
    <form method="POST" enctype="multipart/form-data">
        <div style="border:1px solid black; padding:10px;">
            <div class="form-group">
                <lable> Destination ID : </lable>
                <input type="text" name="dest_id" value=<?php echo $row["dest_id"]; ?> disabled>
            </div>
            <br>
            <div class="form-group">
                <label>Current Image</label><br>
                <img src="../Uploads/dest_img/<?php echo $row['dest_img']; ?>.jpg" width="200">

                <br><br>

                <?php echo $row['dest_img']; ?>

                <br><br>

                <input type="hidden" name="old_image" value="<?php echo $row['dest_img']; ?>">
                <input type="file" name="dest_img">
            </div>
            <br>
            <div class="form-group">
                <lable for="edit_dest_name"> Destination Name : </lable>
                <input type="text" name="edit_dest_name" value='<?php echo $row["dest_name"]; ?>' id="edit_dest_name">
            </div>
            <br>
            <div class="form-group">
                <lable for="edit_dest_desc"> Destination Description : </lable>
                <br>
                <textarea
                    name="edit_dest_desc"
                    id="edit_dest_desc"
                    rows="8" z
                    cols="60"
                    style="width:500px; height:200px;"><?php echo htmlspecialchars(trim($row["dest_desc"])); ?></textarea>
            </div>
            <br>
            <div class="form-group">
                <lable for="best_time"> Best Time : </lable>
                <input type="text" name="best_time" value="<?php echo $row["dest_besttime"] ?>" style="width:20%;" id="best_time">
            </div>
            <br>
            <div class="form-group">
                <lable for="dest_status"> Destination Status : </lable>
                <select name="dest_status" id="dest_status">
                    <option value="Open" <?php if ($row["dest_status"] == "Open") echo "selected"; ?>>
                        Open
                    </option>

                    <option value="Close" <?php if ($row["dest_status"] == "Close") echo "selected"; ?>>
                        Close
                    </option>
                </select>
            </div>
        </div>
        <div class="form-btns" style="margin-top: 10px;">
            <input type="reset" value="Reset">
            <input type="submit" value="Update" name="update_data">
        </div>
    </form>
</body>

</html>
<?php
// echo $_GET["edit"]; 
?>