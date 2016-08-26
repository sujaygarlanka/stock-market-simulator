<?php
include_once "functions.php"; // sent from display_stocks.php to get data on selling stock ticker for the selling modal
if (isset($_POST['sell_id'])){
    $ticker = $_POST['sell_id'];
    $array = readRow($ticker);
    
    
    // $response = file_get_contents("http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in%20(%22$ticker%22)&env=http://datatables.org/alltables.env&format=json");
    // $response = json_decode($response,true); // the true returns an associative array from json
    // $price = $response['query']['results']['quote']['Ask'];
    // $price = (float)$price;
    // $passedValue = substr($_POST['passedValue'],1); // value of the stock selected in the table with dollar sign removed
    // $passedValue = floatval($passedValue);
    $price = $array['net_worth']/$array['initial']; 
    if(is_null($price)){
        $price = 0;
    }
    session_start();
    $_SESSION['sell_price'] = $price;
    
    $array = json_encode($array);
    echo $array;
}

?>