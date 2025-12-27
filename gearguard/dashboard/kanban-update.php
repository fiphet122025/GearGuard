<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['request_id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$requestId = (int)$data['request_id'];
$status = $data['status'];

$role = strtolower(trim($_SESSION['user']['role'] ?? ''));
$userId = (int)($_SESSION['user']['id'] ?? 0);

// Only technicians can update status via drag-and-drop
if ($role !== ROLE_TECHNICIAN) {
    http_response_code(403);
    echo json_encode(['error' => 'Only technicians may update status']);
    exit;
}

// Ensure the technician is assigned to the request OR the request is unassigned.
// If unassigned, claim it for the current technician.
$stmt = $conn->prepare("SELECT assigned_technician_id FROM maintenance_requests WHERE id = ? FOR UPDATE");
$stmt->bind_param('i', $requestId);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$assigned = isset($res['assigned_technician_id']) ? (int)$res['assigned_technician_id'] : 0;

if ($assigned === 0) {
    // Claim the task for this technician
    $claim = $conn->prepare("UPDATE maintenance_requests SET assigned_technician_id = ? WHERE id = ?");
    $claim->bind_param('ii', $userId, $requestId);
    $claim->execute();
    $assigned = $userId;
}

if ($assigned !== $userId) {
    http_response_code(403);
    echo json_encode(['error' => 'Not allowed']);
    exit;
}

// Validate status against known set
$valid = ['New','In Progress','Repaired','Scrap'];
if (!in_array($status, $valid, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

$stmt = $conn->prepare("UPDATE maintenance_requests SET status = ? WHERE id = ?");
$stmt->bind_param('si', $status, $requestId);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB error']);
}

?>
