<?php 
    /*
     * This is the base controller which all controllers will extend to.
     * Basically creates a require request when the model function is called from a controller.
     */
    class BaseController {
        public function model($model){
            require_once APPROOT . "/models/" . $model . ".php";
            return new $model;
        }
    }