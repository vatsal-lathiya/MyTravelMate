<?php require "../DB/dbconn.php" ?>
<?php
// Number of Destination
$sql_query = "SELECT * FROM `tbl_destination`";
$result = mysqli_query($conn, $sql_query);

if ($result) {
    echo "Total Destination : " . mysqli_num_rows($result);
} else {
    echo "Data Not Founded";
}
?>