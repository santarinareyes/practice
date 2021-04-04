<?php 
    /*
     * Use $this->db::connectMasterDB() to connect to the master database
     * Use $this->db::connectReadDB() to connect to the read database
     */
    class Product {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db->connectReadDB();
        }

        public function checkProductExist($title){
            $this->db->query("SELECT product_title FROM products WHERE product_title = :title");
            $this->db->bind(":title", $title);
            return $this->db->single("There was an issue fetching product");
        }

        public function createProduct($data){
            $this->db->connectMasterDB();
            $this->db->query("INSERT INTO products (product_title, product_category_id, 
                              product_price, product_description) 
                              VALUES (:product_title, :product_category_id, 
                                      :product_price, :product_description)");
            $this->db->bind(":product_title", $data["title"]);
            $this->db->bind(":product_category_id", $data["category"]);
            $this->db->bind(":product_price", $data["price"]);
            $this->db->bind(":product_description", $data["description"]);

            return trueOrFalse($this->db->execute("There was an issue creating product"));
        }

        public function getLastCreatedProduct(){
            $this->db::connectMasterDB();
            $lastInsertId = $this->db->lastInsertId();
            $this->db->query("SELECT p.product_id, p.product_title, p.product_price, 
                              p.product_description, c.category_title FROM products p 
                              INNER JOIN categories c on p.product_category_id = c.category_id 
                              WHERE product_id = :id");
            $this->db->bind(":id", $lastInsertId);
            $this->db->execute("There was an issue fetching product info after creation");

            return $this->db->single("There was an issue fetching product info after creation");
        }

        public function getAllProducts(){
            $this->db->query("SELECT p.product_id, p.product_title, p.product_price, 
                              p.product_description, c.category_title FROM products p 
                              INNER JOIN categories c on p.product_category_id = c.category_id");
            return $this->db->resultSet("There was an issue fetching products");
        }

        public function getSingleProduct($id){
            $this->db->query("SELECT p.product_id, p.product_title, p.product_price, 
                              p.product_description, c.category_title FROM products p 
                              INNER JOIN categories c on p.product_category_id = c.category_id 
                              WHERE product_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single("There was an issue fetching product");
        }

        public function updateProduct($query, $id, $newTitle, $newtitleCategory, $newPrice, $newDescription){
            $this->db->connectMasterDB();
            $this->db->query("UPDATE products SET ".$query." WHERE product_id = :id");
            $this->db->bind(":id", $id);

            if($newTitle != ""){
                $this->db->bind(":title", $newTitle);
            }

            if($newtitleCategory != ""){
                $this->db->bind(":category", $newtitleCategory);
            }

            if($newPrice != ""){
                $this->db->bind(":price", $newPrice);
            }

            if($newDescription != ""){
                $this->db->bind(":description", $newDescription);
            }

            $this->db->execute("An issue occured while trying to update product");
        }

        public function getUpdatedProduct($id){
            $this->db->connectMasterDB();
            $this->db->query("SELECT p.product_id, p.product_title, p.product_price, 
                              p.product_description, c.category_title FROM products p 
                              INNER JOIN categories c on p.product_category_id = c.category_id 
                              WHERE product_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single("There was an issue trying to get product info");
        }

        public function deleteProduct($id){
            $this->db->connectMasterDB();
            $this->db->query("DELETE FROM products WHERE product_id = :id");
            $this->db->bind(":id", $id);
            return trueOrFalse($this->db->execute("There was an issue trying to delete product"));
        }

        public function countAllProducts(){
            $this->db->query("SELECT count(*) FROM products");
            return $this->db->fetchColumn("There was an issue counting products");
        }

        public function getProductsPagination($limitPerPage, $offset){
            $this->db->query("SELECT p.product_id, p.product_title, p.product_price, 
                              p.product_description, c.category_title FROM products p 
                              INNER JOIN categories c on p.product_category_id = c.category_id 
                              ORDER BY product_id ASC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(":limit", $limitPerPage);
            $this->db->bind(":offset", $offset);
            return $this->db->resultSet("There was an issue trying to fetch products");
        }
    }