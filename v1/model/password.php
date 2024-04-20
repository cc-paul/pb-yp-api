<?php
	class PasswordException extends Exception {

	}

	class Password {
		private $_new_password;

		public function __construct($new_password) {
			$this->setNewPassword($new_password);
		}

		public function setNewPassword($new_password) {
			$this->_new_password = $new_password;
		}

		public function getNewPassword() {
			return $this->_new_password;
		}

		public function returnPasswordAsArray() {
			$password = array();
			$password['new_password'] = $this->getNewPassword();
			return $password;
		}
	}
?>