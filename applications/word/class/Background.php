<?php

    class BackgroundManager{
        private function getFromRowData($row){
            return new Background($row['id'], $row['name'], $row['server_key'], $row['url']);
        }

        public function getByServerKey($key){
            $sth = Database::getInstance()->prepare('SELECT id, name, url FROM word_backgrounds WHERE server_key = ?;');
            $sth->execute(array($key));

            $backgrounds = array();
            while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $row['server_key'] = $key;
                $backgrounds[] = $this->getFromRowData($row);
            }

            return $backgrounds;
        }

        public function getById($id){
            $sth = Database::getInstance()->prepare('SELECT name, server_key, url FROM word_backgrounds WHERE id = ?;');
            $sth->execute(array($id));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['id'] = $id;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function add(Background $bg){
            $sth = Database::getInstance()->prepare('INSERT INTO word_backgrounds(name, server_key, url) VALUES(?, ?, ?);');
            $success = $sth->execute(array($bg->name(), $bg->serverKey(), $bg->url()));
            if($success){
                return Database::getInstance()->lastInsertId();
            }else{
                return false;
            }
        }

        public function update(Background $bg){
            $sth = Database::getInstance()->prepare('
                UPDATE 
                    word_backgrounds
                SET
                    name = ?,
                    server_key = ?,
                    url = ?
                WHERE
                    id = ?;
            ');
            
            return $sth->execute(array($bg->name(), $bg->serverKey(), $bg->url(), $bg->id()));
        }

        public function countByServerKey($key){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM word_backgrounds WHERE server_key = ?;');
            $sth->execute(array($key));
            return $sth->fetchColumn();
        }

        public function remove($id){
            $sth = Database::getInstance()->prepare('DELETE FROM word_backgrounds WHERE id = ?;');
            return $sth->execute(array($id));
        }
    }

    class Background{
        private $_id;
        private $_name;
        private $_serverKey;
        private $_url;

        public function __construct($id, $name, $serverKey, $url){
            $this->setId($id);
            $this->setName($name);
            $this->setServerKey($serverKey);
            $this->setUrl($url);
        }

        public function id(){
            return $this->_id;
        }
    
        public function setId($_id){
            $this->_id = $_id;
        }
    
        public function name(){
            return $this->_name;
        }
    
        public function setName($_name){
            $this->_name = $_name;
        }
    
        public function serverKey(){
            return $this->_serverKey;
        }
    
        public function setServerKey($_serverKey){
            $this->_serverKey = $_serverKey;
        }
    
        public function url(){
            return $this->_url;
        }
    
        public function setUrl($_url){
            $this->_url = $_url;
        }
    }