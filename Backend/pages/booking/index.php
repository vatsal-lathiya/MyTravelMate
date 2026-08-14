<?php
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../../core/config.php';
    require_once __DIR__ . '/../../core/dbconn.php';
}

require_once BASE_PATH . "/components/auth_check.php";

$message = "";
$message_type = ""; // 'success' or 'danger'
$new_booking_id = null;

/* =====================================================
   HANDLE BOOKING SUBMISSION (POST)
   Exact Schema tbl_booking:
   b_id (AUTO_INCREMENT PK)
   pkg_id (INT)
   u_id (INT)
   pickup_id (INT)
   vehicletype_id (INT)
   b_travel_date (DATE)
   b_persons (INT)
   b_status (ENUM/VARCHAR: 'Pending','Confirm','Cancelled')
   created_at (TIMESTAMP)
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $u_id            = isset($_POST['u_id']) ? (int) $_POST['u_id'] : 0;
    $pkg_id          = isset($_POST['pkg_id']) ? (int) $_POST['pkg_id'] : 0;
    $pickup_id       = isset($_POST['pickup_id']) ? (int) $_POST['pickup_id'] : 0;
    $vehicletype_id  = isset($_POST['vehicletype_id']) ? (int) $_POST['vehicletype_id'] : 0;
    $b_travel_date   = isset($_POST['b_travel_date']) ? trim($_POST['b_travel_date']) : '';
    $b_persons       = isset($_POST['b_persons']) ? max(1, (int) $_POST['b_persons']) : 1;
    $b_status        = isset($_POST['b_status']) && in_array($_POST['b_status'], ['Confirm', 'Pending', 'Cancelled']) ? $_POST['b_status'] : 'Pending';

    // Basic Validation
    if ($u_id <= 0) {
        $message = "Please select a customer/user.";
        $message_type = "danger";
    } elseif ($pkg_id <= 0) {
        $message = "Please select a tour package.";
        $message_type = "danger";
    } elseif ($pickup_id <= 0) {
        $message = "Please select a pickup location.";
        $message_type = "danger";
    } elseif ($vehicletype_id <= 0) {
        $message = "Please select a vehicle type.";
        $message_type = "danger";
    } elseif (empty($b_travel_date)) {
        $message = "Please choose a valid travel date.";
        $message_type = "danger";
    } else {

        /* 1. Verify Package Exists */
        $package_sql = "SELECT pkg_id, pkg_price, pkg_title FROM tbl_package WHERE pkg_id = ? LIMIT 1";
        $package_stmt = $conn->prepare($package_sql);
        if ($package_stmt) {
            $package_stmt->bind_param("i", $pkg_id);
            $package_stmt->execute();
            $package_result = $package_stmt->get_result();
            $package = $package_result->fetch_assoc();
            $package_stmt->close();
        } else {
            $package = null;
        }

        if (!$package) {
            $message = "Selected tour package not found.";
            $message_type = "danger";
        } else {

            /* 2. Insert into tbl_booking */
            $insert_sql = "
                INSERT INTO tbl_booking 
                (
                    pkg_id,
                    u_id,
                    pickup_id,
                    vehicletype_id,
                    b_travel_date,
                    b_persons,
                    b_status
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($insert_sql);
            if ($stmt) {
                $stmt->bind_param(
                    "iiiisis",
                    $pkg_id,
                    $u_id,
                    $pickup_id,
                    $vehicletype_id,
                    $b_travel_date,
                    $b_persons,
                    $b_status
                );

                if ($stmt->execute()) {
                    $new_booking_id = $stmt->insert_id;
                    $message = "Booking created successfully! Booking ID: #" . $new_booking_id;
                    $message_type = "success";
                    // Clear POST to avoid duplicate submissions on refresh
                    $_POST = [];
                } else {
                    $message = "Booking Insert Error: " . $stmt->error;
                    $message_type = "danger";
                }
                $stmt->close();
            } else {
                $message = "Database Prepare Error: " . $conn->error;
                $message_type = "danger";
            }
        }
    }
}

/* =====================================================
   FETCH DATA FOR FORM DROPDOWNS & PRICING MATRIX
===================================================== */

// 1. Users / Customers
$user_sql = "SELECT u_id, u_name, u_email FROM tbl_users ORDER BY u_name ASC";
$users_result = mysqli_query($conn, $user_sql);
if (!$users_result) {
    $user_sql = "SELECT u_id, u_name, u_email FROM tbl_uauth ORDER BY u_name ASC";
    $users_result = mysqli_query($conn, $user_sql);
}

// 2. Tour Packages
$pkg_sql = "SELECT pkg_id, pkg_title, pkg_price FROM tbl_package ORDER BY pkg_title ASC";
$packages_result = mysqli_query($conn, $pkg_sql);

// 3. Pickup Locations: tbl_pickup (pickup_id, pickup_location, created_at)
$pickup_sql = "SELECT pickup_id, pickup_location FROM tbl_pickup ORDER BY pickup_location ASC";
$pickups_result = mysqli_query($conn, $pickup_sql);

// 4. Vehicle Types: tbl_vehicletype (vehicletype_id, vehicle_type)
$vehicle_sql = "SELECT vehicletype_id, vehicle_type FROM tbl_vehicletype ORDER BY vehicle_type ASC";
$vehicles_result = mysqli_query($conn, $vehicle_sql);

// 5. Travel Cost Matrix: tbl_pickup_travelcost (travel_cost_id, pickup_id, vehicletype_id, travel_cost_pp, created_At)
$cost_matrix = [];
$matrix_sql = "SELECT pickup_id, vehicletype_id, travel_cost_pp FROM tbl_pickup_travelcost";
$matrix_res = mysqli_query($conn, $matrix_sql);
if ($matrix_res) {
    while ($row = mysqli_fetch_assoc($matrix_res)) {
        $key = $row['pickup_id'] . '_' . $row['vehicletype_id'];
        $cost_matrix[$key] = (float) $row['travel_cost_pp'];
    }
}

// 6. Recent Bookings for Display (Calculates totals via JOINs)
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
    LIMIT 6
";
$recent_bookings_result = mysqli_query($conn, $recent_bookings_sql);

require BASE_PATH . "/components/header.php";
require BASE_PATH . "/components/sidebar.php";
?>

<style>
    /* =====================================================
   BOOKING FORM & REAL-TIME PRICING STYLES
===================================================== */
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --success-color: #10b981;
        --success-hover: #059669;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --card-bg: #ffffff;
        --border-color: #e2e8f0;
        --bg-muted: #f8fafc;
        --text-primary: #1e293b;
        --text-muted: #64748b;
    }

    .booking-wrapper {
        max-width: 1300px;
        margin: 0 auto;
        padding-bottom: 2rem;
    }

    /* Page Header & Navigation */
    .booking-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-title-group h1 {
        font-size: 1.65rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .header-title-group p {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Alert Notification */
    .custom-alert {
        padding: 1rem 1.25rem;
        border-radius: 0.625rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.95rem;
        font-weight: 500;
        animation: fadeIn 0.3s ease;
    }

    .custom-alert-success {
        background-color: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .custom-alert-danger {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* Grid Layout: 2 Columns */
    .booking-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1.75rem;
        align-items: start;
        margin-bottom: 2.5rem;
    }

    @media (max-width: 1024px) {
        .booking-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Form Card */
    .form-card {
        background: var(--card-bg);
        border-radius: 0.875rem;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        padding: 1.75rem;
    }

    .card-heading {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .form-field {
        display: flex;
        flex-direction: column;
    }

    .form-field.full-width {
        grid-column: 1 / -1;
    }

    .field-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .field-label .req {
        color: var(--danger-color);
    }

    .field-input,
    .field-select {
        width: 100%;
        padding: 0.7rem 0.9rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        font-size: 0.925rem;
        color: #1e293b;
        background-color: #ffffff;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .field-input:focus,
    .field-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        background-color: #ffffff;
    }

    .field-hint {
        font-size: 0.775rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
    }

    /* Counter Control for Persons */
    .persons-stepper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stepper-btn {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        border: none;
        border-radius: 0.5rem;
        font-size: 1.2rem;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .stepper-btn:hover {
        background: #cbd5e1;
        color: #0f172a;
    }

    /* Pricing Summary Box (Sticky) */
    .pricing-sidebar {
        position: sticky;
        top: 90px;
    }

    .pricing-card {
        background: #ffffff;
        border-radius: 0.875rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .pricing-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .pricing-header h3 {
        font-size: 1.05rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .live-badge {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, 0.4);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .live-badge .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #34d399;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.9);
            opacity: 0.7;
        }

        50% {
            transform: scale(1.3);
            opacity: 1;
        }

        100% {
            transform: scale(0.9);
            opacity: 0.7;
        }
    }

    .pricing-body {
        padding: 1.5rem;
    }

    .section-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.75rem;
        padding-bottom: 0.25rem;
        border-bottom: 1px dashed #e2e8f0;
    }

    .price-row-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.9rem;
        color: #334155;
    }

    .price-row-item .item-title {
        display: flex;
        flex-direction: column;
    }

    .price-row-item .item-sub {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .price-row-item .item-val {
        font-weight: 600;
        color: #0f172a;
        font-feature-settings: "tnum";
        transition: all 0.3s ease;
    }

    .price-highlight-box {
        background: #f1f5f9;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        margin: 0.75rem 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .price-highlight-box .hl-label {
        font-size: 0.825rem;
        font-weight: 600;
        color: #475569;
    }

    .price-highlight-box .hl-val {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .grand-total-container {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
        border: 1px solid #c7d2fe;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-top: 1.25rem;
        text-align: center;
    }

    .grand-total-container .gt-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #4338ca;
        margin-bottom: 0.25rem;
    }

    .grand-total-container .gt-amount {
        font-size: 1.85rem;
        font-weight: 800;
        color: #1e1b4b;
        margin: 0.25rem 0;
        font-feature-settings: "tnum";
        transition: all 0.3s ease;
    }

    .grand-total-container .gt-sub {
        font-size: 0.75rem;
        color: #6366f1;
    }

    /* Subtle animation on price change */
    .price-pulse {
        animation: flashPrice 0.4s ease;
    }

    @keyframes flashPrice {
        0% {
            transform: scale(1);
            color: #4f46e5;
        }

        50% {
            transform: scale(1.1);
            color: #10b981;
        }

        100% {
            transform: scale(1);
        }
    }

    /* Form Actions */
    .form-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .btn-submit-booking {
        flex: 1;
        background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
        color: white;
        font-size: 1rem;
        font-weight: 600;
        padding: 0.85rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-submit-booking:hover {
        background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
    }

    .btn-reset-form {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        padding: 0.85rem 1.25rem;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-reset-form:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    /* Recent Bookings Table */
    .recent-bookings-card {
        background: #ffffff;
        border-radius: 0.875rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 1.75rem;
        margin-top: 1rem;
    }

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
    <div class="booking-wrapper">

        <!-- HEADER BAR -->
        <div class="booking-header-bar">
            <div class="header-title-group">
                <h1>Book Tour Package</h1>
                <p>Select customer, tour package, pickup location, and vehicle type to calculate live pricing.</p>
            </div>
            <div class="header-actions">
                <a href="<?= BASE_URL ?>/booking" class="btn btn-secondary">
                    📋 View All Bookings
                </a>
            </div>
        </div>

        <!-- STATUS MESSAGE -->
        <?php if (!empty($message)): ?>
            <div class="custom-alert custom-alert-<?= $message_type ?>">
                <div>
                    <strong><?= $message_type === 'success' ? '✓ Success: ' : '⚠ Notice: ' ?></strong>
                    <?= htmlspecialchars($message) ?>
                </div>
                <?php if ($new_booking_id): ?>
                    <a href="<?= BASE_URL ?>/booking" style="color: inherit; text-decoration: underline; font-weight: 600;">
                        View All Bookings &rarr;
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- MAIN BOOKING GRID: FORM (LEFT) + LIVE PRICING (RIGHT) -->
        <div class="booking-grid">

            <!-- LEFT COLUMN: BOOKING FORM -->
            <div class="form-card">
                <div class="card-heading">
                    <span>📝 Booking Information</span>
                    <span style="font-size: 0.8rem; font-weight: normal; color: #64748b;">
                        * Required fields
                    </span>
                </div>

                <form method="POST" action="" id="fullBookingForm">
                    <!-- Hidden field to ensure POST detection -->
                    <input type="hidden" name="submit_booking" value="1">

                    <div class="form-row">
                        <!-- CUSTOMER / USER (u_id) -->
                        <div class="form-field full-width">
                            <label class="field-label" for="u_id">
                                👤 Customer / User <span class="req">*</span>
                            </label>
                            <select name="u_id" id="u_id" class="field-select" required>
                                <option value="">-- Choose Customer --</option>
                                <?php if ($users_result): ?>
                                    <?php mysqli_data_seek($users_result, 0); ?>
                                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                        <option value="<?= htmlspecialchars($user['u_id']) ?>"
                                            <?= (isset($_POST['u_id']) && $_POST['u_id'] == $user['u_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['u_name'] ?? 'User') ?> (<?= htmlspecialchars($user['u_email']) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                            <span class="field-hint">Select the registered user for this booking (u_id in tbl_booking).</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- TOUR PACKAGE (pkg_id) -->
                        <div class="form-field full-width">
                            <label class="field-label" for="pkg_id">
                                🏖️ Tour Package <span class="req">*</span>
                            </label>
                            <select name="pkg_id" id="pkg_id" class="field-select" required>
                                <option value="" data-price="0">-- Select Tour Package --</option>
                                <?php if ($packages_result): ?>
                                    <?php mysqli_data_seek($packages_result, 0); ?>
                                    <?php while ($pkg = mysqli_fetch_assoc($packages_result)): ?>
                                        <option
                                            value="<?= htmlspecialchars($pkg['pkg_id']) ?>"
                                            data-price="<?= htmlspecialchars($pkg['pkg_price']) ?>"
                                            data-title="<?= htmlspecialchars($pkg['pkg_title']) ?>"
                                            <?= (isset($_POST['pkg_id']) && $_POST['pkg_id'] == $pkg['pkg_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($pkg['pkg_title']) ?> - ₹<?= number_format((float)$pkg['pkg_price'], 2) ?> / person
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                            <span class="field-hint">Select package to calculate base tour package cost per person (pkg_id in tbl_booking).</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- PICKUP LOCATION: tbl_pickup (pickup_id, pickup_location) -->
                        <div class="form-field">
                            <label class="field-label" for="pickup_id">
                                📍 Choose Location <span class="req">*</span>
                            </label>
                            <select name="pickup_id" id="pickup_id" class="field-select" required>
                                <option value="">-- Select Pickup Location --</option>
                                <?php if ($pickups_result): ?>
                                    <?php mysqli_data_seek($pickups_result, 0); ?>
                                    <?php while ($pk = mysqli_fetch_assoc($pickups_result)): ?>
                                        <option
                                            value="<?= htmlspecialchars($pk['pickup_id']) ?>"
                                            data-name="<?= htmlspecialchars($pk['pickup_location']) ?>"
                                            <?= (isset($_POST['pickup_id']) && $_POST['pickup_id'] == $pk['pickup_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($pk['pickup_location']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                            <span class="field-hint">Pickup point from tbl_pickup (pickup_id in tbl_booking).</span>
                        </div>

                        <!-- VEHICLE TYPE: tbl_vehicletype (vehicletype_id, vehicle_type) -->
                        <div class="form-field">
                            <label class="field-label" for="vehicletype_id">
                                🚗 Choose Vehicle <span class="req">*</span>
                            </label>
                            <select name="vehicletype_id" id="vehicletype_id" class="field-select" required>
                                <option value="">-- Select Vehicle Type --</option>
                                <?php if ($vehicles_result): ?>
                                    <?php mysqli_data_seek($vehicles_result, 0); ?>
                                    <?php while ($vh = mysqli_fetch_assoc($vehicles_result)): ?>
                                        <option
                                            value="<?= htmlspecialchars($vh['vehicletype_id']) ?>"
                                            data-name="<?= htmlspecialchars($vh['vehicle_type']) ?>"
                                            <?= (isset($_POST['vehicletype_id']) && $_POST['vehicletype_id'] == $vh['vehicletype_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($vh['vehicle_type']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                            <span class="field-hint">Vehicle type from tbl_vehicletype (vehicletype_id in tbl_booking).</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- TRAVEL DATE (b_travel_date) -->
                        <div class="form-field">
                            <label class="field-label" for="b_travel_date">
                                📅 Travel Date <span class="req">*</span>
                            </label>
                            <input
                                type="date"
                                name="b_travel_date"
                                id="b_travel_date"
                                class="field-input"
                                value="<?= isset($_POST['b_travel_date']) && !empty($_POST['b_travel_date']) ? htmlspecialchars($_POST['b_travel_date']) : date('Y-m-d') ?>"
                                min="<?= date('Y-m-d') ?>"
                                required>
                            <span class="field-hint">b_travel_date in tbl_booking.</span>
                        </div>

                        <!-- NUMBER OF PERSONS (b_persons) -->
                        <div class="form-field">
                            <label class="field-label" for="b_persons">
                                👥 Number of Persons <span class="req">*</span>
                            </label>
                            <div class="persons-stepper">
                                <button type="button" class="stepper-btn" id="btnDecrement">-</button>
                                <input
                                    type="number"
                                    name="b_persons"
                                    id="b_persons"
                                    class="field-input"
                                    min="1"
                                    max="100"
                                    value="<?= isset($_POST['b_persons']) && (int)$_POST['b_persons'] > 0 ? (int)$_POST['b_persons'] : 1 ?>"
                                    style="text-align: center; font-weight: 700;"
                                    required>
                                <button type="button" class="stepper-btn" id="btnIncrement">+</button>
                            </div>
                            <span class="field-hint">b_persons in tbl_booking.</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- BOOKING STATUS (b_status) -->
                        <div class="form-field full-width">
                            <label class="field-label" for="b_status">
                                🏷️ Booking Status
                            </label>
                            <select name="b_status" id="b_status" class="field-select">
                                <option value="Pending" <?= (!isset($_POST['b_status']) || $_POST['b_status'] == 'Pending') ? 'selected' : '' ?>>Pending (Default)</option>
                                <option value="Confirm" <?= (isset($_POST['b_status']) && $_POST['b_status'] == 'Confirm') ? 'selected' : '' ?>>Confirm</option>
                                <option value="Cancelled" <?= (isset($_POST['b_status']) && $_POST['b_status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <span class="field-hint">b_status in tbl_booking.</span>
                        </div>
                    </div>

                    <!-- FORM SUBMIT BUTTONS -->
                    <div class="form-buttons">
                        <button type="submit" class="btn-submit-booking" id="btnSubmitBooking">
                            ✓ Confirm & Create Booking
                        </button>
                        <button type="reset" class="btn-reset-form" id="btnResetForm">
                            Reset
                        </button>
                    </div>

                </form>
            </div>

            <!-- RIGHT COLUMN: LIVE REAL-TIME PRICE BREAKDOWN -->
            <div class="pricing-sidebar">
                <div class="pricing-card">

                    <div class="pricing-header">
                        <h3>💳 Price Breakdown</h3>
                        <span class="live-badge">
                            <span class="dot"></span> Live Calc
                        </span>
                    </div>

                    <div class="pricing-body">

                        <!-- PER PERSON RATES -->
                        <div class="section-label">Per-Person Pricing</div>

                        <div class="price-row-item">
                            <div class="item-title">
                                <span>Package Price</span>
                                <span class="item-sub" id="lbl_pkg_name">None selected</span>
                            </div>
                            <div class="item-val">
                                ₹<span id="dsp_pkg_pp">0.00</span>
                            </div>
                        </div>

                        <div class="price-row-item">
                            <div class="item-title">
                                <span>Travel Cost / Person (travel_cost_pp)</span>
                                <span class="item-sub" id="lbl_travel_route">Pickup + Vehicle</span>
                            </div>
                            <div class="item-val">
                                ₹<span id="dsp_travel_pp">0.00</span>
                            </div>
                        </div>

                        <div class="price-highlight-box">
                            <span class="hl-label">Combined / Person:</span>
                            <span class="hl-val">
                                ₹<span id="dsp_pp_total">0.00</span>
                            </span>
                        </div>

                        <!-- TRAVELER COUNT & SUBTOTALS -->
                        <div class="section-label" style="margin-top: 1.25rem;">Subtotals for Group</div>

                        <div class="price-row-item">
                            <div class="item-title">
                                <span>Persons (b_persons)</span>
                            </div>
                            <div class="item-val">
                                <span id="dsp_persons_count">1</span> Person(s)
                            </div>
                        </div>

                        <div class="price-row-item">
                            <div class="item-title">
                                <span>Package Subtotal</span>
                                <span class="item-sub" id="dsp_pkg_calc">₹0.00 × 1</span>
                            </div>
                            <div class="item-val">
                                ₹<span id="dsp_pkg_total">0.00</span>
                            </div>
                        </div>

                        <div class="price-row-item">
                            <div class="item-title">
                                <span>Travel Subtotal</span>
                                <span class="item-sub" id="dsp_travel_calc">₹0.00 × 1</span>
                            </div>
                            <div class="item-val">
                                ₹<span id="dsp_travel_total">0.00</span>
                            </div>
                        </div>

                        <!-- GRAND TOTAL -->
                        <div class="grand-total-container">
                            <div class="gt-label">Estimated Total Price</div>
                            <div class="gt-amount">
                                ₹<span id="dsp_grand_total">0.00</span>
                            </div>
                            <div class="gt-sub">(Package Rate + travel_cost_pp) × b_persons</div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- RECENT BOOKINGS TABLE -->
        <?php if ($recent_bookings_result && mysqli_num_rows($recent_bookings_result) > 0): ?>
            <div class="recent-bookings-card">
                <div class="card-heading">
                    <span>🕒 Recent Bookings</span>
                    <a href="<?= BASE_URL ?>/booking" style="font-size: 0.85rem; color: var(--primary-color); text-decoration: none; font-weight: 600;">
                        View All &rarr;
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>b_id</th>
                                <th>Customer (u_id)</th>
                                <th>Package (pkg_id)</th>
                                <th>Pickup / Vehicle</th>
                                <th>Travel Date</th>
                                <th>Persons</th>
                                <th>travel_cost_pp</th>
                                <th>Total Price</th>
                                <th>b_status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($b = mysqli_fetch_assoc($recent_bookings_result)): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #4338ca;">#<?= $b['b_id'] ?></td>
                                    <td>
                                        <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($b['u_name'] ?? 'N/A') ?></div>
                                        <div style="font-size: 0.775rem; color: #64748b;"><?= htmlspecialchars($b['u_email'] ?? '') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($b['pkg_title'] ?? 'N/A') ?></td>
                                    <td>
                                        <div style="font-size: 0.85rem; color: #1e293b;"><?= htmlspecialchars($b['pickup_location'] ?? 'N/A') ?></div>
                                        <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($b['vehicle_type'] ?? 'N/A') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($b['b_travel_date']) ?></td>
                                    <td style="font-weight: 600; text-align: center;"><?= (int)$b['b_persons'] ?></td>
                                    <td>₹<?= number_format((float)($b['travel_cost_pp'] ?? 0), 2) ?></td>
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
            </div>
        <?php endif; ?>

    </div>
</main>

<!-- =====================================================
     REAL-TIME DYNAMIC PRICING JAVASCRIPT
===================================================== -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Preloaded Travel Cost Matrix from tbl_pickup_travelcost for 0ms instantaneous updates
        const travelCostMatrix = <?= json_encode($cost_matrix, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?> || {};

        // 2. DOM Elements
        const pkgSelect = document.getElementById("pkg_id");
        const pickupSelect = document.getElementById("pickup_id");
        const vehicleSelect = document.getElementById("vehicletype_id");
        const personsInput = document.getElementById("b_persons");
        const btnIncrement = document.getElementById("btnIncrement");
        const btnDecrement = document.getElementById("btnDecrement");
        const form = document.getElementById("fullBookingForm");
        const submitBtn = document.getElementById("btnSubmitBooking");

        // Price Display Elements
        const dspPkgPP = document.getElementById("dsp_pkg_pp");
        const dspTravelPP = document.getElementById("dsp_travel_pp");
        const dspPPTotal = document.getElementById("dsp_pp_total");
        const dspPersons = document.getElementById("dsp_persons_count");
        const dspPkgTotal = document.getElementById("dsp_pkg_total");
        const dspTravelTotal = document.getElementById("dsp_travel_total");
        const dspGrandTotal = document.getElementById("dsp_grand_total");

        // Label Details
        const lblPkgName = document.getElementById("lbl_pkg_name");
        const lblTravelRoute = document.getElementById("lbl_travel_route");
        const dspPkgCalc = document.getElementById("dsp_pkg_calc");
        const dspTravelCalc = document.getElementById("dsp_travel_calc");

        // State
        let currentPkgPrice = 0;
        let currentTravelPrice = 0;

        // Helper: Currency Formatter (Indian Rupee style)
        function formatCurrency(val) {
            const num = parseFloat(val) || 0;
            return num.toLocaleString("en-IN", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Helper: Trigger visual highlight animation on update
        function triggerPulse(el) {
            if (!el) return;
            el.classList.remove("price-pulse");
            void el.offsetWidth; // Force DOM reflow
            el.classList.add("price-pulse");
        }

        // Step 1: Update Package Price on change
        function updatePackageInfo() {
            if (!pkgSelect || pkgSelect.selectedIndex <= 0) {
                currentPkgPrice = 0;
                if (lblPkgName) lblPkgName.innerText = "None selected";
            } else {
                const opt = pkgSelect.options[pkgSelect.selectedIndex];
                currentPkgPrice = parseFloat(opt.getAttribute("data-price")) || 0;
                if (lblPkgName) lblPkgName.innerText = opt.getAttribute("data-title") || opt.text.split(" - ")[0];
            }
            calculateAllTotals();
        }

        // Step 2: Update Travel Cost on change (tbl_pickup_travelcost: pickup_id + vehicletype_id -> travel_cost_pp)
        async function updateTravelCost() {
            const pickupId = pickupSelect ? pickupSelect.value : "";
            const vehicleId = vehicleSelect ? vehicleSelect.value : "";

            // Update Subtitle Label
            if (pickupSelect && vehicleSelect && pickupId && vehicleId) {
                const pText = pickupSelect.options[pickupSelect.selectedIndex].text;
                const vText = vehicleSelect.options[vehicleSelect.selectedIndex].text;
                if (lblTravelRoute) lblTravelRoute.innerText = pText + " (" + vText + ")";
            } else if (lblTravelRoute) {
                lblTravelRoute.innerText = "Pickup + Vehicle";
            }

            if (!pickupId || !vehicleId) {
                currentTravelPrice = 0;
                calculateAllTotals();
                return;
            }

            // Fast path: Check preloaded matrix
            const key = pickupId + "_" + vehicleId;
            if (typeof travelCostMatrix[key] !== "undefined") {
                currentTravelPrice = parseFloat(travelCostMatrix[key]) || 0;
                calculateAllTotals();
                return;
            }

            // Async path: Fetch via get_travel_cost.php endpoint
            try {
                const url = "<?= BASE_URL ?>/get_travel_cost.php?pickup_id=" +
                    encodeURIComponent(pickupId) +
                    "&vehicletype_id=" +
                    encodeURIComponent(vehicleId);

                const response = await fetch(url);
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        currentTravelPrice = parseFloat(data.travel_cost_pp) || 0;
                        travelCostMatrix[key] = currentTravelPrice; // Cache for next time
                    } else {
                        currentTravelPrice = 0;
                    }
                } else {
                    currentTravelPrice = 0;
                }
            } catch (e) {
                console.error("Travel cost fetch error:", e);
                currentTravelPrice = 0;
            }

            calculateAllTotals();
        }

        // Step 3: Master Calculation & Live UI Update
        function calculateAllTotals() {
            const persons = Math.max(1, parseInt(personsInput ? personsInput.value : 1) || 1);

            const perPersonTotal = currentPkgPrice + currentTravelPrice;
            const packageSubtotal = currentPkgPrice * persons;
            const travelSubtotal = currentTravelPrice * persons;
            const grandTotal = packageSubtotal + travelSubtotal;

            // Render Values
            if (dspPkgPP) dspPkgPP.innerText = formatCurrency(currentPkgPrice);
            if (dspTravelPP) dspTravelPP.innerText = formatCurrency(currentTravelPrice);
            if (dspPPTotal) dspPPTotal.innerText = formatCurrency(perPersonTotal);
            if (dspPersons) dspPersons.innerText = persons;
            if (dspPkgTotal) dspPkgTotal.innerText = formatCurrency(packageSubtotal);
            if (dspTravelTotal) dspTravelTotal.innerText = formatCurrency(travelSubtotal);
            if (dspGrandTotal) dspGrandTotal.innerText = formatCurrency(grandTotal);

            // Render Formulas in Subtitles
            if (dspPkgCalc) dspPkgCalc.innerText = "₹" + formatCurrency(currentPkgPrice) + " × " + persons;
            if (dspTravelCalc) dspTravelCalc.innerText = "₹" + formatCurrency(currentTravelPrice) + " × " + persons;

            // Visual flash feedback
            triggerPulse(dspPkgPP);
            triggerPulse(dspTravelPP);
            triggerPulse(dspPPTotal);
            triggerPulse(dspPkgTotal);
            triggerPulse(dspTravelTotal);
            triggerPulse(dspGrandTotal);
        }

        // Event Listeners for Changes
        if (pkgSelect) {
            pkgSelect.addEventListener("change", updatePackageInfo);
        }

        if (pickupSelect) {
            pickupSelect.addEventListener("change", updateTravelCost);
        }

        if (vehicleSelect) {
            vehicleSelect.addEventListener("change", updateTravelCost);
        }

        if (personsInput) {
            personsInput.addEventListener("input", calculateAllTotals);
            personsInput.addEventListener("change", calculateAllTotals);
        }

        // Stepper Button Listeners (+ and -)
        if (btnIncrement && personsInput) {
            btnIncrement.addEventListener("click", function() {
                let val = parseInt(personsInput.value) || 1;
                personsInput.value = val + 1;
                calculateAllTotals();
            });
        }

        if (btnDecrement && personsInput) {
            btnDecrement.addEventListener("click", function() {
                let val = parseInt(personsInput.value) || 1;
                if (val > 1) {
                    personsInput.value = val - 1;
                    calculateAllTotals();
                }
            });
        }

        // Reset Form button
        const btnReset = document.getElementById("btnResetForm");
        if (btnReset) {
            btnReset.addEventListener("click", function() {
                setTimeout(function() {
                    updatePackageInfo();
                    updateTravelCost();
                }, 50);
            });
        }

        // Initial run on page load
        updatePackageInfo();
        updateTravelCost();
    });
</script>

<?php require BASE_PATH . "/components/footer.php"; ?>