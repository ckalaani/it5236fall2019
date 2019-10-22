<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dbconnecterror = FALSE;
$dbh = NULL;
require_once 'credentials.php';
try{
	
	$conn_string = "mysql:host=".$dbserver.";dbname=".$db;
	
	$dbh= new PDO($conn_string, $dbusername, $dbpassword);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
}catch(Exception $e){
	http_response_code(504);
	echo "";
	exit();
}







if ($_SERVER['REQUEST_METHOD'] == "PUT") {
	if (array_key_exists('listID',$_GET)) {
		$listID = $_GET['listID'];
	}
	else {
		http_response_code(400);
		echo "";
		exit();
	}
	
	//Decoding the json body from the request
	$task = json_decode(file_get_contents('php://input'), true);

	if (array_key_exists('completed', $task)) {
		$complete = $task["completed"];
	} else {
		$complete = FALSE;
	}
	
	if (array_key_exists('taskName', $task)) {
		$taskName = $task["taskName"];
	} else {
		http_response_code(400);
		echo "";
		exit();
	}
	
	if (array_key_exists('taskDate', $task)) {
		$taskDate = $task["taskDate"];
	} else {
		http_response_code(400);
		echo "";
		exit();
	}
	
	if (!$dbconnecterror) {
		try {
			$sql = "UPDATE doList SET complete=:complete, listItem=:listItem, finishDate=:finishDate WHERE listID=:listID";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":complete", $complete);
			$stmt->bindParam(":listItem", $taskName);
			$stmt->bindParam(":finishDate", $taskDate);
			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();	
			// Return response code here
			http_response_code(200);
			
			exit();
			
			
		} catch (PDOException $e) {
			http_response_code(504);
			echo "database exception maybe fields";
			exit();
		}	
	} else {
		// return 500 message
		http_response_code(504);
		echo "database error";
		exit();
	}
}
	
else if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$task = json_decode(file_get_contents('php://input'), true);

	if (array_key_exists('completed', $task)) {
		$complete = $task["completed"];
	} else {
		$complete = FALSE;
	}
	
	if (array_key_exists('taskName', $task)) {
		$taskName = $task["taskName"];
	} else {
		http_response_code(400);
		echo "400 level error";
		exit();
	}
	
	if (array_key_exists('taskDate', $task)) {
		$taskDate = $task["taskDate"];
	} else {
		http_response_code(400);
		echo "400 level error";
		exit();
	}
	
	if (!$dbconnecterror) {
		try {
			$sql = "INSERT INTO doList (complete, listItem, finishDate) VALUES (:complete, :listItem, :finishDate)";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":complete", $complete);
			$stmt->bindParam(":listItem", $taskName);
			$stmt->bindParam(":finishDate", $taskDate);
			$response = $stmt->execute();	
			http_response_code(200);
			exit();
			
		} catch (PDOException $e) {
			http_response_code(504);
			echo "database exception maybe fields";
			exit();
		}	
	} else {
		http_response_code(504);
		echo "database error";
		exit();
	}
}

else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
	if (array_key_exists('listID',$_GET)) {
		$listID = $_GET['listID'];
	}
	else {
		http_response_code(400);
		echo "";
		exit();
	}
	
	
	if (!$dbconnecterror) {
		try {
			$sql = "DELETE FROM doList where listID = :listID";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":listID", $_GET['listID']);
		
			$response = $stmt->execute();	
			
			http_response_code(200);
			exit();
			
		} catch (PDOException $e) {
			http_response_code(504);
			echo "database exception maybe fields";
			exit();
		}	
	} else {
		http_response_code(400);
		echo "";
		exit();
	}
}
	
else {
    http_response_code(405);
    echo "expected PUT";
    exit();
}

?>