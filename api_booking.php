<?php
header('Content-Type: application/json');
require 'db_connect.php';

// POST: Create new booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reservations 
                                  (room_id, guest_name, guest_email, check_in, check_out, special_requests) 
                                  VALUES (:room_id, :guest_name, :guest_email, :check_in, :check_out, :special_requests)");
            
            $stmt->execute([
                ':room_id' => $input['room_id'],
                ':guest_name' => $input['guest_name'],
                ':guest_email' => $input['guest_email'],
                ':check_in' => $input['check_in'],
                ':check_out' => $input['check_out'],
                ':special_requests' => $input['special_requests']
            ]);
            
            echo json_encode(['status' => 'success', 'booking_id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
}
?>