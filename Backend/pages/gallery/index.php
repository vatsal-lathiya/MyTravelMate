
<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
if (isset($_POST['delete_data'])) {
    $id = $_POST['delete_id'];
    $stmt = $conn->prepare('DELETE FROM tbl_gallery WHERE g_id=?');
    if ($stmt) {
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "/gallery");
            exit();
        } else {
            $error = $stmt->error;
        }
    }
}
?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Gallery</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/gallery/add" class="btn btn-primary">Add Gallery Image</a>
        </div>
    </header>

    <div class="content-body">
        <?php if(isset($error)): ?>
            <div style="color: var(--danger); margin-bottom: 1rem;"><?php echo $error; ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
            <?php
            $sql = "SELECT tbl_gallery.g_id, tbl_gallery.g_img, tbl_destination.dest_name 
                FROM tbl_gallery, tbl_destination
                WHERE tbl_gallery.dest_id = tbl_destination.dest_id";
            $result = mysqli_query($conn, $sql);
            $count = 0;
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $count++;
                    echo "
                    <div class='card' style='padding: 1rem; display: flex; flex-direction: column;'>
                        <img src='" . BASE_URL . "/public/Uploads/gallery_img/{$row["g_img"]}' style='width: 100%; height: 180px; object-fit: cover; border-radius: var(--radius-md); margin-bottom: 1rem;' alt=''>
                        <h3 style='font-size: 1.1rem; margin-bottom: 0.5rem;'>{$row["dest_name"]}</h3>
                        <p style='color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;'>ID: {$count}</p>
                        
                        <div style='margin-top: auto; display: flex; gap: 0.5rem;'>
                            <a href='" . BASE_URL . "/gallery/edit?edit={$row["g_id"]}' class='btn btn-sm btn-primary' style='flex:1; text-align:center;'>Edit</a>
                            <form method='POST' style='flex:1;' onsubmit='return confirm(\"Are you sure you want to delete this gallery image?\");'>
                                <input type='hidden' name='delete_id' value='{$row["g_id"]}'>
                                <button type='submit' name='delete_data' class='btn btn-sm btn-danger' style='width:100%;'>Delete</button>
                            </form>
                        </div>
                    </div>
                    ";
                }
            } else {
                echo "<div style='grid-column: 1 / -1; text-align: center; color: var(--text-muted);'>No gallery images found</div>";
            }
            ?>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>