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

$events = getEvents($conn);

echo json_encode( $events[0] );

function getEvents($conn)
{
	$events = [];

	$result = $conn->query('SELECT eventId, eventName, UNIX_TIMESTAMP(eventTime) AS eventTime FROM events');

	while($row = $result->fetch_assoc())
	{
		$events[] = [
			'id' => $row['eventId'],
			'name' => $row['eventName'],
			'timestamp' => $row['eventTime']
		];
	}

	return $events;
}