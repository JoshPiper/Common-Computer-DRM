<?php
    class DocumentManager{
        private function getFromRowData($row){
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $row['date']);
            return new Document($row['id'], $row['steamid'], $row['server_key'], $row['category'], $row['background'], $row['title'], $row['content'], $date);
        }

        public function getById($id){
            $sth = Database::getInstance()->prepare('SELECT steamid, server_key, category, background, title, content, date FROM word_docs WHERE id = ?;');
            $sth->execute(array($id));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['id'] = $id;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function getInRange($cat_id, $start, $end){
            $start = intval($start);
            $end = intval($end);

            $sth = Database::getInstance()->prepare('SELECT id, steamid, server_key, background, title, content, date FROM word_docs WHERE category = ? LIMIT ' . $end . ' OFFSET ' . $start .';');
            $sth->execute(array($cat_id));

            $documents = array();
            while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $row['category'] = $cat_id;
                $documents[] = $this->getFromRowData($row);
            }

            return $documents;
        }

        public function countByServerKey($serverKey){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM word_docs WHERE server_key = ?;');
            $sth->execute(array($serverKey));
            return $sth->fetchColumn();
        }

        public function countByCategory($serverKey, $cat_id){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM word_docs WHERE server_key = ? AND category = ?;');
            $sth->execute(array($serverKey, $cat_id));
            return $sth->fetchColumn();
        }

        public function countBySteamId($serverKey, $steamId){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM word_docs WHERE server_key = ? AND steamid = ?;');
            $sth->execute(array($serverKey, $steamId));
            return $sth->fetchColumn();
        }

        public function add(Document $doc){
            $sth = Database::getInstance()->prepare('INSERT INTO word_docs(steamid, server_key, category, background, title, content, date) VALUES(?, ?, ?, ?, ?, ?, ?);');
            $success = $sth->execute(array($doc->steamId(), $doc->serverKey(), $doc->category(), $doc->background(), $doc->title(), $doc->content(), $doc->date()->format('Y-m-d H:i:s')));
            if($success){
                return Database::getInstance()->lastInsertId();
            }else{
                return false;
            }
        }

        public function update(Document $doc){
            $sth = Database::getInstance()->prepare('
                UPDATE 
                    word_docs
                SET
                    steamid = ?,
                    server_key = ?,
                    category = ?,
                    background = ?,
                    title = ?,
                    content = ?,
                    date = ?
                WHERE
                    id = ?;
            ');
            
            return $sth->execute(array($doc->steamId(), $doc->serverKey(), $doc->category(), $doc->background(), $doc->title(), $doc->content(), $doc->date()->format('Y-m-d H:i:s'), $doc->id()));
        }

        public function remove($id){
            $sth = Database::getInstance()->prepare('DELETE FROM word_docs WHERE id = ?;');
            return $sth->execute(array($id));
        }
    }

    class Document{
        private $_id;
        private $_steamid;
        private $_serverKey;
        private $_category;
        private $_background;
        private $_title;
        private $_content;
        private $_date;

        public function __construct($id, $steamid, $serverKey, $category, $background, $title, $content, $date){
            $this->setId($id);
            $this->setSteamId($steamid);
            $this->setServerKey($serverKey);
            $this->setCategory($category);
            $this->setBackground($background);
            $this->setTitle($title);
            $this->setContent($content);
            $this->setDate($date);
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
    
        public function serverKey(){
            return $this->_serverKey;
        }
    
        public function setServerKey($_serverKey){
            $this->_serverKey = $_serverKey;
        }
    
        public function category(){
            return $this->_category;
        }
    
        public function setCategory($_category){
            $this->_category = $_category;
        }
    
        public function background(){
            return $this->_background;
        }
    
        public function setBackground($_background){
            $this->_background = $_background;
        }
    
        public function title(){
            return $this->_title;
        }
    
        public function setTitle($_title){
            $this->_title = $_title;
        }
    
        public function content(){
            return $this->_content;
        }
    
        public function setContent($_content){
            $this->_content = $_content;
        }
    
        public function date(){
            return $this->_date;
        }
    
        public function setDate($_date){
            $this->_date = $_date;
        }
    }