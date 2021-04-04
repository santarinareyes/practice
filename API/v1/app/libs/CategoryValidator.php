<?php 
    class CategoryException extends Exception {}

    class CategoryValidator {
        private $_id;
        private $_title;

        public function __construct($id, $title)
        {
            $this->setId($id);
            $this->setTitle($title);
        }

        public function getId(){
            return $this->_id;
        }
        
        public function getTitle(){
            return $this->_title;
        }

        public function setId($id){
            if(($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)){
                throw new CategoryException('Category Id error');
            }

            $this->_id = $id;
        }

        public function setTitle($title){
            if(strlen($title) < 0 || strlen($title) > 20){
                throw new CategoryException('Category title error');
            }

            $this->_title = $title;
        }

        public function returnAsArray(){
            $array = [];
            $array['id'] = $this->getId();
            $array['title'] = $this->getTitle();
            return $array;
        }
    }