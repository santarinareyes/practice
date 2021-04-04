<?php 
    class ProductException extends Exception {}

    class ProductValidator {
        private $_id;
        private $_title;
        private $_category;
        private $_price;
        private $_description;

        public function __construct($id, $title, $category, $price, $description)
        {
            $this->setId($id);
            $this->setTitle($title);
            $this->setCategory($category);
            $this->setPrice($price);
            $this->setDescription($description);
        }

        public function getId(){
            return $this->_id;
        }
    
        public function getTitle(){
            return $this->_title;
        }

        public function getCategory(){
            return $this->_category;
        }
    
        
        public function getPrice(){
            return $this->_price;
        }
        
        public function getDescription(){
            return $this->_description;
        }

        public function setId($id){
            if(($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)){
                throw new UserException('Product Id error');
            }

            $this->_id = $id;
        }

        public function setTitle($title){
            if(strlen($title) < 0 || strlen($title) > 255){
                throw new UserException('Product title error');
            }

            $this->_title = $title;
        }

        public function setCategory($category){
            if(strlen($category) < 0 || strlen($category) > 255){
                throw new UserException('Product category error');
            }

            $this->_category = $category;
        }

        public function setPrice($price){
            if(strlen($price) < 0 || strlen($price) > 20){
                throw new UserException('Product price error');
            }
            $this->_price = $price;
        }

        public function setDescription($description){
            if(strlen($description) < 0 || strlen($description) > 255){
                throw new UserException('Product description error');
            }

            $this->_description = $description;
        }

        public function returnAsArray(){
            $product = [];
            $product['id'] = $this->getId();
            $product['title'] = $this->getTitle();
            $product['category'] = $this->getCategory();
            $product['price'] = $this->getPrice();
            $product['description'] = $this->getDescription();
            return $product;
        }
    }