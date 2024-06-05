<?php

    class ArticleManager{
        private function getFromRowData($row){
            return new Article($row['id'], $row['name'], $row['image_url'], $row['server_key'], $row['price'], $row['description'], $row['category'], $row['class']);
        }

        public function getById($id){
            $sth = Database::getInstance()->prepare('SELECT name, image_url, server_key, price, description, category, class FROM darknet_articles WHERE id = ?;');
            $sth->execute(array($id));

            if($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $row['id'] = $id;
                return $this->getFromRowData($row);
            }else{
                return false;
            }
        }

        public function getByCategory($category){
            $sth = Database::getInstance()->prepare('SELECT id, name, image_url, server_key, price, description, class FROM darknet_articles WHERE category = ?;');
            $sth->execute(array($category));

            $articles = array();
            while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $row['category'] = $category;
                $articles[] = $this->getFromRowData($row);
            }

            return $articles;
        }

        public function countByServerKey($key){
            $sth = Database::getInstance()->prepare('SELECT COUNT(id) FROM darknet_articles WHERE server_key = ?;');
            $sth->execute(array($key));
            return $sth->fetchColumn();
        }

        public function add(Article $article){
            $sth = Database::getInstance()->prepare('INSERT INTO darknet_articles(name, image_url, server_key, price, description, category, class) VALUES(?, ?, ?, ?, ?, ?, ?);');
            $success = $sth->execute(array($article->name(), $article->imageUrl(), $article->serverKey(), $article->price(), $article->description(), $article->category(), $article->class()));
            if($success){
                return Database::getInstance()->lastInsertId();
            }else{
                return false;
            }
        }

        public function update(Article $article){
            $sth = Database::getInstance()->prepare('
                UPDATE 
                    darknet_articles
                SET
                    name = ?,
                    image_url = ?,
                    server_key = ?,
                    price = ?,
                    description = ?,
                    category = ?,
                    class = ?
                WHERE
                    id = ?;
            ');
            
            return $sth->execute(array($article->name(), $article->imageUrl(), $article->serverKey(), $article->price(), $article->description(), $article->category(), $article->class(), $article->id()));
        }

        public function remove($id){
            $sth = Database::getInstance()->prepare('DELETE FROM darknet_articles WHERE id = ?;');
            return $sth->execute(array($id));
        }
    }

    class Article{
        private $_id;
        private $_name;
        private $_imageUrl;
        private $_serverKey;
        private $_price;
        private $_description;
        private $_category;
        private $_class;

        public function __construct($id, $name, $imageUrl, $serverKey, $price, $description, $category, $class){
            $this->setId($id);
            $this->setName($name);
            $this->setImageUrl($imageUrl);
            $this->setServerKey($serverKey);
            $this->setPrice($price);
            $this->setDescription($description);
            $this->setCategory($category);
            $this->setClass($class);
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

        public function setImageUrl($imageUrl){
            $this->_imageUrl = $imageUrl;
        }

        public function serverKey(){
            return $this->_serverKey;
        }
    
        public function setServerKey($_serverKey){
            $this->_serverKey = $_serverKey;
        }
    
        public function price(){
            return $this->_price;
        }
    
        public function setPrice($_price){
            $this->_price = $_price;
        }
    
        public function description(){
            return $this->_description;
        }
    
        public function setDescription($_description){
            $this->_description = $_description;
        }
    
        public function category(){
            return $this->_category;
        }
    
        public function setCategory($_category){
            $this->_category = $_category;
        }
    
        public function class(){
            return $this->_class;
        }
    
        public function setClass($_class){
            $this->_class = $_class;
        }
    }