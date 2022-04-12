<?php

namespace Core\Database;
use PDO;

use App\Config\Database;

use \Core\{
	Utilities\Util
};

class Mysql { 


	private $db;
	private $error;

	private $stmt;

	public function __construct(){
		
		$conf = Database::getInstance()->mysql();

		$this->host = $conf['host'];
		$this->user = $conf['user'];
		$this->pass = $conf['pass'];
		$this->dbname = $conf['dbname'];

		$this->connect();
		
		unset($this->host);
		unset($this->user);
		unset($this->pass);
		unset($this->dbname);

	}

	public function connect(){
		try{
			$this->db = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname.';charset=UTF8', $this->user, $this->pass, Array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			));
		} catch(PDOException $e){
			$this->error = $e->getMessage();
		}
	}

	public function query($query){
		$this->stmt = $this->db->prepare($query);

	}

	public function bind($param, $value){
		$this->stmt->bindValue($param, $value, PDO::PARAM_STR);
	}

	public function execute(){
		return $this->stmt->execute();
	}

	public function resultset(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function rowCount(){
		return $this->stmt->rowCount();
	}

	public function lastInsertId(){
		return $this->db->lastInsertId();
	}
}
