<?php require "../DB/dbconn.php" ?>
<?php require("../session.php") ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> MTM - Add State </title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $state_id = $_POST['state_id'];
        $state_name = $_POST['state_name'];
        $state_code = $_POST['state_code'];
        $state_status = $_POST['state_status'];

        if (empty($state_id) || empty($state_name) || empty($state_code) || empty($state_status)) {
            echo "<script> alert('Please fill all the required fields!'); </script>";
        } else {
            $sql_query = "INSERT INTO tbl_states (state_id, state_name, state_code, state_status) VALUES ($state_id, '$state_name', '$state_code', '$state_status')";
            $result = mysqli_query($conn, $sql_query);

            if ($result) {
                echo "<script> alert('Data Added Successfully') </script>";
                header("location:States.php");
                exit();
            } else {
                echo "<script> alert('Data Not Added Successfully') </script>";
            }
        }
    }
    ?>
    <h2> Add State </h2>
    <form action="AddState.php" method="POST" style="display: grid; gap:15px;">
        <div class="form-group">
            <label for="state_id"> State ID </label>
            <input type="text" name="state_id" id="state_id">
        </div>
        <div class="form-group">
            <label for="state_name"> State Name </label>
            <input type="text" name="state_name" id="state_name">
        </div>
        <div class="form-group">
            <label for="state_code"> State Code </label>
            <input type="text" name="state_code" id="state_code">
        </div>
        <div class="form-group">
            <label for="state_status"> Status </label>
            <select name="state_status" id="state_status">
                <option value="Active"> Active </option>
                <option value="Unactive"> Unactive </option>
            </select>
        </div>
        <div class="form-group">
            <button type="reset"> Reset </button>
            <button type="submit"> Add </button>
        </div>
    </form>
</body>

</html>