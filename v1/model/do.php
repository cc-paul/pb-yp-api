<?php
	class Dos {
		private $_id;
		private $_isDo;
		private $_details;
		private $_category;

		public function __construct($id,$isDo,$details,$category) {
			$this->setID($id);
			$this->setIsDo($isDo);
			$this->setDetails($details);
			$this->setCategory($category);
		}

		/* Getters */
		public function getID() {
			return $this->_id;
		}

		public function getIsDo() {
			return $this->_isDo;
		}

		public function getDetails() {
			return $this->_details;
		}

		public function getCategory() {
			return $this->_category;
		}

		/* Setters */
		public function setID($id) {
			$this->_id = $id;
		}

		public function setIsDo($isDo) {
			$this->_isDo = $isDo;
		}

		public function setDetails($details) {
			$this->_details = $details;
		}

		public function setCategory($category) {
			$this->_category = $category;
		}

		public function returnDoAsArray() {
			$do = array();
			$do["id"]      = $this->getID();
			$do["isDo"]    = $this->getIsDo();
			$do["details"] = $this->getDetails();
			$do["category"] = $this->getCategory();
			return $do;
		}
	}
?>