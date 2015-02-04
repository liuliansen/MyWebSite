<?php
namespace services;

class Service {
    protected $tabName   = '';
    protected $sql       = array();
    protected $errorInfo = array(); 
    
    /**
     * @var \PDO
     */
    protected $conn = NULL;
    
    public function __construct(){
        $this->conn = new \PDO (
				'mysql:host=localhost;dbname=lscms',
				'root',
				'root'
		);
		$this->conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }
    
    
    
    /**
     * 执行增删改等数据库操作.<br />
     * 如果成功返回受影响的记录数<br />
     * 失败返回false,失败原因可通过getErrorInfo获取
     * @param string $sqlName
     * @param array  $params
     * @throws Exception
     * @return number|boolean
     */
    public function execute($sqlName , $params){
        try{            
            $sql = $this->sql[$sqlName];
            foreach ($params as $key => $val){
                $sql = str_replace($key, '\''.$val.'\'', $sql);
            }             
            $rs = $this->conn->exec($sql);
            $errInfo = $this->conn->errorInfo();           
            if(intval($errInfo[0]) !== 0){
               throw new \Exception('errCode:'.$errInfo[1].',errMsg:'.$errInfo[2],$errInfo[0]);
            }
            return $rs;
        }catch (\Exception $e){
           $this->errorInfo = $e;
        }
        return false;
    }
    
    
    /**
     * 查询数据库<br />
     * 如果成功返回查询到的结果<br />
     * 失败返回false,失败原因可通过getErrorInfo获取
     * @param string $sqlName
     * @param string $getType
     * @param array  $params
     * @throws Exception
     * @return Ambigous <NULL, multitype:>|boolean
     */
    public function query($sqlName ,$getType = 'getAll', $params = array()){
        try{
            $sql = $this->sql[$sqlName];            
            $st = $this->conn->prepare($sql);
            $st->execute($params);
            
            $errInfo = $st->errorInfo();
            if(intval($errInfo[0]) !== 0){
                throw new \Exception('errCode:'.$errInfo[1].',errMsg:'.$errInfo[2],$errInfo[0]);
            }
            $rs = null;
            switch ($getType){
                case 'getOne':
                    $rs = $st->fetchColumn(0);
                    break;
                case 'getRow':
                    $rs = $st->fetchAll();
                    $rs = isset($rs[0]) ? $rs[0] : array();
                    break;
                case 'getAll':
                default:
                    $rs = $st->fetchAll();
            } 
            return $rs;
        }catch (\Exception $e){
            $this->errorInfo = $e;
        }
        return false;       
    }
    
    
    /**
     * 获取最后插入的id
     * @return string
     */
    public function getLastInsertId(){
        return $this->conn->lastInsertId();
    }


    /**
     * 获取sql错误信息
     * @return array:
     */
    public function getErrorInfo(){
        return $this->errorInfo;
    }
}