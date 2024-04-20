<?php
	require_once('../helper/conn.php');
	require_once('../model/response.php');
	require_once('../model/event.php');
	require_once('../model/barangay.php');
	require_once('../model/hotline.php');
	require_once('../model/faq.php');
	require_once('../model/notification.php');
	require_once('../model/do.php');
	require_once('../model/video_details.php');
	require_once('../model/videos.php');
	require_once('../model/files.php');
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

	if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1) {
		sendResponse(401,false,"Access token is missing from the header");
	}

	$returned_userid = checkAccess();
	$method          = $_SERVER['REQUEST_METHOD'];
	$domain          = getDomainURL();


	if ($method === 'GET') { 
		if (array_key_exists("mode", $_GET)) {
		
			$mode = $_GET['mode'];

			if ($mode === 'all-markers') {
				$query = $readDB->prepare("
					SELECT 
						a.id AS markerID,
						b.id AS eventID,
						a.`event`,
						a.isActive,
						a.dateCreated,
						a.description,
						a.hasImage,
						a.origin,
						a.needRadius,
						b.lat,
						b.lng,
						b.radius,
						IF(TIMESTAMPDIFF(SECOND,'".$global_date."',b.dateDuration) < 0,0,TIMESTAMPDIFF(SECOND,'".$global_date."',b.dateDuration)) AS dateSeconds,
						b.remarks,
						b.alertLevel,
						b.passableVehicle,
						DATE_FORMAT(a.dateCreated,'%M %d %Y %r') AS dateReported
					FROM
						yp_event a 
					INNER JOIN
						yp_disaster_mapping b 
					ON 
						a.id = b.eventID 
					WHERE
						b.isActive = 1
				");
				$query->execute();

				$rowCount = 0;
				$markerCount = 0;
				$eventArray = array();

				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				    if ($row["dateSeconds"] > 0) {
				        $event = new Event(
				            $row['markerID'],
				            $row['eventID'],
				            $row['event'],
				            $row['isActive'],
				            $row['dateCreated'],
				            $row['description'],
				            $row['hasImage'],
				            $row['origin'],
				            $row['needRadius'],
				            $row['hasImage'] == 1 ? "{$domain}/yopreparado/dist/img/{$row["markerID"]}.png" : "",
				            $row['lat'],
				            $row['lng'],
				            $row['radius'],
				            $row['remarks'],
				            $row['alertLevel'],
				            $row['passableVehicle'],
				            $row['dateReported'],
				            $row['dateSeconds']
				        );

				        $eventArray[] = $event->returnEventAsArray();
				    }
				}

				// Check if there are any markers with dateSeconds > 0
				if (!empty($eventArray)) {
				    $event = new Event(
				        0,
				        0,
				        'Select all Event',
				        1,
				        '0000-00-00 00:00:00',
				        '',
				        1,
				        '',
				        0,
				        "{$domain}/yopreparado/dist/img/select-all.png",
				        '0.0',
				        '0.0',
				        0,
				        '',
				        '',
				        '',
				       	'',
				       	0
				    );

				    $eventArray = array_merge([$event->returnEventAsArray()], $eventArray);
				}

				$rowCount = count($eventArray);
				$addOne = $rowCount != 0 ? 1 : 0;

				$returnData = array();
				$returnData["rows_returned"] = $rowCount + $addOne;
				$returnData["event"] = $eventArray;

				sendResponse(201,true,"Events and Disasters has been retreived",$returnData,false);
			} else if ($mode == "all-brgy") {
				$query = $readDB->prepare('
					SELECT 
						a.id,
						a.barangayName,
						FORMAT(a.totalPopulation,0) AS totalPopulation,
						a.isActive,
						a.lat,
						a.lng
					FROM
						yp_barangay a 
					WHERE
						a.isActive = 1
					ORDER BY
						a.barangayName ASC
				');
				$query->execute();
				$rowCount = $query->rowCount();
				$message = $rowCount == 0 ? "No municipality retreived" : "List of municipality has been retreived";

				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					$barangay = new Barangay(
						$row['id'],
						$row['barangayName'],
						$row['totalPopulation'],
						$row['isActive'],
						$row['lat'],
						$row['lng']
					);

					$barangayArray[] = $barangay->returnBarangayAsArray();
				}

				$returnData = array();
				$returnData["rows_returned"] = $rowCount;
				$returnData["municipality"] = $barangayArray;

				sendResponse(201,true,$message,$returnData,false);
			} else if ($mode == "all-contact") {
				$query = $readDB->prepare('
					SELECT 
						a.id,
						a.hotline,
						a.mobileNumber,
						a.telephoneNumber,
						a.emailAddress,
						a.isActive,
						a.dateCreated,
						a.isRemoved
					FROM
						yp_hotline a 
					WHERE
						a.isActive = 1 
					AND
						a.isRemoved = 0
					ORDER BY
						a.hotline ASC;
				');
				$query->execute();
				$rowCount = $query->rowCount();
				$message = $rowCount == 0 ? "No hotline retreived" : "List of hotline has been retreived";

				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					$hotline = new Hotline(
						$row['id'],
						$row['hotline'],
						$row['mobileNumber'],
						$row['telephoneNumber'],
						$row['emailAddress'],
						$row['isActive'],
						$row['dateCreated'],
						$row['isRemoved']
					);

					$hotlineArray[] = $hotline->returnHotlineAsArray();
				}

				$returnData = array();
				$returnData["rows_returned"] = $rowCount;
				$returnData["hotline"] = $hotlineArray;

				sendResponse(201,true,$message,$returnData,false);
			} else if ($mode == "all-notif") {
				$query = $readDB->prepare('
					SELECT 
						a.eventID,
						a.title,
						a.body,
						a.dateCreated,
						b.`event`,
						b.hasImage
					FROM
						yp_notification a 
					INNER JOIN
						yp_event b 
					ON 
						a.eventID = b.id
					ORDER BY
						a.dateCreated DESC
				');
				$query->execute();
				$rowCount = $query->rowCount();
				$message  = $rowCount == 0 ? "No notification retreived" : "List of notification has been retreived";

				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					$eventID  = $row['hasImage'] == 1 ? $row['eventID'] : 'no-picture-taking';

					$notification = new Notification(
						$row['eventID'],
						$row['title'],
						$row['body'],
						formatTimeAgo($row['dateCreated']),
						$row['event'],
						"{$domain}/yopreparado/dist/img/{$eventID}.png"
					);

					$notificationArray[] = $notification->returnNotificationAsArray();
				}

				$returnData = array();
				$returnData["rows_returned"] = $rowCount;
				$returnData["notification"] = $notificationArray;

				sendResponse(201,true,$message,$returnData,false);
			} else if ($mode == "all-menu") {
				$query = $readDB->prepare('
					SELECT 
						a.id,
						a.event,
						a.isActive,
						a.dateCreated,
						a.description,
						a.hasImage,
						a.origin
					FROM
						yp_event a 
					WHERE
						a.isActive = 1
					ORDER BY 
						a.`event`
				');

				$query->execute();
				$rowCount = $query->rowCount();
				$message  = $rowCount == 0 ? "No event menu retreived" : "List of menu has been retreived";

				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					$eventID  = $row['hasImage'] == 1 ? $row['id'] : 'no-picture-taking';

					$menu = new Event(
						0,
						$row['id'],
						$row['event'],
						$row['isActive'],
						$row['dateCreated'],
						$row['description'],
						$row['hasImage'],
						$row['origin'],
						0,
						"{$domain}/yopreparado/dist/img/{$eventID}.png",
						0.0,
						0.0,
						0,
						'',
						'',
						'',
						'',
						0

					);

					$menuArray[] = $menu->returnEventAsArray();
				}

				$returnData = array();
				$returnData["rows_returned"] = $rowCount;
				$returnData["event"] = $menuArray;

				sendResponse(201,true,$message,$returnData,false);
			} else if ($mode == "all-faq") {
				$query = $readDB->prepare('
					SELECT 
						a.id,
						a.question,
						a.answer
					FROM
						yp_faq a 
					INNER JOIN
						yp_user_registration b 
					ON 
						a.createdBy = b.id 
					WHERE
						a.isActive = 1
					ORDER BY
						a.dateCreated DESC
				');

				$query->execute();
				$rowCount = $query->rowCount();
				$message  = $rowCount == 0 ? "No FAQ retreived" : "List of FAQ has been retreived";
				$faqArray = array();

				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					$faq = new FAQ(
						$row['id'],
						$row['question'],
						$row['answer'],
					);

					$faqArray[] = $faq->returnFaqAsArray();
				}

				$returnData = array();
				$returnData["rows_returned"] = $rowCount;
				$returnData["faq"] = $faqArray;

				sendResponse(201,true,$message,$returnData,false);
			} else if ($mode == "all-files") {
				$query = $readDB->prepare('
					SELECT 
						a.id,
						a.filename,
						a.type
					FROM
						yp_files a 
					WHERE 
						a.isActive = 1 
					ORDER BY 
						a.dateCreated DESC;
				');

				$query->execute();
				$rowCount = $query->rowCount();
				$message  = $rowCount == 0 ? "No files retreived" : "List of files has been retreived";
				$filesArray = array();

				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					$fileName = $row['filename'];

					$files = new Files(
						$row['id'],
						$fileName,
						$row['type'],
						"{$domain}/yopreparado/files/{$fileName}"
					);

					$filesArray[] = $files->returnFilesAsArray();
				}

				$returnData = array();
				$returnData["rows_returned"] = $rowCount;
				$returnData["files"] = $filesArray;

				sendResponse(201,true,$message,$returnData,false);
			}
		} else if (array_key_exists("id", $_GET)) {
			$id = $_GET['id'];

			$query = $writeDB->prepare('
				SELECT 
					a.id,
					a.`event`,
					a.description,
					a.origin,
					IFNULL(b.fileName,"no-video") AS filename,
					a.hasImage
				FROM
					yp_event a 
				LEFT JOIN 
					yp_event_videos b 
				ON 
					a.id = b.eventID 
				AND 
					b.isPrimary = 1 
				WHERE
					a.id = :eventid
				GROUP BY
					a.id
			');
			$query->bindParam(':eventid',$id,PDO::PARAM_INT);
			$query->execute();

			$rowCount = $query->rowCount();
			$message  = $rowCount == 0 ? "No event details retreived" : "Details retreived";
			$videoArray = array();
			

			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$eventID  = $row['hasImage'] == 1 ? $row['id'] : 'no-picture-taking';
				$doArray  = getDo($writeDB,$row["id"]);

				$video = new Videos(
					$row["id"],
					$row["event"],
					$row["description"],
					$row["origin"],
					"{$domain}/yopreparado/videos/{$row["filename"]}.mp4",
					"{$domain}/yopreparado/dist/img/{$eventID}.png",
					$doArray
				);

				$videoArray[] = $video->returnVideoAsArray();
			}

			$returnData = array();
			$returnData["rows_returned"] = $rowCount;
			$returnData["video"] = $videoArray;

			sendResponse(201,true,$message,$returnData,false);
		} else if (array_key_exists("event_id", $_GET)) {
			$event_id    = $_GET['event_id'];
			$queryFields  = "";
			$has_id      = false;

			if ($event_id != 0) {
				$has_id = true;
				$queryFields .= " AND a.`eventID` = :eventid";
			}


			$query = $writeDB->prepare('
				SELECT 
					a.id,
					b.`event`,
					a.fileName,
					a.title,
					a.hasThumbnail,
					COUNT(c.videoID) AS views
				FROM
					yp_event_videos a 
				INNER JOIN 
					yp_event b 
				ON 
					a.eventID = b.id 
				LEFT JOIN 
					yp_views c 
				ON 
					a.id = c.videoID
				WHERE
					a.isActive = 1  
					'.$queryFields.'
				GROUP BY
					a.id
				ORDER BY
					a.title ASC
			');
			if ($has_id) {
				$query->bindParam(':eventid',$event_id,PDO::PARAM_INT);
			}
			$query->execute();

			$rowCount = $query->rowCount();
			$message  = $rowCount == 0 ? "No videos retreived" :  "Videos retreived";
			$videoArray = array();

			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$thumbnail  = $row['hasThumbnail'] == 1 ? $row['fileName'] : 'empty';

				$video = new VideoDetails(
					$row["id"],
					$row["event"],
					"{$domain}/yopreparado/videos/{$row["fileName"]}.mp4",
					"{$domain}/yopreparado/thumbnails/{$thumbnail}.png",
					$row["title"],
					$row["views"]
				);

				$videoArray[] = $video->returnVideoAsArray();
			}

			$returnData = array();
			$returnData["rows_returned"] = $rowCount;
			$returnData["videos"] = $videoArray;

			sendResponse(201,true,$message,$returnData,false);
		} else if (array_key_exists("video_id", $_GET)) {
			$video_id = $_GET['video_id'];

			$query = $writeDB->prepare("INSERT INTO yp_views (videoID) VALUES (:videoID)");
			$query->bindParam(':videoID',$video_id,PDO::PARAM_INT);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				sendResponse(500,false,"Error adding views in the video");
			}

			sendResponse(201,true,"Video added views");
		}
	} else {
		sendResponse(405,false,"Request method not allowed");
	}

	function getDo($readDB,$eventID) {
		$query = $readDB->prepare('
			SELECT 
				a.id,
				a.isDo,
				a.details,
				a.category
			FROM
				yp_dosdonts a 
			WHERE
				a.isDeleted = 0 
			AND 
				a.eventID = :eventID 
			ORDER BY
				a.eventID ASC,
				a.isDo DESC;
		');
		$query->bindParam(':eventID',$eventID,PDO::PARAM_INT);
		$query->execute();

		$doArray = array();

		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$do = new Dos(
				$row["id"],
				$row["isDo"],
				$row["details"],
				$row["category"]
			);

			$doArray[] = $do->returnDoAsArray();
		}

		return $doArray;
	}
?>