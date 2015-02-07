<?php
namespace controllers;
use controllers\Controller as Controller;
use includes\util\captcha as captcha;
use services\AdminsServ as AdminsServ;

class LoginController extends Controller{
    /**
     * 验证码类实例
     * @var Captcha
     */
    private $captcha = NULL;
    
    /** 
     * 用户类实例
     * @var AdminsServ
     */
    private $adminServ = NULL;
    
    
    /**
     * (non-PHPdoc)
     * @see \controllers\Controller::def()
     */
    public function def(){
        try{ 
            $this->loadView(APP_PATH .'views/login.html');
        }catch (\Exception $e){
           die("服务器端发生错误,载入失败!");
        }
    }
    
    
    /**
     * 生成验证码
     */
    public function mkCaptcha(){
        $this->captcha = new captcha(APP_PATH .'image/captcha/', 80,24);
        @ob_end_clean (); 	// 清除之前出现的多余输入
        $this->captcha->generate_image ();
    }
    
    
    /**
     * 用户登录
     */
    public function login(){
        try{
            $this->captcha = new captcha();
            if(!$this->_chkCaptcha($_POST['checkcode'])){
               throw new \Exception('验证码错误', '10001');
            }
            
            $this->adminServ = new AdminsServ();
            $user = $this->adminServ->getUserByAccount($_POST['account']);          
            if(empty($user)){
                throw new \Exception('账号不存在', '10002');
            }            
            if($user['PassWord'] != $_POST['password']){
                throw new \Exception('账号或密码错误', '10003');
            }
           
            $this->_setLoginInfo($user);
                        
        }catch(\Exception $e){
            $this->setExecuteInfo(false, $e->getMessage() , $e->getCode());
        }         
        $this->returnExecuteInfo();
    }
    
    
    /**
     * 检查提交的验证码是否正确
     * @param string $captcahCode 提交的验证码
     * @return boolean
     */
    private function _chkCaptcha($captcahCode){
        $captcahCode = substr($captcahCode, 1,10);
        return $_SESSION[$this->captcha->session_word] == base64_encode($captcahCode);
    }
    
    
    /**
     * 更新用户记录的登录信息和设置session信息
     * @param array $userInfo
     */
    private function _setLoginInfo($userInfo){
        $_SESSION['UserID']   = $userInfo['UserID'];
        $_SESSION['Account']  = $userInfo['Account'];
        $_SESSION['UserName'] = $userInfo['UserName'];
        $rs = $this->adminServ->updUserLoginInfo(
            $userInfo['UserID'],
            date('Y-m-d H:i:s', time()),
            $_SERVER['REMOTE_ADDR']
        );  
    }
    
    
    
}