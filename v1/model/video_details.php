<?php
	class VideoDetails {
		private $_id;
		private $_event;
		private $_videolink;
		private $_imagelink;
		private $_title;
		private $_views;

		public function __construct($id,$event,$videolink,$imagelink,$title,$views) {
			$this->setID($id);
			$this->setEvent($event);
			$this->setVideoLink($videolink);
			$this->setImageLink($imagelink);
			$this->setTitle($title);
			$this->setViews($views);
		}

		/* Getters */
		public function getID() {
			return $this->_id;
		}

		public function getEvent() {
			return $this->_event;
		}

		public function getVideoLink() {
			return $this->_videolink;
		}

		public function getImageLink() {
			return $this->_imagelink;
		}

		public function getTitle() {
			return $this->_title;
		}

		public function getViews() {
			return $this->_views;
		}


		/* Setters */
		public function setID($id) {
			$this->_id = $id;
		}

		public function setEvent($event) {
			$this->_event = $event;
		}

		public function setVideoLink($videolink) {
			$this->_videolink = $videolink;
		}

		public function setImageLink($imagelink) {
			$this->_imagelink = $imagelink;
		}

		public function setTitle($title) {
			$this->_title = $title;
		}

		public function setViews($views) {
			$this->_views = $views;
		}

		public function returnVideoAsArray() {
			$video = array();
			$video["id"]        = $this->getID();
			$video["event"]     = $this->getEvent();
			$video["videolink"] = $this->getVideoLink();
			$video["imagelink"] = $this->getImageLink();
			$video["title"]     = $this->getTitle();
			$video["views"]     = $this->getViews();
			return $video;
		}
	}
?>