<?php
    class ServerKeyManager{
        private function getFromRowData($row){
            $creationDate = DateTime::createFromFormat('Y-m-d H:i:s', $row['creation_date']);
            return new ServerKey($row['id'], $row['steamid'], $row['disabled'], $row['server_key'], $creationDate);
        }

        public function getBySteamId($steamId){
            $sth = Database::getInstance()->prepare('SELECT id, disabled, server_key, creation_date FROM server_keys WHERE steamid = ?;');
            $sth->execute(array($steamId));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['steamid'] = $steamId;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function getByKey($key){
            $sth = Database::getInstance()->prepare('SELECT id, steamid, disabled, creation_date FROM server_keys WHERE server_key = ?;');
            $sth->execute(array($key));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['server_key'] = $key;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function getById($id){
            $sth = Database::getInstance()->prepare('SELECT steamid, disabled, creation_date, server_key FROM server_keys WHERE id = ?;');
            $sth->execute(array($id));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['id'] = $id;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function add(ServerKey $sk){
            $sth = Database::getInstance()->prepare('INSERT INTO server_keys(steamid, disabled, server_key, creation_date) VALUES(?, ?, ?, ?);');
            $success = $sth->execute(array($sk->steamId(), $sk->disabled(), $sk->key(), $sk->creationDate()->format('Y-m-d H:i:s')));
            if($success){
                return Database::getInstance()->lastInsertId();
            }else{
                return false;
            }
        }
    }

    class ServerKey{
        private $_id;
        private $_steamid;
        private $_disabled;
        private $_key;
        private $_creationDate;

        public function __construct($id, $steamid, $disabled, $key, $creationDate){
            $this->setId($id);
            $this->setSteamId($steamid);
            $this->setDisabled($disabled);
			$this->setKey($key);
			$this->setCreationDate($creationDate);
		}

        public function id(){
            return $this->_id;
        }
    
        public function setId($_id){
            $this->_id = $_id;
        }
    
        public function steamId(){
            return $this->_steamid;
        }
    
        public function setSteamId($_steamid){
            $this->_steamid = $_steamid;
        }

        public function disabled(){
            return $this->_disabled;
        }

        public function setDisabled($_disabled){
            $this->_disabled = $_disabled;
        }
    
        public function key(){
            return $this->_key;
        }
    
        public function setKey($_key){
            $this->_key = $_key;
        }
    
        public function creationDate(){
            return $this->_creationDate;
        }
    
        public function setCreationDate($_creationDate){
            $this->_creationDate = $_creationDate;
        }
    }