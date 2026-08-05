<?php require("../DB/dbconn.php") ?>
<?php
if (isset($_POST['delete_data'])) {
    $id = $_POST['delete_id'];

    $stmt = $conn->prepare('DELETE FROM tbl_gallery WHERE g_id=?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
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
    <title> MTM - Gallery </title>
</head>

<body>
    <h1> Gallery </h1>
    <div class="gallery-images" style="display: flex; justify-content: center; margin: auto; gap:10px; flex-wrap: wrap;">
        <?php
        $sql = "SELECT tbl_gallery.g_id,tbl_gallery.g_img,tbl_destination.dest_name 
            FROM tbl_gallery,tbl_destination
            WHERE tbl_gallery.dest_id = tbl_destination.dest_id";
        $result = mysqli_query($conn, $sql);
        $count = 0;
        if (!$result)
            die(mysqli_error($conn));
        while ($row = mysqli_fetch_assoc($result)) {
            $count++;
            echo '
            <div class="img" style="border:1px solid black; width:20%; padding: 10px; display: block;">
                <img src=" ../Uploads/gallery_img/' . $row["g_img"] . '" width="100%" alt="">
                <p> ID : ' . $count . ' </p>
                <h3> Destination Name : ' . $row["dest_name"] . ' </h3>
                <form method="POST">
                    <input type="submit" name="edit_data" value="Edit">
                    <input type="hidden" name="delete_id" value=' . $row["g_id"] . '>
                    <input
                    type="submit"
                    name="delete_data"
                    value="Delete"
                    onclick="return confirm("Are you sure you want to delete this gallery image?");">
                </form>
            </div>
            ';
        }

        if (isset($_POST["edit_data"])) {
            $id = $_POST["delete_id"];
            header("Location:EditGallery.php?edit=$id");
        }
        ?>
</body>

</html>