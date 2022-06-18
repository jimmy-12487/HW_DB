<?php
    session_start();
    if(!isset($_SESSION['Auth']))
        $auth = false;
    if($_SESSION['Auth'] == false)
        $auth = false;

    if($auth == false){
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
    
    $status = $_GET['status'];

    $result = "";
    
    try {
        if(empty($_SESSION['storename']) || !isset($_SESSION['storename']))
            throw new Exception("Run a bussiness first!");

    $storename = $_SESSION['storename'];
    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if($status == "All")
        $stmt = $conn->prepare("SELECT * FROM orders WHERE storename = '$storename'");
    else if ($status == "Finished")
        $stmt = $conn->prepare("SELECT * FROM orders WHERE status = 'Finished' and storename = '$storename'");
    else if ($status == "Not Finished") 
        $stmt = $conn->prepare("SELECT * FROM orders WHERE status = 'Not Finished' and storename = '$storename'");
    else if ($status == "Cancel")
        $stmt = $conn->prepare("SELECT * FROM orders WHERE status = 'Cancel' and storename = '$storename'");
     $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_CLASS);
    

    $result = show_order($rows, $result) . "|";
    $result = show_detail($rows, $result, $conn) . "|";
    $result = show_button($rows, $result);
    
    echo $result;

    }

    catch(Exception $e){
        $msg=$e->getMessage();
        echo <<<EOT
        <!DOCTYPE html>
        <html>
            <body>
                <script>
                alert("$msg");
                window.location.replace("../home_page/home.php");
                </script>
            </body>
        </html>
        EOT;
    }

    function show_order($rows, $result){
        foreach ($rows as $row) {
            $result = $result . '
                <tr>
                    <td> ' . $row->OID .  '</td>
                    <td> ' . $row->status .'</td>
                    <td> ' . $row->start .'</td>
                    <td> ' . $row->end .  '</td>
                    <td> ' . $row->storename.' </td>
                    <td> ' . $row->price .'</td>
                    <td> <button type = "button" class="btn btn-info " data-toggle="modal" data-target="#order' . $row->OID. '"> details</button></td>
                ';
            if ($row->status == "Not Finished"){
                $result = $result .'<td> <button class="btn btn-primary" style = "height: 35px; weight: 70px;" type = "button" id = "done' . $row->OID . '">done 
                            <button class="btn btn-danger " style = "height: 35px; weight: 70px;" type = "button"  id = "cancel' . $row->OID . '">cancel </td>';
            }
            else
                $result = $result . '<td></td>';
            
            $result = $result . '</tr>';
        }
        return $result;
    }

    function show_detail($rows, $result, $conn){
        foreach ($rows as $row) {
            $total = 0;
            $delivery_free = 19;
            $result = $result . '
                <div class = "modal fade" id = "order' . $row->OID . '" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"> Detail </h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class = "col-xs-12">
                                        <table class="table" style=" margin-top: 15px">
                                            <thead>
                                                <tr>
                                                    <th scope="col"> Picture </th>
                                                    <th scope="col"> Meal name </th>
                                                    <th scope="col"> price </th>
                                                    <th scope="col"> amount </th>
                                                    <th scope="col"> store name </th>
                                                    <th scope="col"> total </th>
                                                </tr>
                                            </thead>
                                            <tbody>';
            $stmt = $conn->prepare("SELECT * FROM order_detail WHERE OID = $row->OID");
            $stmt->execute();
            $detail_rows = $stmt->fetchAll(PDO::FETCH_CLASS);
    
            foreach($detail_rows as $detail_row){
                $stmt = $conn->prepare("SELECT picture FROM dish WHERE name = '$row->storename' AND dish_name = '$detail_row->foodname' ");
                $stmt->execute();
                $tmp = $stmt->fetchAll(PDO::FETCH_CLASS);
                $picture = $tmp[0]->picture;
                $result = $result . '
                                            <tr>
                                                <td><img src = "data:png;base64, ' . $picture . '"/ width="100" height="100" ></td>
                                                <td>' . $detail_row->foodname . '</td>
                                                <td>' . $detail_row->price . '</td>
                                                <td>' . $detail_row->amount . '</td>
                                                <td>' . $row->storename . '</td>
                                                <td>' . $detail_row->price * $detail_row->amount . '</td>
                                            </td>';
                $total += $detail_row->price * $detail_row->amount;
            }
    
            $result = $result               .'</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer"> 
                                subtotal : $ ' . $total . ' <br>
                                delivery free : $' . $delivery_free . ' <br>
                                total price : $ ' . $total + $delivery_free . ' <br>
                                <button type="button" class="btn btn-default" data-dismiss="modal"> Cancel </button>
                            </div>
                        </div>
                    </div>
                </div>';   
        }
        return $result;
    }

    function show_button($rows, $result){
        foreach ($rows as $row) { 
            if ($row->status == "Not Finished"){
                $result = $result . '
                    <script>
                        $(document).ready(function () {
                            $("#done' . $row->OID . '").click( function() {
                                let OID = ' . $row->OID . ';
                                console.log("BEFORE AJAX");
                                $.ajax({
                                    url : "../shop_order_page/done.php",
                                    type : "GET",
                                    data : {
                                        "OID" : OID
                                    },
                                    success : function(msg){
                                        alert(msg);
                                    },
                                    complete : function(){
                                        window.location.replace("../home_page/home.php");
                                    }
                                })     
                            })
                        });
                        $(document).ready(function () {
                            $("#cancel' . $row->OID . '").click( function() {
                                let OID = ' . $row->OID . ';
                                console.log("BEFORE AJAX");
                                $.ajax({
                                    url : "../shop_order_page/cancel.php",
                                    type : "GET",
                                    data : {
                                        "OID" : OID
                                    },
                                    success : function(msg){
                                        alert(msg);
                                    },
                                    complete : function(){
                                        window.location.replace("../home_page/home.php");
                                    }
                                })     
                            })
                        });
                    </script>
                ';
            }
       }
       return $result;
    }
?>