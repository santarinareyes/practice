<?php
    /*
     * These functions creates a new Response class from Response.php in libs
     * and build up the messages displayed
     */
    function PDOException($e, $message) {
        error_log("PDO Error: ".$e, 0);
        $response = new Response;
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage([$message]);
        $response->send();
        exit;
    }

    function status200($returnData, $table = "", $cache = false){
        $response = new Response;
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);

        if($cache){
            $response->setCache(true);
        }
        
        if(isset($returnData[$table]["message"])){
            $response->addMessage($returnData[$table]["message"]);
            unset($returnData[$table]["message"]);
        }
        
        if(is_array($returnData)){
            $response->setData($returnData);
        } else {
            $response->addMessage($returnData);
        }

        $response->send();
        exit;
    }

    function status201($returnData, $table = ""){
        $response = new Response;
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);

        if(isset($returnData[$table]["message"])){
            $response->addMessage($returnData[$table]["message"]);
            unset($returnData[$table]["message"]);
        }
        
        if(is_array($returnData)){
            $response->setData($returnData);
        } else {
            $response->addMessage($returnData);
        }

        $response->setData($returnData);
        $response->send();
        exit;
    }

    function status307($returnData, $table = ""){
        $response = new Response;
        $response->setHttpStatusCode(307);
        $response->setSuccess(true);

        if(isset($returnData[$table]["message"])){
            $response->addMessage($returnData[$table]["message"]);
            unset($returnData[$table]["message"]);
        }
        
        if(is_array($returnData)){
            $response->setData($returnData);
        } else {
            $response->addMessage($returnData);
        }

        $response->setData($returnData);
        $response->send();
        exit;
    }

    function status400($e){
        $response = new Response;
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);

        if(is_array($e)){
            $response->addMessage($e);
        }elseif(is_string($e)){
            $response->addMessage([$e]);
        } else {
            $response->addMessage($e->getMessage());
        }

        $response->send();
        exit;
    }

    function status401($e){
        $response = new Response;
        $response->setHttpStatusCode(401);
        $response->setSuccess(false);

        if(is_array($e)){
            $response->addMessage($e);
        }elseif(is_string($e)){
            $response->addMessage([$e]);
        } else {
            $response->addMessage($e->getMessage());
        }

        $response->send();
        exit;
    }
    
    function status404($message){
        $response = new Response;
        $response->setHttpStatusCode(404);
        $response->setSuccess(false);
        $response->addMessage([$message]);
        $response->send();
        exit;
    }

    function status405($message){
        $response = new Response;
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage([$message]);
        $response->send();
        exit;
    }

    function status409($e){
        $response = new Response;
        $response->setHttpStatusCode(409);
        $response->setSuccess(false);

        if(is_array($e)){
            $response->addMessage($e);
        }elseif(is_string($e)){
            $response->addMessage([$e]);
        } else {
            $response->addMessage($e->getMessage());
        }

        $response->send();
        exit;
    }

    function status500($e){
        $response = new Response;
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);

        if(is_array($e)){
            $response->addMessage($e);
        }elseif(is_string($e)){
            $response->addMessage([$e]);
        } else {
            $response->addMessage($e->getMessage());
        }
        
        $response->send();
        exit;
    }