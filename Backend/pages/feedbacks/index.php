<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
if (isset($_GET['delete'])) {

    $c_id = (int) $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM tbl_contact
        WHERE c_id = ?
    ");

    $stmt->bind_param("i", $c_id);

    if ($stmt->execute()) {
        header("Location: contact.php");
        exit();
    } else {
        echo "Delete Error: " . $stmt->error;
    }

    $stmt->close();
}


/* =========================
   FETCH CONTACT
========================= */

$sql = "
    SELECT
        c.c_id,
        c.c_email,
        c.c_subject,
        c.c_message,
        c.u_id,
        u.u_name,
        c.created_at

    FROM tbl_contact c

    LEFT JOIN tbl_users u
        ON c.u_id = u.u_id

    ORDER BY c.c_id DESC
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
        <h2> Contact/Feedbacks</h2>
    </header>
    <div class="content-body">
        <table border="1" cellpadding="10" cellspacing="0">

            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <tr>

                    <td>
                        <?= $row['c_id'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['u_name'] ?? 'Guest') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['c_email']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['c_subject']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['c_message']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($row['created_at']) ?>
                    </td>

                    <td>

                        <a
                            href="feedbacks?delete=<?= $row['c_id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this message?');">
                            <button type="button">
                                Delete
                            </button>
                        </a>

                    </td>

                </tr>

            <?php } ?>

        </table>
    </div>
</main>