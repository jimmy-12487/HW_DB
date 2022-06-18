<?php

    session_start();
    $storename =  $_GET['storename'];
    $username = $_SESSION['username'];

    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $delivery_fee = 0;
    if($_GET['type'] == "Delivery")
        $delivery_fee += 19;

    $stmt = $conn->prepare("SELECT wallet_balance FROM users WHERE username = '$username'");
    $stmt->execute();
    $wallet_balance = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->wallet_balance;

    $stmt = $conn->prepare("SELECT dish_name
                            FROM dish
                            WHERE name = '$storename'");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_CLASS);

    $stmt = $conn->prepare("SELECT max(OID) as new_OID
                            FROM orders");
    $stmt->execute();
    $OID = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->new_OID + 1;

    $total = 0;

    try{
        foreach($rows as $row){
            $dish_name = str_replace(" ", "_", $row->dish_name);
            $amount = $_GET["$dish_name"];
            if(!is_numeric($amount)){
                throw new Exception("NOT A NUMBER");
            }
            if ($amount > $_GET["$dish_name" . "_amount"]){
                throw new Exception(str_replace("_", " ", $dish_name) . " max amount exceeded");
            }
            else if ($amount < 0)
                throw new Exception("Should be positive");
            else if (!ctype_digit($amount))
                throw new Exception("Not a integer");

            
            $dish_name = str_replace(" ", "_", $row->dish_name);
            $amount = $_GET["$dish_name"];
            $price = $_GET["$dish_name" . "_price"];
            if($amount > 0)
                $total += $price * $amount;
            
        }
        if($wallet_balance < $total)
            throw new Exception("your wallet balance : $wallet_balance less than total_money : $total");
        $total_price = $total + $delivery_fee;
        echo <<<EOT
            Subtotal : $total <br>
            Delivery fee : $delivery_fee <br>
            Total Price : $total_price <br>
        EOT;
    }
    catch (Exception $e){
        $msg=$e->getMessage();
        echo '*' . $msg;
    }
?>
