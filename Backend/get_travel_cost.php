<?php
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/dbconn.php';

header('Content-Type: application/json');

$pickup_id = isset($_GET['pickup_id']) ? (int)$_GET['pickup_id'] : 0;
$vehicletype_id = isset($_GET['vehicletype_id']) ? (int)$_GET['vehicletype_id'] : 0;

if ($pickup_id > 0 && $vehicletype_id > 0) {
    $stmt = $conn->prepare("
        SELECT travel_cost_pp
        FROM tbl_pickup_travelcost
        WHERE pickup_id = ?
        AND vehicletype_id = ?
    ");

    if ($stmt) {
        $stmt->bind_param("ii", $pickup_id, $vehicletype_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $row = $res->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'travel_cost_pp' => (float)$row['travel_cost_pp']
            ]);
            $stmt->close();
            exit();
        }
        $stmt->close();
    }
}

echo json_encode([
    'success' => false,
    'travel_cost_pp' => 0
]);
