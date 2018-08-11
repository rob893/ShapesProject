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
			
			updateSelectOptions();
			
			room['h'] = $("#container").height();
			room['w'] = $("#container").width();
		} 
	);
	
	$(function() {
		$(".draggable").draggable({
			containment: "#container",
			scroll: false,
			start: function(event){
				$(this).appendTo("#container");
				$(this).offset({ top: 0, left: 0});
			},
			stop: function(event) {
				
				o = $(this).offset();
				p = $(this).position();
			
				for(var i = 1; i <= 6; i++){
					
					if(!($(this).is($("#" + i))) && collision($(this), $("#" + i))){
						alert("two shapes cannot occupy the same space!");
	
						if(dictionary.hasOwnProperty($(this).attr('id'))){
							console.log("removing from dictionary");
							delete dictionary[$(this).attr('id')];
						}
		
						resetToStartPosition($(this).attr("id"));
						return;
					}
				}
				
				if(!dictionary.hasOwnProperty($(this).attr('id'))){
					console.log("adding location for " + $(this).attr('id'));
					dictionary[$(this).attr('id')] = {};
					dictionary[$(this).attr('id')]['x'] = p.left;
					dictionary[$(this).attr('id')]['y'] = p.top;
					dictionary[$(this).attr('id')]['h'] = $(this).height();
					dictionary[$(this).attr('id')]['w'] = $(this).width();
					console.log(dictionary[$(this).attr('id')]);
				} else {
					dictionary[$(this).attr('id')]['x'] = p.left;
					dictionary[$(this).attr('id')]['y'] = p.top;
					dictionary[$(this).attr('id')]['h'] = $(this).height();
					dictionary[$(this).attr('id')]['w'] = $(this).width();
					console.log(dictionary[$(this).attr('id')]);
					console.log("updating location for " + $(this).attr('id'));
				}
			}
		});
		
		$(".square").resizable({
			stop: function(event){
				o = $(this).offset();
				p = $(this).position();
			
				for(var i = 1; i <= 6; i++){
					
					if(!($(this).is($("#" + i))) && collision($(this), $("#" + i))){
						alert("two shapes cannot occupy the same space!");
	
						if(dictionary.hasOwnProperty($(this).attr('id'))){
							console.log("removing from dictionary");
							delete dictionary[$(this).attr('id')];
						}
		
						resetToStartPosition($(this).attr("id"));
						return;
					}
				}
				
				if(!dictionary.hasOwnProperty($(this).attr('id'))){
					console.log("adding location for " + $(this).attr('id'));
					dictionary[$(this).attr('id')] = {};
					dictionary[$(this).attr('id')]['x'] = p.left;
					dictionary[$(this).attr('id')]['y'] = p.top;
					dictionary[$(this).attr('id')]['h'] = $(this).height();
					dictionary[$(this).attr('id')]['w'] = $(this).width();
					console.log(dictionary[$(this).attr('id')]);
				} else {
					dictionary[$(this).attr('id')]['x'] = p.left;
					dictionary[$(this).attr('id')]['y'] = p.top;
					dictionary[$(this).attr('id')]['h'] = $(this).height();
					dictionary[$(this).attr('id')]['w'] = $(this).width();
					console.log(dictionary[$(this).attr('id')]);
					console.log("updating location for " + $(this).attr('id'));
				}
			}
		});
		
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
	
	function setPosition(id, x, y, h, w){
		$("#" + id).appendTo("#container");
		$("#" + id).css({ top: y, left: x});
		$("#" + id).height(h);
		$("#" + id).width(w);
		
		for(var i = 1; i <= 6; i++){
			console.log(i);
			if(!($("#" + id).is($("#" + i))) && collision($("#" + id), $("#" + i))){
				alert("two shapes cannot occupy the same space!");

				if(dictionary.hasOwnProperty(id)){
					console.log("removing from dictionary");
					delete dictionary[id];
				}

				resetToStartPosition(id);
				return;
			}
		}
		
		if(!dictionary.hasOwnProperty(id)){
			console.log("adding location for " + id);
			dictionary[id] = {};
			dictionary[id]['x'] = x;
			dictionary[id]['y'] = y;
			dictionary[id]['h'] = h;
			dictionary[id]['w'] = w;
			console.log(dictionary[id]);
		} else {
			dictionary[id]['x'] = x;
			dictionary[id]['y'] = y;
			dictionary[id]['h'] = h;
			dictionary[id]['w'] = w;
			console.log(dictionary[id]);
			console.log("updating location for " + id);
		}
	}
	
	function setRoomSize(h, w){
		$("#container").height(h);
		$("#container").width(w);
	}
	
	function resetToStartPosition(id){
		$("#" + id).appendTo("body");
		$("#" + id).css({ top: 0, left: 0 });
		$("#" + id).height(75);
		$("#" + id).width(75);
		switch(id){
			case "1":
				$("#1").offset({ top: 0, left: 0});
				break;
			case "2":
				$("#2").offset({ top: 85, left: 0});
				break;
			case "3":
				$("#3").offset({ top: 170, left: 0});
				break;
			case "4":
				$("#4").offset({ top: 255, left: 0});
				break;
			case "5":
				$("#5").offset({ top: 340, left: 0});
				break;
			case "6":
				$("#6").offset({ top: 425, left: 0});
				break;
		}
	}
	
	function printDictionary(){
		document.getElementById("text1").innerHTML = "";
		document.getElementById("text1").innerHTML += "Room size: h: " + room['h'] + " w: " + room['w'] + "<br>";
		for(var key in dictionary){
			console.log(key + " " + dictionary[key]);
			document.getElementById("text1").innerHTML += "Element id: " + key + ". Location: x: " + dictionary[key]['x'] + ", y: " + dictionary[key]['y'] + 
				" Size: h: " + dictionary[key]['h'] + ", w: " + dictionary[key]['w'] + "<br>";
		}
	}
	
	function clearDictionary(){
		for(var item in dictionary){
			resetToStartPosition(item);
		}
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
					setPosition(item, roomToLoad['roomShapes'][item]['x'], roomToLoad['roomShapes'][item]['y'], roomToLoad['roomShapes'][item]['h'], roomToLoad['roomShapes'][item]['w']);
				}
			}
		});
	}

  </script>
</head>
<body>

<div id="container" style="width: 500px; height: 500px; border: 1px solid black; position: relative; left: 125px; right: 50px;" ></div>

<div id="1" class="square draggable" style="left: 0px; top: 0px;"></div>
	
<div id="2" class="square draggable" style="left: 0px; top: 85px;"></div>

<div id="3" class="square draggable" style="left: 0px; top: 170px;"></div>

<div id="4" class="square draggable" style="left: 0px; top: 255px;"></div>
	
<div id="5" class="square draggable" style="left: 0px; top: 340px;"></div>

<div id="6" class="square draggable" style="left: 0px; top: 425px;"></div>

<button type="button" id="printDictionary">Print Dictionary</button>
<br>
<br>
<button type="button" id="clearDictionary">Clear Room</button>
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