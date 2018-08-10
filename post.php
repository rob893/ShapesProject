<?php

if(isset($_POST['data'])){
	$data = json_decode($_POST['data'], true);
	//foreach($data as $id => $location){
	//	echo $id;
	//	foreach($location as $cord => $num){
	//		echo $cord." ".$num;
	//	}
	//}
	
	print_r($data);
	//echo "ok";
	//echo $_POST['data'];
}

?>