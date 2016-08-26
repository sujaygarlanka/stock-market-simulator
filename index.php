<?php

include "functions.php";

?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF=8">

    <title>Stock Market Simulator</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css">

  </head>

  <body>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"></script>
    
    <script>
      var display_stock = <?php echo '"'. getFirstStock() .'"' ; ?>; // stock displayed in top blue box that is selected from the table
      // this variable has a global scope because it is defined outside of
      // any function. So it can be accessed in any file like it is in display_stocks.php
      var refreshRate = 3000;
      var dash; // both variables are declared outside document.ready so they can be accessed in display_stocks.php
      var table; // this is so that updates(setIntervals) are turned off for selling stocks and turned on afterward

      $(document).ready(function() {
        // refresh();
        // table_refresh();
        dash = setInterval(function() {
          refresh();

        }, refreshRate);

        table = setInterval(function() {
          table_refresh();

        }, refreshRate);

        function table_refresh() {
          $.ajax({

            url: "display_stocks.php",
            type: 'POST',
            success: function(show_stocks) {

              if (!show_stocks.error) {

                $("#show_stocks").html(show_stocks);

              }



            }


          });
        }

        function refresh() {

          // table_refresh();

          $.ajax({
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


        }

        $('#ticker_search').keyup(function() { // for search function for buying stocks
          var tickerSearch = $('#ticker_search').val();

          $.ajax({

            url: 'ticker_search.php',
            data: {
              tickerSearch: tickerSearch //word before colon is the POST string id
            },
            type: 'POST',
            success: function(data) {

              if (!data.error) {
                $('#buy_data').html(data);
              }


            }

          });

        });

        $('#ticker_cost').keyup(function() { // for search function for buying stocks

          var tickerCost = $('#ticker_cost').val();

          $.ajax({

            url: 'ticker_cost.php',
            data: {
              tickerCost: tickerCost
            },
            type: 'POST',
            success: function(data) {

              if (!data.error) {
                $('#buy_cost').html(data);
              }
            }

          });

        });


        $('#buy_stock').submit(function(evt) {
          evt.preventDefault(); // only sends data if data is entered
          var postData = $(this).serialize(); // postData is POST data with the string id of form elements
          // like ticker_search and ticker_cost

          var url = $(this).attr('action'); // in form html code as buy_stock.php
          $.post(url, postData, function(php_table_data) {

            $('#buy_result').html(php_table_data);

            $('#myModal').modal('show');
            // alert("Transaction Completed");


          });

        });

        $('#ok').click(function(evt) { // to reload a page after a stock is purchased
          //location.reload();
          $('.nav-tabs a[href="#portfolio"]').tab('show'); // the following avoids reloading by instead clearing fields
          $('#ticker_search').val('');
          $('#ticker_cost').val('');
          $('#buy_data').html('');
          $('#buy_cost').html('');

        });


        ///////////////////////////////////////// Sell Stocks //////////////////////////////////////////////////////////
        $('#sell_number').keyup(function() { // for search function calculating total cost of sold stocks

          var sellTicker = $('#sell_ticker').val();
          var sellNumber = $('#sell_number').val();

          $.ajax({

            url: 'sell_cost.php',
            data: {
              sellTicker: sellTicker,
              sellNumber: sellNumber
            },
            type: 'POST',
            success: function(data) {

              if (!data.error) {

                $('#sell_cost').html(data);

              }

            }

          });

        });


        $('#sell_stock').submit(function(evt) {
          evt.preventDefault(); // only sends data if data is entered
          var postData = $(this).serialize(); // postData is POST data with the string id of form elements
          // like sell_ticker and sell_number. Same value as sellTicker and sellNumber in
          // POST method above in key_up. This is a practice, but unnecessary post method

          var url = $(this).attr('action'); // in form html code as sell_stock.php
          dash = setInterval(function() { // restarts updates (setIntervals)
            refresh();

          }, refreshRate);

          table = setInterval(function() {
            table_refresh();

          }, refreshRate);

          $.post(url, postData, function(data) {



          });
          $('#sellModal').modal('hide');

        });


        $('#close').click(function(evt) { // for the selling modal to resume updating
          dash = setInterval(function() {
            refresh();

          }, refreshRate);

          table = setInterval(function() {
            table_refresh();

          }, refreshRate);
        });

        //////////////////////////////////////// Transaction History ////////////////////////////////////////////

        $('#transaction_button').click(function(evt) {

          $.ajax({

            url: 'display_transactions.php',
            type: 'POST',
            success: function(data) {

              if (!data.error) {
                $('#show_transactions').html(data);
              }


            }

          });

        });







      }); //document ready function end
    </script>

    <style>
      /*.panel {
padding: 0px;
}*/
    </style>

    <!--Sell stocks modal-->
    <div id="sellModal" class="modal fade">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button id='close' type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Sell</h4>
          </div>


          <br>
          <h3 class=text-xs-center id='sell_name'></h3>

          <div class="modal-body">
            <form method="post" id="sell_stock" class="form-horizontal" action="sell_stock.php">
              <!--<p id="sell_ticker"></p>-->
              <label>Ticker:</label>
              <div class="form-group">
                <input type="text" name="sell_ticker" id="sell_ticker" class="form-control" placeholder="Ticker for stock" required>
              </div>
              <label>Number of Stocks:</label>
              <div class="form-group">
                <input type="text" name="sell_number" id="sell_number" class="form-control" placeholder="Number of stocks" required>
              </div>

              <div class="form-group">
                <!-- All the following data is from display_stocks.php -->
                <table class="table">
                  <thead class="thead-inverse">
                    <tr>
                      <th>Number of Stocks</th>
                      <th>Current Value</th>
                      <th>Selling Value</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td id='sell_numStock'></td>
                      <td id='sell_netWorth'></td>
                      <td id='sell_cost'></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Sell">
              </div>
            </form>

          </div>

        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div id="myModal" class="modal fade">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Transaction Completed</h4>
          </div>
          <div class="modal-body">
            <p id="buy_result"></p>
          </div>
          <div class="modal-footer">
            <input id='ok' type="button" class="btn btn-primary" data-dismiss="modal" value="Okay">
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <h1 class="text-sm-center"> Stock Market </h1>
    <p style="text-align: center;"> Takes a Few Moments to Load ...</p>
    <br>
    <br>

    <div class="row">

      <div class="col-sm-1"> </div>

      <div class="col-sm-4">
        <div class="card text-sm-center">
          <div class="card-header">
            <h3>Account</h3>
          </div>
          <div class="card-block">
            <h2 id='account_net_worth'></h2>
          </div>
          <div class="card-footer text-muted">
            3 minutes ago
          </div>
        </div>
      </div>

      <div class="col-sm-2"> </div>

      <div class="col-sm-4">
        <div class="card text-sm-center">
          <div class="card-header">
            <h3 id="stock_name"></h3>
          </div>
          <div class="card-block">
            <h2 class="card-text" id='stock_price'></h2>
          </div>
          <div class="card-footer text-muted">
            2 minutes ago
          </div>
        </div>
      </div>

      <div class="col-sm-1"> </div>

    </div>
    <br>
    <br>

    <!------------------------------------- Table Below -------------------------------------------------------->
    <div class="row">
      <div class="col-sm-1"> </div>

      <div class="col-sm-10">

        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a href="#portfolio" class="nav-link active" data-toggle="tab" role="tab">Portfolio</a>
          </li>
          <li class="nav-item">
            <a href="#buy" class="nav-link" data-toggle="tab" role="tab">Buy</a>
          </li>
        </ul>

        <div class="tab-content">
          <div role="tabpanel" class="tab-pane fade in active" id="portfolio">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Stock Name</th>
                  <th>Ticker</th>
                  <th>Invested</th>
                  <th>Current Value</th>
                  <th>Percent Change</th>
                  <th>Sell</th>
                </tr>
              </thead>
              <tbody id="show_stocks">

              </tbody>
            </table>
          </div>

          <div role="tabpanel" class="tab-pane fade" id="buy">
            <br>
            <form method="post" id="buy_stock" class="col-sm-6" action="buy_stock.php">
              <label>Ticker:</label>
              <div class="form-group">
                <input type="text" name="ticker_search" id="ticker_search" class="form-control" placeholder="Ticker for stock" required>
              </div>
              <label>Number of Stocks:</label>
              <div class="form-group">
                <input type="text" name="ticker_cost" id="ticker_cost" class="form-control" placeholder="Number of stocks" required>
              </div>
              <div class="form-group">
                <h2 id='buy_data'></h2>
                <h2 id='buy_cost'></h2>
                <!--<h2 id='buy_result'></h2>-->
              </div>

              <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Buy">
              </div>


            </form>
          </div>

        </div>
      </div>

      <div class="col-sm-1"> </div>
    </div>

    <div class="row">
      <p>
        <button id='transaction_button' class="btn btn-primary-outline center-block" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" style="display: block; width: 80%;">
          Transaction History
        </button>
      </p>
      <div class="collapse" id="collapseExample">
        <table class="table table-hover center-block" style="display: block; width: 80%;">
          <thead>
            <tr>
              <th>Action</th>
              <th>Stock Name</th>
              <th>Ticker</th>
              <th>Number of Stocks</th>
              <th>Transaction Value</th>
              <th>Profit</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody id="show_transactions">

          </tbody>
        </table>
      </div>
    </div>







  </body>

  </html>