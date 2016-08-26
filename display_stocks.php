<?php
include "connection.php";

$query = "SELECT * FROM portfolio";
$query_info = mysqli_query($connection, $query);

if(!$query_info){
    
    die("Query Failed" . mysqli_error($connection));
    
}

while($row = mysqli_fetch_array($query_info)){
    if ($row['net_worth']==0){
        $percent_change = "NA";
    }
    else{
        $percent_change = ($row['net_worth']-$row['invested'])/$row['net_worth'] *100;
        $percent_change = round($percent_change,2);
        $percent_change .= "%";
    }
    
    if ($percent_change < 0){
        $url = 'https://upload.wikimedia.org/wikipedia/commons/0/04/Red_Arrow_Down.svg';
        $color= 'table-danger';
    }
    else {
        $url = 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Green_Arrow_Up_Darker.svg/700px-Green_Arrow_Up_Darker.svg.png';
        $color= 'table-success';
    }
    if (strcmp($row['stock'], 'account')!=0){
        echo "<tr class = \"myClass $color\" id={$row['stock']}>"; // the \ is used to insert the quote without
        $invested = '$' . $row['invested'];
        $net_worth = '$' . $row['net_worth'];
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['stock']}</td>";
        echo "<td>{$invested}</td>";
        echo "<td class = 'passedValue'>{$net_worth}</td>";
        echo "<td>{$percent_change}  </td>";
        echo "<td><input type='button' class='btn btn-primary-outline sell' value='Sell'></td>";
        echo "</tr>";
    }
    
}


?>

  <script>
    // Data is not in new.php but in this file's DOM, need to figure out what that is

    $('.myClass').click(function(evt) {
      display_stock = $(this).closest('tr').attr('id');
      //alert($(this).closest('tr').attr('id')); // .closest gets the id of the row that is clicked on
      $.ajax({ // quickly changes the display boxes at the top of the page without having to wait for the refresh method to be called every 3000 seconds
        type: 'POST',
        data: {
          stock: display_stock
        },
        url: 'process.php',
        dataType: "json",
        success: function(data) {
          if (!data.error) { // this sort of json accessing data only works in ajax
            $('#stock_name').html(data.name);
            $('#stock_price').html(data.price);
            $('#stock_initial').html(data.stock_initial);
            $('#stock_invested').html(data.stock_invested);
            $('#stock_net_worth').html(data.stock_net_worth);
            $('#account_initial').html(data.account_initial);
            $('#account_invested').html(data.account_invested);
            $('#account_net_worth').html(data.account_net_worth);

          }

        }
      });


    });

    $('.sell').click(function(evt) {
      // alert($(this).closest('tr').attr('id'));
      // $('#sell_ticker').html($(this).closest('tr').attr('id'));
      clearInterval(dash); // turn off updates by setInterval for selling stocks
      clearInterval(table);

      $('#sellModal').modal('show');
      $('#sell_ticker').val($(this).closest('tr').attr('id')); // puts ticker of stock in sell form field

      $('#sell_number').val(''); // resets number in form without reloading the whole page to make another purchase
      $('#sell_cost').html(''); // resets costs in form without reloading the whole page to make another purchase
      $.ajax({
        type: 'POST',
        data: {
          sell_id: $(this).closest('tr').attr('id')
          //passedValue: $(this).closest('tr').find('.passedValue').text() gets value from table row with class passedValue
        },
        url: 'sell_search.php',
        dataType: "json",
        success: function(data) {
          if (!data.error) { // this sort of json accessing data only works in ajax
            $('#sell_name').html(data.name);
            $('#sell_numStock').html(data.initial);
            var net_worth = '$' + data.net_worth;
            $('#sell_netWorth').html(net_worth);

          }

        }
      });




    });
  </script>