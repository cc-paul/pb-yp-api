<?php
	class Files {
		private $_id;
		private $_filename;
		private $_type;
		private $_link;

		public function __construct($id,$filename,$type,$link) {
			$this->setID($id);
			$this->setFileName($filename);
			$this->setType($type);
			$this->setLink($link);
		}

		/* Getters */
		public function getID() {
			return $this->_id;
		}

		public function getFilename() {
			return $this->_filename;
		}

		public function getType() {
			return $this->_type;
		}

		public function getLink() {
			return $this->_link;
		}


		/* Setters */
		public function setID($id) {
			$this->_id = $id;
		}

		public function setFileName($filename) {
			$this->_filename = $filename;
		}

		public function setType($type) {
			$this->_type = $type;
		}

		public function setLink($link) {
			$this->_link = $link;
		}

		public function returnFilesAsArray() {
			$files = array();
			$files["id"]       = $this->getID();
			$files["filename"] = $this->getFilename();
			$files["type"]     = $this->getType();
			$files["link"]     = $this->getLink();
			return $files;
		}
	}
?>