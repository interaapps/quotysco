<?php
namespace app\classes;



class FTP {
    private $conn;

    public function __construct($url){
        $this->conn = ftp_connect($url);
    }
   
    public function __call($func,$a){
        $func = "ftp_".$func;
        if(strstr($func,'ftp_') !== false && function_exists($func)){
            array_unshift($a,$this->conn);

            return call_user_func_array($func,$a);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
