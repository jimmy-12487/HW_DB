<?php

    session_start();
    echo <<< EOT
        <!doctype html>
        <html>
            <head>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
            </head>
        </html>
    EOT;

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

    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $name_set = $_GET['name_set'];
    $name_arr = explode(",", str_replace('(', "", str_replace(')', "", $name_set)));
    $show = '';

    for($i = 0; $i < count($name_arr); $i++){
        $stmt = $conn->prepare("SELECT name, dish_name, price, amount, picture FROM dish where name = " . $name_arr[$i]);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_CLASS);

        $tmp_dish_name = get_attr_arr($row, "dish_name");
        $tmp_price = get_attr_arr($row, "price");
        $tmp_amount = get_attr_arr($row, "amount");
        $tmp_picture = get_attr_arr($row, 'picture');

        $show = paste($show, $name_arr[$i], $tmp_dish_name, $tmp_price, $tmp_amount, $tmp_picture);
    }


    echo $show;

    function get_attr_arr($arr, $attr){
        $name = array();
        for($i = 0; $i < count($arr); $i++)
            $name[] = $arr[$i]->$attr;
        return $name;
    }

    function paste($show, $name, $dish, $price, $amount, $picture){
        $name = str_replace("'", "", $name);
        $show = $show . '<div class="modal fade" id = "' . $name . '" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">menu</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class = "col-xs-12">
                                                    <table class="table" style=" margin-top: 15px;">
                                                        <thead>
                                                             <select class="form-control" id = "' . $name . 'sel">
                                                                <option>Pick-up</option>
                                                                <option>Delivery</option>
                                                            </select>
                                                            <tr>
                                                                <th scope="col">#</th>
                                                                <th scope="col">Picture</th>
                                                                <th scope="col">meal name</th>
                                                                <th scope="col">price</th>
                                                                <th scope="col">Quantity</th>
                                                                <th scope="col">amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>';

        for($i = 0; $i < count($dish); $i++){
            $space_name = $dish[$i];
            $dish[$i] = str_replace(" ", "_", $dish[$i]);
            $show = $show .                             
                                                            '<tr>' .
                                                                '<th scope = "row">' . ($i + 1) . '</th>' .
                                                                '<td><img src = "data:png;base64, ' . $picture[$i] . '"/ width="100" height="100" ></td>' . 
                                                                '<td>' . $space_name . '</td>' .
                                                                '<td>' . $price[$i] . '</td>' . 
                                                                '<td>' . $amount[$i] . '</td>'. 
                                                                '<td> <button id = "' . $name . '_'. $dish[$i] .'_minus" > - </button>
                                                                      <input type ="text" style = "height : 30px; width : 30px" id = "' . $name . $dish[$i] . '" value = "0"/> 
                                                                      <button id = "' . $name . '_'. $dish[$i] .'_plus"> + </button></td>' .
                                                            '</tr>                                                            <script>
                                                                $(document).ready( function(){
                                                                    $("#' . $name . '_'. $dish[$i] .'_minus").click( function(){
                                                                        if($("#'. $name . $dish[$i] . '").val() > 0){
                                                                            $("#'. $name . $dish[$i] . '").attr({
                                                                                "value" : $("#'. $name . $dish[$i] . '").val() - 1
                                                                            });
                                                                        }
                                                                    })
                                                                    $("#' . $name . '_'. $dish[$i] .'_plus").click( function(){
                                                                        $("#'. $name . $dish[$i] . '").attr({
                                                                            "value" : parseInt($("#'. $name . $dish[$i] . '").val()) + 1
                                                                        });
                                                                    })
                                                                });
                                                            </script>';                                                           
        }

        $ajax_data = "";
        for($i = 0; $i < count($dish); $i++){
            $dish_name = '"' . $dish[$i] . '"';
            $dish_price = '"' . $dish[$i] . '_price"';
            $dish_amount = '"' . $dish[$i] . '_amount"';
            $id = '$(' . "'#" . $name . $dish[$i] . "'" . ').val(),';

            $ajax_data = $ajax_data . $dish_name .  ' : ' . $id;
            $ajax_data = $ajax_data . $dish_price. ' : '. $price[$i] . ',';
            $ajax_data = $ajax_data . $dish_amount. ' : '. $amount[$i] . ',';
        }
        $show = $show .                                 '</tbody> 
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" id = ' . $name . '_footer> 
                                            <button type="button" class="btn btn-default" id = "' . $name . '_ORDER_preview" >Order</button>
                                        </div>
                                        <script id = ' . $name . '_script>
                                            $(document).ready( function(){
                                                $("#' . $name . '_ORDER_preview").click( function() {
                                                    console.log("preview");
                                                    $.ajax({
                                                        url : "./order_preview.php",
                                                        type : "GET",
                                                        data : {
                                                            "type" : $("#' . $name . 'sel").val(),
                                                            "storename" :' . '"' . $name . '",' .
                                                            $ajax_data .'
                                                        },
                                                        success : function(msg){
                                                            if(msg[0] != "*"){
                                                                $("#'. $name . '_footer").html( msg + `<button type="button" class="btn btn-default" data-dismiss="modal" id = "' . $name . '_ORDER">CHECK</button>`);
                                                                $("#'. $name . '_script").html(
                                                                    $(document).ready( function(){
                                                                        $("#'. $name . '_ORDER").click( function() {
                                                                            $.ajax({
                                                                                url : "./order.php",
                                                                                type : "GET",
                                                                                data : {
                                                                                    "type" : $("#' . $name . 'sel").val(),
                                                                                    "storename" :' . '"' . $name . '",' .
                                                                                    $ajax_data .'
                                                                                },
                                                                                success : function(msg){
                                                                                    alert(msg);
                                                                                    window.location.replace("./home.php");
                                                                                }
                                                                            })
                                                                        })
                                                                    })
                                                                );
                                                            }
                                                            else
                                                                alert(msg);
                                                        }
                                                    })
                                                })
                                                
                                            });
                                        </script>
                                    </div>
                                </div> 
                            </div>';
        return $show;
    }
?>