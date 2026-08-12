<?php
require BASE_PATH . "/components/auth_check.php";
require BASE_PATH . "/components/header.php";
require BASE_PATH . "/components/sidebar.php";

$sql = "SELECT 
            p.pkg_id,
            p.pkg_title,
            p.pkg_img,
            p.pkg_desc,
            p.pkg_duration,
            p.pkg_price,
            p.pkg_status,
            s.state_name
        FROM tbl_package p
        LEFT JOIN tbl_states s 
            ON p.state_id = s.state_id
        ORDER BY p.pkg_id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}

if (isset($_POST["delete_pkg"])) {

    $id = (int) $_POST["delete_id"];

    $stmt = $conn->prepare(
        "DELETE FROM tbl_package WHERE pkg_id = ?"
    );

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        $stmt->close();

        header(
            "Location: " . BASE_URL . "/packages"
        );

        exit();
    } else {

        $error = $stmt->error;

        $stmt->close();
    }
}

?>

<main class="main-content">

    <header class="top-header">
        <h2>Package DataList</h2>

        <a href="<?= BASE_URL ?>/packages/add"
            style="
                background:green;
                color:white;
                padding:8px 15px;
                text-decoration:none;
                border-radius:5px;
           ">
            + Add Package
        </a>
    </header>

    <div class="content-body">

        <div style="
            display:flex;
            gap:20px;
            flex-wrap:wrap;
        ">

            <?php if (mysqli_num_rows($result) > 0) { ?>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <div class="pkg-card"
                        style="
                        border:1px solid #ddd;
                        width:30%;
                        min-width:280px;
                        padding:15px;
                        border-radius:10px;
                     ">

                        <!-- Package Image -->
                        <img
                            src="<?= BASE_URL ?>/public/Uploads/Package_img/<?= htmlspecialchars($row['pkg_img']) ?>"
                            width="100%"
                            style="
                            height:200px;
                            object-fit:cover;
                            border-radius:8px;
                        "
                            alt="Package Image">

                        <br><br>

                        <!-- Title -->
                        <h3>
                            <?= htmlspecialchars($row['pkg_title']) ?>
                        </h3>

                        <!-- State -->
                        <b>
                            State:
                            <?= htmlspecialchars($row['state_name'] ?? 'N/A') ?>
                        </b>

                        <!-- Description -->
                        <p>
                            <?= htmlspecialchars($row['pkg_desc']) ?>
                        </p>

                        <!-- Duration -->
                        <p>
                            <b>Duration:</b>
                            <?= htmlspecialchars($row['pkg_duration']) ?>
                        </p>

                        <!-- Price -->
                        <p>
                            <b>Price:</b>
                            ₹<?= number_format($row['pkg_price'], 2) ?>
                        </p>

                        <!-- Status -->
                        <?php if ($row['pkg_status'] == 'Active') { ?>

                            <span style="
                            background:green;
                            color:white;
                            padding:5px 10px;
                            border-radius:5px;
                        ">
                                Active
                            </span>

                        <?php } else { ?>

                            <span style="
                            background:red;
                            color:white;
                            padding:5px 10px;
                            border-radius:5px;
                        ">
                                Inactive
                            </span>

                        <?php } ?>

                        <br><br>

                        <!-- Actions -->

                        <div style="display:flex; gap:5px;">

                            <!-- READ -->
                            <a href="<?= BASE_URL ?>/packages/read?read=<?= $row['pkg_id'] ?>">
                                <button type="button">
                                    Read
                                </button>
                            </a>

                            <!-- EDIT -->
                            <a href="<?= BASE_URL ?>/packages/edit?edit=<?= $row['pkg_id'] ?>">
                                <button type="button">
                                    Edit
                                </button>
                            </a>

                            <!-- DELETE -->
                            <form method="POST"
                                action="<?= BASE_URL ?>/packages"
                                style="display:inline;"
                                onsubmit="return confirm('Are you sure you want to delete this package?');">

                                <input
                                    type="hidden"
                                    name="delete_id"
                                    value="<?= $row['pkg_id'] ?>">

                                <button
                                    type="submit"
                                    name="delete_pkg">
                                    Delete
                                </button>

                            </form>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <p>No Package Found.</p>

            <?php } ?>

        </div>

    </div>

</main>

<?php require BASE_PATH . "/components/footer.php"; ?>