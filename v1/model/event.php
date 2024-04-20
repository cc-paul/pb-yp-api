<?php
	class Event {
		private $_markerID;
		private $_eventID;
		private $_event;
		private $_isAcitve;
		private $_dateCreated;
		private $_description;
		private $_hasImage;
		private $_origin;
		private $_needRadius;
		private $_link;
		private $_lat;
		private $_lng;
		private $_radius;
		private $_remarks;
		private $_alertLevel;
		private $_passableVehicle;
		private $_dateReported;
		private $_duration; 

		public function __construct(
			$markerID,
			$eventID,
			$event,
			$isActive,
			$dateCreated,
			$description,
			$hasImage,
			$origin,
			$needRadius,
			$link,
			$lat,
			$lng,
			$radius,
			$remarks,
			$alertLevel,
			$passableVehicle,
			$dateReported,
			$duration
		) {
			$this->setMarkerID($markerID);
			$this->setEventID($eventID);
			$this->setEvent($event);
			$this->setIsActive($isActive);
			$this->setDateCreated($dateCreated);
			$this->setDescription($description);
			$this->setHasImage($hasImage);
			$this->setOrigin($origin);
			$this->setNeedRadius($needRadius);
			$this->setLink($link);
			$this->setLat($lat);
			$this->setLng($lng);
			$this->setRadius($radius);
			$this->setRemarks($remarks);
	        $this->setAlertLevel($alertLevel);
	        $this->setPassableVehicle($passableVehicle);
	        $this->setDateReported($dateReported);
	        $this->setDuration($duration);
		}


		/* Getters */
		public function getMarkerID() {
			return $this->_markerID;
		}

		public function getEventID() {
			return $this->_eventID;
		}

		public function getEvent() {
			return $this->_event;
		}

		public function getIsActive() {
			return $this->_isAcitve;
		}

		public function getDateCreated() {
			return $this->_dateCreated;
		}

		public function getDescription() {
			return $this->_description;
		}

		public function getHasImage() {
			return $this->_hasImage;
		}

		public function getOrigin() {
			return $this->_origin;
		}

		public function getNeedRadius() {
			return $this->_needRadius;
		}

		public function getLink() {
			return $this->_link;
		}

		public function getLat() {
			return $this->_lat;
		}

		public function getLng() {
			return $this->_lng;
		}

		public function getRadius() {
			return $this->_radius;
		}

		public function getRemarks() {
	        return $this->_remarks;
	    }

	    public function getAlertLevel() {
	        return $this->_alertLevel;
	    }

	    public function getPassableVehicle() {
	        return $this->_passableVehicle;
	    }

	    public function getDateReported() {
	        return $this->_dateReported;
	    }

	    public function getDuration() {
	        return $this->_duration;
	    }


		/* Setters */
		public function setMarkerID($markerID) {
			$this->_markerID = $markerID;
		}

		public function setEventID($eventID) {
			$this->_eventID = $eventID;
		}

		public function setEvent($event) {
			$this->_event = $event;
		}

		public function setIsActive($isAcitve) {
			$this->_isAcitve = $isAcitve;
		}

		public function setDateCreated($dateCreated) {
			$this->_dateCreated = $dateCreated;
		}

		public function setDescription($description) {
			$this->_description = $description;
		}

		public function setHasImage($hasImage) {
			$this->_hasImage = $hasImage;
		}

		public function setOrigin($origin) {
			$this->_origin = $origin;
		}

		public function setNeedRadius($needRadius) {
			$this->_needRadius = $needRadius;
		}

		public function setLink($link) {
			$this->_link = $link;
		}

		public function setLat($lat) {
			$this->_lat = $lat;
		}

		public function setLng($lng) {
			$this->_lng = $lng;
		}

		public function setRadius($radius) {
			$this->_radius = $radius;
		}

		public function setRemarks($remarks) {
	        $this->_remarks = $remarks;
	    }

	    public function setAlertLevel($alertLevel) {
	        $this->_alertLevel = $alertLevel;
	    }

	    public function setPassableVehicle($passableVehicle) {
	        $this->_passableVehicle = $passableVehicle;
	    }

	    public function setDateReported($dateReported) {
	        $this->_dateReported = $dateReported;
	    }

	    public function setDuration($duration) {
	        $this->_duration = $duration;
	    }

		public function returnEventAsArray() {
			$event = array(
		        "markerID" => $this->getMarkerID(),
		        "eventID" => $this->getEventID(),
		        "event" => $this->getEvent(),
		        "isActive" => $this->getIsActive(),
		        "dateCreated" => $this->getDateCreated(),
		        "description" => $this->getDescription(),
		        "hasImage" => $this->getHasImage(),
		        "origin" => $this->getOrigin(),
		        "needRadius" => $this->getNeedRadius(),
		        "link" => $this->getLink(),
		        "lat" => $this->getLat(),
		        "lng" => $this->getLng(),
		        "radius" => $this->getRadius(),
		        "remarks" => $this->getRemarks(),
		        "alertLevel" => $this->getAlertLevel(),
		        "passableVehicle" => $this->getPassableVehicle(),
		        "dateReported" => $this->getDateReported(),
		        "duration" => $this->getDuration()
		    );
		    return $event;
		}
	}
?>