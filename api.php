<?php
header('Content-Type: application/json');
require 'db_connect.php';

$dataFile = 'data/records.json';

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// GET: Fetch all or single record
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Fetch single room
        try {
            $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Room not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    } else {
        // Fetch all rooms
        try {
            $stmt = $pdo->query("SELECT * FROM rooms");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($results);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    }
}

// POST: Add new record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input) {
        try {
            $stmt = $pdo->prepare("INSERT INTO rooms (id, type, description, price, amenities, image_url) 
                                  VALUES (:id, :type, :description, :price, :amenities, :image_url)");
            
            $stmt->execute([
                ':id' => $input['id'],
                ':type' => $input['type'],
                ':description' => $input['description'],
                ':price' => $input['price'],
                ':amenities' => json_encode($input['amenities']),
                ':image_url' => $input['image_url']
            ]);
            
            echo json_encode(['status' => 'success', 'id' => $input['id']]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
}

// PUT: Update record
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("UPDATE rooms SET
                                  type = :type,
                                  description = :description,
                                  price = :price,
                                  amenities = :amenities,
                                  image_url = :image_url
                                  WHERE id = :id");
            
            $stmt->execute([
                ':id' => $_GET['id'],
                ':type' => $input['type'],
                ':description' => $input['description'],
                ':price' => $input['price'],
                ':amenities' => json_encode($input['amenities']),
                ':image_url' => $input['image_url']
            ]);
            
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Database error']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
}

// DELETE: Remove record
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Room not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID required']);
    }
}
?>