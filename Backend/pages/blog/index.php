<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php
if (isset($_POST['delete_data'])) {
    $id = $_POST['delete_id'];
    $stmt = $conn->prepare('DELETE FROM tbl_blogs WHERE blog_id=?');
    if ($stmt) {
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "/blog");
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
        <h1 class="page-title">Blogs</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/blog/add" class="btn btn-primary">Add Blog</a>
        </div>
    </header>

    <div class="content-body">
        <?php if (isset($error)): ?>
            <div style="color: var(--danger); margin-bottom: 1rem;"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Blog Title</th>
                            <th>Blog Image</th>
                            <th>Blog Description</th>
                            <th>Destination</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT tbl_blogs.blog_id,tbl_blogs.blog_title,tbl_blogs.blog_img,tbl_blogs.blog_desc,tbl_destination.dest_name 
                                FROM tbl_blogs,tbl_destination
                                WHERE tbl_blogs.dest_id = tbl_destination.dest_id";
                        $result = mysqli_query($conn, $sql);
                        $count = 0;
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $count++;
                                echo "<tr>
                                        <td>{$count}</td>
                                        <td>" . substr($row["blog_title"], 0, 30) . "...</td>
                                        <td><img src='" . BASE_URL . "/public/Uploads/Blog_img/{$row["blog_img"]}' width='80' height='60' alt=''></td>
                                        <td>" . substr($row["blog_desc"], 0, 50) . "...</td>
                                        <td>{$row["dest_name"]}</td>
                                        <td>
                                            <div class='action-btns'>
                                                <a href='" . BASE_URL . "/blog/read?read={$row["blog_id"]}' class='btn btn-sm btn-secondary' style='background:#4b5563;'>Read</a>
                                                <a href='" . BASE_URL . "/blog/edit?edit={$row["blog_id"]}' class='btn btn-sm btn-primary'>Edit</a>
                                                <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure?\");'>
                                                    <input type='hidden' name='delete_id' value='{$row["blog_id"]}'>
                                                    <button type='submit' name='delete_data' class='btn btn-sm btn-danger'>Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;'>No Blogs Found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>