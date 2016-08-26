<?php

include "connection.php";
include "transaction_history.php";

function updateRow($stock,$initial,$invested,$price) {
    global $connection;
    
    $array = readRow($stock);
    $old_net_worth = $array['net_worth'];
    $new_net_worth = $array['initial']*$price; //updates the value of the stock with the price of an individual stock times number of stocks
    updateAccount(-1,$old_net_worth,$new_net_worth);
    $query = "UPDATE portfolio SET ";
    
    if ($initial!=-1){ // these if statments allow individual columns in a row of the data table to be updated
        $query .= "initial = $initial, ";
    }
    
    if ($invested!=-1){
        $query .= "invested = $invested, ";
    }
    
    if ($price!=-1){
        $query .= "net_worth = $new_net_worth ";
    }
    
    $query .= "WHERE stock = '$stock' "; // need quotes around string variables for mysqli
    $result = mysqli_query($connection, $query);
    if (!$result) {
        die("Query Failed" .mysqli_error($connection));
    }
    
}


function readRow($stock){
    global $connection;
    $query = "SELECT * FROM portfolio";
    
    
    $result = mysqli_query($connection, $query); //making it a variable to check if it works
    
    if(!$result){
        die('Query Failed' . mysqli_error($connection));
    }
    
    
    while($row = mysqli_fetch_assoc($result)){
        if (strcmp($row['stock'], $stock)==0){
            return $row;
        }
        
    }
    return null;
    
}

function updateAccount($new_invested, $old_net_worth,$new_net_worth) {
    global $connection;
    
    $array = readRow('account');
    $net_worth = $array['net_worth']-$old_net_worth + $new_net_worth; //updates the value of the account
    $invested = $array['invested'] + $new_invested;
    $query = "UPDATE portfolio SET ";
    
    
    
    if ($new_net_worth!=-1){ //for not updating net worth if value is -1
        $query .= "net_worth = $net_worth ";
    }
    else {
        $query .= "invested = $invested ";
    }
    $query .= "WHERE stock = 'account' "; // must have single quotes around this because its a string. don't completely know why
    $result = mysqli_query($connection, $query);
    if (!$result){
        die("Query Failed" .mysqli_error($connection));
        
    }
    
    
}

function buyStock($name,$cost){
    global $connection;
    if(isset($_POST['ticker_search'])  && isset($_POST['ticker_cost'])) {
        
        $stock = $_POST['ticker_search']; //ticker
        $initial = $_POST['ticker_cost']; //number of stocks
        $invested = $cost; // amount it costs to buy the stock, which is the investment amount
        $net_worth = $cost; // initial invested amount is equal to the net worth of the stock at the beginning
        
        $name = mysqli_real_escape_string($connection, $name); //cleans the username by allowing commas, etc to prevent hacking
        $stock = mysqli_real_escape_string($connection, $stock); 
        $initial = mysqli_real_escape_string($connection, $initial);
        $invested = mysqli_real_escape_string($connection, $cost);
        $net_worth = mysqli_real_escape_string($connection, $net_worth);
        $array = readRow($stock);
        
        if (is_null($array)){
            $query = "INSERT INTO portfolio(name,stock,initial,invested,net_worth) ";
            $query .= "VALUES ('$name','$stock',$initial,$invested,$net_worth)";  //.= concatenates with query in the line above
            //variables like $name must have quotes around them to indicate they
            //are strings
            
        }
        else {
            $new_initial = $initial + $array['initial'];
            $new_invested = $invested + $array['invested'];
            $new_net_worth = $net_worth + $array['net_worth'];
            $query = "UPDATE portfolio SET ";
            $query .= "initial = $new_initial, ";
            $query .= "invested = $new_invested, ";
            $query .= "net_worth = $new_net_worth ";
            $query .= "WHERE stock = '$stock' ";
                
            
        }
        
        $result = mysqli_query($connection, $query); //making it a variable to check if it works
        
        if(!$result){
            die('Query Failed ' . mysqli_error($connection));
        }
        
        updateAccount($cost,-1,-1);
        transactionAdd('Buy',$name,$stock,$initial,$cost,0);
        
    }
}


function sellStock($ticker,$number,$cost){
    global $connection;
    
    $ticker = mysqli_real_escape_string($connection, $ticker);
    $number = mysqli_real_escape_string($connection, $number); //cleans the username by allowing commas, etc to prevent hacking
    $cost = mysqli_real_escape_string($connection, $cost);
    
    $array = readRow($ticker);
    if ($array['initial'] == $number){ // need form validation to prevent higher number than stocks available
        $query = "DELETE FROM portfolio ";
        $cost = $cost *-1; // to subtract from invested in account and is selling value of the stocks. It is poorly named :(
        updateAccount($cost,-1,-1);
        $cost = $cost*-1; // selling value without negative
        $profit = $cost - $array['invested'];
        
    }
    
    else {
        $new_number = $array['initial'] - $number; //stocks remaining after sale
        $new_invested = $array['invested'] * ($new_number/$array['initial']);
        $new_net_worth = $array['net_worth'] - $cost;
        $query = "UPDATE portfolio SET ";
        $query .= "initial = $new_number, ";
        $query .= "invested = $new_invested, ";
        $query .= "net_worth = $new_net_worth ";
        $cost = $cost*-1; // to subtract from invested in account
        updateAccount($cost,-1,-1);
        $cost = $cost*-1; // selling value without negative
        $profit = $cost - ($array['invested'] - $new_invested);
        
    }
    
    
    $query.="WHERE stock = '$ticker' "; // need quotes around string variables for mysqli
    
    $result = mysqli_query($connection, $query); //making it a variable to check if it works
    
    if(!$result){
        die('Query Failed' . mysqli_error($connection));
    }
    transactionAdd('Sell',$array['name'],$ticker,$number,$cost,$profit);
    
    
}


?>