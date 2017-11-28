<?php
                
	class db extends PDO {
	
    public function __construct($options = null) {
      parent::__construct('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PWD, $options);
      $this->setAttribute(PDO::ATTR_TIMEOUT, 5);
      $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    
    public function exec($sql) {
      return (parent::exec($sql) or die(print_r($this->errorInfo(), true)));
    }
    
    public function query($sql) {
      $args = func_get_args();
      return call_user_func_array(array($this, 'parent::query'), $args);
    }
    
    public function select($table, $where = null, $data = null, $columns = '*') {
      $sql = 'SELECT '.$columns.' FROM '.$table;
      if($where != null) {
        $sql .= ' WHERE '.$where;
      }
      $stmt = $this->prepare($sql);
      $stmt->execute($data);
      return $stmt;
    }
    
    public function insert($table, $data) {
      $tmp = '';
      foreach($data as $key=>$value) {
        $tmp .= '?,'; 
      }
      $sql = 'INSERT INTO '.$table.' ('.implode(',', array_keys($data)).') VALUES ('.rtrim($tmp,',').')';
      $stmt = $this->prepare($sql);
      $stmt->execute(array_values($data));
      return $this->lastInsertId();
    }
    
    public function update($table, $data, $where = null) {
      $tmp = '';
      foreach($data as $key=>$value) {
        $tmp .= $key.'=?,'; 
      }
      $sql = 'UPDATE '.$table.' SET '.rtrim($tmp,',');
      if($where != null) {
        $sql .= ' WHERE '.$where;
      }
      $stmt = $this->prepare($sql);
      $stmt->execute(array_values($data));
      return $stmt->rowCount();
    }
    
    public function delete($table, $where = null, $data = null) {
      $sql = 'DELETE FROM '.$table;
      if($where != null) {
        $sql .= ' WHERE '.$where;
      }
      $stmt = $this->prepare($sql);
      $stmt->execute($data);
      return $stmt->rowCount();
    }
    
    public function status() {
      $stmt = $this->prepare('SHOW STATUS WHERE variable_name LIKE "Threads_%" OR variable_name = "Connections"');
      $stmt->execute();
      return $stmt;
    }
	}
    
?>