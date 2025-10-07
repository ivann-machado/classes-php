<?php
	$db = mysqli_connect('localhost', 'root', '', 'classes');
	class User {
		private $id;
		public $login;
		public $email;
		public $firstname;
		public $lastname;

		public function __construct() {
			if (isset($_SESSION['user_id'])) {
				$user = $this->getAllInfos($_SESSION['user_id']);
				$this->id = $user['id'];
				$this->login = $user['login'];
				$this->email = $user['email'];
				$this->firstname = $user['firstname'];
				$this->lastname = $user['lastname'];
			}
		}

		public function register($login, $password, $email, $firstname, $lastname) {
			$query = 'INSERT INTO `users` (`login`, `password`, `email`, `firstname`, `lastname`) VALUES ("'.htmlspecialchars($login).'", "'.password_hash($password, PASSWORD_BCRYPT).'", "'.htmlspecialchars($email).'", "'.htmlspecialchars($firstname).'", "'.htmlspecialchars($lastname).'")';
			if (mysqli_query($db, $query)) {
				return getAllInfos(mysqli_insert_id($db));
			}
		}

		public function connect($login, $password) {
			$result = mysqli_query($db, 'SELECT * FROM `users` WHERE `login` = "'.htmlspecialchars($login).'"');
			if (mysqli_num_rows($result) == 1) {
				$user = mysqli_fetch_assoc($result);
				if (password_verify($password, $user['password'])) {
					$this->id = $user['id'];
					$this->login = $user['login'];
					$this->email = $user['email'];
					$this->firstname = $user['firstname'];
					$this->lastname = $user['lastname'];
					return true;
				}
				else {
					throw new Exception('Incorrect password.');
				}
			}
			else {
				throw new Exception('User not found.');
			}

		}

		public function disconnect() {
			$this->id = null;
			$this->login = null;
			$this->email = null;
			$this->firstname = null;
			$this->lastname = null;
		}

		public function delete() {
			if (mysqli_query($db, 'DELETE FROM `users` WHERE `id` = "'.$this->id.'"')) {
				$this->disconnect();
			}
		}

		public function update($login, $password = null, $email = null, $firstname = null, $lastname = null) {
			// code...
		}

		public function isConnected() {
			return isset($this->id);
		}

		public function getAllInfos($id = null) {
			$id = $id ?? $this->id;
			$result = mysqli_query($db, 'SELECT * FROM `users` WHERE `id` = "'.htmlspecialchars($id).'"');
			if (mysqli_num_rows($result) == 1) {
				return mysqli_fetch_assoc($result);
			}
			else {
				throw new Exception('User not found.');
			}
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