<?php 
    /*
     * This functions will return another array with the values inside an array
     * and act accordingly. We then use the response helper to display.
     */
    function returnData($rows, $array) {
        if(!empty($rows)){
            $returnData['rows_returned'] = $rows;
        }
        
        if(isset($array["data"])){
            $returnData[$array["data"]] = $array;
            unset($returnData[$array["data"]]["data"]);
        }
        
        return $returnData;
    }

    function returnPageData($rows, $pageRows, $pages, $hasNextPage, $hasPrevPage, $array) {
        $returnData['total_rows'] = $rows;
        $returnData['current_page_rows'] = $pageRows;
        $returnData['total_pages'] = $pages;

        $hasNextPage === true ? $returnData['has_next_page'] = true : $returnData['has_next_page'] = false;
        $hasPrevPage === true ? $returnData['hasPrevPage'] = false : $returnData['hasPrevPage'] = true;

        if(isset($array["data"])){
            $returnData[$array["data"]] = $array;
            unset($returnData[$array["data"]]["data"]);
        }
        
        return $returnData;
    }