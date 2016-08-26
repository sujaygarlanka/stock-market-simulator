<?php
session_start();
if(isset($_SESSION['ticker_price'])) {
    $price = $_SESSION['ticker_price'];
    $num = (float)$_POST['tickerCost'];
    $answer = $price*$num;
    $_SESSION['tickerCost'] = $answer; // for use in buy_stock.php
    echo "Investment Cost: $$answer";
    
}



?>