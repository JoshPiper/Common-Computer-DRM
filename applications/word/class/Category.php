<?php

    class CategoryManager{
        private function getFromRowData($row){
            return new Category($row['id'], $row['server_key'], $row['name']);
        }

        public function getById($id){
            $sth = Database::getInstance()->prepare('SELECT server_key, name FROM word_categories WHERE id = ?;');
            $sth->execute(array($id));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['id'] = $id;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function getByServerKey($key){
            $sth = Database::getInstance()->prepare('SELECT id, name FROM word_categories WHERE server_key = ?;');
            $sth->execute(array($key));

            $categories = array();
            while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $row['server_key'] = $key;
                $categories[] = $this->getFromRowData($row);
            }

            return $categories;
        }

        public function add(Category $cat){
            $sth = Database::getInstance()->prepare('INSERT INTO word_categories(name, server_key) VALUES(?, ?);');
            $success = $sth->execute(array($cat->name(), $cat->serverKey()));
            if($success){
                return Database::getInstance()->lastInsertId();
            }else{
                return false;
            }
        }
        
        public function update(Category $cat){
            $sth = Database::getInstance()->prepare('
                UPDATE 
                    word_categories
                SET
                    name = ?,
                    server_key = ?
                WHERE
                    id = ?;
            ');
            
            return $sth->execute(array($cat->name(), $cat->serverKey(), $cat->id()));
        }

        public function countByServerKey($key){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM word_categories WHERE server_key = ?;');
            $sth->execute(array($key));
            return $sth->fetchColumn();
        }

        public function remove($id){
            $sth = Database::getInstance()->prepare('DELETE FROM word_categories WHERE id = ?;');
            return $sth->execute(array($id));
        }
    }

    class Category{
        private $_id;
        private $_serverKey;
        private $_name;

        public function __construct($id, $serverKey, $name){
            $this->setId($id);
            $this->setServerKey($serverKey);
            $this->setName($name);
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
    
        public function name(){
            return $this->_name;
        }
    
        public function setName($_name){
            $this->_name = $_name;
        }
    }