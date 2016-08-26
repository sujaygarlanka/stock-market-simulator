<?php
include "functions.php";
if(isset($_POST['sell_ticker']) && isset($_POST['sell_number'])){
    session_start();
    $ticker = $_POST['sell_ticker'];
    $number = $_POST['sell_number'];
    $cost = $_SESSION['sell_cost']; // from sell_cost.php
    sellStock($ticker,$number,$cost);
}


?>