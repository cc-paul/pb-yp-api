<?php
	class FAQ {
		private $_id;
		private $_question;
		private $_answer;

		public function __construct($id,$question,$answer) {
			$this->setID($id);
			$this->setQuestion($question);
			$this->setAnswer($answer);
		}

		/* Getters */
		public function getID() {
			return $this->_id;
		}

		public function getQuestion() {
			return $this->_question;
		}

		public function getAnswer() {
			return $this->_answer;
		}

		/* Setter */
		public function setID($id) {
			$this->_id = $id;
		}

		public function setQuestion($question) {
			$this->_question = $question;
		}

		public function setAnswer($answer) {
			$this->_answer = $answer;
		}

		public function returnFaqAsArray() {
			$faq = array();
			$faq["id"]       = $this->getID();
			$faq["question"] = $this->getQuestion();
			$faq["answer"]   = $this->getAnswer();
			return $faq;
		}
	}
?>