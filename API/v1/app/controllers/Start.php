<?php 
    class Start extends BaseController {
        public function index(){
            $array = [
                "data" => "socials",
                "message" => "REST-API by: Richard Santarina Reyes",
                "Github" => "https://github.com/santarinareyes",
                "Contact" => "rsrprivat@gmail.com",
            ];
            $returnData = returnData(false, $array);
            status307($returnData, $array["data"]);
        }
    }