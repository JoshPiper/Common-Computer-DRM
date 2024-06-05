<?php

    class CategoryManager{
        private function getFromRowData($row){
            return new Category($row['id'], $row['name'], $row['image_url'], $row['server_key'], $row['description'], $row['footer']);
        }

        public function getById($id){
            $sth = Database::getInstance()->prepare('SELECT name, image_url, server_key, description, footer FROM darknet_categories WHERE id = ?;');
            $sth->execute(array($id));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['id'] = $id;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function getByServerKey($serverKey){
            $sth = Database::getInstance()->prepare('SELECT id, name, image_url, description, footer FROM darknet_categories WHERE server_key = ?;');
            $sth->execute(array($serverKey));

            $categories = array();
            while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $row['server_key'] = $serverKey;
                $categories[] = $this->getFromRowData($row);
            }

            return $categories;
        }

        public function add(Category $cat){
            $sth = Database::getInstance()->prepare('INSERT INTO darknet_categories(name, image_url, server_key, description, footer) VALUES(?, ?, ?, ?, ?);');
            $success = $sth->execute(array($cat->name(), $cat->imageUrl(), $cat->serverKey(), $cat->description(), $cat->footer()));
            if($success){
                return Database::getInstance()->lastInsertId();
            }else{
                return false;
            }
        }

        public function countByServerKey($key){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM darknet_categories WHERE server_key = ?;');
            $sth->execute(array($key));
            return $sth->fetchColumn();
        }

        public function update(Category $cat){
            $sth = Database::getInstance()->prepare('
                UPDATE 
                    darknet_categories
                SET
                    name = ?,
                    image_url = ?,
                    server_key = ?,
                    description = ?,
                    footer = ?
                WHERE
                    id = ?;
            ');
            
            return $sth->execute(array($cat->name(), $cat->imageUrl(), $cat->serverKey(), $cat->description(), $cat->footer(), $cat->id()));
        }

        public function remove($id){
            $sth = Database::getInstance()->prepare('DELETE FROM darknet_categories WHERE id = ?;');
            return $sth->execute(array($id));
        }
    }

    class Category{
        private $_id;
        private $_name;
        private $_imageUrl;
        private $_serverKey;
        private $_description;
        private $_footer;

        public function __construct($id, $name, $imageUrl, $serverKey, $description, $footer){
            $this->setId($id);
            $this->setName($name);
            $this->setImageUrl($imageUrl);
            $this->setServerKey($serverKey);
            $this->setDescription($description);
            $this->setFooter($footer);
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
    
        public function imageUrl(){
            return $this->_imageUrl;
        }
    
        public function setImageUrl($_imageUrl){
            $this->_imageUrl = $_imageUrl;
        }
    
        public function serverKey(){
            return $this->_serverKey;
        }
    
        public function setServerKey($_serverKey){
            $this->_serverKey = $_serverKey;
        }
    
        public function description(){
            return $this->_description;
        }
    
        public function setDescription($_description){
            $this->_description = $_description;
        }
    
        public function footer(){
            return $this->_footer;
        }
    
        public function setFooter($_footer){
            $this->_footer = $_footer;
        }
    }