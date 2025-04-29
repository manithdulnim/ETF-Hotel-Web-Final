<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
	<?php
		header('Content-Type: application/json');

		$input = file_get_contents('php://input');
		$data = $_POST;

		$required = ['firstName', 'lastName', 'email', 'phone', 'service', 'date'];
		$missing = array();
		foreach ($required as $field) {
			if (empty($data[$field])) {
				$missing[] = $field;
			}
		}

		if (!empty($missing)) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Missing required fields: ' . implode(', ', $missing)
			]);
			exit;
		}

		$booking = [
			'firstName' => htmlspecialchars($data['firstName']),
			'lastName' => htmlspecialchars($data['lastName']),
			'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
			'phone' => htmlspecialchars($data['phone']),
			'service' => htmlspecialchars($data['service']),
			'date' => htmlspecialchars($data['date']),
			'message' => htmlspecialchars($data['message']),
			'timestamp' => !empty($data['timestamp']) ? $data['timestamp'] : date('c')
		];

		$file = 'Spa_bookings.json';

		$bookings = [];
		if (file_exists($file)) {
			$json = file_get_contents($file);
			$bookings = json_decode($json, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				$bookings = []; // Reset if corrupted
			}
		}

		$bookings[] = $booking;

		if (file_put_contents($file, json_encode($bookings, JSON_PRETTY_PRINT))) {
			echo json_encode([
				'status' => 'success',
				'message' => 'Booking saved successfully'
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Failed to save booking'
			]);
		}
	?>
</body>
</html>