<?php
include "connection.php";

function transactionAdd($action,$name,$ticker,$num_stocks,$value,$profit){ // buy profit is 0
        global $connection;
        $query = "INSERT INTO transaction_history(action,name,ticker,num_stocks,value,profit,date) ";
        $query .= "VALUES ('$action','$name','$ticker',$num_stocks,$value,$profit,CURRENT_TIMESTAMP)";  //.= concatenates with query in the line above
        //variables like $name must have quotes around them to indicate they
        //are strings
        $result = mysqli_query($connection, $query); //making it a variable to check if it works
        
        if(!$result){
            die('Query Failed' . mysqli_error($connection));
        } 
    
    
}


function readTransactionRow($ticker){
    global $connection;
    $query = "SELECT * FROM transaction_history";
    
    
    $result = mysqli_query($connection, $query); //making it a variable to check if it works
    
    if(!$result){
        die('Query Failed' . mysqli_error($connection));
    }
    
    
    while($row = mysqli_fetch_assoc($result)){
        if (strcmp($row['ticker'], $ticker)==0){
            return $row;
        }
        
    }
}

?>