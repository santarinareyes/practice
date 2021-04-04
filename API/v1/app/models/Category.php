<?php 
    /*
     * Use $this->db::connectMasterDB() to connect to the master database
     * Use $this->db::connectReadDB() to connect to the read database
     */
    class Category {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db::connectReadDB();
        }

        public function checkCategoryExist($title){
            $this->db->query("SELECT * FROM categories WHERE category_title = :title");
            $this->db->bind(":title", $title);
            return $this->db->single("There was an issue fetching categories");
        }

        public function getSingleCategory($id){
            $this->db->query("SELECT * FROM categories WHERE category_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single("There was an issue fetching category");
        }

        public function createCategory($title){
            $this->db::connectMasterDB();
            $this->db->query("INSERT INTO categories (category_title) 
                              VALUES (:title)");
            $this->db->bind(":title", $title);
            return trueOrFalse($this->db->execute("There was an issue creating an ew category"));
        }

        public function getLastCreatedCategory(){
            $this->db::connectMasterDB();
            $lastInsertId = $this->db->lastInsertId();
            $this->db->query("SELECT * FROM categories WHERE category_id = :id");
            $this->db->bind(":id", $lastInsertId);
            $this->db->execute("There was an issue fetching category info after creation");

            return $this->db->single("There was an issue fetching category info after creation");
        }

        public function getAllCategories(){
            $this->db->query("SELECT * FROM categories 
                              GROUP BY category_id ASC");
            return $this->db->resultSet("There was an issue fetching categories");
        }

        public function updateCategory($data){
            $this->db::connectMasterDB();
            $this->db->query("UPDATE categories SET category_title = :title 
                              WHERE category_id = :id");
            $this->db->bind(":id", $data["id"]);
            $this->db->bind(":title", $data["title"]);
            $this->db->execute("An issure occured while trying to update category");
        }

        public function getUpdatedCategory($id){
            $this->db::connectMasterDB();
            $this->db->query("SELECT * FROM categories WHERE category_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->single("There was an issue trying to get category info");
        }

        public function deleteCategory($id){
            $this->db->connectMasterDB();
            $this->db->query("DELETE FROM categories WHERE category_id = :id");
            $this->db->bind(":id", $id);
            return trueOrFalse($this->db->execute("There was an issue trying to delete category"));
        }

        public function countAllCategories(){
            $this->db->query("SELECT count(*) FROM categories");
            return $this->db->fetchColumn("There was an issue counting categories");
        }

        public function getCategoriesPagination($limitPerPage, $offset){
            $this->db->query("SELECT * FROM categories 
                              ORDER BY category_id ASC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(":limit", $limitPerPage);
            $this->db->bind(":offset", $offset);
            return $this->db->resultSet("There was an issue trying to fetch categories");
        }
    }