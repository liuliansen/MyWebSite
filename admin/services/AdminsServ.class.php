<?php
namespace services;
use services\UuidServ as UuidServ;

class AdminsServ extends Service {
    public function __construct(){
        parent::__construct();
        $this->tabName = 'Admins';
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
            $rs = $this->execute( 'createUser','insert' , $params);
            if($rs === false) throw $this->getErrorInfo();         
            return true;
        }catch (\Exception $e){
            throw $e;
        }
    }
    
    
    public function getAllUsers(){
        $params = array(
            'orderby' => 'UserID DESC'
        );
        return $this->execute('getAllUsers', 'getAll',$params);
    }
    
    
    /**
     * 获取指定account的用户记录
     * @param string $account
     * @return array
     */
    public function getUserByAccount($account){
        return $this->execute('getUserByAccount', 'getRow' , array(
            ':Account' => $account
        ));
    }


    /**
     * 更新用户的最后登录时间和ip
     * @param string $userID
     * @param string $time
     * @param string $ip
     * @return int
     */
    public function updUserLoginInfo($userID,$time,$ip){     
        return $this->execute('updUserLoginInfo','update', array(
            ':UserID' => $userID,
            ':LastLoginTime' => $time,
            ':LastLoginIP' => $ip  
        ));
    }
}