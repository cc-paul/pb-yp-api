<?php
	require_once('../helper/conn.php');
	require_once('../model/response.php');
	require_once('../model/event.php');
	require_once('../model/barangay.php');
	require_once('../model/hotline.php');
	require_once('../helper/date.php');
	require_once('../helper/message.php');
	require_once('../helper/utils.php');

	try {
		$writeDB = DB::connectionWriteDB();
		$readDB = DB::connectionReadDB();
	} catch (PDOException $ex) {
		error_log("Connection Error - ".$ex, 0);
		sendResponse(500,false,"Database Connection Error");
	}

	$method = $_SERVER['REQUEST_METHOD'];

	if ($method == 'POST') {

		if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
			sendResponse(400,false,"Content type header is not JSON");
		}

		$rawPOSTData = file_get_contents('php://input');

		if (!$jsonData = json_decode($rawPOSTData)) {
			sendResponse(400,false,"Request body is not JSON");
		}

		if (
			!isset($jsonData->event_id) ||
			!isset($jsonData->title) ||
			!isset($jsonData->body) 
		) {
			sendResponse(400,false,"The JSON body you sent has incomplete parameters");
		}


		$url     = "https://fcm.googleapis.com/fcm/send";
		$apiKey  = "";
		$domain  = getDomainURL();
		$keys    = array();

		$query = $readDB->prepare('SELECT fcm FROM yp_mobile_registration WHERE IFNULL(fcm,"") != "" AND isActive = 1');
		$query->execute();
		$rowCount = $query->rowCount();

		if ($rowCount !== 0) {
			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				array_push($keys,$row["fcm"]);
			}


			$headers = array(
				'Authorization:key='.$apiKey,
				'Content-type:application/json'
			); 

			$notifData = [
				'title' => $jsonData->title,
				'body' => $jsonData->body,
				'image' => "{$domain}/yopreparado/dist/img/{$jsonData->event_id}.png?random=" . uniqid()
			];

			$notifBody = [
				'notification' => $notifData,
				'registration_ids' => $keys
			];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notifBody));

			$result = curl_exec($ch);
			curl_close($ch);
		} else {
			sendResponse(401,false,"Unable to send notification. There are no users registered");
		}

	} else {
		sendResponse(405,false,"Request method not allowed");
	}
?>  
