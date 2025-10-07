<?php
$db = mysqli_connect('localhost', 'root', '', 'classes');
class Userpdo {
	private $id;
	public $login;
	public $email;
	public $firstname;
	public $lastname;
	public $pdo;

	public function __construct() {
		try {
			$dsn = "mysql:host=localhost;dbname=classes;charset=" . DB_CHARSET;
			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			];
			$this->pdo = new PDO($dsn, 'root', '', $options);
		} catch (PDOException $e) {
			die("Erreur de connexion à la base de données : " . $e->getMessage());
		}
	}

	public function register($login, $password, $email, $firstname, $lastname) {
		$stmt = $this->pdo->prepare('SELECT * FROM `utilisateurs` WHERE `login` = :login');
		$stmt->execute(['login' => htmlspecialchars($login)]);
		if ($stmt->rowCount() > 0) {
			throw new Exception('Login already taken.');
		}
		else {
			$stmt = $this->pdo->prepare('INSERT INTO `utilisateurs` (`login`, `password`, `email`, `firstname`, `lastname`) VALUES (:login, :password, :email, :firstname, :lastname)');
			$result = $stmt->execute([
				'login' => htmlspecialchars($login),
				'password' => password_hash($password, PASSWORD_BCRYPT),
				'email' => htmlspecialchars($email),
				'firstname' => htmlspecialchars($firstname),
				'lastname' => htmlspecialchars($lastname)
			]);
			if ($result) {
				return $this->getAllInfos($this->pdo->lastInsertId());
			}
			else {
				throw new Exception('Registration failed.');
			}
		}

	}

	public function connect($login, $password) {
		if ($this->isConnected()) {
			throw new Exception('User already connected.');
		}
		$stmt = $this->pdo->prepare('SELECT * FROM `utilisateurs` WHERE `login` = :login');
		$stmt->execute(['login' => htmlspecialchars($login)]);
		$user = $stmt->fetch();
		if ($user && password_verify($password, $user['password'])) {
			$this->id = $user['id'];
			$this->login = $user['login'];
			$this->email = $user['email'];
			$this->firstname = $user['firstname'];
			$this->lastname = $user['lastname'];
			return true;
		} else {
			throw new Exception('Incorrect login or password.');
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
		$stmt = $this->pdo->prepare('DELETE FROM `utilisateurs` WHERE `id` = :id');
		$stmt->execute(['id' => $this->id]);
		if ($stmt->rowCount() == 0) {
			throw new Exception('User not found or already deleted.');
		}
		if ($this->isConnected()) {
			$this->disconnect();
		}
	}

	public function update($login, $password = null, $email = null, $firstname = null, $lastname = null) {
			// code...
	}

	public function isConnected() {
		return isset($this->id);
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