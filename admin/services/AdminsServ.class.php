<?php
namespace services;
use services\UuidServ as UuidServ;

class AdminsServ extends Service {
    public function __construct(){
        include_once APP_PATH .'includes/sql/Admins.inc.php';
        parent::__construct();
        $this->tabName = 'Admins';
        $this->sql = $GLOBALS['SQL']['Admins'];
    }
    
    
    /**
     * 创建用户
     * @throws Exception
     * @return boolean
     */
    public function createUser(){
        try{
            $uuidServ = new UuidServ();
            $params = array(
                ':UserID'   =>  $uuidServ->getUuid(),
                ':UserName' => '刘炼森',
                ':Account'  => 'liansen',
                ':PassWord' => md5(123456)
            );
            $rs = $this->execute( 'createUser' , $params);
            if($rs === false) throw $this->getErrorInfo();         
            return true;
        }catch (\Exception $e){
            throw $e;
        }
    }    
}