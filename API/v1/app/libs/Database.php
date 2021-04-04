<?php 
    class Database {
        private static $host_m = DB_HOST_M;
        private static $user_m = DB_USER_M;
        private static $pass_m = DB_PASS_M;
        private static $name_m = DB_NAME_M;

        private static $host_w = DB_HOST_W;
        private static $user_w = DB_USER_W;
        private static $pass_w = DB_PASS_W;
        private static $name_w = DB_NAME_W;

        private static $dbh;
        private $stm;
        private static $error;

        private static $masterDBConnection;
        private static $readDBConnection;

        public static function connectMasterDB(){
            if(self::$masterDBConnection === null){
                $dsn = "mysql:host=" . self::$host_m . ";dbname=" . self::$name_m;
                $options = array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );
                
                try{
                    self::$masterDBConnection = new PDO($dsn, self::$user_m, self::$pass_m, $options);
                } catch(PDOException $e){
                    self::$error = $e->getMessage();
                    PDOException(self::$error, "Database connection error");
                }
            }

            self::$dbh = self::$masterDBConnection;
        }

        public static function connectReadDB(){
            if(self::$readDBConnection === null){
                $dsn = "mysql:host=" . self::$host_w . ";dbname=" . self::$name_w;
                $options = array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );
                
                try{
                    self::$readDBConnection = new PDO($dsn, self::$user_w, self::$pass_w, $options);
                } catch(PDOException $e){
                    self::$error = $e->getMessage();
                    PDOException(self::$error, "Database connection error");
                }
            }

            self::$dbh = self::$readDBConnection;
        }

        public function query($sql){
            $this->stm = self::$dbh->prepare($sql);
        }

        public function bind($param, $value, $type = null){
            if(is_null($type)){
                switch(true){
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            
            $this->stm->bindValue($param, $value, $type);
        }

        public function execute($message, $rollback = false){
            try{
                return $this->stm->execute();

            } catch(PDOException $e) {
                self::$error = $e->getMessage();
                $rollback === true ? self::$dbh->rollBack() : false;
                PDOException(self::$error, $message);
            }
        }

        public function resultSet($message){
            $this->execute($message);
            return $this->stm->fetchAll(PDO::FETCH_OBJ);
        }

        public function single($message){
            $this->execute($message);
            return $this->stm->fetch(PDO::FETCH_OBJ);
        }

        public function rowCount(){
            return $this->stm->rowCount();
        }

        public function fetchColumn($message){
            $this->execute($message);
            return $this->stm->fetchColumn();
        }

        public function lastInsertId(){
            return self::$dbh->lastInsertId();
        }

        public function beginTransaction(){
            return self::$dbh->beginTransaction();
        }

        public function rollBack(){
            return self::$dbh->rollBack();
        }

        public function commit(){
            return self::$dbh->commit();
        }
    }