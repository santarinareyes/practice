<?php 
    /*
     * Use $this->db::connectMasterDB() to connect to the master database
     * Use $this->db::connectReadDB() to connect to the read database
     */
    class Cart {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db->connectReadDB();
        }

        public function checkUsername($username){
            $this->db->query("SELECT username, user_id FROM users 
                              WHERE username = :username");
            $this->db->bind(":username", $username);
            return $this->db->single("There was an issue trying to check user");
        }

        public function checkUserId($id){
            $this->db->query("SELECT username, user_id FROM users 
                              WHERE user_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single("There was an issue fetching user");
        }

        public function checkProduct($title){
            $this->db->query("SELECT product_id, product_title, product_price 
                              FROM products WHERE product_title = :title");
            $this->db->bind(":title", $title);
            return $this->db->single("There was an issue fetching user");
        }

        public function addCartItem($data){
            $this->db->connectMasterDB();
            $this->db->query("INSERT INTO carts (cart_user_id, cart_product_id) 
                              VALUES (:cart_user_id, :cart_product_id)");
            $this->db->bind(":cart_user_id", $data["user"]);
            $this->db->bind(":cart_product_id", $data["product"]);

            return trueOrFalse($this->db->execute("There was an issue adding product to cart"));
        }

        public function getLastAddedCartItem(){
            $this->db::connectMasterDB();
            $lastInsertId = $this->db->lastInsertId();
            $this->db->query("SELECT c.cart_id, u.username, p.product_title FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              WHERE cart_id = :id");
            $this->db->bind(":id", $lastInsertId);
            $this->db->execute("There was an issue fetching the added product info");

            return $this->db->single("There was an issue fetching the added product info");
        }

        public function getAllCartItems($loggedInUserRole, $loggedInUserId){
            $AdminOrUser = "";
            $loggedInUserRole == 'User' ? $AdminOrUser = "WHERE user_id = ".$loggedInUserId : "";
            $this->db->query("SELECT c.cart_id, u.username, p.product_title FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              $AdminOrUser
                              GROUP BY cart_id ASC");
            return $this->db->resultSet("There was an issue fetching all cart items");
        }

        public function getAllCartTotals($loggedInUserRole, $loggedInUserId){
            $AdminOrUser = "";
            $loggedInUserRole == 'User' ? $AdminOrUser = "WHERE user_id = ".$loggedInUserId : "";
            $this->db->query("SELECT u.user_id, u.username, SUM(p.product_price) AS total FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              $AdminOrUser
                              GROUP BY u.username ASC");
            return $this->db->resultSet("There was an issue fetching carts info");
        }

        public function getUserCartTotal($username){
            $this->db->query("SELECT u.user_id, u.username, SUM(p.product_price) AS total FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              WHERE username = :username 
                              GROUP BY u.username ASC");
            $this->db->bind(":username", $username);
            return $this->db->single("There was an issue fetching a users cart");
        }

        public function getIdCartTotal($id){
            $this->db->query("SELECT u.user_id, u.username, SUM(p.product_price) AS total FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              WHERE user_id = :id 
                              GROUP BY u.username ASC");
            $this->db->bind(":id", $id);
            return $this->db->single("There was an issue fetching a users cart");
        }

        public function deleteUserCart($id){
            $this->db->connectMasterDB();
            $this->db->query("DELETE FROM carts WHERE cart_user_id = :id");
            $this->db->bind(":id", $id);
            return trueOrFalse($this->db->execute("There was an issue trying to delete users cart"));
        }

        public function checkCartItem($id){
            $this->db->query("SELECT c.cart_user_id, u.username, c.cart_id, p.product_title, p.product_price FROM carts c 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              WHERE c.cart_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single("There was an issue fetching cart item");
        }

        public function deleteCartItem($id){
            $this->db->connectMasterDB();
            $this->db->query("DELETE FROM carts WHERE cart_id = :id");
            $this->db->bind(":id", $id);
            return trueOrFalse($this->db->execute("There was an issue trying to delete cart item"));
        }

        public function countAllCarts(){
            $this->db->query("SELECT count(*) FROM carts 
                              GROUP BY cart_user_id");
            return $this->db->fetchColumn("There was an issue counting carts");
        }

        public function getCartsPagination($limitPerPage, $offset){
            $this->db->query("SELECT u.user_id, u.username, SUM(p.product_price) AS total FROM carts c 
                              INNER JOIN users u ON c.cart_user_id = u.user_id 
                              INNER JOIN products p ON c.cart_product_id = p.product_id 
                              GROUP BY u.username 
                              ORDER BY cart_id ASC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(":limit", $limitPerPage);
            $this->db->bind(":offset", $offset);
            return $this->db->resultSet("There was an issue trying to fetch carts");
        }
    }