<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
$query = "SELECT a_id, a_name, a_img, a_category, a_status 
          FROM tbl_avatar";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

// DELETE AVATARS


if (isset($_POST['delete_data'])) {

    $id = $_POST['delete_id'];

    $query = "DELETE FROM tbl_avatar WHERE a_id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location:" . BASE_URL . "/avatars");
        exit;
    }
}

?>

<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>


<main class="main-content">

    <header class="top-header">
        <h1>Avatars</h1>
        <a href="<?php echo BASE_URL; ?>/avatars/add"> Add Avatars </a>
    </header>

    <div class="content-body">

        <table class="table">

            <thead>
                <tr>
                    <th>Avatar ID</th>
                    <th>Avatar Name</th>
                    <th>Avatar Profile</th>
                    <th>Avatar Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php $count = 0;
                if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): $count += 1; ?>
                        <tr>
                            <td>
                                <?php echo $count ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['a_name']); ?>
                            </td>

                            <td>
                                <img
                                    src="<?php echo BASE_URL; ?>/public/Uploads/Avatar_img/<?php echo htmlspecialchars($row['a_img']); ?>"
                                    alt="Avatar"
                                    width="60"
                                    height="60">
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['a_category']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['a_status']); ?>
                            </td>

                            <td>
                                <?php
                                echo "
                                        <a href='" . BASE_URL . "/avatars/edit?edit={$row["a_id"]}' class='btn btn-sm btn-primary'>Edit</a>
                                        <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure?\");'>
                                                    <input type='hidden' name='delete_id' value='{$row["a_id"]}' >
                                                    <button type='submit' name='delete_data' class='btn btn-sm btn-danger'>Delete</button>
                                                </form>
                                    ";
                                ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5">No avatars found.</td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>