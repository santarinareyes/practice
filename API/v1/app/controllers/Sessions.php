<?php 
    class Sessions extends BaseController {
        private $singular = "Session";
        private $plural = "sessions";
        private $_userId = "";
        private $_userRole = "";
        private $_username = "";

        public function __construct()
        {
            $this->sessionModel = $this->model($this->singular);
            $this->userModel = $this->model('User');
        }

        public function index($id = ""){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                sleep(1);
                if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    status400("Content type header is not set to JSON");
                }

                $inputData = file_get_contents('php://input');

                if(!$jsonData = json_decode($inputData)){
                    status400("Request body is not valid JSON");
                }

                if(!isset($jsonData->username) || !isset($jsonData->password)){

                    $error_message = [];
                    !isset($jsonData->username) ? array_push($error_message, "Username cannot be empty") : false;
                    !isset($jsonData->password) ? array_push($error_message, "Password cannot be empty") : false;
                    
                    status400($error_message);
                }

                if(strlen($jsonData->username) < 1 || strlen($jsonData->username) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 255){

                    $error_message = [];
                    strlen($jsonData->username) < 1 ? array_push($error_message, "Username cannot be blank") : false;
                    strlen($jsonData->username) > 255 ? array_push($error_message, "Username cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                    strlen($jsonData->password) < 1 ? array_push($error_message, "Password cannot be blank") : false;
                    strlen($jsonData->password) > 255 ? array_push($error_message, "Password cannot be greater than 255 characters (Your input: ".strlen($jsonData->password)." characters)") : false;

                    status400($error_message);
                }

                $checkUserExist = $this->userModel->checkUserExist(trim($jsonData->username), "");
                empty($checkUserExist) ? status401("Username or password is incorrect") : false;
                
                $inputPassword = $jsonData->password;

                $user_id = $checkUserExist->user_id;
                $username = $checkUserExist->username;
                $email = $checkUserExist->email;
                $password = $checkUserExist->password;
                $isactive = $checkUserExist->isactive;
                $loginattempts = $checkUserExist->loginattempts;

                $isactive !== "Y" ? status401("User account is not active") : false;
                $loginattempts >= 3 ? status401("User account is locked") : false;

                if(!password_verify($inputPassword, $password)){
                    $this->sessionModel->updateLoginAttemptsOnFail($user_id);
                    status401("Username or password is incorrect");
                }

                /*
                 * Convert binary bytes to hexadecimal then converts it 
                 * to readable and valid characters
                 */
                $accesstoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time());
                $refreshtoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time());
                
                $accesstoken_expiry = 1800; // expire after 30 minutes
                $refreshtoken_expiry = 2592000; // expire after 30 days
                
                $data = [
                    "user_id" => $user_id,
                    "accesstoken" => $accesstoken,
                    "refreshtoken" => $refreshtoken,
                    "accesstoken_expiry" => $accesstoken_expiry,
                    "refreshtoken_expiry" => $refreshtoken_expiry,
                ];

                $success = $this->sessionModel->resetLoginAttemptsOnSuccess($data);
                $rows = count(array($success));
                
                if($success){
                    $array = [
                        "data" => $this->plural,
                        "session_id" => $success,
                        "username" => $username,
                        "email" => $email,
                        "accesstoken" => $accesstoken,
                        "accesstoken_expiry" => ($accesstoken_expiry/60)." min",
                        "refreshtoken" => $refreshtoken,
                        "refreshtoken_expiry" => ($refreshtoken_expiry/86400)." days"
                    ];

                    $returnData = returnData($rows, $array);
                    status201($returnData);
                }
            } elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
                if(isLoggedIn(isset($_SERVER['HTTP_AUTHORIZATION']))){
                    $this->sessionModel = $this->model('Session');
                    $checkSessionToken = $this->sessionModel->checkSessionToken($_SERVER['HTTP_AUTHORIZATION']);
                    empty($checkSessionToken) ? status401("Invalid Access Token") : false ;
    
                    
                    if(count(array($checkSessionToken)) > 0){
                        
                        $array = [];
                        $checkSessionToken->isactive != "Y" ? array_push($array, "User account is not active") : false ;
                        $checkSessionToken->loginattempts >= 3 ? array_push($array, "User account is locked") : false ;
                        strtotime($checkSessionToken->accesstoken_expiry) < time() ? array_push($array, "Access token has expired") : false;
                        
                        if(!empty($array)){
                            status401($array);
                        } else {
                            $this->_userId = $checkSessionToken->session_user_id;
                            $this->_userRole = $checkSessionToken->role;
                            $this->_username = $checkSessionToken->username;
                        }
                    }
                }

                $this->_userRole !== 'Admin' ? status405("Request method not allowed") : false;
                
                if($id == ""){
                    $sessions = $this->sessionModel->getAllSessions();
                    $rows = count($sessions);
                    $array = [];
                    
                    foreach($sessions as $session){
                        $new = [];
                        $new["session_id"] = $session->session_id;
                        $new["username"] = $session->username;
                        $new["isactive"] = $session->isactive;
                        array_push($array, $new);
                    }

                    $array['data'] = $this->plural;
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);

                }

                if(!is_numeric($id)){
                    $sessions = $this->sessionModel->checkUsernameSessions($id);
                    empty($sessions) ? status400("Username does not exist. Please try again.") : false;
                    
                    if($sessions !== 0){
                        $rows = count($sessions);
                        $array = [];
                    
                        foreach($sessions as $session){
                            $new = [];
                            $new["session_id"] = $session->session_id;
                            $new["user_id"] = $session->session_user_id;
                            $new["username"] = $session->username;
                            array_push($array, $new);
                        }

                        $array['data'] = $this->plural;
                        $returnData = returnData($rows, $array);
                        status200($returnData, false, true);

                    } else {
                        status404("This user does not have an existing session");
                    }
                }

                if(is_numeric($id)){
                    $sessions = $this->sessionModel->checkUserIdSessions($id);
                    empty($sessions) ? status400("User Id does not exist. Please try again.") : false;
                    
                    if($sessions !== 0){
                        $rows = count($sessions);
                        $array = [];
                    
                        foreach($sessions as $session){
                            $new = [];
                            $new["session_id"] = $session->session_id;
                            $new["user_id"] = $session->session_user_id;
                            $new["username"] = $session->username;
                            array_push($array, $new);
                        }

                        $array['data'] = $this->plural;
                        $returnData = returnData($rows, $array);
                        status200($returnData, false, true);

                    } else {
                        status404("This user Id does not have an existing session");
                    }
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'PATCH'){
                if($id === "" || !is_numeric($id)){
                    $error_array = [];
                    $id === "" ? array_push($error_array, "$this->singular Id cannot be empty") : false;
                    !is_numeric($id) ? array_push($error_array, "$this->singular Id must me numeric") : false;
                    status400($error_array);
                }

                if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1){
                    $error_array = [];
                    !isset($_SERVER['HTTP_AUTHORIZATION']) ? array_push($error_array, "$this->singular Access token is missing from the header") : false;
                    isset($_SERVER['HTTP_AUTHORIZATION']) && strlen($_SERVER['HTTP_AUTHORIZATION']) < 1 ? array_push($error_array, "$this->singular Access token cannot be blank") : false;
                    status401($error_array);
                }
                
                if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    status400("Content type header is not set to JSON");
                }

                $inputData = file_get_contents('php://input');

                if(!$jsonData = json_decode($inputData)){
                    status400("Request body is not valid JSON");
                }

                if(!isset($jsonData->refreshtoken) || strlen($jsonData->refreshtoken) < 1){

                    $error_message = [];
                    !isset($jsonData->refreshtoken) ? array_push($error_message, "Refresh token cannot be empty") : false;
                    isset($jsonData->refreshtoken) && empty($jsonData->refreshtoken) ? array_push($error_message, "Refresh token cannot be blank") : false;
                    
                    status400($error_message);
                }

                $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];
                $refreshtoken = $jsonData->refreshtoken;

                $getUserSession = $this->sessionModel->getUserSession($id, $accesstoken, $refreshtoken);
                empty($getUserSession) ? status401("Access token or refresh token is incorrect for session id") : false;

                $session_id = $getUserSession->session_id;
                $user_id = $getUserSession->user_id;
                $username = $getUserSession->username;
                $isactive = $getUserSession->isactive;
                $loginattempts = $getUserSession->loginattempts;
                $accesstoken_expiry = $getUserSession->accesstoken_expiry;
                $refreshtoken_expiry = $getUserSession->refreshtoken_expiry;

                $isactive !== "Y" ? status401("User account is not active") : false;
                $loginattempts >= 3 ? status401("User account is locked") : false;
                strtotime($refreshtoken_expiry) < time() ? status401("Refresh token has expired, please log in again") : false;

                /*
                 * Convert binary bytes to hexadecimal then converts it 
                 * to readable and valid characters
                 */
                $newAccesstoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time());
                $newRefreshtoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time());
                
                $newAccesstoken_expiry = 1800; // expire after 30 minutes
                $newRefreshtoken_expiry = 2592000; // expire after 30 days
                
                $data = [
                    "session_id" => $session_id,
                    "user_id" => $user_id,
                    "username" => $username,
                    "oldAccesstoken" => $accesstoken,
                    "oldRefreshtoken" => $refreshtoken,
                    "newAccesstoken" => $newAccesstoken,
                    "newRefreshtoken" => $newRefreshtoken,
                    "accesstoken_expiry" => $newAccesstoken_expiry,
                    "refreshtoken_expiry" => $newRefreshtoken_expiry,
                ];

                $success = $this->sessionModel->updateUserSession($data);
                $rows = $success === true ? 1 : status401("Access token could not be refreshed, please log in again");
                
                if($success){
                    $array = [
                        "data" => $this->plural,
                        "message" => "Token refreshed",
                        "session_id" => $session_id,
                        "username" => $username,
                        "accesstoken" => $newAccesstoken,
                        "accesstoken_expiry" => ($newAccesstoken_expiry/60)." min",
                        "refreshtoken" => $newRefreshtoken,
                        "refreshtoken_expiry" => ($newRefreshtoken_expiry/86400)." days"
                    ];

                    $returnData = returnData($rows, $array);
                    status201($returnData, $array["data"]);
                }
                
            } elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){
                if($id === "" || !is_numeric($id)){
                    $error_array = [];
                    $id === "" ? array_push($error_array, "$this->singular Id cannot be empty") : false;
                    !is_numeric($id) ? array_push($error_array, "$this->singular Id must me numeric") : false;
                    status400($error_array);
                }

                if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1){
                    $error_array = [];
                    !isset($_SERVER['HTTP_AUTHORIZATION']) ? array_push($error_array, "$this->singular Access token is missing from the header") : false;
                    isset($_SERVER['HTTP_AUTHORIZATION']) && strlen($_SERVER['HTTP_AUTHORIZATION']) < 1 ? array_push($error_array, "$this->singular Access token cannot be blank") : false;
                    status401($error_array);
                }

                $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

                $success = $this->sessionModel->checkAccessToken($accesstoken, $id);
                $success === 0 ? status400("Failed to log out of this session using access token") : false;
                $rows = count(array($success));

                if($success !== 0){
                    $array = [
                        "data" => $this->plural,
                        "message" => "Successfully logged out",
                        "session_id" => $id,
                    ];

                    $returnData = returnData($rows, $array);
                    status200($returnData, $array["data"]);
                }
            } else {
                status405("Request method not allowed");
            }
        }

        public function page($currentPage = ""){
            if($_SERVER['REQUEST_METHOD'] === 'GET'){
                if(isLoggedIn(isset($_SERVER['HTTP_AUTHORIZATION']))){
                    $this->sessionModel = $this->model('Session');
                    $checkSessionToken = $this->sessionModel->checkSessionToken($_SERVER['HTTP_AUTHORIZATION']);
                    empty($checkSessionToken) ? status401("Invalid Access Token") : false ;
    
                    
                    if(count(array($checkSessionToken)) > 0){
                        
                        $array = [];
                        $checkSessionToken->isactive != "Y" ? array_push($array, "User account is not active") : false ;
                        $checkSessionToken->loginattempts >= 3 ? array_push($array, "User account is locked") : false ;
                        strtotime($checkSessionToken->accesstoken_expiry) < time() ? array_push($array, "Access token has expired") : false;
                        
                        if(!empty($array)){
                            status401($array);
                        } else {
                            $this->_userId = $checkSessionToken->session_user_id;
                            $this->_userRole = $checkSessionToken->role;
                            $this->_username = $checkSessionToken->username;
                        }
                    }
                }

                $this->_userRole !== 'Admin' ? status405("Request method not allowed") : false;

                if($currentPage == "" || !is_numeric($currentPage)){
                    status400("Page cannot be empty or must be numeric");
                }

                if($currentPage == 0){
                    $currentPage = 1;
                }

                $limitPerPage = 5;
                $numRows = intval($this->sessionModel->countAllSessions());
                $numPages = ceil($numRows/$limitPerPage);

                if($numPages == 0 || $numPages == ""){
                    $numPages = 1;
                }

                if($currentPage > $numPages){
                    status404("Page not found");
                }

                $offset = ($currentPage == 1 ? 0 : ($limitPerPage*($currentPage-1)));
                $rows = $this->sessionModel->getSessionsPagination($limitPerPage, $offset);
                $pageRows = count($rows);

                $array = [];
                $array['data'] = $this->plural;
                    
                foreach($rows as $row){
                    $new = [];
                    $new["session_id"] = $row->session_id;
                    $new["user_id"] = $row->session_user_id;
                    $new["username"] = $row->username;
                    array_push($array, $new);
                }

                $hasNextPage = $currentPage < $numPages;
                $hasPrevPage = $currentPage == 1 ? true : $currentPage > $numPages;

                $returnData = returnPageData($numRows, $pageRows, $numPages, $hasNextPage, $hasPrevPage, $array);
                status200($returnData, true);

            } else {
                status405("Request method not allowed");
            }
        }
    }