<?php
    session_start();

    if($_SESSION['Auth'] == false){
        echo <<< EOT
            <html>
                <body>
                    <script>
                        alert("auth error");
                        window.location.replace("../../index.php");
                    </script>
                </body>
            </html>
        EOT;
    }
    $account = $_SESSION['account'];
    $phonenumber = $_SESSION['phonenumber'];
    $username = $_SESSION['username'];
    
    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if(isset($_GET['lat']) && isset($_GET['lon'])){
        $ulong = $_GET['lon'];
        $ulati = $_GET['lat'];
        $stmt = $conn->prepare("update users SET Location = ST_GeomFromText(:location,2154) where username =:username");
        $stmt->execute(array('username' => $username, 'location' => "POINT($ulong $ulati)") );
        $_SESSION['location'] = "POINT(" . $_GET['lon'] . " " . $_GET['lat'] . ")" ;
        echo <<< EOT
        <!DOCTYPE>
            <html> 
                <body> 
                    <script> 
                        window.location.replace("./home.php")
                    </script>
                </body>
        </html>
        EOT;
    }
    $location = str_replace("POINT", "", $_SESSION['location']);

    $stmt = $conn->prepare("SELECT wallet_balance
                            FROM users
                            WHERE username = '$username' and account = '$account'");
    $stmt->execute();
    $balance = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->wallet_balance;

?>
  
<!doctype html>
<html lang="en">

<head>
<!-- Required meta tags -->

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link type="text/css" rel="styleSheet"  href="../../css/style.css" />
<link type="text/css" rel="styleSheet"  href="../../css/Register.css" />
<link type="text/css" rel="styleSheet"  href="../../css/nicepage.css" />
<!-- Bootstrap CSS -->

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<title>Home Page</title>
</head>

<body>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand " href="#">FoodPigeon</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <ul class="nav nav-tabs">
            <li class = "active" ><a href="#home">Home</a></li>
            <li><a href = "#menu1" >Shop</a></li>
            <li><a href = '#MyOrder'>My Order</a></li>
            <li><a href = "#ShopOrder"> Shop Order </a></li>
            <li><a href = "#TransactionRecord">Transaction Record</a></li>
            <li><a href = "../../index.php">Logout</a></li>
        </ul>
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <h3 class = "subtitle" >Profile</h3>
                <div class="row">
                    <div class=" col-xs-4">
                        <table class="table" style=" margin-top: 15px;">
                            <thead>        
                                <tr>
                                    <th> Account </th>
                                    <th> Username </th>
                                    <th> Phone </th>
                                    <th align = "center"> location</th>
                                    <th> wallet_balance </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    echo <<< EOT
                                        <td> $account </td>
                                        <td> $username </td>
                                        <td> $phonenumber </td>
                                        <td align = "center">  $location 
                                            <button type="button" id = "loc" style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal" data-target="#location"> Edit Location</button>
                                            </td>
                                        <td align = "center"> $balance 
                                            <button type="button " style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal" data-target="#myModal">Add value</button>
                                        </td>
                                    EOT;
                                    ?>
                                </tr>
                            </tbody>
                        </table>
                        <div class="modal fade" id = "location"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog  modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">edit location</h4>
                                    </div>
                                    <input type = "hidden" class = "content" name = "id">
                                    <div class="modal-body">
                                        <label class="control-label " for="longitude">longitude</label>
                                        <input type="text" class="form-control" id="longitude" placeholder="enter longitude">
                                        <br>
                                        <label class="control-label " for="latitude">latitude</label>
                                        <input type="text" class="form-control" id="latitude" placeholder="enter latitude">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" id = "editlocation" data-dismiss="modal"> Edit </button>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $('#editlocation').click( function(){
                                    let latitude = $('#latitude').val();
                                    let longitude = $('#longitude').val();
                                    if(latitude < -90 && latitude > 90)
                                        alert("latitude error!");
                                    else if (longitude < -180 && longitude > 180)
                                        alert("longitude error!");
                                    else
                                        window.location.replace("./home.php?lat=" + latitude + "&lon=" + longitude);
                                });
                            </script>
                        </div>  
                
                
                <div class="modal fade" id="myModal"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog  modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add value</h4>
                            </div>
                            <div class="modal-body">
                                <input type="text" class="form-control" id="value" placeholder="enter add value">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal" id = "ADDVALUE">Add</button>
                            </div>
                        </div>
                        <script>
                            $('#ADDVALUE').click( function(){
                                let add_value = $('#value').val();
                                if (add_value <= 0){
                                    alert('value must > 0');    
                                }
                                else{
                                    $.ajax({
                                        url : './recharge.php',
                                        type : 'GET',
                                        data : {
                                            'add_value' : add_value
                                        },
                                        success : function(msg){
                                            alert(msg);
                                            window.location.replace("./home.php");
                                        }
                                    })
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <h3 class = "subtitle">Search</h3>
        <div class=" row  col-xs-8">
            <form class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-sm-1" for="Shop">Shop</label>
                    <div class="col-sm-5">
                        <input type="text" id = "Shop" class="form-control" placeholder="Enter Shop name">
                    </div>
                    <label class="control-label col-sm-1" for="distance">distance</label>
                    <div class="col-sm-5">
                        <select class="form-control" id="sel1">
                            <option>near</option>
                            <option>medium </option>
                            <option>far</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-1" for="Price">Price</label>
                    <div class="col-sm-2">
                        <input id = 'lowerbound' type="text" class="form-control">
                    </div>

                    <label class="control-label col-sm-1" for="~">~</label>
                    <div class="col-sm-2">
                        <input id = 'upperbound' type="text" class="form-control">
                    </div>

                    <label class="control-label col-sm-1" for="Meal">Meal</label>
                    <div class="col-sm-5">
                        <input type="text" list="Meals" class="form-control" id="Meal" placeholder="Enter Meal">
                        <datalist id="Meals">
                            <option value="Hamburger">
                            <option value="coffee">
                        </datalist>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-1" for="category"> category</label>
                    <div class="col-sm-5">
                        <input type="text" list="categories" class="form-control" id="category" placeholder="Enter shop category">
                        <datalist id="categorys">
                            <option value="fast food">
                        </datalist>
                    </div>
                    <button type="button" id = "searchsubmit" style="margin-left: 18px;"class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
        <?php
        echo <<< EOT
            <script>
                $(document).ready( function() {
                    $("#searchsubmit").click( function(){
                        $.ajax({
                            type: 'GET',
                            url : './search_page.php',
                            data: {
                                'shopname': $('#Shop').val(),
                                'sel1': $('#sel1').val(),
                                'lowerbound': $('#lowerbound').val(),
                                'upperbound': $('#upperbound').val(),
                                'meals': $('#Meal').val(),
                                'categories': $('#category').val(),
                                'self_location': "POINT$location"
                            },
                            success: function(msg){
                                let args = msg.split("|");
                                
                                console.log(args);
                                $('#search_result').html(args[0]);
                                let names_set = args[1];
                                $.ajax({
                                    type: 'GET',
                                    url: './menu.php',
                                    data: {
                                        'name_set': names_set
                                    },
                                    success: function(msg2){
                                        $('#menu').html(msg2);
                                    }
                                })
                                
                            },
                            error: function(){
                                alert("fail");
                            }
                        })
                    })
                });
            </script>
        EOT;
        ?>
        
        <div class="row">
            <div class="  col-xs-8">
                <table class="table" style=" margin-top: 15px;">
                    <thead>
                        <tr>
                            <th scope="col">#</th>    
                            <th scope="col">shop name</th>
                            <th scope="col">shop category</th>
                            <th scope="col">Distance </th>
                        </tr>
                    </thead>
                    <tbody id = "search_result"> </tbody>
                    <tbody id = "menu"></tbody>
                </table>
            </div>
        </div>
    </div>

<!-- END OF HOME PAGE-->
<!-- SHOP PAGE -->

<!DOCTYPE html>
<html>
    
    <div id="menu1" class="tab-pane fade">
        <?php
        if(is_null($_SESSION['storename'])){
            $account = $_SESSION['account'];
            echo <<< EOT
            <h3 class = "subtitle"> Start a business </h3>
            <form action = '../shop_page/shop_reg.php' method = "POST">
                <div class="form-group ">
                    <div class="row">
                        <div class="col-xs-2">
                            <label for="ex5">shop name<not id = "not" style = 'font-size: 4px; color: red;'> </not></label>
                            <input class="form-control"  id = "stname" placeholder="macdonald" type="text" name='sname'>
                            <input name="account" type="hidden" value = $account>
                        </div>
                        <div class="col-xs-2">
                            <label for="ex5">shop category</label>
                            <input class="form-control" id="ex5" placeholder="fast food" type="text" name='scat'>
                        </div>
                        <div class="col-xs-2">
                            <label for="ex6">longtitude</label>
                            <input class="form-control" id="ex6" placeholder="121.00028167648875" type="text" name='slo'>
                        </div>
                        <div class="col-xs-2">
                            <label for="ex8">latitude</label>
                            <input class="form-control" id="ex8" placeholder="24.78472733371133" type="text" name='sla'>
                        </div>
                    </div>
                </div>
                <div class=" row" style=" margin-top: 25px;">
                    <div class=" col-xs-3">
                        <button type="submit" class="btn btn-primary"  >register</button>
                    </div>
                </div>            
            </form>

            <script>
                $('#stname').change( function(){
                    $.ajax({
                        type: 'GET',
                        url: '../shop_page/CheckStore.php',
                        data:{
                            storename: $('#stname').val()
                        },
                        success: function(msg){
                            $('#not').html(msg);
                        }
                    })
                });
            </script>

            EOT;
        }
        else{
            $storename = $_SESSION['storename'];
            $storecategory = $_SESSION['storecategory'];
            $storelocation = $_SESSION['storelocation'];
            $storelocation = str_replace("(", "", str_replace(")", "", $storelocation));
            $storelongitude = explode(" ", $storelocation)[0];
            $storelatitude = explode(" ", $storelocation)[1];
            echo <<< EOT
            <h3 class = "subtitle" > Your business </h3>
            <fieldset disabled>
                <div class="row">
                    <div class="  col-xs-8">
                        <table class="table" style=" margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th scope="col">shop name</th>    
                                    <th scope="col">shop category</th>
                                    <th scope="col">longitude</th>
                                    <th scope="col">latitude</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> $storename</td>
                                    <td> $storecategory </td>
                                    <td> $storelongitude </td>
                                    <td> $storelatitude </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </fieldset>
            EOT;    
        }

        ?>
        <hr>
        <h3 class = "subtitle" >ADD</h3>
        <form action="../shop_page/add.php" method="post" Enctype="multipart/form-data">
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-6">
                        <label for="ex3">meal name</label>
                        <input class="form-control" id="ex3" type="text", name="mname">
                    </div>
                </div>

                <div class="row" style=" margin-top: 15px;">
                    <div class="col-xs-3">
                        <label for="ex7">price</label>
                        <input class="form-control" id="ex7" type="text", name="price">
                    </div>
                    <div class="col-xs-3">
                        <label for="ex4">quantity</label>
                        <input class="form-control" id="ex4" type="text", name="quan">
                    </div>
                </div>

                <div class="row" style=" margin-top: 25px;">
                    <div class=" col-xs-3">
                        <label for="ex12">上傳圖片</label>
                        <input id="myFile" type="file" name="myFile" multiple class="file-loading">
                    </div>
                    <div class=" col-xs-3">
                        <button style=" margin-top: 15px;" type="submit" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="  col-xs-8">
                <table class="table" style=" margin-top: 15px;">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Picture</th>
                            <th scope="col">meal name</th>
                            <th scope="col">price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $dbservername='localhost';
                        $dbname='examdb';
                        $dbusername='examdb';
                        $dbpassword='';

                        $conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stmt=$conn->prepare("SELECT * FROM dish WHERE name = :storename");
                        $stmt->execute(array('storename' => $_SESSION['storename']));
                        if ($stmt->rowCount() > 0) {
                            $result = $stmt->fetchAll(PDO::FETCH_CLASS);
                            $i = 0;
                            $storename = $_SESSION['storename'];
                            foreach ($result as $row) { 
                                $i += 1;
                                $meal = $row->dish_name;
                                $price = $row->price;
                                $quan = $row->amount;
                                $picture = $row->picture;

                                $_SESSION['meal'] = $meal;
                                echo <<< EOT
                                <tr>
                                    <th scope = "row" >$i</th>
                                    <td><img src = "data: png;base64, $picture "/ width="100" height="100" ></td>
                                    <td>$meal</td>
                                    <td>$price </td>
                                    <td>$quan </td>
                                    <td> <button type="button" class="btn btn-info" data-toggle="modal" data-target="#$meal"> Edit</button></td>
                                    <div class="modal fade" id=$meal data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form action = "../shop_page/edit.php", method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">$meal Edit</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row" >
                                                            <div class="col-xs-6">
                                                                <label for="ex71">price</label>
                                                                <input class="form-control" id="ex71" type="text", name="epr">
                                                                <input name="meal" type="hidden" value=$meal>
                                                                <input name="storename" type="hidden" value=$storename>
                                                            </div>
                                                            <div class="col-xs-6">
                                                                <label for="ex41">quantity</label>
                                                                <input class="form-control" id="ex41" type="text", name="equ">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-seconary" data-dismiss = "modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary" >Edit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <form action ="../shop_page/delete.php" method="post">
                                            <input name="meal" type="hidden" value=$meal>
                                            <input name="storename" type="hidden" value = $storename>   
                                            <td><button type="submit" class="btn btn-danger" id = "del" name'na' onclick="test()">Delete</button></td>
                                        </form>
                                    </div>
                                </tr>
                                EOT;
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="MyOrder" class="tab-pane fade">
        <form method = "post">
            <div class=" row  col-xs-8">
                <label class="control-label col-sm-1" for="distance">Status</label>
                <button type="submit" style="margin-left: 18px;"class="btn btn-primary">Search</button>
                <div class="col-sm-5">
                    <select class="form-control" id="status", name='status'>
                        <option>All</option>
                        <option value="Not Finished">未完成 </option>
                        <option value="Finished">已完成</option>
                        <option value='Canceled'>已取消</option>
                </select>
                </div>
            </div>
        </form>
            <div class="row">
                <div class="  col-xs-16">
                    <table class="table" style=" margin-top: 15px;">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Order ID</th>
                                <th scope="col">Status</th>
                                <th scope="col">Start</th>
                                <th scope="col">End</th>
                                <th scope="col">Shop name</th>
                                <th scope="col">Total Price</th>
                                <th scope="col">Order Details</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

                            if(!isset($_POST['status'])) $status='All';
                            else $status=$_POST['status'];
                            if($status=='All'){
                                $stmt=$conn->prepare("select Count(*) from orders where username=:username");
                                $stmt->execute(array('username' => $_SESSION['username']));
                            }
                            else{
                                $stmt=$conn->prepare("select Count(*) from orders where username=:username and status=:status");
                                $stmt->execute(array('username' => $_SESSION['username'], 'status' => $status));
                            }
                            $a = array();
                            if($stmt->fetchColumn()>0){
                                if($status=='All'){
                                    $stmt=$conn->prepare("select * from orders where username=:username");
                                    $stmt->execute(array('username' => $_SESSION['username']));
                                }
                                else{
                                    $stmt=$conn->prepare("select * from orders where username=:username and status=:status");
                                    $stmt->execute(array('username' => $_SESSION['username'], 'status' => $status));
                                }
                                $result=$stmt->fetchAll();
                                $i=0;

                                foreach ($result as $row){
                                    $i += 1;
                                    $nstatus=$row['status'];
                                    $start = $row['start'];
                                    $end = $row['end'];
                                    $shop = $row['storename'];
                                    $price = $row['price'];
                                    $OID = $row['OID'];
                                    $delievery_fee = $row['delievery_fee'];
                                    $total = $price + $delievery_fee;
                                    array_push($a, array($shop, $OID));
                                    echo '<tr>';

                                    
                                    if($nstatus == "Not Finished")
                                        echo "<td><input type='checkbox' id=$i.$OID name='checkbox1[]' value='$OID'></td>";
                                    else
                                        echo "<td></td>";
                                    echo <<<EOT
                                    <th scope="row">$i</th>
                                    <td>$nstatus</td>
                                    <td>$start</td>
                                    <td>$end</td>
                                    <td>$shop</td>
                                    <td>$total</td>
                                    <td>  <button type="button" class="btn btn-info " data-toggle="modal" data-target="#$shop$OID">Open menu</button></td>
                                    EOT;
                                    if($nstatus=="Not Finished"){
                                        echo <<<EOT
                                        <form action ="../my_order_page/cancel.php" method="post">
                                        <input name="cancel" type="hidden" value=$OID>
                                        <td><input type="submit" class="btn btn-danger" id = "cancel" value="Cancel" onclick="return confirm('Are you sure to cancel the order?');"></td>
                                        </form>
                                        EOT;
                                    }
                                    echo '</tr>';
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <button type="button" class="btn btn-danger" id='cancelbtn' >Cancel selected order</button>
            <script>
                $("#cancelbtn").click(function(){
                    var total_checked = document.querySelectorAll('input[name="checkbox1[]"]:checked');
                    var checked=[];

                    if(total_checked.length == 0) 
                        alert('No order is checked');
                    else{
                        for (var i = 0; i < total_checked.length; i++)
                            checked.push(total_checked[i].value);
                                
                        $.ajax(
                            {
                                type:"POST",
                                url: "../my_order_page/allcancel.php",
                                data: {'OID' : checked},
                                success: function(results){
                                    alert(results);
                                },
                                complete : function(){
                                    window.location.replace("../home_page/home.php");
                                }
                            }
                        );
                    }

                });
            </script>
            
            <?php    
                for($i=0; $i<sizeof($a); $i++){
                    $shop = $a[$i][0];
                    $oid = $a[$i][1];
                    echo <<<EOT
                    <div class="modal fade" id="$shop$oid"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Order</h4>
                            </div>
                            <div class="modal-body">
                            <div class="row">
                            <div class="  col-xs-12">
                                <table class="table" style=" margin-top: 15px;">
                                    <thead>
                                        <tr>
                                            <th scope="col">Picture</th>
                                            <th scope="col">meal name</th>
                                            <th scope="col">price</th>
                                            <th scope="col">Order Quantity</th>
                                        </tr>
                                    </thead>
                    EOT;
                    $stmt=$conn->prepare("select * from order_detail where OID=:oid");
                    $stmt->execute(array('oid' => $oid));
                    $result=$stmt->fetchAll();
                    $sum=0;
                    foreach($result as $srow){
                        $foodname = $srow['foodname'];
                        $price = $srow['price'];
                        $quantity = $srow['amount'];
                        $stmt=$conn->prepare("select picture from dish where dish_name=:food");
                        $stmt->execute(array('food' => $foodname));
                        $result2 = $stmt->fetch();
                        $src = $result2['picture'];
                        $sum += $quantity * $price;
                        echo '<tr>';
                        echo '<td><img src = "data:png;base64, ' . $src . '"/ width="100" height="100" ></td>';
                        echo '<td>'.$foodname.'</td>';
                        echo '<td>'.$price.'</td>';
                        echo '<td>'.$quantity.'</td>';
                        echo '</tr>';
                    }
                    $stmt=$conn->prepare("select delievery_fee from orders where OID=:oid");
                    $stmt->execute(array('oid' => $oid));
                    $fee = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->delievery_fee;
                    $f = $sum + $fee;
                    echo <<<EOT
                                <tbody>
                                </tbody>
                                </table>
                            </div>
                            </div>
                        <div style="text-align:right;"><font size=4>Subtotal: $$sum</font>
                        <br>
                        <font size=2>Delivery fee: $$fee</font>
                        <br><br>
                        <font size=4>Total Price: $$f</font></div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                  EOT;
                }
            ?>
    </div>

    <div id="ShopOrder" class="tab-pane fade">
        Status  
        <select id = "query_status" style = "margin-left : 50px">
            <option> All </option>
            <option> Finished </option>
            <option> Not Finished </option>
            <option> Cancel </option>
        </select>
        <button type="button" id = "status_filter"> search </button>

        <script>
            $("#status_filter").click( function(){
                $.ajax({
                    type: 'GET',
                    url: '../shop_order_page/search_order.php',
                    data: {
                        'status': $("#query_status").val()
                    },
                    success: function(msg){
                        let args = msg.split("|");
                        console.log(args.length);
                        $('#order_result').html(args[0]);
                        $('#order_detail').html(args[1]);
                        $('#scripts').html(args[2]);
                    }
                })
            });
        </script>

        <div style = "width : 150%;" class = "row">
            <div class=" col-xs-8">
                <table class = "table">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">OID</th>    
                            <th scope="col">Status</th>
                            <th scope="col">Start</th>
                            <th scope="col">End</th>
                            <th scope="col">Shop name</th>    
                            <th scope="col">Total Price</th>
                            <th scope="col">Order Detail</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id = "order_result" ></tbody>
                </table>
                <div id = "order_detail"> </div>
                <div id = "scripts"> </div>
            </div>
        </div>
        <button type="button" class="btn btn-danger" id='cancelbtn_shop' >Cancel selected order</button>
        <button type="button" class="btn btn-primary" id='donebtn_shop'> Finish selected order</button>
        <script>
            $("#cancelbtn_shop").click(function(){
                var total_checked = document.querySelectorAll('input[name="checkbox2[]"]:checked');
                var checked=[];

                if(total_checked.length == 0) 
                    alert('No order is checked');
                else{
                    for (var i = 0; i < total_checked.length; i++)
                        checked.push(total_checked[i].value);
                            
                    $.ajax(
                        {
                            type:"POST",
                            url: "../shop_order_page/allcancel.php",
                            data: {'OID' : checked},
                            success : function(msg){
                                alert(msg);
                            },
                            complete : function(){
                                window.location.replace("../home_page/home.php");
                            }
                        }
                    );
                }

            });
        </script>
        <script>
            $("#donebtn_shop").click(function(){
                var total_checked = document.querySelectorAll('input[name="checkbox2[]"]:checked');
                var checked=[];

                if(total_checked.length == 0) 
                    alert('No order is checked');
                else{
                    for (var i = 0; i < total_checked.length; i++)
                        checked.push(total_checked[i].value);
                            
                    $.ajax(
                        {
                            type:"POST",
                            url: "../shop_order_page/alldone.php",
                            data: {'OID' : checked},
                            success : function(msg){
                                alert(msg);
                            },
                            complete : function(){
                                window.location.replace("../home_page/home.php");
                            }
                        }
                    );
                }

            });
        </script>
        
    </div>

    <div id="TransactionRecord" class="tab-pane fade">
        Action
        <select id = "query_action" style = "margin-left : 50px">
            <option> ALL </option>
            <option> PAYMENT </option>
            <option> RECEIVE </option>
            <option> RECHARGE </option>
        </select>
        <button type="button" id = "action_filter"> search </button>
        <script>
            $("#action_filter").click( function(){
                $.ajax({
                    type: 'GET',
                    url: '../transaction_page/search_record.php',
                    data: {
                        'action': $("#query_action").val()
                    },
                    success: function(msg){
                        $("#record_result").html(msg);
                    }
                })
            });
        </script>
        <div style = "width : 150%;" class = "row">
            <div class=" col-xs-8">
                <table class = "table">
                    <thead>
                        <tr>
                            <th scope="col">RID</th>    
                            <th scope="col">Action</th>
                            <th scope="col">Time</th>
                            <th scope="col">Trader</th>
                            <th scope="col">$$</th>
                        </tr>
                    </thead>
                    <tbody id = "record_result" ></tbody>
                </table>
            </div>
        </div>
    </div>



<script>
    $(document).ready(function () {
    $(".nav-tabs a").click(function () {
        $(this).tab('show');
    });
    });
</script>


</html>