<?php
require_once('dbconnection.php');

if(isset($_POST['data']) && isset($_POST['roomName'])){
	$data = json_decode($_POST['data'], true);
	$roomSize = json_decode($_POST['roomSize'], true);
	$roomName = $_POST['roomName'];
	
	$sqlInsertRoom = $conn->prepare("INSERT INTO rooms(roomName, height, width) VALUES(?, ?, ?)");
	$sqlInsertRoom->bind_param('sii', $roomName, $roomSize['h'], $roomSize['w']);
	
	if($sqlInsertRoom->execute() === true){
		$sqlInsertRoom->close();
	}
	else {
		echo "Error: ".$sqlInsertRoom->error;
		$sqlInsertRoom->close();
		exit;
	}
	
	foreach($data as $id => $location){

		$sqlInsert = $conn->prepare("INSERT INTO shapes(roomName, x, y, h, w, shapeId) VALUES(?, ?, ?, ?, ?, ?)");
		$sqlInsert->bind_param('siiiii', $roomName, $location['x'], $location['y'], $location['h'], $location['w'], $id);
		
		if($sqlInsert->execute() === true){
			$sqlInsert->close();
		}
		else {
			echo "Error: ".$sqlInsert->error;
			$sqlInsertRoom->close();
			exit;
		}
	}
	
	echo "Room ".$_POST['roomName']." saved!";
	//print_r($data);
}

else if(isset($_POST['roomId'])){
	$roomId = $_POST['roomId'];
	$sqlRoom = "SELECT * FROM shapes INNER JOIN rooms ON rooms.roomName = shapes.roomName WHERE rooms.id = '$roomId'";
	$results = $conn->query($sqlRoom);

	$roomToLoad = [];
	while($row = $results->fetch_assoc()){
		$roomToLoad['roomSize']['h'] = (float)$row['height'];
		$roomToLoad['roomSize']['w'] = (float)$row['width'];
		$roomToLoad['roomShapes'][$row['shapeId']]['x'] = (float)$row['x'];
		$roomToLoad['roomShapes'][$row['shapeId']]['y'] = (float)$row['y'];
		$roomToLoad['roomShapes'][$row['shapeId']]['h'] = (float)$row['h'];
		$roomToLoad['roomShapes'][$row['shapeId']]['w'] = (float)$row['w'];
	}
	
	$roomToLoadJSON = json_encode($roomToLoad);
	echo $roomToLoadJSON;
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