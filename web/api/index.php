<?php

$db_host = 'localhost';
$db_username = 'countdown';
$db_password = 'LynzeeRose';
$db_name = 'countdown';

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if($conn->connect_error)
{
	die("Database connection failed: " . $conn->connect_error);
}

if($_POST['action'] == 'list')
{
	$response = getEvents($conn);
}
else
{
	$response = ['error' => 'Invalid request format'];
}

echo json_encode( $response );

function getEvents($conn)
{
	$events = [];

	$stmt = $conn->prepare('SELECT eventId, eventName, UNIX_TIMESTAMP(eventTime) FROM events');
	$stmt->execute();
	$stmt->bind_result($eventId, $eventName, $eventTime);

	while($stmt->fetch())
	{
		$events[] = [
			'id' => $eventId,
			'name' => $eventName,
			'timestamp' => $eventTime
		];
	}

	return $events;
}