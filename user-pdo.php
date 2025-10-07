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
			$dsn = "mysql:host=localhost;dbname=classes;charset=utf8mb4";
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

	public function update($login = null, $password = null, $email = null, $firstname = null, $lastname = null) {
		$fields = [];
		$params = ['id' => $this->id];
		if ($login !== null) {
			$fields[] = '`login` = :login';
			$params['login'] = htmlspecialchars($login);
		}
		if ($password !== null) {
			$fields[] = '`password` = :password';
			$params['password'] = password_hash($password, PASSWORD_BCRYPT);
		}
		if ($email !== null) {
			$fields[] = '`email` = :email';
			$params['email'] = htmlspecialchars($email);
		}
		if ($firstname !== null) {
			$fields[] = '`firstname` = :firstname';
			$params['firstname'] = htmlspecialchars($firstname);
		}
		if ($lastname !== null) {
			$fields[] = '`lastname` = :lastname';
			$params['lastname'] = htmlspecialchars($lastname);
		}
		if (empty($fields)) {
			throw new Exception('No fields to update.');
		}
		$query = 'UPDATE `utilisateurs` SET ' . implode(', ', $fields) . ' WHERE `id` = :id';
		$stmt = $this->pdo->prepare($query);
		$result = $stmt->execute($params);
		if ($result) {
			$this->login = $login ?? $this->login;
			$this->email = $email ?? $this->email;
			$this->firstname = $firstname ?? $this->firstname;
			$this->lastname = $lastname ?? $this->lastname;
			return true;
		} else {
			throw new Exception('Update failed.');
		}

	}

	public function isConnected() {
		return isset($this->id);
	}

	public function getAllInfos($id = null) {
		$id = $id ?? $this->id;
		$stmt = $this->pdo->prepare('SELECT * FROM `utilisateurs` WHERE `id` = :id');
		$stmt->execute(['id' => $id]);
		return $stmt->fetch();
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