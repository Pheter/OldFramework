<?php

class Plugins_ADOdb {
    
    private $conn;
    
    public function __construct($settings) {
        
        require('lib/ADOdb/adodb.inc.php');
        $this->conn = ADONewConnection($settings['database']['dsn']);
        $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    }
    
    
    /**
     * Magic methods turn this object into a proxy for $this->conn.
     * This allows it to be called as such:
     *     $ADOdb->Execute();
     * Instead of:
     *     $ADOdb->conn->Execute();
     **/
    public function __call($name, $args) {
        
        return call_user_func_array(array($this->conn, $name), $args);
    }
    
    
    public function __get($name) {
        
        return $this->conn->$name;
    }
}
