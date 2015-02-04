<?php
namespace services;
use services\Service as Service;

class UuidServ extends Service {
    public function getUuid(){       
        $st = $this->conn->prepare('SELECT uuid() AS uuid');
        $st->execute();
        return strtoupper($st->fetchColumn(0));
    }
    
}