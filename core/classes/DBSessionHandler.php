<?php

namespace Core\Classes;

# use \PDOStatement;

class DBSessionHandler implements \SessionHandlerInterface, \SessionIdInterface {

    private $pdo;
    private $readStatement;
    private $writeStatement;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;  
    }
  
    # create new session_id
    private function getNextFreeId(){
        $id = md5(microtime(true));
        $this->readStatement->execute([":sessionId"=>$id]);
        if($this->readStatement->rowCount() > 0){
            $id = $this->getNextFreeId();
        }
        return $id;
    }

    # it will call if session_regenerate_id() active  
    public function create_sid(){
        $id  = $this->getNextFreeId();
        return $id;
    }

    # session close
    public function close(){
        return true;
    }

    public function open($savePath, $sessionName)
    {
        $sql ="SELECT value FROM sessions WHERE id=:sessionId";
        $this->readStatement = $this->pdo->prepare($sql);

        $sql ="INSERT INTO sessions SET value = :value, id = :sessionId ON DUPLICATE KEY UPDATE value=:value";

        $this->writeStatement = $this->pdo->prepare($sql);
      
        return true;
    }

    # read session from db
    public function read($id)
    {
       $this->readStatement->execute([":sessionId"=>$id]);
       return (string) $this->readStatement->fetchColumn();
    }

    # write session into database
    public function write($id, $data){

        $result = $this->writeStatement->execute([
             ":value"=>$data,
             ":sessionId"=>$id
         ]);
        
         return $result;
     }

     # delete sesion, if call session_delete()
     public function destroy($id){
        #doSomething;
        $deleteSQL = "DELETE FROM sessions WHERE id = :sessionId";
        $statement = $this->pdo->prepare($deleteSQL);
        $result= $statement->execute([":sessionId"=>$id]);
       return $result;
    }


    # expired delete ,if call session_gc();
    public function gc( $maxlifetime ) {
        #doSomething;
        $cleanSQL ="DELETE FROM sessions WHERE NOW() > DATE_ADD(lastUpdate, INTERVAL " . (int)$maxlifetime." SECOND)";
        return $this->pdo->exec($cleanSQL);
    }
}

