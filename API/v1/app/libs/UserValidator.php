<?php 
    class UserException extends Exception {}

    class UserValidator {
        private $_id;
        private $_firstname;
        private $_lastname;
        private $_username;
        private $_email;
        private $_role = 'User';

        public function __construct($id, $firstname, $lastname, $username, $email, $role)
        {
            $this->setId($id);
            $this->setFirstname($firstname);
            $this->setLastname($lastname);
            $this->setUsername($username);
            $this->setEmail($email);
            $this->setRole($role);
        }

        public function getId(){
            return $this->_id;
        }
        
        public function getFirstname(){
            return $this->_firstname;
        }
        
        public function getLastname(){
            return $this->_lastname;
        }
        
        public function getUsername(){
            return $this->_username;
        }
        
        public function getEmail(){
            return $this->_email;
        }

        public function getRole(){
            return $this->_role;
        }

        public function setId($id){
            if(($id !== null) && (!is_numeric($id) || $id <= 0 || $id > 9223372036854775807 || $this->_id !== null)){
                throw new UserException('User Id error');
            }

            $this->_id = $id;
        }

        public function setFirstname($firstname){
            if(strlen($firstname) < 0 || strlen($firstname) > 255){
                throw new UserException('User firstname error');
            }

            $this->_firstname = $firstname;
        }

        public function setLastname($lastname){
            if(strlen($lastname) < 0 || strlen($lastname) > 255){
                throw new UserException('User lastname error');
            }

            $this->_lastname = $lastname;
        }

        public function setUsername($username){
            if(strlen($username) < 0 || strlen($username) > 20){
                throw new UserException('User username error');
            }
            $this->_username = $username;
        }

        public function setEmail($email){
            if(strlen($email) < 0 || strlen($email) > 255){
                throw new UserException('User email error');
            }

            $this->_email = $email;
        }

        public function setRole($role){
            $this->_role = $role;
        }

        public function returnAsArray(){
            $user = [];
            $user['id'] = $this->getId();
            $user['firstname'] = $this->getFirstname();
            $user['lastname'] = $this->getLastname();
            $user['username'] = $this->getUsername();
            $user['email'] = $this->getEmail();
            $user['role'] = $this->getRole();
            return $user;
        }
    }