<?php

namespace Api\Data_Access;

use PDO;
use Api\Data_Access\Dbconnect;
use Api\Core\Response;

class dbHandler {

    private $db;
    private $response;

    public function __CONSTRUCT() {
        // opening db connection
        try {
            $this->db = dbConnect::StartUp();
            $this->response = new Response();
        } catch(PDOException $e) {
            $this->response->setResponse(false, $e->getMessage());
            exit;
        }
    }

    /**
    * Fetching single record
    */
    public function getOneRecord($query) {
        try {

            $result = array();
            $stm = $this->db->prepare($query.' LIMIT 1');
            $stm->execute();

			$this->response->setResponse(true);
            $this->response->result = $stm->fetch();

        } catch(PDOException $e) {

            $this->response->setResponse(false, $e->getMessage());
        }

        return $this->response;
    }

    /**
    * Fetching all record
    */
    public function getAll($table, $columns, $where, $order) {
        try {
            $a = array();
            $w = "";
            if($where != null){
                foreach ($where as $key => $value) {
                    $w .= " AND " .$key. " LIKE :".$key;
                    $a[":".$key] = $value;
                }
            }
            $query = "SELECT ".$columns." FROM ".$table." WHERE 1=1 ". $w." ".$order;
            $stmt = $this->db->prepare($query);
            $stmt->execute($a);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(count($rows) <= 0){
                $this->response->setResponse(false, "No data found.");
                $this->response->result = null;
            }else{
                $this->response->setResponse(true, "success");
                $this->response->result = $rows;
            }

        } catch(Exception $e) {
            $this->response->setResponse(false, $e->getMessage());
        }

        return $this->response;
    }

    /**
    * Fetching all record by Order
    */
    public function getAllByOrder($table, $columns, $where, $order) {
        try {
            $a = array();
            $w = "";
            foreach ($where as $key => $value) {
                $w .= " AND " .$key. " LIKE :".$key;
                $a[":".$key] = $value;
            }
            $stmt = $this->db->prepare("SELECT ".$columns." FROM ".$table." WHERE 1=1 ". $w." ORDER BY ".$order);
            $stmt->execute($a);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(count($rows) <= 0){
                $this->response->setResponse(false, "No data found.");
                $this->response->result = null;
            }else{
                $this->response->setResponse(true);
                $this->response->result = $rows;
            }

        } catch(PDOException $e) {
            $this->response->setResponse(false, $e->getMessage());
        }

        return $this->response;
    }

    /**
    * Creating new record
    **/
    public function insert($table, $columnsArray)
    {
        try{
            $a = array();
            $c = "";
            $v = "";
            foreach ($columnsArray as $key => $value) {
                $c .= $key. ", ";
                $v .= ":".$key. ", ";
                $a[":".$key] = $value;
            }
            $c = rtrim($c,', ');
            $v = rtrim($v,', ');
            $stmt =  $this->db->prepare("INSERT INTO $table($c) VALUES($v)");
            $stmt->execute($a);
            $affected_rows = $stmt->rowCount();
            $lastInsertId = $this->db->lastInsertId();
            $this->response->getBody()->write($lastInsertId);
            $this->response->setResponse(true, $affected_rows." row inserted into database");
        } catch(PDOException $e) {
            $this->response->setResponse(false, $e->getMessage());    
        }

        return $this->response;
    }

    /**
    * Updating record
    **/
    public function update($table, $columnsArray, $where)
    {
       try {
           $a = array();
            $w = "";
            $c = "";
            foreach ($where as $key => $value) {
                $w .= " and " .$key. " = :".$key;
                $a[":".$key] = $value;
            }
            foreach ($columnsArray as $key => $value) {
                $c .= $key. " = :".$key.", ";
                $a[":".$key] = $value;
            }
            
            $c = rtrim($c,", "); 

            $stmt =  $this->db->prepare("UPDATE $table SET $c WHERE 1=1 ".$w);
            $stmt->execute($a);
            $affected_rows = $stmt->rowCount();
            if($affected_rows <= 0){
                $this->response->setResponse(false, "No row updated.");
            }else{
                $this->response->setResponse(true, $affected_rows." row(s) updated in database");
            }
       } catch(PDOException $e) {
           $this->response->setResponse(false, $e->getMessage());
       }
        
       return $this->response;      
    }

    /**
    * Deleting record
    */
    public function delete($table, $where) {
        try {
            $w = "";
            foreach ($where as $key => $value) {
                $w .= " AND " .$key. " = ".$value;
            }

            $stm = $this->db
			            ->prepare("DELETE FROM $table WHERE ". $w);			          

            $stm->execute();
            
			$this->response->setResponse(true);

        } catch(PDOException $e) {
            $this->response->setResponse(false, $e->getMessage());
        }

        return $this->response;
    }
}