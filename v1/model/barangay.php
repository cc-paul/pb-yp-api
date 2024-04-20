<?php
	class Barangay {
		private $_id;
		private $_barangayName;
		private $_totalPopulation;
		private $_isActive;
		private $_lat;
		private $_lng;

		public function __construct($id,$barangayName,$totalPopulation,$isActive,$lat,$lng) {
			$this->setID($id);
			$this->setBarangayName($barangayName);
			$this->setTotalPopulation($totalPopulation);
			$this->setIsActive($isActive);
			$this->setLat($lat);
			$this->setLng($lng);
		}

		/* Getters */
		public function getID() {
			return $this->_id;
		}

		public function getBarangayName() {
			return $this->_barangayName;
		}

		public function getTotalPopulation() {
			return $this->_totalPopulation;
		}

		public function getIsActive() {
			return $this->_isActive;
		}

		public function getLat() {
			return $this->_lat;
		}

		public function getLng() {
			return $this->_lng;
		}


		/* Setters */
		public function setID($id) {
			$this->_id = $id;
		}

		public function setBarangayName($barangayName) {
			$this->_barangayName = $barangayName;
		}

		public function setTotalPopulation($totalPopulation) {
			$this->_totalPopulation = $totalPopulation;
		}

		public function setIsActive($isActive) {
			$this->_isActive = $isActive;
		}

		public function setLat($lat) {
			$this->_lat = $lat;
		}

		public function setLng($lng) {
			$this->_lng = $lng;
		}

		public function returnBarangayAsArray() {
			$barangay = array();
			$barangay["id"]              = $this->getID();
			$barangay["barangayName"]    = $this->getBarangayName();
			$barangay["totalPopulation"] = $this->getTotalPopulation();
			$barangay["isActive"]        = $this->getIsActive();
			$barangay["lat"]             = $this->getLat();
			$barangay["lng"]             = $this->getLng();
			return $barangay;
		}
	}
?>