<?php
namespace controllers;
use controllers\Controller as Controller;
use services\AdminsServ as AdminsServ;

class IndexController extends Controller{
    public function def(){
        try{
            (new AdminsServ()) -> createUser();
        }catch (\Exception $e){
            die($e->getMessage());
        }        
    }
}