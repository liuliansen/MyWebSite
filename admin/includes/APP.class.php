<?php
namespace includes;

class APP{
   static $crlDir = 'controllers/';
    
   static public function start(){
        session_start();
        //获取需要执行的controller
        $ctrl = 'controllers\\'.self::_getController();
     
        //需要执行的action
        $act = self::_getAction();
        
        
        //检查方法是否存在
        if(!method_exists($ctrl, $act)){
           throw new \Exception("ERROR:function '{$act}' not found in '{$ctrl}'");
        }        
     
        (new $ctrl()) -> $act(); //调用方法 
        
        session_write_close();
   }
  

   /**
    * 获取需要执行的controller名
    * @return string
    */
   static private function _getController(){
       $ctrl = 'Index';
       if(!isset($_SESSION['UserID']) || trim($_SESSION['UserID']) == ''){
           $ctrl = 'Index';
       }elseif(isset($_REQUEST['ctrl']) && trim($_REQUEST['ctrl']) != ''){
           $ctrl = trim($_REQUEST['ctrl']);
       }
       $ctrl .= 'Controller';
   
       $clsFielPath = self::_getControllerPath(APP_PATH .self::$crlDir, $ctrl.'.class.php');
       if($clsFielPath === false) throw new \Exception("ERROR:class '{$ctrl}' not found.");
       require $clsFielPath;
       return $ctrl;
   }
      

   /**
    * 获取需要执行的action(方法)
    * @return string
    */
   static private function _getAction(){
       $act = 'def';
       if(isset($_REQUEST['act']) && trim($_REQUEST['act']) != ''){
           $act = trim($_REQUEST['act']);
       }
       return $act;
   }
   
   
   /**
    * 查找指定controller的文件全路径<br/>
    * 返回第一个匹配名称的文件全路径<br/>
    * 未找到则返回false
    * @param string $controllersDir controller文件根目录
    * @param string $clsFileName	 需要查找的controller名
    * @return string|Ambigous <boolean, string>
    */
   static private function _getControllerPath($controllersDir , $clsFileName){
       $clsPath = false;
       foreach(scandir($controllersDir) as $path){
           if($path == $clsFileName){
               return $controllersDir.$clsFileName;
           }elseif($path != '.' && $path != '..' && is_dir($controllersDir.$path)){
               $clsPath = self::_getControllerPath($controllersDir.$path.'/', $clsFileName);
               if($clsPath !== false) break;
           }
       }
       return $clsPath;
   }
}