<?php require "../DB/dbconn.php" ?>
<?php require("../session.php") ?>
<!--Update Data-->
<?php
if (isset($_POST['update_data'])) {
    $id = $_POST['update_id'];
    $name = $_POST['update_name'];
    $code = $_POST['update_code'];
    $status = $_POST['update_status'];

    if (empty($name) || empty($code) || empty($status)) {
        echo "<script> alert('Please fill all the fields') </script>";
    } else {
        $sql_query = "UPDATE tbl_states SET state_name='$name',state_code='$code',state_status='$status' WHERE state_id='$id'";
        $result = mysqli_query($conn, $sql_query);
        if ($result) {
            echo "<script> alert('Data Updated Successfully') </script>";
            header("location:States.php");
            exit();
        } else {
            echo mysqli_error($conn);
        }
    }
}

//    Delete Data
if (isset($_POST['remove_state'])) {
    $id = $_POST['target_id'];

    $sql_query = "DELETE FROM tbl_states WHERE state_id='$id'";
    $result = mysqli_query($conn, $sql_query);
    if ($result) {
        header("location:States.php?deleted='.$id.'");
        exit();
    } else {
        header("location:States.php?error='.$id.'");
        exit();
    }
}

if (isset($_GET['deleted'])) {
    echo "<script> alert('Data Deleted Successfully'); window.location.href='States.php' </script>";
}

if (isset($_GET['error'])) {
    echo "<script> alert('State Have Destination You Cannot Be Delete'); window.location.replace('States.php') </script>";
}
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> MTM - StateList </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        th {
            border: 1px solid black;
        }

        td {
            border: 1px solid black;
        }
    </style>
    <script>
        const handleEditData = (event) => {
            let state_id = event.target.parentElement.parentElement.children[0].innerText;
            let state_name = event.target.parentElement.parentElement.children[1].innerText;
            let state_code = event.target.parentElement.parentElement.children[2].innerText;
            let state_status = event.target.parentElement.parentElement.children[4].innerText;
            console.log(state_id, state_name, state_code, state_status);

            document.getElementById("update_id").value = state_id;
            document.getElementById("update_name").value = state_name;
            document.getElementById("update_code").value = state_code;
            document.getElementById("update_status").value = state_status;
        }
    </script>
</head>

<body>
    <div class="modal fade" id="editData" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel"> Edit Data </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body justify-content-center mx-3 ">
                    <form method="POST">
                        <div class="d-flex w-full form-group">
                            <div class="mb-2">
                                <label for="state_id" class="col-form-label text-secondary fw-semibold "> State_ID </label>
                                <input type="text" class="form-control" id="update_id" name="update_id" readonly>
                            </div>
                            <div class="mb-2 mx-3 col-7">
                                <label for="update_name " class="col-form-label"> State_Name </label>
                                <input type="text" class="form-control" id="update_name" name="update_name">
                            </div>
                        </div>
                        <div class="d-flex mt-2 w-full form-group">
                            <div class="mb-2">
                                <label for="update_code" class="col-form-label text-secondary fw-semibold "> State_Code </label>
                                <input type="text" class="form-control" name="update_code" id="update_code">
                            </div>
                            <div class="mb-2 mx-3 col-7">
                                <label for="message-text" class="col-form-label"> Status </label>
                                <select class="form-control" name="update_status">
                                    <option value="Active"> Active </option>
                                    <option value="Unactive"> Unactive </option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal"> Cancel </button>
                            <button type="submit" name="update_data" class="btn btn-primary"> Update Data </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <h4 class="text-xl fw-bold pb-4 ms-4 py-3"> States Data </h4>
    <table width="95%" style="text-align: center; margin: auto;">
        <thead>
            <tr>
                <th> # </th>
                <th> State Name </th>
                <th> State Code </th>
                <th> No of Destinations </th>
                <th> Status </th>
                <th> Action </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $loading = false;
            // GET STATES DATA
            $sql_query = "SELECT tbl_states.state_id,tbl_states.state_name,tbl_states.state_code,COUNT(tbl_destination.dest_id) AS No_of_Destination,tbl_states.state_status
                                  FROM tbl_states
                                  LEFT JOIN tbl_destination
                                  ON tbl_states.state_id = tbl_destination.state_id
                                  GROUP BY tbl_states.state_id";
            $result = mysqli_query($conn, $sql_query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['state_id'] . "</td>";
                    echo "<td>" . $row['state_name'] . "</td>";
                    echo "<td>" . $row['state_code'] . "</td>";
                    echo "<td>" . $row['No_of_Destination'] . "</td>";
                    echo "<td>" . $row['state_status'] . "</td>";
                    echo "<td class='d-flex gap-2 align-items-center m-auto justify-content-center'> 
                                      <button type='button' class='btn btn-primary ' data-bs-toggle='modal' id=" . $row['state_id'] . " data-bs-target='#editData' onclick='handleEditData(event)'> Edit </button> <input type='hidden' name='target_id' id=" . $row["state_id"] . ">  
                                      <form action='States.php' method='POST'>
                                        <input type='hidden' name='target_id' value=" . $row['state_id'] . ">
                                        <button type='submit' class='btn btn-danger col-12 border-0 p-2 my-2 font-semibold text-white' onclick='handleRemoveData(event)' name='remove_state'> Remove </button> </td>
                                      </form>";
                    echo "</tr>";
                }
            } else {
                echo "No Data Found" . mysqli_error($conn);
            }
            ?>
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>