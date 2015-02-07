<?php
namespace includes\util;

class LoadView {
    static private $me = NULL;
    private $leftLimt = '{:';
    private $rightLimt = ':}';
    
    private $holders = array('v');
    
    private function __construct(){
        $this->holders['v'] = time();
    }
    
    static public function getInstance(){
        if(self::$me === null){
            self::$me = new self();
        }
        return self::$me;
    }

    
    /**
     * 设置占位符<br/>
     * 如果设置的占位符不存在则添加进默认占位符.<br/>
     * 如果设置的占位符已存在，且$compel == true，则默认占位符的值会被覆盖.
     * @param array $holers
     * @param boolean $compel
     * @return boolean
     */
    public function setHolders($holers , $compel = false){
        foreach ($holers as $key => $val){
            if(!isset($this->holders[$key])){
                $this->holders[$key] = $val;
            }elseif($compel){
                $this->holders[$key] = $val;
            }
        }
        return true;
    }
        
    
    /**
     * 载入前端界面文件.<br/>
     * 并且会替换文件中的占位符
     * @param string $viewFile
     * @return string 界面文件内容
     */
    public function loadFile($file){        
        $content = '';        
        $handle = fopen($file,'r');
        while(!feof($handle)){
            $row = fgets($handle);
            foreach ($this->holders as $key => $val){
                $holder = $this->leftLimt .'$'. $key . $this->rightLimt;
                $row = str_replace($holder, $val,$row);
            }
            $content .= $row;
        }
        fclose($handle);       
        return $content;
    }
}

