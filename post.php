<?php
require_once('dbconnection.php');

if(isset($_POST['data']) && isset($_POST['roomName'])){
	$data = json_decode($_POST['data'], true);
	$roomName = $_POST['roomName'];
	
	$sqlInsertRoom = $conn->prepare("INSERT INTO rooms(roomName) VALUES(?)");
	$sqlInsertRoom->bind_param('s', $roomName);
	
	if($sqlInsertRoom->execute() === true){
		$sqlInsertRoom->close();
	}
	else {
		echo "Error: ".$sqlInsertRoom->error;
		$sqlInsertRoom->close();
		exit;
	}
	
	foreach($data as $id => $location){

		$sqlInsert = $conn->prepare("INSERT INTO shapes(roomName, x, y, shapeId) VALUES(?, ?, ?, ?)");
		$sqlInsert->bind_param('siii', $roomName, $location['x'], $location['y'], $id);
		
		if($sqlInsert->execute() === true){
			$sqlInsert->close();
		}
		else {
			echo "Error: ".$sqlInsert->error;
			$sqlInsertRoom->close();
			exit;
		}
	}
	
	echo "Room name: ".$_POST['roomName']."<br>";
	print_r($data);
}

else if(isset($_POST['roomId'])){
	$roomId = $_POST['roomId'];
	$sqlRoom = "SELECT * FROM shapes INNER JOIN rooms ON rooms.roomName = shapes.roomName WHERE rooms.id = '$roomId'";
	$results = $conn->query($sqlRoom);

	$testRoom = [];
	while($row = $results->fetch_assoc()){
		$testRoom[$row['shapeId']]['x'] = (float)$row['x'];
		$testRoom[$row['shapeId']]['y'] = (float)$row['y'];
	}
	
	$testRoomJSON = json_encode($testRoom);
	echo $testRoomJSON;
}

else if(isset($_POST['updateSelectOptions'])){
	$sqlRoom = "SELECT * FROM rooms";
	$results = $conn->query($sqlRoom);

	$rooms = [];
	while($row = $results->fetch_assoc()){
		$rooms[$row['id']] = $row['roomName'];
	}
	
	$roomsJSON = json_encode($rooms);
	echo $roomsJSON;
}

else {
	echo "nothing";
}

?>