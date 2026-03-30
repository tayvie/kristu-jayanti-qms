<?php
header('Content-Type: application/json');
include '../config.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }
    
    $name = $data['name'] ?? '';
    $serviceType = $data['service_type'] ?? '';

    if (empty($name) || empty($serviceType)) {
        echo json_encode(['success' => false, 'message' => 'Name and service type are required']);
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();
    
    // Generate queue number (e.g., A001, A002, etc.)
    $prefix = strtoupper(substr($serviceType, 0, 1));
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(queue_number, 2) AS UNSIGNED)) as last_num 
                           FROM customers WHERE queue_number LIKE ? AND DATE(created_at) = CURDATE()");
    $likePattern = $prefix . '%';
    $stmt->execute([$likePattern]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextNum = ($result['last_num'] ?? 0) + 1;
    $queueNumber = $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    
    // Insert customer
    $stmt = $conn->prepare("INSERT INTO customers (queue_number, name, service_type) VALUES (?, ?, ?)");
    $stmt->execute([$queueNumber, $name, $serviceType]);
    
    echo json_encode([
        'success' => true, 
        'queue_number' => $queueNumber,
        'message' => 'Customer added successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>