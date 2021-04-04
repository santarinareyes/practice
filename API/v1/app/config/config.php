<?php 
    /*
     * Settings for MasterDB
     * If you only have 1 database,
     * then just put identical values
     */
    define("DB_HOST_M", "localhost:8888");
    define("DB_USER_M", "username");
    define("DB_PASS_M", "password");
    define("DB_NAME_M", "ecom");

    /*
     * Settings for ReadDB
     * If you only have 1 database,
     * then just put identical values
     */
    define("DB_HOST_W", "localhost:8888");
    define("DB_USER_W", "username");
    define("DB_PASS_W", "password");
    define("DB_NAME_W", "ecom");

    define("APPROOT", dirname(dirname(__FILE__)));
    define("URLROOT", "http://localhost/Ecom_REST-API/v1");