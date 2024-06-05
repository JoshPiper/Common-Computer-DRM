<?php

    class PlayerKeyManager{
        private function getFromRowData($row){
            $lastActivity = DateTime::createFromFormat('Y-m-d H:i:s', $row['last_activity']);
            return new PlayerKey($row['id'], $row['steamid'], $row['server'], $row['player_key'], $row['admin'], $lastActivity);
        }

        public function getByKey($key){
            $sth = Database::getInstance()->prepare('SELECT id, steamid, server, admin, last_activity FROM player_keys WHERE player_key = ?;');
            $sth->execute(array($key));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['player_key'] = $key;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function getBySteamId($steamid, $server){
            $sth = Database::getInstance()->prepare('SELECT id, player_key, admin, last_activity FROM player_keys WHERE steamid = ? AND server = ?;');
            $sth->execute(array($steamid, $server));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['steamid'] = $steamid;
                $row['server'] = $server;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function countByServer($id_server){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM player_keys WHERE server = ?;');
            $sth->execute(array($id_server));
            return $sth->fetchColumn();
        }

        public function add(PlayerKey $pk){
            $sth = Database::getInstance()->prepare('INSERT INTO player_keys(steamid, server, player_key, admin, last_activity) VALUES(?, ?, ?, ?, ?);');
            return $sth->execute(array($pk->steamId(), $pk->server(), $pk->key(), $pk->admin(), $pk->lastActivity()->format('Y-m-d H:i:s')));
        }

        public function removeBySteamId($steamid, $server){
            $sth = Database::getInstance()->prepare('DELETE FROM player_keys WHERE server = ? AND steamid = ?;');
            return $sth->execute(array($server, $steamid));
        }

        public function removeByServer($server){
            $sth = Database::getInstance()->prepare('DELETE FROM player_keys WHERE server = ?;');
            return $sth->execute(array($server));
        }

        public function update(PlayerKey $pk){
            $sth = Database::getInstance()->prepare('
                UPDATE 
                    player_keys 
                SET
                    steamid = ?,
                    server = ?,
                    player_key = ?,
                    admin = ?,
                    last_activity = ?
                WHERE
                    id = ?;
            ');
            
            return $sth->execute(array($pk->steamId(), $pk->server(), $pk->key(), $pk->admin(), $pk->lastActivity()->format('Y-m-d H:i:s'), $pk->id()));
        }
    }

    class PlayerKey{
        private $_id;
        private $_steamid;
        private $_server;
        private $_playerKey;
        private $_admin;
        private $_lastActivity;

        public function __construct($id, $steamid, $server, $playerKey, $admin, $lastActivity){
            $this->setId($id);
            $this->setSteamId($steamid);
            $this->setServer($server);
            $this->setKey($playerKey);
            $this->setAdmin($admin);
            $this->setLastActivity($lastActivity);
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
    
        public function server(){
            return $this->_server;
        }
    
        public function setServer($_server){
            $this->_server = $_server;
        }
    
        public function key(){
            return $this->_playerKey;
        }
    
        public function setKey($_playerKey){
            $this->_playerKey = $_playerKey;
        }
    
        public function admin(){
            return $this->_admin;
        }
    
        public function setAdmin($_admin){
            $this->_admin = $_admin;
        }
    
        public function lastActivity(){
            return $this->_lastActivity;
        }
    
        public function setLastActivity($_lastActivity){
            $this->_lastActivity = $_lastActivity;
        }
    }