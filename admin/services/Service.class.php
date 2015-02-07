<?php
namespace services;

class Service {
    protected $tabName   = '';
    protected $errorInfo = array(); 
    
    static public $paramsKeyWord = array(
        'GROUPBY', 
        'HAVING' ,
        'ORDERBY',  
        'LIMIT',        
    );
    
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
     * 执行数据库操作<br />
     * 当为select时,返回结果为查询到的结果集(取决于$getType指定的方式)<br/>
     * 当为update、insert或delete时返回收影响的记录数
     * @param string $sqlName
     * @param string $getType
     * @param array  $params
     * @throws Exception
     * @return Ambigous <NULL, multitype:>|boolean
     */
    public function execute($sqlName ,$getType, $params = array()){
        try{
            $sql = $this->_getSqlStatement($sqlName, $params);
            
            $st = $this->conn->prepare($sql);
            $st->execute($params);
            $errInfo = $st->errorInfo();

            if($errInfo[0] !== '00000'){
                throw new \Exception($errInfo[2],intval($errInfo[1]));
            }
            $rs = null;
            $getType = strtoupper($getType);
            switch ($getType){
                case 'GETONE':
                    $rs = $st->fetchColumn(0);
                    break;
                case 'GETROW':
                    $rs = $st->fetchAll();
                    $rs = isset($rs[0]) ? $rs[0] : array();
                    break;
                case 'GETALL':
                    $rs = $st->fetchAll();
                    break;
                case 'UPDATE':
                case 'INSERT':
                case 'DELETE':
                    $rs = $st->rowCount();
                    break;
                default:
                    throw new \Exception('未知的数据库操作类型!');                    
            }    
            return $rs;
        }catch (\Exception $e){
            throw $e;
        }           
    }
    
    
//     private function _processParams($params){
//         foreach ($params as $k => $v){
//             if($v )
//         }
//     }

    /**
     * 获取最后插入的id
     * @return string
     */
    public function getLastInsertId(){
        return $this->conn->lastInsertId();
    }

    
    /**
     * 获取指定sqlName的sql语句
     * @param string $sqlName
     * @param array $params
     * @return string
     */
    private function _getSqlStatement($sqlName , &$params){
        $xml = new \SimpleXMLElement(
            file_get_contents(
                APP_PATH . 'includes/sql/'. $this->tabName  .'.xml'
        ));        

        return trim($this->_mkSqlStatement($xml, $sqlName , $params))
               .$this->_mkSqlStatementSuffix($params);
    }
 
    
    /**
     * 生成指定sqlName的sql语句
     * @param \SimpleXMLElement $xml
     * @param string $sqlName
     * @param array  $params
     * @return string
     */
    private function _mkSqlStatement( \SimpleXMLElement $xml , $sqlName,&$params){        
        $statement = '';        
        foreach ($xml->children() as $key => $sqlGroup){
            $_sqlName = (string)$sqlGroup->attributes()->name;
            if($_sqlName == $sqlName){             
                $statement = (string) $sqlGroup->sql;
                $hasWhere  = (int) $sqlGroup->has_where;
                if ($hasWhere == 0){
                    $where = '';
                    foreach ($sqlGroup->options->option as $option){
                        $optName   = (string)$option->attributes()->name;
                        if(isset($params[$optName])){
                            $linkType  = (string)$option->attributes()->link;
                            $condition = trim((string)$option);                    
                            $subWhere  = '';
                            if($where != '') {
                                $subWhere = ' ' . $linkType .' ' .$condition;
                            }else{
                                $subWhere = ' ' . $condition;
                            }
                            $where .= $subWhere;
                        }
                    }
                    if($where != '') $statement .= ' WHERE' . $where;
                }     
                break;
            }
        }
   
        foreach ($params as $key => $param){
            if(is_array($param)){
                foreach ($param as $k => $val){
                    $param[$k] = "'{$val}'";
                }
                $optVal = implode(',', $param);
                $statement = str_replace($key, $optVal, $statement);
                unset($params[$key]);
            }
        }
        return $statement;
    }
    
    
    /**
     * 生成sql语句后缀(指语句末尾的 order by 和limit等).<br />
     * 当前支持 GROUP BY、HAVING、ORDER BY、LIMIT,支持的列表参见self::$paramsKeyWord数组
     * @param array $params
     * @return string
     */
    private function _mkSqlStatementSuffix(&$params){      
        $keyWords = array();
        $keyWordStr = '';
        foreach ($params as $key => $param){
            $_key = strtoupper($key);
            if(in_array($_key, self::$paramsKeyWord)){
                unset($params[$key]);
                $keyWords[] = array('key' => $_key,'val' => $param);
            }            
        }
        if(!empty($keyWords)){
            usort($keyWords, function($val1 , $val2){             
                $key1 = array_search($val1['key'], Service::$paramsKeyWord);
                $key2 = array_search($val2['key'], Service::$paramsKeyWord);
                return $key1 > $key2;
            });          
            foreach ($keyWords as $keyWord){
                if($keyWord['key'] == 'ORDERBY') $keyWord['key'] = 'ORDER BY';
                if($keyWord['key'] == 'GROUPBY') $keyWord['key'] = 'GROUP BY';
                $keyWordStr .= ' '.$keyWord['key'].' '.$keyWord['val'];
            }
        }
         
        return $keyWordStr;
    }
    
}