<?php 
	class Videos {
		private $_id;
		private $_event;
		private $_description;
		private $_origin;
		private $_videolink;
		private $_imagelink;
		private $_do;

		public function __construct($id,$event,$description,$origin,$videolink,$imagelink,$do) {
			$this->setID($id);
			$this->setEvent($event);
			$this->setDescription($description);
			$this->setOrigin($origin);
			$this->setVideoLink($videolink);
			$this->setImageLink($imagelink);
			$this->setDo($do);
		}

		/* Getters */
		public function getID() {
			return $this->_id;
		}

		public function getEvent() {
			return $this->_event;
		}

		public function getDescription() {
			return $this->_description;
		}

		public function getOrigin() {
			return $this->_origin;
		}

		public function getVideoLink() {
			return $this->_videolink;
		}

		public function getImageLink() {
			return $this->_imagelink;
		}

		public function getDo() {
			return $this->_do;
		}

		/* Setters */
		public function setID($id) {
			$this->_id = $id;
		}

		public function setEvent($event) {
			$this->_event = $event;
		}

		public function setDescription($description) {
			$this->_description = $description;
		}

		public function setOrigin($origin) {
			$this->_origin = $origin;
		}

		public function setVideoLink($videolink) {
			$this->_videolink = $videolink;
		}

		public function setImageLink($imagelink) {
			$this->_imagelink = $imagelink;
		}

		public function setDo($do) {
			$this->_do = $do;
		}

		public function returnVideoAsArray() {
			$video = array();
			$video["id"]          = $this->getID();
			$video["event"]       = $this->getEvent();
			$video["description"] = $this->getDescription();
			$video["origin"]      = $this->getOrigin();
			$video["videolink"]   = $this->getVideoLink();
			$video["imagelink"]   = $this->getImageLink();
			$video["do"]          = $this->getDo();
			return $video;
		}
	}
?>