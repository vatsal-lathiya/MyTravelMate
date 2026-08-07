<?php require("../DB//dbconn.php") ?>
<?php require("../session.php") ?>
<?php
if (!isset($_GET["edit"]) || $_GET["edit"] === "") {
    header("Location:../Gallery");
    exit();
}

$sql = 'SELECT * FROM tbl_gallery where g_id = ' . $_GET["edit"] . '';
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (isset($_POST['edit_data'])) {
    $edit_dest_name = $_POST['edit_dest_name'];
    if (!empty($_FILES["edit_img"]["name"])) {

        $g_img = $_FILES["edit_img"]["name"];
        $tmp = $_FILES["edit_img"]["tmp_name"];

        move_uploaded_file($tmp, "../Uploads/gallery_img/" . $g_img);
    } else {

        // Keep old image
        $g_img = $_POST["old_image"];
    }
    $stmt = $conn->prepare('UPDATE tbl_gallery SET dest_id=?,g_img=?');
    $stmt->bind_param(
        'is',
        $edit_dest_name,
        $g_img
    );
    if ($stmt->execute() === TRUE) {
        header("Location:../Gallery");
        exit();
    } else {
        echo $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM - Edit Gallery </title>
</head>

<body>
    <h1> Edit Gallery </h1>
    <form action="" enctype="multipart/form-data" method="POST">
        <label for="g_id"> Gallery ID </label>
        <input type="text" name="g_id" id="g_id" value=<?php echo $row["g_id"] ?> disabled>
        <br><br>
        <label for="dest_name"> Destination Name </label>
        <select name="edit_dest_name" id="edit_dest_name" required>
            <option> Choose Destination </option>
            <?php
            //Fetch Destination Names
            $sql = "SELECT dest_id,dest_name from tbl_destination";
            $result = mysqli_query($conn, $sql);
            while ($rowdata = mysqli_fetch_assoc($result)) {
                echo '
                    <option value="' . $rowdata["dest_id"] . '" ' . ($rowdata["dest_id"] == $row["dest_id"] ? 'selected' : '') . '>' .
                    $rowdata["dest_name"] . '
                    </option>
                    ';
            }
            ?>
        </select>
        <br><br>

        <p> <?php echo 'Current File : ' . $row["g_img"] . '';  ?></p>

        <br><br>

        <input type="hidden" name="old_image" value="<?php echo $row['g_img']; ?>">
        <input type="file" name="edit_img" id="edit_img">

        <br><br>

        <input type="submit" name="edit_data" id="edit_data" value="Update">
        <input type="reset" value="Reset">
    </form>
</body>

</html>