<?php  ?>
<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
if (!isset($_GET["read"]) || $_GET["read"] === "") {
    header("Location: " . BASE_URL . "/blog");
    exit();
}

$sql = 'SELECT tbl_blogs.*, tbl_destination.dest_name FROM tbl_blogs JOIN tbl_destination ON tbl_blogs.dest_id = tbl_destination.dest_id WHERE blog_id = ' . intval($_GET["read"]);
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Blog not found");
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Read Blog</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/blog" class="btn btn-secondary">Back to List</a>
            <a href="<?php echo BASE_URL; ?>/blog/edit?edit=<?php echo $row['blog_id']; ?>" class="btn btn-primary">
                Edit Blog
            </a>
        </div>
    </header>

    <div class="content-body">
        <div class="card" style="max-width: 900px; margin: 0 auto;">
            <h1 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($row['blog_title']); ?></h1>
            <div style="color: var(--text-muted); margin-bottom: 1.5rem;">Destination: <?php echo htmlspecialchars($row['dest_name']); ?></div>

            <div style="margin-bottom: 2rem;">
                <img src="<?php echo BASE_URL; ?>/public/Uploads/Blog_img/<?php echo htmlspecialchars($row['blog_img']); ?>" style="width: 100%; border-radius: var(--radius-lg);" alt="Blog Image">
            </div>

            <div style="line-height: 1.8; color: var(--text-main); white-space: pre-wrap; font-size: 1.1rem;">
                <?php echo htmlspecialchars($row['blog_desc']); ?>
            </div>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>