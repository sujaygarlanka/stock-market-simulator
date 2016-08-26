<?php
include "transaction_history.php";
$query = "SELECT * FROM transaction_history";
$query_info = mysqli_query($connection, $query);

if(!$query_info){
    
    die("Query Failed" . mysqli_error($connection));
    
}
while($row = mysqli_fetch_array($query_info)){
    echo "<tr>"; // the \ is used to insert the quote without
    $value = '$' . $row['value'];
    $profit = $row['profit'];
    $profit = round($profit, 2);
    $profit = '$' . $profit;
    echo "<td>{$row['action']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['ticker']}</td>";
    echo "<td>{$row['num_stocks']}</td>";
    echo "<td>{$value}</td>";
    echo "<td>{$profit}</td>";
    echo "<td>{$row['date']} </td>";
    echo "</tr>";
}


?>