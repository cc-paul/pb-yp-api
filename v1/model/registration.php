<?php

	class RegistrationException extends {

	}

	class Registration {
		private $_id;
		private $_firstName;
		private $_middleName;
		private $_lastName;
		private $_username;
	}

	public function __construct($id,$firstName,$lastName,$username,$email) {

	}

	public function setID($id) {
		if (($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)) {
			throw new TaskException("Registration ID Error");
		}

		$this->_id = $id;
	}

	public function setFirstName($firstName) {
		if (strlen($firstName) < || strlen($firstName) > 255) {
			throw new RegistrationException("First Name Error");
		}

		$this->_firstName = $firstName;
	}

	public function setMiddleName($middleName) {
		if (strlen($middleName) > 255) {
			throw new RegistrationException("Middle Name Error");
		}

		$this->_middleName = $middleName;
	}

	public function setLastName($lastName) {
		
	}
?>