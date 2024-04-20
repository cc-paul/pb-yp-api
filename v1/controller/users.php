<?php
	require_once('../helper/conn.php');
	require_once('../model/response.php');
	require_once('../model/password.php');
	require_once('../model/profile.php');
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

	if ($method === 'POST') {
		if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
			sendResponse(400,false,"Content type header is not JSON");
		}

		$rawPOSTData = file_get_contents('php://input');

		if (!$jsonData = json_decode($rawPOSTData)) {
			sendResponse(400,false,"Request body is not JSON");
		}

		if (
			!isset($jsonData->firstname) ||
			!isset($jsonData->middlename) ||
			!isset($jsonData->lastname) ||
			!isset($jsonData->username) ||
			!isset($jsonData->password) ||
			!isset($jsonData->rpassword) ||
			!isset($jsonData->emailaddress) || 
			!isset($jsonData->fcm)
		) {
			sendResponse(400,false,"The JSON body you sent has incomplete parameters");
		}

		if (strlen($jsonData->firstname) < 1) {
			sendResponse(400,false,"First Name must not be empty");
		} else if (strlen($jsonData->firstname > 255)) {
			sendResponse(400,false,"First Name is too long");
		}

		if (strlen($jsonData->middlename) > 255) {
			sendResponse(400,false,"Middle Name is too long");
		}

		if (strlen($jsonData->lastname) < 1) {
			sendResponse(400,false,"Last Name must not be empty");
		} else if (strlen($jsonData->lastname) > 255) {
			sendResponse(400,false,"Last Name is too long");
		}

		if (strlen($jsonData->username) < 1) {
			sendResponse(400,false,"Username must not be empty");
		} else if (strlen($jsonData->username) < 8) {
			sendResponse(400,false,"Username must be at least 8 characters");
		} else if (strlen($jsonData->username) > 255) {
			sendResponse(400,false,"Username is too long");
		}

		$has_specialChars = preg_match('@[^\w]@', $jsonData->username);

		if ($has_specialChars) {
			sendResponse(400,false,"Username must not have special characters");
		}

		if (strlen($jsonData->emailaddress) < 1) {
			sendResponse(400,false,"Email Address must not be empty");
		} else {
			if (!validateEmail($jsonData->emailaddress)) {
				sendResponse(400,false,"Invalid Email Address");
			}
		}

		if (strlen($jsonData->password) < 1 || strlen($jsonData->rpassword) < 1) {
			sendResponse(400,false,"Password must not be empty");
		}

		if (strlen($jsonData->fcm) < 1) {
			sendResponse(400,false,"Please provide a FCM");
		}

		$has_eightchar = strlen($jsonData->password) >= 8 ? true : false;
		$has_uppercase = preg_match('@[A-Z]@', $jsonData->password);
		$has_number    = preg_match('@[0-9]@', $jsonData->password);
		$has_specialChars = preg_match('/[^a-zA-Z0-9]/', $jsonData->password);
		$arrErrors = array();

		if (!$has_eightchar) {
			array_push($arrErrors,"Password must be at least 8 characters");
		}

		if (!$has_uppercase) {
			array_push($arrErrors, "Password must have a capital letter");
		}

		if (!$has_number) {
			array_push($arrErrors,"Password must have a number");
		}

		if (!$has_specialChars) {
			array_push($arrErrors,"Password must have a special character");
		}

		if (count($arrErrors) >= 1) {
			sendResponse(400,false,"There are errors in your password",$arrErrors,false);
		}

		$username = trim($jsonData->username);
		$emailaddress = trim($jsonData->emailaddress);
		$password = trim($jsonData->password);

		try {
			
			$query = $writeDB->prepare("SELECT * FROM yp_user_registration WHERE username = :username");
			$query->bindParam(':username',$username,PDO::PARAM_STR);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount !== 0) {
				sendResponse(409,false,"Username already exist");
			}

			$query = $writeDB->prepare("SELECT * FROM yp_mobile_registration WHERE username = :username");
			$query->bindParam(':username',$username,PDO::PARAM_STR);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount !== 0) {
				sendResponse(409,false,"Username already exist");
			}


			$query = $writeDB->prepare("SELECT * FROM yp_user_registration WHERE email = :email");
			$query->bindParam(':email',$emailaddress,PDO::PARAM_STR);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount !== 0) {
				sendResponse(409,false,"Email address already exist");
			}

			$query = $writeDB->prepare("SELECT * FROM yp_mobile_registration WHERE emailAddress = :email");
			$query->bindParam(':email',$emailaddress,PDO::PARAM_STR);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount !== 0) {
				sendResponse(409,false,"Email address already exist");
			}


			$hashed_password = password_hash($password, PASSWORD_DEFAULT);

			$query = $writeDB->prepare("
				INSERT INTO yp_mobile_registration 
					(firstName,middleName,lastName,username,password,emailAddress,fcm) 
				VALUES 
					(:firstName,:middleName,:lastName,:username,:password,:emailAddress,:fcm)
			");
			$query->bindParam(':firstName',$jsonData->firstname,PDO::PARAM_STR);
			$query->bindParam(':middleName',$jsonData->middlename,PDO::PARAM_STR);
			$query->bindParam(':lastName',$jsonData->lastname,PDO::PARAM_STR);
			$query->bindParam(':username',$username,PDO::PARAM_STR);
			$query->bindParam(':password',$hashed_password,PDO::PARAM_STR);
			$query->bindParam(':emailAddress',$emailaddress,PDO::PARAM_STR);
			$query->bindParam(':fcm',$jsonData->fcm,PDO::PARAM_STR);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				sendResponse(500,false,"There was an issue creating your user account.Please try again");
			}

			sendResponse(201,true,"User account has been created");

		} catch (PDOException $ex) {
			error_log("Database query error: ".$ex,0);
			sendResponse(500,false,"There was an error creating account. Please try again");
		}
	} else if ($method == 'GET') {
		if (array_key_exists("email", $_GET)) {
			
			$email = $_GET["email"];

			if ($email === '') {
				sendResponse(400,false,"Email address cannot be blank");
			}

			if (strlen($email) < 1) {
				sendResponse(400,false,"Please provide an email address");
			}

			if (!validateEmail($email)) {
				sendResponse(400,false,"Invalid Email Address");
			}

			$query = $writeDB->prepare("SELECT * FROM yp_mobile_registration WHERE emailAddress = :email AND isActive = 1");
			$query->bindParam(':email',$email,PDO::PARAM_STR);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				sendResponse(409,false,"Email address does not exist or registered");
			}

			$newPassword = randomPassword();
			$hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

			$query = $writeDB->prepare("UPDATE yp_mobile_registration SET `password` = :password WHERE emailAddress = :email");
			$query->bindParam(':password',$hashed_password,PDO::PARAM_STR);
			$query->bindParam(':email',$email,PDO::PARAM_STR);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				sendResponse(500,false,"There was an issue resetting your Password.Please try again");
			}

			$arrMessages = array();
			array_push($arrMessages,"Password has been reset. Please check your email");
			array_push($arrMessages,"Your new password is $newPassword . You may now login your account with this new credential");

			sendResponse(201,true,"Password Success",$arrMessages,false);
		} else {
			sendResponse(400,false,"No email address provided");
		}
	} else if ($method == 'PATCH') {
		$returned_userid = checkAccess();

		if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
			sendResponse(400,false,"Content type header is not JSON");
		}

		$rawPOSTData = file_get_contents('php://input');

		if (!$jsonData = json_decode($rawPOSTData)) {
			sendResponse(400,false,"Request body is not JSON");
		}


		$mode = !isset($jsonData->mode) ? '' : $jsonData->mode;

		if ($mode === "change_password") {
			if (
				!isset($jsonData->new_password) ||
				!isset($jsonData->confirm_password)
			) {
				sendResponse(400,false,"The JSON body you sent has incomplete parameters");
			}

			$new_password     = $jsonData->new_password;
			$confirm_password = $jsonData->confirm_password;

			if (strlen($new_password) < 1) {
				sendResponse(401,false,"Please provide new password");
			} 

			if (strlen($confirm_password) < 1) {
				sendResponse(401,false,"Please provide confirm password");
			} 

			if ($new_password != $confirm_password) {
				sendResponse(401,false,"Password does not match");
			}

			$has_eightchar = strlen($new_password) >= 8 ? true : false;
			$has_uppercase = preg_match('@[A-Z]@', $new_password);
			$has_number    = preg_match('@[0-9]@', $new_password);
			$has_specialChars = preg_match('/[^a-zA-Z0-9]/', $new_password);
			$arrErrors = array();

			if (!$has_eightchar) {
				array_push($arrErrors,"Password must be at least 8 characters");
			}

			if (!$has_uppercase) {
				array_push($arrErrors, "Password must have a capital letter");
			}

			if (!$has_number) {
				array_push($arrErrors,"Password must have a number");
			}

			if (!$has_specialChars) {
				array_push($arrErrors,"Password must have a special character");
			}

			if (count($arrErrors) >= 1) {
				sendResponse(400,false,"There are errors in your password",$arrErrors,false);
			}

			$password_updated = false;
			$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
			$queryFields = "";

			if (isset($new_password)) {
				$password_updated = true;
				$queryFields .= "`password` = :password";
			}

			if ($password_updated === false) {
				sendResponse(400,false,"Unable to update password");
			}

			$query = $writeDB->prepare('SELECT `password` FROM yp_mobile_registration WHERE id = :userid');
			$query->bindParam(':userid',$returned_userid,PDO::PARAM_INT);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				sendResponse(404,false,"Account not found");	
			}

			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$password = new Password(
					$row["password"]
				);
			}

			$queryString = "UPDATE yp_mobile_registration SET " . $queryFields . " WHERE id = :userid";
			$query = $writeDB->prepare($queryString);


			if ($password_updated === true) {
				$password->setNewPassword($hashed_password);
				$up_password = $password->getNewPassword();
				$query->bindParam(':password',$up_password,PDO::PARAM_STR);
			}

			$query->bindParam(':userid',$returned_userid,PDO::PARAM_INT);
			$query->execute();


			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				sendResponse(400,false,"Password not updated");
			}

			sendResponse(201,true,"Password has been updated successfully");

		} else if ($mode === "change_profile") {

			if (
				!isset($jsonData->image_link)
			) {
				sendResponse(400,false,"No image provided");
			}

			$image_link = $jsonData->image_link;

			if (strlen($image_link) < 1) {
				sendResponse(400,false,"No image link");
			}

			$image_link_updated = false;
			$queryFields = "";

			if (isset($image_link)) {
				$image_link_updated = true;
				$queryFields = "imageLink = :imageLink";
			}

			if ($image_link_updated === false) {
				sendResponse(404,false,"Unable to update profile");	
			}


			$query = $writeDB->prepare('SELECT `imageLink` FROM yp_mobile_registration WHERE id = :userid');
			$query->bindParam(':userid',$returned_userid,PDO::PARAM_INT);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				sendResponse(404,false,"Account not found for changing profile");	
			}

			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$imagelink = new ProfileImage(
					$row["imageLink"]
				);
			}


			$queryString = "UPDATE yp_mobile_registration SET " . $queryFields . " WHERE id = :userid";
			$query = $writeDB->prepare($queryString);


			if ($image_link_updated === true) {
				$imagelink->setImageLink($image_link);
				$up_image_link = $imagelink->getImageLink();
				$query->bindParam(':imageLink',$up_image_link,PDO::PARAM_STR);
			}

			$query->bindParam(':userid',$returned_userid,PDO::PARAM_INT);
			$query->execute();

			$rowCount = $query->rowCount();

			if ($rowCount === 0) {
				sendResponse(400,false,"Profile not updated");
			}

			
			$query = $readDB->prepare('SELECT imageLink FROM yp_mobile_registration WHERE id = :userid');
			$query->bindParam(':userid',$returned_userid,PDO::PARAM_INT);
			$query->execute();
			$rowCount = $query->rowCount();

			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$profile = new ProfileImage(
					$row['imageLink']
				);

				$profileArray[] = $profile->returnProfileImageAsArray();
			}

			sendResponse(201,true,"Profile has been updated",$profileArray,false);

		} else {
			sendResponse(404,false,"Endpoint not found");
		}
		
	} else {
		sendResponse(404,false,"Endpoint not found");
	}
?>