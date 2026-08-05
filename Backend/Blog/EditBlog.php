<?php require("../DB/dbconn.php"); ?>
<?php
if (!isset($_GET["edit"]) || $_GET["edit"] === "") {
    header("Location:../Blog");
    exit();
}

$sql = 'SELECT * FROM tbl_blogs where blog_id = ' . $_GET["edit"] . '';
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (isset($_POST['edit_data'])) {
    $edit_blog_title = $_POST['blog_title'];
    $edit_blog_desc = $_POST['blog_desc'];
    $edit_dest_name = $_POST['blog_dest_id'];

    if (empty($blog_title) || empty($blog_description) || empty($blog_destination)) {
        echo "<script> alert('All Fields Are Required') </script>";
    }

    if (!empty($_FILES["blog_img"]["name"])) {

        $edit_blog_img = $_FILES["blog_img"]["name"];
        $tmp = $_FILES["blog_img"]["tmp_name"];

        move_uploaded_file($tmp, "../Uploads/Blog_img/" . $edit_blog_img);
    } else {
        // Keep old image
        $g_img = $_POST["old_image"];
    }

    $stmt = $conn->prepare('UPDATE tbl_blogs SET blog_title=?,blog_img=?,blog_desc=?,dest_id=?  WHERE  blog_id=' . $row["blog_id"] . '');
    $stmt->bind_param(
        'sssi',
        $edit_blog_title,
        $edit_blog_img,
        $edit_blog_desc,
        $edit_dest_name,
    );
    if ($stmt->execute() === TRUE) {
        header("Location:../Blog");
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
    <title> MTM - Edit Blog </title>
</head>

<body>
    <h1> Edit Blog </h1>
    <form action="" enctype="multipart/form-data" method="POST">
        <label for="blog_ID"> Blog ID </label>
        <input type="text" name="blog_id" value="<?php echo $row["blog_id"] ?>" id="blog_id" disabled>
        <br><br>
        <label for="blog_title"> Blog Title </label>
        <input type="text" name="blog_title" value="<?php echo $row["blog_title"] ?>" id="blog_title">
        <br><br>
        <label for="blog_img"> Blog Image </label>
        <p> Current-Image : <?php echo $row["blog_img"] ?></p>
        <input type="file" name="blog_img" id="blog_img" value="<?php echo $row["blog_img"] ?>">
        <input type="hidden" name="old_img" id="old_img" value="<?php echo $row['blog_img']; ?>">
        <br><br>
        <label for="blog_desc"> Blog Description </label>
        <br>
        <textarea name="blog_desc" rows=10 cols=100 id="blog_desc"> <?php echo $row["blog_desc"] ?> </textarea>
        <br><br>
        <label for="blog_dest"> Choose Destination </label>
        <select name="blog_dest_id" id="blog_dest_id">
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
        <input type="submit" name="edit_data" value="Edit Data ">
        <input type="reset" value="Cancel">
    </form>
</body>

</html>