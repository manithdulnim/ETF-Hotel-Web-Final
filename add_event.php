<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$data = json_decode(file_get_contents('events_data.json'), true);

// Generate new ID
$newId = max(array_column($data, 'id')) + 1;
$input['id'] = $newId;
$input['timestamp'] = date('c');

$data[] = $input;
file_put_contents('events_data.json', json_encode($data, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'id' => $newId]);
?>