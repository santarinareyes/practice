<?php 
    require_once "config/config.php";
    require_once "helpers/response_helper.php";
    require_once "helpers/returnData_helper.php";
    require_once "helpers/sanitize_helper.php";
    require_once "helpers/trueOrFalse_helper.php";
    require_once "helpers/sessions_helper.php";

    spl_autoload_register(function($className){
        require_once "libs/$className.php";
    });