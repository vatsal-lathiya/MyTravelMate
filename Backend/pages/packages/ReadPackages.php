<?php

require BASE_PATH . "/components/auth_check.php";

if (!isset($_GET['read']) || $_GET['read'] == "") {

    header("Location: " . BASE_URL . "/package");
    exit();
}

$id = intval($_GET['read']);


$sql = "SELECT
            p.*,
            s.state_name
        FROM tbl_package p

        LEFT JOIN tbl_states s
            ON p.state_id = s.state_id

        WHERE p.pkg_id = $id";


$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Package not found");
}

?>

<?php require BASE_PATH . "/components/header.php"; ?>
<?php require BASE_PATH . "/components/sidebar.php"; ?>


<main class="main-content">

    <header class="top-header">

        <h2>
            Read Package
        </h2>

        <div>

            <a href="<?= BASE_URL ?>/package">
                Back
            </a>

            <a href="<?= BASE_URL ?>/package/edit?edit=<?= $row['pkg_id'] ?>">
                Edit
            </a>

        </div>

    </header>


    <div class="content-body">

        <div
            style="
                max-width:900px;
                margin:auto;
                border:1px solid #ddd;
                padding:20px;
                border-radius:10px;
            ">

            <?php if (!empty($row['pkg_img'])) { ?>

                <img
                    src="<?= BASE_URL ?>/public/Uploads/Package_img/<?= htmlspecialchars($row['pkg_img']) ?>"
                    width="100%"
                    style="
                        max-height:400px;
                        object-fit:cover;
                        border-radius:10px;
                    ">

            <?php } ?>


            <h1>
                <?= htmlspecialchars($row['pkg_title']) ?>
            </h1>


            <h3>
                State:
                <?= htmlspecialchars($row['state_name'] ?? 'N/A') ?>
            </h3>


            <p>

                <b>Duration:</b>

                <?= htmlspecialchars($row['pkg_duration']) ?>

            </p>


            <p>

                <b>Price:</b>

                ₹<?= number_format($row['pkg_price'], 2) ?>

            </p>


            <p>

                <b>Status:</b>

                <?= htmlspecialchars($row['pkg_status']) ?>

            </p>


            <hr>


            <h3>
                Package Description
            </h3>


            <p
                style="
                    line-height:1.8;
                    white-space:pre-wrap;
                ">
                <?= htmlspecialchars($row['pkg_desc']) ?>
            </p>


        </div>

    </div>

</main>


<?php require BASE_PATH . "/components/footer.php"; ?>