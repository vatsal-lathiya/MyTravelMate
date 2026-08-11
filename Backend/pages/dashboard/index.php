<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Dashboard Overview</h1>
        <div>Admin: <?php echo $_SESSION['sess_name']; ?></div>
    </header>

    <div class="content-body">
        <div class="stats-grid">
            <?php
            // Destinations Count
            $dest_count = 0;
            $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_destination");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $dest_count = $row['cnt'];
            }

            // Blogs Count
            $blog_count = 0;
            $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_blogs");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $blog_count = $row['cnt'];
            }

            // Gallery Count
            $gallery_count = 0;
            $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_gallery");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $gallery_count = $row['cnt'];
            }

            // States Count
            $state_count = 0;
            $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_states");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $state_count = $row['cnt'];
            }

            // Vehicles Count
            $vehicle_count = 0;
            $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_vehicletype");
            if ($res && $row = mysqli_fetch_assoc($res)) {
                $vehicle_count = $row['cnt'];
            }
            ?>

            <div class="stat-card">
                <h3>Total Destinations</h3>
                <div class="value"><?php echo $dest_count; ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Blogs</h3>
                <div class="value"><?php echo $blog_count; ?></div>
            </div>

            <div class="stat-card">
                <h3>Gallery Images</h3>
                <div class="value"><?php echo $gallery_count; ?></div>
            </div>

            <div class="stat-card">
                <h3>States</h3>
                <div class="value"><?php echo $state_count; ?></div>
            </div>

            <div class="stat-card">
                <h3>Vehicles</h3>
                <div class="value"><?php echo $vehicle_count; ?></div>
            </div>

        </div>

        <div class="card">
            <h2>Welcome to MTM Admin Panel</h2>
            <p style="margin-top: 1rem; color: var(--text-muted);">Use the sidebar to navigate through the different sections of the backend. You can manage destinations, blogs, gallery images, and more from this dashboard.</p>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>