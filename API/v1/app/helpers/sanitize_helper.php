<?php 
    function sanitizeString($string){
        $string = strip_tags($string);
        $string = preg_replace("/\s+/", " ", $string);
        $string = trim($string);
        $string = strtolower($string);
        $string = ucwords($string);
        return $string;
    }

    function sanitizeUsername($string){
        $string = strip_tags($string);
        $string = preg_replace("/\s+/", " ", $string);
        $string = trim($string);
        return $string;
   }

    function sanitizeEmail($string){
        $string = strip_tags($string);
        $string = str_replace(" ", "", $string);
        $string = trim($string);
        return $string;
   }