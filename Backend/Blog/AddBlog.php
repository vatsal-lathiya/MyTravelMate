<?php require("../DB/dbconn.php") ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> MTM - Add Blog </title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["add_data"])) {
            $blog_title = $_POST["blog_title"];
            $blog_description = $_POST["blog_desc`"];
            $blog_destination = $_POST["blog_dest_id"];
            if (empty($blog_title) || empty($blog_description) || empty($blog_destination)) {
                echo "<script> alert('All Fields Are Required') </script>";
            }

            if (!empty($_FILES["blog_img"]["name"])) {
                $blog_img = $_FILES["blog_img"]["name"];
                $tmp = $_FILES["blog_img"]["tmp_name"];

                move_uploaded_file($tmp, "../Uploads/Blog_img/$blog_img");
            } else {
                echo "<script> alert('Please Select Image of Blog') </script>";
            }

            $stmt = $conn->prepare("INSERT INTO tbl_blogs(blog_title,blog_img,blog_desc,dest_id) VALUES (?,?,?,?)");
            $stmt->bind_param(
                "sssi",
                $blog_title,
                $blog_img,
                $blog_description,
                $blog_destination
            );
            if ($stmt->execute() === TRUE) {
                $stmt->close();
                header("Location:../Blog ");
                exit();
            } else {
                echo $stmt->error;
            }
        }
    }
    ?>
    <h1> Add Blog </h1>
    <form action="" enctype="multipart/form-data" method="POST">
        <label for="blog_title"> Blog Title </label>
        <input type="text" name="blog_title" id="blog_title">
        <br><br>
        <label for="blog_img"> Blog Image </label>
        <input type="file" name="blog_img" id="blog_img">
        <br><br>
        <label for="blog_desc"> Blog Description </label>
        <br>
        <textarea name="blog_desc" rows=10 cols=100 id="blog_desc"></textarea>
        <br><br>
        <label for="blog_dest"> Choose Destination </label>
        <select name="blog_dest_id" id="blog_dest_id">
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
        <input type="submit" name="add_data" value="Add Data ">
        <input type="reset" value="Cancel">
    </form>
</body>

</html>