<?php
header('Content-Type: application/json');

$data = file_get_contents('events_data.json');
$events = json_decode($data, true);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $event = array_filter($events, function($item) use ($id) {
        return $item['id'] == $id;
    });
    echo json_encode(array_values($event)[0] ?? null);
} else {
    echo $data;
}
?>