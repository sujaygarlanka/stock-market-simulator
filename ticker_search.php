<?php
if(isset($_POST['tickerSearch'])) {
    $ticker_search = $_POST['tickerSearch'];
    $response = file_get_contents("http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in%20(%22$ticker_search%22)&env=http://datatables.org/alltables.env&format=json");
    $response = json_decode($response,true); // the true returns an associative array from json
    $name = $response['query']['results']['quote']['Name'];
    $price = $response['query']['results']['quote']['Ask'];
    $float_price = (float)$price;
    if (is_null($name)){
        echo "Stock does not exist";
    }
    else {
        if(is_null($price)){
            $price = 0;
        }
        echo "$name : $$price";
        echo "<br>";
        session_start();
        $_SESSION['ticker_price']=$float_price; // for ticker_cost.php
        $_SESSION['name'] = $name; // for buy_stock.php and later buyStock function
    }
    
}



?>