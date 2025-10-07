<?php
	$db = mysqli_connect('localhost', 'root', '', 'classes');
	class User {
		private $id;
		public $login;
		public $email;
		public $firstname;
		public $lastname;

		public function __construct() {
			// code...
		}

		public function register($login, $password, $email, $firstname, $lastname) {

		}

		public function connect($login, $password) {

		}

		public function disconnect() {
			$this->id = null;
			$this->login = null;
			$this->email = null;
			$this->firstname = null;
			$this->lastname = null;
		}

		public function delete() {

			$this->disconnect();
		}

		public function update($login, $password = null, $email = null, $firstname = null, $lastname = null) {
			// code...
		}

		public function isConnected() {
			// code...
		}

		public function getAllInfos() {
			// code...
		}

		public function getLogin() {
			return $this->login;
		}

		public function getEmail() {
			return $this->email;
		}

		public function getFirstname() {
			return $this->firstname;
		}

		public function getLastname() {
			return $this->lastname;
		}
	}
?>