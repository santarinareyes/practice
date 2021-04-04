<?php 
    class CartException extends Exception {}

    class CartValidator {
        private $_id;
        private $_user;
        private $_product;

        public function __construct($id, $user, $product)
        {
            $this->setId($id);
            $this->setUser($user);
            $this->setProduct($product);
        }

        public function getId(){
            return $this->_id;
        }
    
        public function getUser(){
            return $this->_user;
        }

        public function getProduct(){
            return $this->_product;
        }

        public function setId($id){
            if(($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)){
                throw new UserException('Cart Id error');
            }

            $this->_id = $id;
        }

        public function setUser($user){
            if(strlen($user) < 0 || strlen($user) > 255){
                throw new UserException('Cart user error');
            }

            $this->_user = $user;
        }

        public function setProduct($product){
            if(strlen($product) < 0 || strlen($product) > 255){
                throw new UserException('Cart product error');
            }

            $this->_product = $product;
        }

        public function returnAsArray(){
            $cart = [];
            $cart['id'] = $this->getId();
            $cart['customer'] = $this->getUser();
            $cart['product'] = $this->getProduct();
            return $cart;
        }

        public function returnAsArrayTotal(){
            $cart = [];
            $cart['user_id'] = $this->getId();
            $cart['username'] = $this->getUser();
            $cart['priceTotal'] = $this->getProduct();
            return $cart;
        }
    }