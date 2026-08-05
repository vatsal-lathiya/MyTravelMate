<?php require("../DB/dbconn.php") ?>
<?php
if (isset($_POST['delete_data'])) {
    $id = $_POST['delete_id'];

    $stmt = $conn->prepare('DELETE FROM tb_blogs WHERE blog_id=?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
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
    <title> MTM - Blogs </title>
    <style>
        th,
        td {
            padding: 10px;
        }
    </style>
</head>

<body>
    <h1> Blogs </h1>
    <button> <a href="../Blog/AddBlog.php" style="padding: 10px;"> Add Blog </a> </button>
    <br><br>
    <table border="2" width="100%" style="text-align: center;">
        <tr>
            <th> # </th>
            <th> Blog Title </th>
            <th> Blog Image </th>
            <th> Blog Description </th>
            <th> Destination </th>
            <th> Action </th>
        </tr>
        <?php
        $sql = "SELECT tbl_blogs.blog_id,tbl_blogs.blog_title,tbl_blogs.blog_img,tbl_blogs.blog_desc,tbl_destination.dest_name 
                FROM tbl_blogs,tbl_destination
                WHERE tbl_blogs.dest_id = tbl_destination.dest_id";
        $result = mysqli_query($conn, $sql);
        $count = 0;
        if (!$result) {
            die(mysqli_error($conn));
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                $count++;
                echo
                '<tr>
                    <td> ' . $count . ' </td>
                    <td> ' . substr($row["blog_title"], 0, 30) . '... </td>
                    <td style="width:20%;"> <img src="../Uploads/Blog_img/' . $row["blog_img"] . '"  alt=' . $row["blog_img"] . ' width="50%"> </td>
                    <td> ' . substr($row["blog_desc"], 0, 50) . '...</td>
                    <td> ' . $row["dest_name"] . ' </td>
                    <td>
                        <form method="POST">
                             <button><a href="../Blog/EditBlog.php?edit=' . $row["blog_id"] . '" style="padding:5px;"> Edit </a></button>
                            <input type="hidden" name="delete_id" value=' . $row["blog_id"] . '>
                             <input type="submit" value="Delete" name="delete_data" id="delete_data">
                        </form>
                    </td>
                </tr>';
            }
        }
        ?>
    </table>
</body>

</html>