<?php
	class Hotline {
		private $_id;
		private $_hotline;
		private $_mobileNumber;
		private $_telephoneNumber;
		private $_emailAddress;
		private $_isActive;
		private $_dateCreated;
		private $_isRemoved;

		public function __construct($id,$hotline,$mobileNumber,$telephoneNumber,$emailAddress,$isActive,$dateCreated,$isRemoved) {
			$this->setID($id);
			$this->setHotline($hotline);
			$this->setMobileNumber($mobileNumber);
			$this->setTelephoneNumber($telephoneNumber);
			$this->setEmailAddress($emailAddress);
			$this->setIsActive($isActive);
			$this->setDateCreated($dateCreated);
			$this->setIsRemoved($isRemoved);
		}

		/* Getters */
		public function getID() {
			return $this->_id;
		}

		public function getHotline() {
			return $this->_hotline;
		}

		public function getMobileNumber() {
			return $this->_mobileNumber;
		}

		public function getTelephoneNumber() {
			return $this->_telephoneNumber;
		}

		public function getEmailAddress() {
			return $this->_emailAddress;
		}

		public function getIsActive() {
			return $this->_isActive;
		}

		public function getDateCreated() {
			return $this->_dateCreated;
		}

		public function getIsRemoved() {
			return $this->_isRemoved;
		}

		
		/* Setters */
		public function setID($id) {
			$this->_id = $id;
		}

		public function setHotline($hotline) {
			$this->_hotline = $hotline;
		}

		public function setMobileNumber($mobileNumber) {
			$this->_mobileNumber = $mobileNumber;
		}

		public function setTelephoneNumber($telephoneNumber) {
			$this->_telephoneNumber = $telephoneNumber;
		}

		public function setEmailAddress($emailAddress) {
			$this->_emailAddress = $emailAddress;
		}

		public function setIsActive($isActive) {
			$this->_isActive = $isActive;
		}

		public function setDateCreated($dateCread) {
			$this->_dateCreated = $dateCread;
		}

		public function setIsRemoved($isRemoved) {
			$this->_isRemoved = $isRemoved;
		}

		public function returnHotlineAsArray() {
			$hotline = array();
			$hotline["id"]              = $this->getID();
			$hotline["hotline"]         = $this->getHotline();
			$hotline["mobileNumber"]    = $this->getMobileNumber();
			$hotline["telephoneNumber"] = $this->getTelephoneNumber();
			$hotline["emailAddress"]    = $this->getEmailAddress();
			$hotline["isActive"]        = $this->getIsActive();
			$hotline["dateCreated"]     = $this->getDateCreated();
			$hotline["isRemoved"]       = $this->getIsRemoved();
			return $hotline;
		}
	}
?>