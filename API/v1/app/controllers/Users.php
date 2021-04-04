<?php 
    class Users extends BaseController {
        private $singular = "User";
        private $plural = "users";

        public function __construct()
        {
            $this->userModel = $this->model($this->singular);
        }

        public function index($id = ""){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    status400("Content type header is not set to JSON");
                }

                $inputData = file_get_contents('php://input');

                if(!$jsonData = json_decode($inputData)){
                    status400("Request body is not valid JSON");
                }

                if(!isset($jsonData->firstname) || !isset($jsonData->lastname) || !isset($jsonData->username) || !isset($jsonData->email) || !isset($jsonData->password)){

                    $error_message = [];
                    !isset($jsonData->firstname) ? array_push($error_message, "Firstname cannot be empty") : false;
                    !isset($jsonData->lastname) ? array_push($error_message, "Lastname cannot be empty") : false;
                    !isset($jsonData->username) ? array_push($error_message, "Username cannot be empty") : false;
                    !isset($jsonData->email) ? array_push($error_message, "Email cannot be empty") : false;
                    !isset($jsonData->password) ? array_push($error_message, "Password cannot be empty") : false;
                    
                    status400($error_message);
                }

                if((isset($jsonData->role) && sanitizeString($jsonData->role) != 'Admin') || strlen($jsonData->firstname) < 1 || strlen($jsonData->firstname) > 255 || strlen($jsonData->lastname) < 1 || strlen($jsonData->lastname) > 255 || strlen($jsonData->username) < 1 || strlen($jsonData->username) > 255 || !filter_var($jsonData->email, FILTER_VALIDATE_EMAIL) || strlen($jsonData->email) < 1 || strlen($jsonData->email) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 255){

                    $error_message = [];
                    $jsonData->role != 'admin' ? array_push($error_message, "Set role to 'admin' or remove the role row to automatically set role to user") : false;
                    strlen($jsonData->firstname) < 1 ? array_push($error_message, "Firstname cannot be blank") : false;
                    strlen($jsonData->firstname) > 255 ? array_push($error_message, "Firstname cannot be greater than 255 characters (Your input: ".strlen($jsonData->firstname)." characters)") : false;
                    strlen($jsonData->lastname) < 1 ? array_push($error_message, "Lastname cannot be blank") : false;
                    strlen($jsonData->lastname) > 255 ? array_push($error_message, "Lastname cannot be greater than 255 characters (Your input: ".strlen($jsonData->lastname)." characters)") : false;
                    str_contains($jsonData->username, " ") ? array_push($error_message, "Username cannot have spaces") : false;
                    strlen($jsonData->username) < 1 ? array_push($error_message, "Username cannot be blank") : false;
                    strlen($jsonData->username) > 255 ? array_push($error_message, "Username cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                    !filter_var($jsonData->email, FILTER_VALIDATE_EMAIL) ? array_push($error_message, "Email is invalid") : false;
                    str_contains($jsonData->email, " ") ? array_push($error_message, "Email cannot have spaces") : false;
                    strlen($jsonData->email) < 1 ? array_push($error_message, "Email cannot be blank") : false;
                    strlen($jsonData->email) > 255 ? array_push($error_message, "Email cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                    strlen($jsonData->password) < 1 ? array_push($error_message, "Password cannot be blank") : false;
                    strlen($jsonData->password) > 255 ? array_push($error_message, "Password cannot be greater than 255 characters (Your input: ".strlen($jsonData->password)." characters)") : false;

                    status400($error_message);
                }

                $firstname = sanitizeString($jsonData->firstname);
                $lastname = sanitizeString($jsonData->lastname);
                $username = sanitizeUsername($jsonData->username);
                $email = sanitizeEmail($jsonData->email);
                $password = $jsonData->password;

                $checkUserExist = $this->userModel->checkUserExist($username, $email);

                if(!empty($checkUserExist)){
                    $existArray = [];

                    if(isset($checkUserExist->email) && $checkUserExist->email == $email){
                        array_push($existArray, "Email already exist");
                    }

                    if(isset($checkUserExist->username) && $checkUserExist->username == $username){
                        array_push($existArray, "Username already exist");
                    }

                    status409($existArray);
                }

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $data = [
                    "firstname" => ucwords(strtolower($firstname)),
                    "lastname" => ucwords(strtolower($lastname)),
                    "username" => $username,
                    "email" => $email,
                    "password" => $hashed_password,
                    "role" => isset($jsonData->role) ? sanitizeString($jsonData->role) : "User"
                ];

                $newUser = $this->userModel->createUser($data);

                if($newUser){
                    $latestInfo = $this->userModel->getLastCreatedUser();
                    $rows = count(array($latestInfo));

                    try{
                        $user = new UserValidator($latestInfo->user_id, $latestInfo->firstname, $latestInfo->lastname, $latestInfo->username, $latestInfo->email, $latestInfo->role);
                        $array[] = $user->returnAsArray();
                        $array['data'] = $this->plural;
                        $array['message'] = "$this->singular created";
                        
                        $returnData = returnData($rows, $array);
                        status201($returnData, $array['data']);
                        
                    } catch(UserException $e){
                        status500($e);
                    } 

                } else {
                    status500("There was an issue creating a new $this->singular. Please try again.");
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
                    $users = $this->userModel->getAllUsers();
                    $rows = count($users);
                    
                    foreach($users as $user){
                        $user = new UserValidator($user->user_id, $user->firstname, $user->lastname, $user->username, $user->email, $user->role);
                        $array[] = $user->returnAsArray();
                    }

                    $array['data'] = $this->plural;
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if($id == "admin"){
                    $users = $this->userModel->getRoleAdmin($id);
                    $rows = count($users);
                    
                    foreach($users as $user){
                        $user = new UserValidator($user->user_id, $user->firstname, $user->lastname, $user->username, $user->email, $user->role);
                        $array[] = $user->returnAsArray();
                    }

                    $array['data'] = $this->plural;
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if($id == "user"){
                    $users = $this->userModel->getRoleUser($id);
                    $rows = count($users);
                    
                    foreach($users as $user){
                        $user = new UserValidator($user->user_id, $user->firstname, $user->lastname, $user->username, $user->email, $user->role);
                        $array[] = $user->returnAsArray();
                    }

                    $array['data'] = $this->plural;
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if(!is_numeric($id)){
                    status400("$this->singular Id must be numeric");
                }

                $singleUser = $this->userModel->getSingleUser($id);
                $rows = count(array($singleUser));

                if(!empty($singleUser)){
                    try{
                        $user = new UserValidator($singleUser->user_id, $singleUser->firstname, $singleUser->lastname, $singleUser->username, $singleUser->email, $singleUser->role);
                        $array[] = $user->returnAsArray();
                        $array['data'] = $this->plural;
                        
                        $returnData = returnData($rows, $array);
                        status200($returnData);
                        
                    } catch(UserException $e){
                        status500($e);
                    } 
                } else {
                    status404("$this->singular not found");
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'PATCH'){
                if($id === "" || !is_numeric($id)){
                    status400("$this->singular Id cannot be empty and must be numeric");
                }

                if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    status400("Content type header is not set to JSON");
                }

                $updateData = file_get_contents('php://input');

                if(!$jsonData = json_decode($updateData)){
                    status400("Request body is not valid JSON");
                }
                
                $firstname = false;
                $lastname = false;
                $username = false;
                $email = false;
                $password = false;
                $role = false;

                $query = "";

                if(isset($jsonData->firstname)){
                    $firstname = true;
                    $query .= "firstname = :firstname, ";
                }

                if(isset($jsonData->lastname)){
                    $lastname = true;
                    $query .= "lastname = :lastname, ";
                }

                if(isset($jsonData->username)){
                    $username = true;
                    $query .= "username = :username, ";
                }

                if(isset($jsonData->email)){
                    $email = true;
                    $query .= "email = :email, ";
                }

                if(isset($jsonData->password)){
                    $password = true;
                    $query .= "password = :password, ";
                }

                if(isset($jsonData->role)){
                    $role = true;
                    $query .= "role = :role, ";
                }

                $query = rtrim($query, ", ");

                if($firstname === false && $lastname === false && $username === false && $email === false && $password === false && $role === false){
                    status400("All fields cannot be empty");
                }
                
                $row = $this->userModel->getSingleUser($id);
                
                if(!empty($row)):
                    try{
                        $user = new UserValidator($row->user_id, $row->firstname, $row->lastname, $row->username, $row->email, $row->role);

                        $newFirstname = "";
                        $newLastname = "";
                        $newUsername = "";
                        $newEmail = "";
                        $newPassword = "";
                        $newRole = "";
                        $error_message = [];

                        if($firstname === true){
                            strlen($jsonData->firstname) < 1 ? array_push($error_message, "Firstname cannot be blank") : false;
                            strlen($jsonData->firstname) > 255 ? array_push($error_message, "Firstname cannot be greater than 255 characters (Your input: ".strlen($jsonData->firstname)." characters)") : false;
                            $user->setFirstname($jsonData->firstname);
                            $newFirstname = sanitizeString($user->getFirstname());
                        }

                        if($lastname === true){
                            strlen($jsonData->lastname) < 1 ? array_push($error_message, "Lastname cannot be blank") : false;
                            strlen($jsonData->lastname) > 255 ? array_push($error_message, "Lastname cannot be greater than 255 characters (Your input: ".strlen($jsonData->lastname)." characters)") : false;
                            $user->setLastname($jsonData->lastname);
                            $newLastname = sanitizeString($user->getLastname());
                        }

                        if($username === true){
                            str_contains($jsonData->username, " ") ? array_push($error_message, "Username cannot have spaces") : false;
                            strlen($jsonData->username) < 1 ? array_push($error_message, "Username cannot be blank") : false;
                            strlen($jsonData->username) > 255 ? array_push($error_message, "Username cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                            $user->setUsername($jsonData->username);
                            $newUsername = sanitizeUsername($user->getUsername());
                        }

                        if($email === true){
                            !filter_var($jsonData->email, FILTER_VALIDATE_EMAIL) ? array_push($error_message, "Email is invalid") : false;
                            str_contains($jsonData->email, " ") ? array_push($error_message, "Email cannot have spaces") : false;
                            strlen($jsonData->email) < 1 ? array_push($error_message, "Email cannot be blank") : false;
                            strlen($jsonData->email) > 255 ? array_push($error_message, "Email cannot be greater than 255 characters (Your input: ".strlen($jsonData->username)." characters)") : false;
                            $user->setEmail($jsonData->email);
                            $newEmail = sanitizeEmail($user->getEmail()); 
                        }

                        if($username === true || $email === true) {
                            $checkUserExist = $this->userModel->checkUserExist($newUsername, $newEmail);

                            if(!empty($checkUserExist)){
                                $existArray = [];
            
                                if(isset($checkUserExist->email) && $checkUserExist->email == $newEmail){
                                    array_push($existArray, "Email already exist");
                                }
            
                                if(isset($checkUserExist->username) && $checkUserExist->username == $newUsername){
                                    array_push($existArray, "Username already exist");
                                }
            
                                status409($existArray);
                            }
                        }

                        if($password === true){
                            strlen($jsonData->password) < 1 ? array_push($error_message, "Password cannot be blank") : false;
                            strlen($jsonData->password) > 255 ? array_push($error_message, "Password cannot be greater than 255 characters (Your input: ".strlen($jsonData->password)." characters)") : false;
                            $newPassword = password_hash($jsonData->password, PASSWORD_DEFAULT);
                        }

                        if($role === true){
                            $jsonData->role != 'admin' ? array_push($error_message, "Set role to 'admin' or remove the role row to automatically set role to user") : false;
                            $user->setRole($jsonData->role);
                            $newRole = sanitizeString($user->getRole()); 
                        }

                        if(!empty($error_message)){
                            status400($error_message);
                        }

                        $this->userModel->updateUser($query, $id, $newFirstname, $newLastname, $newUsername, $newEmail, $newPassword, $newRole);

                        $updatedUser = $this->userModel->getUpdatedUser($id);
                        $rows = count(array($updatedUser));

                        if(!empty($updatedUser)){
                            try{
                                $user = new UserValidator($updatedUser->user_id, $updatedUser->firstname, $updatedUser->lastname, $updatedUser->username, $updatedUser->email, $updatedUser->role);
                                $array[] = $user->returnAsArray();
                                $array['data'] = $this->plural;
                                
                                $returnData = returnData($rows, $array);
                                status200($returnData);
                                
                            } catch(UserException $e){
                                status500($e);
                            } 
                        } else {
                            status404("$this->singular not found");
                        }
                        
                    } catch(UserException $e){
                        status400($e);
                    } 
                else:
                    status404("$this->singular not found");
                endif;
                
            } elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){
                if($id === ""){
                    status404("No $this->singular found to delete");
                }

                if(!is_numeric($id)){
                    status400("$this->singular Id must be numeric");
                }

                $rowToDelete = $this->userModel->getSingleUser($id);
                empty($rowToDelete) ? status404("$this->singular not found") : false;
                $user = $this->userModel->deleteUser($id);
                $rows = $user === true ? 1 : status500("Failed to delete $this->singular");

                if($user):
                    try{
                        $user = new UserValidator($rowToDelete->user_id, $rowToDelete->firstname, $rowToDelete->lastname, $rowToDelete->username, $rowToDelete->email, $rowToDelete->role);
                        $array[] = $user->returnAsArray();
                        $array['data'] = $this->plural;
                        $array['message'] = "$this->singular deleted";
                        
                        $returnData = returnData($rows, $array);
                        status200($returnData, $array['data']);
                        
                    } catch(UserException $e){
                        status500($e);
                    } 
                else:
                    status404("$this->singular not found");
                endif;

            } else {
                status405("Request method not allowed");
            }
        }

        public function page($currentPage = ""){
            if($_SERVER['REQUEST_METHOD'] === 'GET'){
                if($currentPage == "" || !is_numeric($currentPage)){
                    status400("Page cannot be empty or must be numeric");
                }

                if($currentPage == 0){
                    $currentPage = 1;
                }

                $limitPerPage = 20;
                $numRows = intval($this->userModel->countAllUsers());
                $numPages = ceil($numRows/$limitPerPage);

                if($numPages == 0 || $numPages == ""){
                    $numPages = 1;
                }

                if($currentPage > $numPages){
                    status404("Page not found");
                }

                $offset = ($currentPage == 1 ? 0 : ($limitPerPage*($currentPage-1)));
                $rows = $this->userModel->getUsersPagination($limitPerPage, $offset);
                $pageRows = count($rows);

                foreach($rows as $row){
                    $row = new UserValidator($row->user_id, $row->firstname, $row->lastname, $row->username, $row->email, $row->role);
                    $array[] = $row->returnAsArray();
                    $array['data'] = $this->plural;
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