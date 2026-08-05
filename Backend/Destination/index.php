<?php require "../DB/dbconn.php" ?>
<?php
// Delete Destination Method
if (isset($_POST["delete_data"])) {

    $id = (int)$_POST["delete_id"];

    $stmt = $conn->prepare("DELETE FROM tbl_destination WHERE dest_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location:../Destination");
    } else {
        echo $stmt->error;
    }

    $stmt->close();
}
?>
<!doctype html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> MTM - Destinations </title>
</head>

<body>
    <h1> Destination List </h1>
    <table border="1" width="100%" cellpadding="3" style="text-align:center;">
        <tr>
            <th> # </th>
            <th> Destination Image </th>
            <th> Destination Name </th>
            <th> Destination Desc </th>
            <th> State </th>
            <th> Best Time </th>
            <th> Destination_Status </th>
            <th> Action </th>
        </tr>
        <?php
        $sql_query = "SELECT tbl_destination.dest_id,tbl_destination.dest_img,tbl_destination.dest_name,tbl_destination.dest_desc,tbl_states.state_name,tbl_destination.dest_besttime,tbl_destination.dest_status
                          FROM tbl_destination,tbl_states
                          WHERE tbl_destination.state_id = tbl_states.state_id";
        $result = mysqli_query($conn, $sql_query);
        $count = 0;

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $count += 1;
                    $dest_id = $row['dest_id'];
                    $dest_img = $row['dest_img'];
                    $dest_name = $row['dest_name'];
                    $dest_desc = $row['dest_desc'];
                    $state_name = $row['state_name'];
                    $best_time = $row['dest_besttime'];
                    $dest_status = $row['dest_status'];

                    echo "
                        <tr>
                            <td> " . $count . " </td>
                            <td> <img src='../Uploads/dest_img/" . $dest_img . "' width='120' alt=''> </td>
                            <td> " . $dest_name . "</td>
                            <td> " . substr($dest_desc, 0, 30) . "...</td>
                            <td> $state_name </td>
                            <td> $best_time </td>
                            <td> $dest_status </td>
                            <td>
                                <div style='display:flex; justify-content:center; gap:10x;'>
                                    <form method='POST'>
                                            <button><a href='./EditDestination.php?edit=" . $dest_id . "'> Edit </a> </button>
                                            <input type='hidden' name='delete_id' value=" . $dest_id . ">
                                            <input type='submit' name='delete_data' value='Delete'>   
                                    </form>
                                </div>    
                            </td>
                        </tr>
                    ";
                }
            } else {
                echo "<h1> Data Not Found </h1>";
            }
        }
        ?>
    </table>
</body>

</html>