<?php
if(isset($_POST['sellTicker']) && isset($_POST['sellNumber'])) {
    session_start();
    $sell_number = $_POST['sellNumber'];
    $price = $_SESSION['sell_price']; // from sell_search.php
    // the code below is commented out because it is now in sell_search.php 
    // in order to avoid reloading the price of the stock as the user changes the number of stocks
    
    // $sell_ticker = $_POST['sellTicker'];
    // $response = file_get_contents("http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in%20(%22$sell_ticker%22)&env=http://datatables.org/alltables.env&format=json");
    // $response = json_decode($response,true); // the true returns an associative array from json
    // $price = $response['query']['results']['quote']['Ask'];
    // $price = (float)$price;
    // if(is_null($price)){
    //     $price = 0;
    // }
    $cost = $price * $sell_number;
    echo "$$cost";
    $_SESSION['sell_cost'] = $cost;
    
    
}

?>