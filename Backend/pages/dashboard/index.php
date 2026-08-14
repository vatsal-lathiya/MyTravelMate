<?php require BASE_PATH . "/components/auth_check.php" ?>
<?php require BASE_PATH . "/components/header.php" ?>
<?php require BASE_PATH . "/components/sidebar.php" ?>

<?php
// Destinations / Packages Count
$pkg_count = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_package");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $pkg_count = $row['cnt'];
}

// Bookings Count
$booking_count = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_booking");
if ($res && $row = mysqli_fetch_assoc($res)) {
    $booking_count = $row['cnt'];
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

// Users Count
$users_count = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_users");
if (!$res) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_uauth");
}
if ($res && $row = mysqli_fetch_assoc($res)) {
    $users_count = $row['cnt'];
}

// Fetch Recent 5 Bookings
$recent_bookings_sql = "
    SELECT 
        b.b_id,
        b.b_travel_date,
        b.b_persons,
        b.b_status,
        b.created_at,
        p.pkg_title,
        p.pkg_price,
        u.u_name,
        u.u_email,
        pk.pickup_location,
        vt.vehicle_type,
        tc.travel_cost_pp,
        ((COALESCE(p.pkg_price, 0) + COALESCE(tc.travel_cost_pp, 0)) * b.b_persons) AS calculated_total_price
    FROM tbl_booking b
    LEFT JOIN tbl_package p ON b.pkg_id = p.pkg_id
    LEFT JOIN tbl_users u ON b.u_id = u.u_id
    LEFT JOIN tbl_pickup pk ON b.pickup_id = pk.pickup_id
    LEFT JOIN tbl_vehicletype vt ON b.vehicletype_id = vt.vehicletype_id
    LEFT JOIN tbl_pickup_travelcost tc 
        ON b.pickup_id = tc.pickup_id 
        AND b.vehicletype_id = tc.vehicletype_id
    ORDER BY b.b_id DESC
    LIMIT 5
";
$recent_bookings = mysqli_query($conn, $recent_bookings_sql);
?>

<style>
.badge-status {
    padding: 0.25rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    text-transform: capitalize;
}
.badge-status-confirm {
    background-color: #d1fae5;
    color: #065f46;
}
.badge-status-pending {
    background-color: #fef3c7;
    color: #92400e;
}
.badge-status-cancelled {
    background-color: #fee2e2;
    color: #991b1b;
}
</style>

<main class="main-content">
    <header class="top-header">
        <h1 class="page-title">Dashboard Overview</h1>
        <div>Admin: <?php echo htmlspecialchars($_SESSION['sess_name'] ?? 'Admin'); ?></div>
    </header>

    <div class="content-body">
        <!-- STAT CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="value"><?php echo $booking_count; ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Tour Packages</h3>
                <div class="value"><?php echo $pkg_count; ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="value"><?php echo $users_count; ?></div>
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

        <!-- RECENT 5 BOOKINGS CARD -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-main); margin: 0;">
                    🕒 Recent 5 Bookings
                </h2>
                <a href="<?= BASE_URL ?>/booking" class="btn btn-primary btn-sm">
                    View All Bookings &rarr;
                </a>
            </div>

            <?php if ($recent_bookings && mysqli_num_rows($recent_bookings) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Package</th>
                                <th>Pickup / Vehicle</th>
                                <th>Travel Date</th>
                                <th>Persons</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($b = mysqli_fetch_assoc($recent_bookings)): ?>
                                <tr>
                                    <td style="font-weight: 700; color: var(--primary);">#<?= $b['b_id'] ?></td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($b['u_name'] ?? 'Customer') ?></div>
                                        <div style="font-size: 0.775rem; color: var(--text-muted);"><?= htmlspecialchars($b['u_email'] ?? '') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($b['pkg_title'] ?? 'N/A') ?></td>
                                    <td>
                                        <div style="font-size: 0.875rem;"><?= htmlspecialchars($b['pickup_location'] ?? 'N/A') ?></div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($b['vehicle_type'] ?? 'N/A') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($b['b_travel_date']) ?></td>
                                    <td style="font-weight: 600; text-align: center;"><?= (int)$b['b_persons'] ?></td>
                                    <td style="font-weight: 700; color: #047857;">₹<?= number_format((float)($b['calculated_total_price'] ?? 0), 2) ?></td>
                                    <td>
                                        <span class="badge-status badge-status-<?= strtolower(htmlspecialchars($b['b_status'])) ?>">
                                            <?= htmlspecialchars($b['b_status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: var(--text-muted); padding: 1rem 0;">No bookings created yet.</p>
            <?php endif; ?>
        </div>

        <!-- WELCOME CARD -->
        <div class="card">
            <h2>Welcome to MTM Admin Panel</h2>
            <p style="margin-top: 1rem; color: var(--text-muted);">Use the sidebar to navigate through the different sections of the backend. You can manage destinations, packages, bookings, blogs, gallery images, and more from this dashboard.</p>
        </div>
    </div>
</main>

<?php require BASE_PATH . "/components/footer.php" ?>