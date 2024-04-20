<?php
	class ProfileImageException extends Exception {

	}

	class ProfileImage {
		private $_image_link;

		public function __construct($image_link) {
			$this->setImageLink($image_link);
		}

		public function setImageLink($image_link) {
			$this->_image_link = $image_link;
		}

		public function getImageLink() {
			return $this->_image_link;
		}

		public function returnProfileImageAsArray() {
			$profile = array();
			$profile['image_link'] = $this->getImageLink();
			return $profile;
		}
	}
?>