<?php
namespace controllers;
use includes\util\LoadView as LoadView;
/**
 * 控制器的基类
 * @author LianSen
 * @since 2015-02-04
 * @version 1.0
 */
abstract class Controller {
    protected $result   = array('errno' => 0, 'success' => true, 'msg' => '');
    protected $viewer = NULL;
    
    public function __construct(){
        $this->viewer =  LoadView::getInstance();
    }
    
    
    /**
     * 返回信息并结束执行
     */
    protected function returnExecuteInfo($result=array()){
        $this->result = array_merge($this->result,$result);
        die(json_encode($this->result));
    }
    
    
    /**
     * 设置执行信息数组
     * @param boolean $success
     * @param string  $msg
     * @param integer $errno
     */
    protected function setExecuteInfo($success , $msg , $errno = NULL){
        if($errno === NULL)	$errno = $success ? 0 : 1;
        $this->result['errno'] = $errno;
        $this->result['success'] = $success;
        $this->result['msg'] = $msg;
    }
    
    
    protected function loadView($viewFile){
        if(!is_file($viewFile)){
            throw new \Exception('前端界面文件未找到',10004);
        }
        die($this->viewer->loadFile($viewFile));
    }
    
    
    /**
     * controller默认的执行方法<br/>
     * 当未指定需要执行的方法时,该方法将会被调用.<br/>
     * 可以被手动调用.
     */
    abstract public function def();
    
    
   
}