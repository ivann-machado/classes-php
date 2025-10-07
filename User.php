<?php
class User {
	private $id;
	public $login;
	public $email;
	public $firstname;
	public $lastname;
	public $db;

	public function __construct() {
		$this->db = mysqli_connect('localhost', 'root', '', 'classes');

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
		$result = mysqli_query($this->db, 'SELECT * FROM `utilisateurs` WHERE `login` = "'.htmlspecialchars($login).'"');
		if (mysqli_num_rows($result) > 0) {
			throw new Exception('Login already taken.');
		}
		else {
			$pw = password_hash($password, PASSWORD_BCRYPT);
			$query = 'INSERT INTO `utilisateurs` (`login`, `password`, `email`, `firstname`, `lastname`) VALUES ("'.htmlspecialchars($login).'", "'.$pw.'", "'.htmlspecialchars($email).'", "'.htmlspecialchars($firstname).'", "'.htmlspecialchars($lastname).'")';
			if (mysqli_query($this->db, $query)) {
				return [
					'id' => mysqli_insert_id($this->db),
					'login' => $login,
					'password' => $pw,
					'email' => $email,
					'firstname' => $firstname,
					'lastname' => $lastname
				];
			}
		}
	}

	public function connect($login, $password) {
		if ($this->isConnected()) {
			throw new Exception('User already connected.');
		}
		$result = mysqli_query($this->db, 'SELECT * FROM `utilisateurs` WHERE `login` = "'.htmlspecialchars($login).'"');
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
		if (mysqli_query($this->db, 'DELETE FROM `utilisateurs` WHERE `id` = "'.$this->id.'"')) {
			$this->disconnect();
		}
	}

	public function update($login = null, $password = null, $email = null, $firstname = null, $lastname = null) {
		$fields = [];
		if ($login !== null) {
			$fields[] = '`login` = "'.htmlspecialchars($login).'"';
		}
		if ($password !== null) {
			$fields[] = '`password` = "'.password_hash($password, PASSWORD_BCRYPT).'"';
		}
		if ($email !== null) {
			$fields[] = '`email` = "'.htmlspecialchars($email).'"';
		}
		if ($firstname !== null) {
			$fields[] = '`firstname` = "'.htmlspecialchars($firstname).'"';
		}
		if ($lastname !== null) {
			$fields[] = '`lastname` = "'.htmlspecialchars($lastname).'"';
		}
		if (!empty($fields)) {
			$query = 'UPDATE `utilisateurs` SET '.implode(', ', $fields).' WHERE `id` = "'.$this->id.'"';
			if (mysqli_query($this->db, $query)) {
				$this->login = $login ?? $this->login;
				$this->email = $email ?? $this->email;
				$this->firstname = $firstname ?? $this->firstname;
				$this->lastname = $lastname ?? $this->lastname;
				return true;
			}
		}
	}

	public function isConnected() {
		return isset($this->id);
	}

	public function getAllInfos() {
		return [
			'id' => $this->id,
			'login' => $this->login,
			'email' => $this->email,
			'firstname' => $this->firstname,
			'lastname' => $this->lastname
		];
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