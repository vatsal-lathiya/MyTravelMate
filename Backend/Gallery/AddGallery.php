<?php require("../DB//dbconn.php") ?>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add_data"])) {
        $dest_id = $_POST["dest_name"];
        if (!empty($_FILES["gal_img"]["name"])) {
            $gal_img = $_FILES["gal_img"]["name"];
            $tmp = $_FILES["gal_img"]["tmp_name"];
            move_uploaded_file($tmp, "../Uploads/gallery_img/" . $gal_img);
        }

        $sql = "INSERT INTO tbl_gallery (g_img, dest_id) VALUES ('$gal_img',$dest_id)";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("Location:../Gallery");
            exit();
        } else {
            echo mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM - Add Gallery </title>
</head>

<body>
    <h1> Add Gallery </h1>
    <form enctype="multipart/form-data" method="POST">
        <label for="dest_name"> Destination Name </label>
        <select name="dest_name" id="dest_name">
            <option> Choose Destination </option>
            <?php
            //Fetch Destination Names
            $sql = "SELECT dest_id,dest_name from tbl_destination";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '
                      <option value=' . $row["dest_id"] . '> ' . $row["dest_name"] . ' </option>
                    ';
            }
            ?>
        </select>
        <br><br>
        <label for="gal_img"> Detination Gallery Image </label>
        <input type="file" name="gal_img" id="gal_img" required>
        <br><br>
        <input type="submit" name="add_data" value="Add" required>
        <input type="reset" value="Cancel">
    </form>
</body>

</html>