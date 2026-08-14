<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php

if (isset($_GET['delete'])) {

    $u_id = (int) $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM tbl_users
        WHERE u_id = ?
    ");

    $stmt->bind_param("i", $u_id);

    if ($stmt->execute()) {
        header("Location: users.php");
        exit();
    } else {
        echo "Delete Error: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "
    SELECT
        u.u_id,
        u.u_name,
        u.u_email,
        u.u_phone,
        u.u_gender,
        u.u_status,
        a.a_img,
        u.created_at
    FROM tbl_users u
    LEFT JOIN tbl_avatar a
        ON u.a_id = a.a_id
    ORDER BY u.u_id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h2> Users </h2>
    </header>
    <div class="content-body">
        <table border="1" cellpadding="10">

            <tr>
                <th>ID</th>
                <th>Avatar</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <tr>

                    <td><?= $row['u_id'] ?></td>

                    <td>
                        <?php if (!empty($row['avatar_img'])) { ?>
                            <img
                                src="public/Uploads/Avatar_img/<?= htmlspecialchars($row['avatar_img']) ?>"
                                width="50">
                        <?php } ?>
                    </td>

                    <td><?= htmlspecialchars($row['u_name']) ?></td>

                    <td><?= htmlspecialchars($row['u_email']) ?></td>

                    <td><?= htmlspecialchars($row['u_phone']) ?></td>

                    <td><?= htmlspecialchars($row['u_gender']) ?></td>

                    <td><?= htmlspecialchars($row['u_status']) ?></td>

                    <td>
                        <a
                            href="users.php?delete=<?= $row['u_id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this user?');">
                            <button type="button">Delete</button>
                        </a>
                    </td>

                </tr>

            <?php } ?>

        </table>
    </div>
</main>