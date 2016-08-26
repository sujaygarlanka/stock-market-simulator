<?php
include "functions.php";
session_start();
$name = $_SESSION['name'];
$cost = $_SESSION['tickerCost'];
buyStock($name,$cost); //total investment cost of the stock calculated in ticker_search.php
echo "$name";
echo"<br>";
echo "Amount Invested: $$cost";

?>