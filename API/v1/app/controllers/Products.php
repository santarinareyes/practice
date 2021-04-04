<?php 
    class Products extends BaseController {
        private $singular = "Product";
        private $plural = "products";
        private $_userId = "";
        private $_userRole = "";
        private $_username = "";

        public function __construct()
        {   
            $this->productModel = $this->model($this->singular);
            $this->categoryModel = $this->model('Category');
        }

        public function index($id = ""){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
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

                if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    status400("Content type header is not set to JSON");
                }

                $inputData = file_get_contents('php://input');

                if(!$jsonData = json_decode($inputData)){
                    status400("Request body is not valid JSON");
                }

                if(!isset($jsonData->title) || !isset($jsonData->category) || !isset($jsonData->price) || !isset($jsonData->description)){

                    $error_message = [];
                    !isset($jsonData->title) ? array_push($error_message, "Title cannot be empty") : false;
                    !isset($jsonData->category) ? array_push($error_message, "Category cannot be empty") : false;
                    !isset($jsonData->price) ? array_push($error_message, "Price cannot be empty") : false;
                    !isset($jsonData->description) ? array_push($error_message, "Description cannot be empty") : false;
                    
                    status400($error_message);
                }

                if(strlen($jsonData->title) < 1 || strlen($jsonData->title) > 255 || strlen($jsonData->category) < 1 || strlen($jsonData->category) > 255 || !is_numeric($jsonData->price) || strlen($jsonData->description) < 1){

                    $error_message = [];
                    strlen($jsonData->title) < 1 ? array_push($error_message, "Title cannot be blank") : false;
                    strlen($jsonData->title) > 255 ? array_push($error_message, "Title cannot be greater than 255 characters (Your input: ".strlen($jsonData->title)." characters)") : false;
                    strlen($jsonData->category) < 1 ? array_push($error_message, "Category cannot be blank") : false;
                    strlen($jsonData->category) > 255 ? array_push($error_message, "Category cannot be greater than 255 characters (Your input: ".strlen($jsonData->category)." characters)") : false;
                    str_contains($jsonData->price, " ") ? array_push($error_message, "Price cannot have spaces") : false;
                    !is_numeric($jsonData->price) ? array_push($error_message, "Price must be numeric") : false;
                    strlen($jsonData->description) < 1 ? array_push($error_message, "Description cannot be blank") : false;

                    status400($error_message);
                }

                if(isset($jsonData->category)){
                    $checkCategoryExist = $this->categoryModel->checkCategoryExist(sanitizeString($jsonData->category));
                    empty($checkCategoryExist) ? status404("Category does not exist. Please try again.") : false;
                    $categoryId = $checkCategoryExist->category_id;
                }

                $title = sanitizeString($jsonData->title);
                $category = $categoryId;
                $price = $jsonData->price;
                $description = $jsonData->description;

                $checkProductExist = $this->productModel->checkProductExist($title);
                !empty($checkProductExist) ? status409("$this->singular title already exist") : false;

                $data = [
                    "title" => ucwords(strtolower($title)),
                    "category" => $category,
                    "price" => $price,
                    "description" => $description
                ];

                $newProduct = $this->productModel->createProduct($data);

                if($newProduct){
                    $latestInfo = $this->productModel->getLastCreatedProduct();
                    $rows = count(array($latestInfo));

                    try{
                        $product = new ProductValidator($latestInfo->product_id, $latestInfo->product_title, $latestInfo->category_title, $latestInfo->product_price, $latestInfo->product_description);
                        $array[] = $product->returnAsArray();
                        $array['data'] = $this->plural;
                        $array['message'] = "$this->singular created";
                        
                        $returnData = returnData($rows, $array);
                        status201($returnData, $array['data']);
                        
                    } catch(ProductException $e){
                        status500($e);
                    } 

                } else {
                    status500("There was an issue creating a new $this->singular. Please try again.");
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
                if($id == ""){
                    $products = $this->productModel->getAllProducts();
                    $rows = count($products);
                    
                    foreach($products as $product){
                        $product = new ProductValidator($product->product_id, $product->product_title, $product->category_title, $product->product_price, $product->product_description);
                        $array[] = $product->returnAsArray();
                    }

                    $array['data'] = $this->plural;
                    $returnData = returnData($rows, $array);
                    status200($returnData, false, true);
                }

                if(!is_numeric($id)){
                    status400("$this->singular Id must be numeric");
                }

                $singleProduct = $this->productModel->getSingleProduct($id);
                $rows = count(array($singleProduct));

                if(!empty($singleProduct)){
                    try{
                        $singleProduct = new ProductValidator($singleProduct->product_id, $singleProduct->product_title, $singleProduct->category_title, $singleProduct->product_price, $singleProduct->product_description);
                        $array[] = $singleProduct->returnAsArray();
                        $array['data'] = $this->plural;
                        
                        $returnData = returnData($rows, $array);
                        status200($returnData);
                        
                    } catch(ProductException $e){
                        status500($e);
                    } 
                } else {
                    status404("$this->singular not found");
                }

            } elseif($_SERVER['REQUEST_METHOD'] === 'PATCH'){
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
                
                $title = false;
                $category = false;
                $price = false;
                $description = false;

                $query = "";

                if(isset($jsonData->title)){
                    $title = true;
                    $query .= "product_title = :title, ";
                }

                if(isset($jsonData->category)){
                    $category = true;
                    $query .= "product_category_id = :category, ";
                }

                if(isset($jsonData->price)){
                    $price = true;
                    $query .= "product_price = :price, ";
                }

                if(isset($jsonData->description)){
                    $description = true;
                    $query .= "product_description = :description, ";
                }

                $query = rtrim($query, ", ");

                if($title === false && $category === false && $price === false && $description === false){
                    status400("All fields cannot be empty");
                }
                
                $row = $this->productModel->getSingleProduct($id);
                
                if(!empty($row)):
                    try{
                        $product = new ProductValidator($row->product_id, $row->product_title, $row->category_title, $row->product_price, $row->product_description);
                        
                        $newTitle = "";
                        $newCategory = "";
                        $newPrice = "";
                        $newDescription = "";
                        $error_message = [];

                        if($title === true){
                            strlen($jsonData->title) < 1 ? array_push($error_message, "Title cannot be blank") : false;
                            strlen($jsonData->title) > 255 ? array_push($error_message, "Title cannot be greater than 255 characters (Your input: ".strlen($jsonData->title)." characters)") : false;
                            $product->setTitle($jsonData->title);
                            $checkProductExist = $this->productModel->checkProductExist($jsonData->title);
                            !empty($checkProductExist) ? status409("$this->singular title already exist") : $newTitle = sanitizeString($product->getTitle());
                        }

                        if($category === true){
                            strlen($jsonData->category) < 1 ? array_push($error_message, "Category cannot be blank") : false;
                            strlen($jsonData->category) > 255 ? array_push($error_message, "Category cannot be greater than 255 characters (Your input: ".strlen($jsonData->category)." characters)") : false;
                            $checkCategoryExist = $this->categoryModel->checkCategoryExist(sanitizeString($jsonData->category));
                            empty($checkCategoryExist) ? array_push($error_message, "Category does not exist. Please try again.") : $newCategory = $checkCategoryExist->category_id;
                            $product->setCategory($jsonData->category);
                        }

                        if($price === true){
                            str_contains($jsonData->price, " ") ? array_push($error_message, "Price cannot have spaces") : false;
                            !is_numeric($jsonData->price) ? array_push($error_message, "Price must be numeric") : false;$product->setPrice($jsonData->price);
                            $newPrice = $product->getPrice();
                        }

                        if($description === true){
                            strlen($jsonData->description) < 1 ? array_push($error_message, "Description cannot be blank") : false;
                            $product->setDescription($jsonData->description);
                            $newDescription = $product->getDescription(); 
                        }

                        if(!empty($error_message)){
                            status400($error_message);
                        }

                        $this->productModel->updateProduct($query, $id, $newTitle, $newCategory, $newPrice, $newDescription);

                        $updatedProduct = $this->productModel->getUpdatedProduct($id);
                        $rows = count(array($updatedProduct));

                        if(!empty($updatedProduct)){
                            try{
                                $product = new ProductValidator($updatedProduct->product_id, $updatedProduct->product_title, $updatedProduct->category_title, $updatedProduct->product_price, $updatedProduct->product_description);
                                $array[] = $product->returnAsArray();
                                $array['data'] = $this->plural;
                                
                                $returnData = returnData($rows, $array);
                                status200($returnData);
                                
                            } catch(ProductException $e){
                                status500($e);
                            } 
                        } else {
                            status404("$this->singular not found");
                        }
                        
                    } catch(ProductException $e){
                        status400($e);
                    } 
                else:
                    status404("$this->singular not found");
                endif;
                
            } elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){
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
                
                if($id === ""){
                    status404("$this->singular Id cannot be empty");
                }

                if(!is_numeric($id)){
                    status400("$this->singular Id must be numeric");
                }

                $rowToDelete = $this->productModel->getSingleProduct($id);
                empty($rowToDelete) ? status404("$this->singular not found") : false;
                $product = $this->productModel->deleteProduct($id);
                $rows = $product === true ? 1 : status500("Failed to delete $this->singular");

                if($product):
                    try{
                        $product = new ProductValidator($rowToDelete->product_id, $rowToDelete->product_title, $rowToDelete->category_title, $rowToDelete->product_price, $rowToDelete->product_description);
                        $array[] = $product->returnAsArray();
                        $array['data'] = $this->plural;
                        $array['message'] = "$this->singular deleted";
                        
                        $returnData = returnData($rows, $array);
                        status200($returnData, $array['data']);
                        
                    } catch(ProductException $e){
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
                $numRows = intval($this->productModel->countAllProducts());
                $numPages = ceil($numRows/$limitPerPage);

                if($numPages == 0 || $numPages == ""){
                    $numPages = 1;
                }

                if($currentPage > $numPages){
                    status404("Page not found");
                }

                $offset = ($currentPage == 1 ? 0 : ($limitPerPage*($currentPage-1)));
                $rows = $this->productModel->getProductsPagination($limitPerPage, $offset);
                $pageRows = count($rows);

                foreach($rows as $row){
                    $row = new ProductValidator($row->product_id, $row->product_title, $row->category_title, $row->product_price, $row->product_description);
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