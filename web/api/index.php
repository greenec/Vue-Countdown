<?php

$db_host = 'localhost';
$db_username = '';
$db_password = '';
$db_name = '';

date_default_timezone_set('America/New_York');

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if($conn->connect_error)
{
	die("Database connection failed: " . $conn->connect_error);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if($action == 'list')
{
	$response = getEvents($conn);
}
else if($action == 'add')
{
	$eventName = isset($_POST['eventName']) ? $_POST['eventName'] : '';
	$eventTime = isset($_POST['eventTime']) ? $_POST['eventTime'] : '';

	$eventTime = strtotime($eventTime);

	$response = addEvent($conn, $eventName, $eventTime);
}
else if($action == 'update')
{
	$eventId = isset($_POST['eventId']) ? $_POST['eventId'] : 0;
	$eventName = isset($_POST['eventName']) ? $_POST['eventName'] : '';
	$eventTime = isset($_POST['eventTime']) ? $_POST['eventTime'] : '';

	$eventTime = strtotime($eventTime);

	$response = updateEvent($conn, $eventId, $eventName, $eventTime);
}
else if($action == 'remove')
{
	$eventId = isset($_POST['eventId']) ? $_POST['eventId'] : '';
	
	$response = removeEvent($conn, $eventId);
}
else
{
	$response = ['error' => 'Invalid request format'];
}

echo json_encode( $response );

function getEvents(mysqli $conn)
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
			'timestamp' => $eventTime,
			'date' => formatTimeDisplay($eventTime)
		];
	}

	return $events;
}

function addEvent(mysqli $conn, $eventName, $eventTime)
{
	$stmt = $conn->prepare("INSERT INTO events (eventName, eventTime) VALUES(?, FROM_UNIXTIME(?))");
	$stmt->bind_param("si", $eventName, $eventTime);
	$stmt->execute();
	
	$event = [
		'id' => $stmt->insert_id,
		'name' => $eventName,
		'timestamp' => $eventTime,
		'date' => formatTimeDisplay($eventTime)
	];

	$stmt->close();
	
	return $event;
}

function updateEvent(mysqli $conn, $eventId, $eventName, $eventTime)
{
	$stmt = $conn->prepare("UPDATE events SET eventName = ?, eventTime = FROM_UNIXTIME(?) WHERE eventId = ?");
	$stmt->bind_param("sii", $eventName, $eventTime, $eventId);
	$stmt->execute();
	$stmt->close();

	$event = [
		'id' => $eventId,
		'name' =>  $eventName,
		'timestamp' => $eventTime,
		'date' => formatTimeDisplay($eventTime)
	];

	return $event;
}

function removeEvent(mysqli $conn, $eventId)
{
	$stmt = $conn->prepare("DELETE FROM events WHERE eventId = ?");
	$stmt->bind_param("i", $eventId);
	$stmt->execute();
	$stmt->close();

	return [
		'id' => $eventId
	];
}

function formatTimeDisplay($time)
{
	 return date('F jS, Y g:i A', $time);
}
