<?php 
    /*
     * Use $this->db::connectMasterDB() to connect to the master database
     * Use $this->db::connectReadDB() to connect to the read database
     */
    class Session {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
            $this->db::connectMasterDB();
        }

        public function updateLoginAttemptsOnFail($id){
            $this->db->query("UPDATE users SET loginattempts = loginattempts+1 
                              WHERE user_id = :id");
            $this->db->bind(":id", $id);
            $this->db->execute("There was an issue logging in");
        }

        public function resetLoginAttemptsOnSuccess($data){
            $this->db->beginTransaction();
            $this->db->query("UPDATE users SET loginattempts = 0 WHERE user_id = :id");
            $this->db->bind(":id", $data["user_id"]);
            $this->db->execute("There was an issue logging in");

            $this->db->query("INSERT INTO sessions (session_user_id, 
                              accesstoken, accesstoken_expiry, 
                              refreshtoken, refreshtoken_expiry) 
                              VALUES (:session_user_id, :accesstoken, 
                              date_add(NOW(), INTERVAL :accesstoken_expiry SECOND), :refreshtoken, 
                              date_add(NOW(), INTERVAL :refreshtoken_expiry SECOND))");
            $this->db->bind("session_user_id", $data["user_id"]);
            $this->db->bind("accesstoken", $data["accesstoken"]);
            $this->db->bind("accesstoken_expiry", $data["accesstoken_expiry"]);
            $this->db->bind("refreshtoken", $data["refreshtoken"]);
            $this->db->bind("refreshtoken_expiry", $data["refreshtoken_expiry"]);
            $this->db->execute("There was an issue loggin in");
            $lastInsertId = $this->db->lastInsertId();
            $this->db->commit();
            return $lastInsertId;
        }

        public function checkAccessToken($accesstoken, $session_id){
            $this->db->query("DELETE FROM sessions 
                              WHERE session_id = :session_id 
                              AND accesstoken = :accesstoken");
            $this->db->bind(":session_id", $session_id);
            $this->db->bind(":accesstoken", $accesstoken);
            $this->db->execute("There was an issue trying to log out a user using access token");

            return $this->db->rowCount();
        }

        public function getUserSession($session_id, $accesstoken, $refreshtoken){
            $this->db->query("SELECT s.session_id, s.accesstoken, s.accesstoken_expiry, s.refreshtoken, 
                              s.refreshtoken_expiry, u.user_id, u.username, u.isactive, u.loginattempts 
                              FROM sessions s 
                              INNER JOIN users u on s.session_user_id = u.user_id 
                              WHERE s.session_id = :session_id
                              AND s.accesstoken = :accesstoken 
                              AND s.refreshtoken = :refreshtoken");
            $this->db->bind(":session_id", $session_id);
            $this->db->bind(":accesstoken", $accesstoken);
            $this->db->bind(":refreshtoken", $refreshtoken);
            return $this->db->single("There was an issue fetching session to update");
        }

        public function updateUserSession($data){
            $this->db->query("UPDATE sessions SET accesstoken = :newAccesstoken, 
                              refreshtoken = :newRefreshtoken, 
                              accesstoken_expiry = date_add(NOW(), INTERVAL :accesstoken_expiry SECOND), 
                              refreshtoken_expiry = date_add(NOW(), INTERVAL :refreshtoken_expiry SECOND)
                              WHERE session_id = :session_id AND session_user_id = :user_id 
                              AND accesstoken = :oldAccesstoken AND refreshtoken = :oldRefreshtoken");
            $this->db->bind(":user_id", $data["user_id"]);
            $this->db->bind(":session_id", $data["session_id"]);
            $this->db->bind(":refreshtoken_expiry", $data["refreshtoken_expiry"]);
            $this->db->bind(":accesstoken_expiry", $data["accesstoken_expiry"]);
            $this->db->bind(":newRefreshtoken", $data["newRefreshtoken"]);
            $this->db->bind(":newAccesstoken", $data["newAccesstoken"]);
            $this->db->bind(":oldAccesstoken", $data["oldAccesstoken"]);
            $this->db->bind(":oldRefreshtoken", $data["oldRefreshtoken"]);
            return trueOrFalse($this->db->execute("There was an issue trying to update session"));
        }

        public function getAllSessions(){
            $this->db->query("SELECT s.session_id, u.username, u.isactive FROM sessions s 
                              INNER JOIN users u ON s.session_user_id = u.user_id");
            return $this->db->resultSet("There was an issue fetching sessions");
        }

        public function checkUsernameSessions($username){
            $this->db->query("SELECT s.session_id, s.session_user_id, u.username FROM sessions s 
                              INNER JOIN users u on s.session_user_id = u.user_id
                              WHERE username = :username");
            $this->db->bind(":username", $username);
            return $this->db->resultSet("There was an issue trying to check user");
        }

        public function checkUserIdSessions($id){
            $this->db->query("SELECT s.session_id, s.session_user_id, u.username FROM sessions s 
                              INNER JOIN users u on s.session_user_id = u.user_id
                              WHERE session_user_id = :id");
            $this->db->bind(":id", $id);
            return $this->db->resultSet("There was an issue trying to check user");
        }

        public function countAllSessions(){
            $this->db->query("SELECT count(*) FROM sessions");
            return $this->db->fetchColumn("There was an issue counting sessions");
        }

        public function getSessionsPagination($limitPerPage, $offset){
            $this->db->query("SELECT s.session_id, s.session_user_id, u.username FROM sessions s 
                              INNER JOIN users u on s.session_user_id = u.user_id
                              ORDER BY session_id ASC
                              LIMIT :limit OFFSET :offset");
            $this->db->bind(":limit", $limitPerPage);
            $this->db->bind(":offset", $offset);
            return $this->db->resultSet("There was an issue trying to fetch sessions");
        }

        public function checkSessionToken($accesstoken){
            $this->db->query("SELECT s.session_user_id, u.role, u.username, s.accesstoken_expiry, 
                              u.isactive, u.loginattempts FROM sessions s
                              INNER JOIN users u ON s.session_user_id = u.user_id 
                              WHERE s.accesstoken = :accesstoken");
            $this->db->bind(":accesstoken", $accesstoken);
            return $this->db->single("There was an error trying to check the session token");
        }
    }