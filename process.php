<?php
include "functions.php";
include "connection.php";

$query = "SELECT * FROM portfolio";
$query_info = mysqli_query($connection, $query);

if(!$query_info){
    
    die("Query Failed" . mysqli_error($connection));
    
}
$selected_ticker = $_POST['stock']; // ticker or stock selected from the table
while($row = mysqli_fetch_array($query_info)){
    $ticker=$row['stock'];
    if (strcmp($ticker,'account')!=0){
        $response = @file_get_contents("http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in%20(%22$ticker%22)&env=http://datatables.org/alltables.env&format=json");
        if ($response == false){
            continue;
        }
        $response = json_decode($response,true); // the true returns an associative array from json
        $name = $response['query']['results']['quote']['Name'];
        $price = $response['query']['results']['quote']['Ask'];
        $float_price = (float)$price;
        if ($float_price != 0){
            updateRow($ticker,-1,-1,$float_price);
        }
    }
    if (strcmp($ticker, $selected_ticker)==0){
        $stock = readRow("$selected_ticker"); // stock selected to be read and returned to display
        $account = readRow('account'); // account information
        $return_array = array("name"=>$name,"price"=>'$'.$price,"account_initial"=>$account['initial'],"account_invested"=>$account['invested'],"account_net_worth"=>'$'.$account['net_worth'],"stock_initial"=>$stock['initial'],"stock_invested"=>$stock['invested'],"stock_net_worth"=>$stock['net_worth']);
        $return_array = json_encode($return_array);
        
    }
    
}


echo $return_array;



?>