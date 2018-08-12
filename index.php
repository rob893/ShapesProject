<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shapes Project</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link href="stylesheet.css" rel="stylesheet">
  
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="jquery.ui.touch-punch.min.js"></script>
  <script>
	
	var dictionary = {};
	var room = {};
	var shapeIdIndex = 1;
	
	$(document).ready(
		function() {
			document.getElementById("loadSavedRoomButton").addEventListener("click", function() {
				loadSavedRoom($("#loadRoomId").val());
			}, false);
			
			document.getElementById("saveDataAsJSON").addEventListener("click", function() {
				saveDataAsJSON();
			}, false);
			
			document.getElementById("printDictionary").addEventListener("click", function() {
				printDictionary();
			}, false);
			
			document.getElementById("clearDictionary").addEventListener("click", function() {
				clearDictionary();
			}, false);
			
			document.getElementById("makeShape").addEventListener("click", function() {
				createShape();
			}, false);
			
			updateSelectOptions();
			
			room['h'] = $("#container").height();
			room['w'] = $("#container").width();
		} 
	);
	
	$(function() {
		$("#container").resizable({
			stop: function(event){
				
				room['h'] = $(this).height();
				room['w'] = $(this).width();
			}
		});
	});
	
	function collision($div1, $div2) {
		var x1 = $div1.offset().left;
		var y1 = $div1.offset().top;
		var x2 = $div2.offset().left;
		var y2 = $div2.offset().top;
		if ((y1 + $div1.outerHeight(true)) < y2 ||
			y1 > (y2 + $div2.outerHeight(true)) ||
			(x1 + $div1.outerWidth(true)) < x2  ||
			x1 > (x2 + $div2.outerWidth(true)))
			return false;
		return true;
	}
	
	function checkForCollision(shape){
		for(var item in dictionary){
			if(!(shape.is($("#" + item))) && collision(shape, $("#" + item))){
				return $("#" + item);
			}
		}
		return null;
	}
	
	function handleCollision(shape){
		
		o = shape.offset();
		p = shape.position();
		var other = checkForCollision(shape);
		var infiLoopCatcher = 0;
		var prevMove = null;
		while(other != null && infiLoopCatcher < 500){
			
			var distanceRight = (other.position().left + other.outerWidth(true)) - p.left;
			var distanceDown = (other.position().top + other.outerHeight(true)) - p.top;
			var distanceLeft = (p.left + shape.outerWidth(true)) - other.position().left;
			var distanceUp = (p.top + shape.outerHeight(true)) - other.position().top;
			
			if((p.left - distanceLeft - 2) < 0 || prevMove == "right"){
				distanceLeft = Number.POSITIVE_INFINITY; 
			}
			if((p.top - distanceUp - 2) < 0 || prevMove == "down"){
				distanceUp = Number.POSITIVE_INFINITY; 
			}
			if((p.left + shape.outerWidth(true) + distanceRight + 2) > $("#container").width() || prevMove == "left"){
				distanceRight = Number.POSITIVE_INFINITY; 
			}
			if((p.top + shape.outerHeight(true) + distanceDown + 2) > $("#container").height() || prevMove == "up"){
				distanceDown = Number.POSITIVE_INFINITY; 
			}
			
			if(distanceRight < distanceDown && distanceRight < distanceLeft && distanceRight < distanceUp){
				var newLeft = p.left + distanceRight + 2;
				p.left = newLeft;
				shape.css({ left: newLeft});
				prevMove = "right";
			}
			else if(distanceDown < distanceRight && distanceDown < distanceLeft && distanceDown < distanceUp){
				var newTop = p.top + distanceDown + 2;
				p.top = newTop;
				shape.css({ top: newTop});
				prevMove = "down";
			}
			else if(distanceUp < distanceRight && distanceUp < distanceLeft && distanceUp < distanceDown){
				var newTop = p.top - distanceUp - 2;
				p.top = newTop;
				shape.css({ top: newTop});
				prevMove = "up";
			}
			else if(distanceLeft < distanceRight && distanceLeft < distanceUp && distanceLeft < distanceDown){
				var newLeft = p.left - distanceLeft - 2;
				p.left = newLeft;
				shape.css({ left: newLeft});
				prevMove = "left";
			}
			else { //todo, make the move based off of furthest distacne from edge of room.
				var move = Math.floor(Math.random() * 4) + 1;
				prevMove == null;
				switch(move){
					case 1:
						var newLeft = p.left + 2;
						p.left = newLeft;
						shape.css({ left: newLeft});
						break;
					case 2:
						var newTop = p.top + 2;
						p.top = newTop;
						shape.css({ top: newTop});
						break;
					case 3:
						var newTop = p.top - 2;
						p.top = newTop;
						shape.css({ top: newTop});
						break;
					case 4:
						var newLeft = p.left - 2;
						p.left = newLeft;
						shape.css({ left: newLeft});
						break;
				}
			}
			
			other = checkForCollision(shape);
			infiLoopCatcher++;
			if(infiLoopCatcher == 500){
				alert("Cannot find room. Deleting this shape.")
				if(dictionary.hasOwnProperty(shape.attr('id'))){
					delete dictionary[shape.attr('id')];
				}
				shape.remove();
				shapeIdIndex--;
				return false;
			}
		}
		return true;
	}
	
	function createShape(){
		var newShape = $('<div/>', {class: 'square', id: shapeIdIndex, style: 'position: absolute'});
		$(newShape).appendTo($("#container"));
		shapeIdIndex++;
		
		o = newShape.offset();
		p = newShape.position();
		
		if(!handleCollision(newShape)){
			return;
		}
		
		if(!dictionary.hasOwnProperty(newShape.attr('id'))){
			dictionary[newShape.attr('id')] = {};
			dictionary[newShape.attr('id')]['x'] = p.left;
			dictionary[newShape.attr('id')]['y'] = p.top;
			dictionary[newShape.attr('id')]['h'] = newShape.height();
			dictionary[newShape.attr('id')]['w'] = newShape.width();
		} else {
			dictionary[newShape.attr('id')]['x'] = p.left;
			dictionary[newShape.attr('id')]['y'] = p.top;
			dictionary[newShape.attr('id')]['h'] = newShape.height();
			dictionary[newShape.attr('id')]['w'] = newShape.width();
		}
		
		newShape.draggable({
			containment: "#container",
			scroll: false,
			
			stop: function(event) {
				
				o = $(this).offset();
				p = $(this).position();
				
				if(!handleCollision($(this))){
					return;
				}
				
				if(!dictionary.hasOwnProperty($(this).attr('id'))){
					dictionary[$(this).attr('id')] = {};
					dictionary[$(this).attr('id')]['x'] = p.left;
					dictionary[$(this).attr('id')]['y'] = p.top;
					dictionary[$(this).attr('id')]['h'] = $(this).height();
					dictionary[$(this).attr('id')]['w'] = $(this).width();
				} else {
					dictionary[$(this).attr('id')]['x'] = p.left;
					dictionary[$(this).attr('id')]['y'] = p.top;
					dictionary[$(this).attr('id')]['h'] = $(this).height();
					dictionary[$(this).attr('id')]['w'] = $(this).width();
				}
			}
		});
		
		newShape.resizable({
			stop: function(event){
				o = $(this).offset();
				p = $(this).position();
				
				if(!handleCollision($(this))){
					return;
				}
				
				if(!dictionary.hasOwnProperty($(this).attr('id'))){
					dictionary[$(this).attr('id')] = {};
					dictionary[$(this).attr('id')]['x'] = p.left;
					dictionary[$(this).attr('id')]['y'] = p.top;
					dictionary[$(this).attr('id')]['h'] = $(this).height();
					dictionary[$(this).attr('id')]['w'] = $(this).width();
				} else {
					dictionary[$(this).attr('id')]['x'] = p.left;
					dictionary[$(this).attr('id')]['y'] = p.top;
					dictionary[$(this).attr('id')]['h'] = $(this).height();
					dictionary[$(this).attr('id')]['w'] = $(this).width();
				}
			}
		});
		
		return newShape;
	}
	
	function setPosition(item, x, y, h, w){
		var id = $(item).attr('id');
		item.css({ top: y, left: x});
		item.height(h);
		item.width(w);
		
		if(!handleCollision(item)){
			return;
		}
		
		if(!dictionary.hasOwnProperty(id)){
			dictionary[id] = {};
			dictionary[id]['x'] = x;
			dictionary[id]['y'] = y;
			dictionary[id]['h'] = h;
			dictionary[id]['w'] = w;
		} else {
			dictionary[id]['x'] = x;
			dictionary[id]['y'] = y;
			dictionary[id]['h'] = h;
			dictionary[id]['w'] = w;
		}
	}
	
	function setRoomSize(h, w){
		$("#container").height(h);
		$("#container").width(w);
	}
	
	function printDictionary(){
		document.getElementById("text1").innerHTML = "";
		document.getElementById("text1").innerHTML += "Room size: h: " + room['h'] + " w: " + room['w'] + "<br>";
		for(var key in dictionary){
			document.getElementById("text1").innerHTML += "Element id: " + key + ". Location: x: " + dictionary[key]['x'] + ", y: " + dictionary[key]['y'] + 
				" Size: h: " + dictionary[key]['h'] + ", w: " + dictionary[key]['w'] + "<br>";
		}
	}
	
	function clearDictionary(){
		// for(var item in dictionary){
			// resetToStartPosition(item);
		// }
		$(".square").remove();
		shapeIdIndex = 1;
		dictionary = {};
		$("#text1").html("");
		$("#result").html("");
	}
	
	function saveDataAsJSON(){
		if(typeof dictionary == "undefined" || Object.keys(dictionary).length == 0){
			alert("nothing to save");
			return;
		}
		
		if($("#roomName").val() == ""){
			alert("Please enter a room name.");
			return;
		}
		
		var dictionaryAsJSON = JSON.stringify(dictionary);
		var roomSizeAsJSON = JSON.stringify(room);
		
		$.ajax({
			url: "post.php",
			type: "POST",
			data: {
				data: dictionaryAsJSON,
				roomName: $("#roomName").val(),
				roomSize: roomSizeAsJSON
			},
			success: function(jsonString){
				$("#result").html(jsonString);
				updateSelectOptions();
			}
		});
	}
	
	function updateSelectOptions(){
		$.ajax({
			url: "post.php",
			type: "POST",
			data: {
				updateSelectOptions: true
			},
			dataType: "JSON",
			success: function(rooms){
				var select = document.getElementById("loadRoomId");
				select.options.length = 0;
				for(var room in rooms){
					select.options.add(new Option(rooms[room], room));
				}
			}
		});
	}
	
	function loadSavedRoom(roomId){
		
		clearDictionary();
		
		$.ajax({
			url: "post.php",
			type: "POST",
			data: {roomId: roomId},
			dataType: "JSON",
			success: function(roomToLoad){
				setRoomSize(roomToLoad['roomSize']['h'], roomToLoad['roomSize']['w']);
				for(var item in roomToLoad['roomShapes']){
					var newShape = createShape();
					setPosition(newShape, roomToLoad['roomShapes'][item]['x'], roomToLoad['roomShapes'][item]['y'], roomToLoad['roomShapes'][item]['h'], roomToLoad['roomShapes'][item]['w']);
				}
			}
		});
	}

  </script>
</head>
<body>

	<div id="container" style="width: 500px; height: 500px; border: 1px solid black; position: relative;" ></div>
	<br>
	<button type="button" id="printDictionary">Print Dictionary</button>
	<br>
	<br>
	<button type="button" id="clearDictionary">Clear Room</button>
	<br>
	<br>
	<button type="button" id="makeShape">Make shape</button>
	<br>
	<br>
	<button type="button" id='loadSavedRoomButton'>Load Saved Room</button>
	<select class='form-control' id='loadRoomId' name='loadRoomId'></select>
	<br>
	<br>
	<form method="post">
		<button type="button" id="saveDataAsJSON">Save to Database</button>
		<input type="text" name="roomName" id="roomName" placeholder="room name"></input>
	</form>
	<div id='text1'></div>
	<div id='result'></div>
 
</body>
</html>