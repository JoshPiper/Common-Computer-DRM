<?php

    class ServerManager{
        private function getFromRowData($row){
            $lastActivity = DateTime::createFromFormat('Y-m-d H:i:s', $row['last_activity']);
            return new Server($row['id'], $row['server_key'], long2ip($row['ipv4']), $lastActivity);
        }

        public function getById($id){
            $sth = Database::getInstance()->prepare('SELECT server_key, ipv4, last_activity FROM servers WHERE id = ?;');
            $sth->execute(array($id));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['id'] = $id;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function getByServerKey($key, $ip){
            $ip = ip2long($ip);

            $sth = Database::getInstance()->prepare('SELECT id, last_activity FROM servers WHERE server_key = ? AND ipv4 = ?;');
            $sth->execute(array($key, $ip));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['server_key'] = $key;
                $row['ipv4'] = $ip;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function countByServerKey($key){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM servers WHERE server_key = ?;');
            $sth->execute(array($key));
            return $sth->fetchColumn();
        }

        public function add(Server $serv){
            $sth = Database::getInstance()->prepare('INSERT INTO servers(server_key, ipv4, last_activity) VALUES(?, ?, ?);');
            $success = $sth->execute(array($serv->serverKey(), ip2long($serv->ip()), $serv->lastActivity()->format('Y-m-d H:i:s')));
            if($success){
                return Database::getInstance()->lastInsertId();
            }else{
                return false;
            }
        }

        public function update(Server $serv){
            $sth = Database::getInstance()->prepare('
                UPDATE 
                    servers 
                SET
                    server_key = ?,
                    ipv4 = ?,
                    last_activity = ?
                WHERE
                    id = ?;
            ');
            
            return $sth->execute(array($serv->serverKey(), ip2long($serv->ip()), $serv->lastActivity()->format('Y-m-d H:i:s'), $serv->id()));
        }
    }

    class Server{
        private $_id;
        private $_serverKey;
        private $_ip;
        private $_lastActivity;

        public function __construct($id, $serverKey, $ip, $lastActivity){
            $this->setId($id);
            $this->setServerKey($serverKey);
            $this->setIp($ip);
            $this->setLastActivity($lastActivity);
		}

        public function id(){
            return $this->_id;
        }
    
        public function setId($_id){
            $this->_id = $_id;
        }
    
        public function serverKey(){
            return $this->_serverKey;
        }
    
        public function setServerKey($_serverKey){
            $this->_serverKey = $_serverKey;
        }
    
        public function ip(){
            return $this->_ip;
        }
    
        public function setIp($_ip){
            $this->_ip = $_ip;
        }
    
        public function lastActivity(){
            return $this->_lastActivity;
        }
    
        public function setLastActivity($_lastActivity){
            $this->_lastActivity = $_lastActivity;
        }
    }