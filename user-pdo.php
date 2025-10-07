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