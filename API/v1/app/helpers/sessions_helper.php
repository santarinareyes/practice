<?php 
    /*
     * Return true if access token is provided
     */
    function isLoggedIn($http_authorization){
        if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($http_authorization) < 1){
            $error_array = [];
            !isset($_SERVER['HTTP_AUTHORIZATION']) ? array_push($error_array, "Access token is missing from the header") : false;
            strlen($http_authorization) < 1 ? array_push($error_array, "Access token cannot be blank") : false;
            status401($error_array);
        } else {
            return true;
        }
    }