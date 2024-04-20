<?php
	class Notification {
		private $_eventID;
		private $_title;
		private $_body;
		private $_dateCreated;
		private $_event;
		private $_image_link;

		public function __construct($eventID,$title,$body,$dateCreated,$event,$image_link) {
			$this->setEventID($eventID);
			$this->setTitle($title);
			$this->setBody($body);
			$this->setDateCreated($dateCreated);
			$this->setEvent($event);
			$this->setImageLink($image_link);
		}

		
		/* Getters */
		public function getEventID() {
			return $this->_eventID;
		}

		public function getTitle() {
			return $this->_title;
		}

		public function getBody() {
			return $this->_body;
		}

		public function getDateCreated() {
			return $this->_dateCreated;
		}

		public function getEvent() {
			return $this->_event;
		}

		public function getImageLink() {
			return $this->_image_link;
		}

		/* Setters */
		public function setEventID($eventID) {
			$this->_eventID = $eventID;
		}

		public function setTitle($title) {
			$this->_title = $title;
		}

		public function setBody($body) {
			$this->_body = $body;
		}

		public function setDateCreated($dateCreated) {
			$this->_dateCreated = $dateCreated;
		}

		public function setEvent($event) {
			$this->_event = $event;
		}

		public function setImageLink($image_link) {
			$this->_image_link = $image_link;
		}

		public function returnNotificationAsArray() {
			$notification = array();
			$notification["eventID"] = $this->getEventID();
			$notification["title"] = $this->getTitle();
			$notification["body"] = $this->getBody();
			$notification["dateCreated"] = $this->getDateCreated();
			$notification["event"] = $this->getEvent();
			$notification["imageLink"] = $this->getImageLink();
			return $notification;
		}
	}
?>