<?php require "../DB/dbconn.php" ?>
<?php
session_start();
if (!isset($_SESSION['sess_name'])) {
    header("location:../../Backend");
    exit();
}
echo "Hello " . $_SESSION['sess_name'];
// Number of Destination
$sql_query = "SELECT * FROM `tbl_destination`";
$result = mysqli_query($conn, $sql_query);

if ($result) {
    echo "Total Destination : " . mysqli_num_rows($result);
} else {
    echo "Data Not Founded";
}
?>
<a href="../Logout.php"> Log Out </a>