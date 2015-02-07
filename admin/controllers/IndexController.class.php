<?php
namespace controllers;
use controllers\Controller as Controller;

class IndexController extends Controller{
    public function def(){
        try{
            unset($_SESSION['UserID']);
           $this->loadView(APP_PATH .'views/index.html');
        }catch (\Exception $e){
           die("服务器端发生错误,载入失败!");
        }
    }
}