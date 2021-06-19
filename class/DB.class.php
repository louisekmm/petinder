<?php

class DB extends PDO {
	private $db;
	protected $intTransactions = 0;
	protected $insertID = 0;
	public function __construct($dbname, $username, $password) {
        try {
			$this->db = parent::__construct($dbname, $username, $password);
            parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            die($e->getMessage());
        }
	}
	
	public function doit() {
		
	}
	
	public function insert($tabela, $dados) {
		
		try {
			$sql = 'INSERT INTO '.$tabela.' ('.implode(',', array_keys($dados)).') VALUES (?'.str_repeat(',?',count($dados)-1).')';
			
			
			$r = self::prepare($sql);
			
			$r->execute(array_values($dados));
			$this->insertID = self::lastInsertId();
			return $r;
		} catch(Exception $e) {
			throw new Exception($e);
		}
	}
	public function getInsertId() {
		return $this->insertID;
	}
	
	
	
	public function replace($tabela, $dados) {
		$temp = array_keys($dados);
		$temp2 = array();
		foreach($temp as $k) {
			$temp2[] = $k.'=VALUES('.$k.')';
		}
		$sql = 'INSERT INTO '.$tabela.' ('.implode(',', array_keys($dados)).') VALUES (?'.str_repeat(',?',count($dados)-1).')
		ON DUPLICATE KEY UPDATE '.implode(', ', $temp2);

		try {
			$r = self::prepare($sql);
			$r->execute(array_values($dados));
			$this->insertID = self::lastInsertId();
			return $r;
		} catch(Exception $e) {
			return;
		}
		
	}
	
	public function getDb() {
		return $this->db;
	}
	
	
	function beginTransaction () {
		$this->intTransactions++;
		if ($this->intTransactions > 1) { //se houve alguma transação ativa, não inicia uma nova
			return true;
		} else { //caso contrário, inicia
			return parent::beginTransaction();
		}
	}

	function commit() {
		if ($this->intTransactions == 1) {
			parent::commit();
		}
		$this->intTransactions--;
   }

	function rollback() {
		if ($this->intTransactions == 1) {
			parent::rollback();
		}
		$this->intTransactions--;
	}
	
}