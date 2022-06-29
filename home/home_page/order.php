<?php
    session_start();
    $storename =  $_GET['storename'];
    $username = $_SESSION['username'];

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

    $stmt = $conn->prepare("SELECT location FROM store where name = '$storename'");
    $stmt->execute();
    $store_location = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->location;
    
    $stmt = $conn->prepare("SELECT wallet_balance, ST_Distance_Sphere( '$store_location', Location) as distance FROM users WHERE username = '$username'");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_CLASS);
    $wallet_balance = $rows[0]->wallet_balance;
    $distance =  $rows[0]->distance;

    $stmt = $conn->prepare("SELECT dish_name
                            FROM dish
                            WHERE name = '$storename'");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_CLASS);

    $stmt = $conn->prepare("SELECT max(OID) as new_OID
                            FROM orders");
    $stmt->execute();
    $OID = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->new_OID + 1;

    $date = date("y-m-d");

    $total = 0;

    $delivery_fee = 0;

    if($_GET['type'] == 'Delivery'){
        $delivery_fee = round($distance / 1000);
        if($delivery_fee < 10)
            $delivery_fee = 10;
    }
    
    $total += $delivery_fee;
    
    try {
        foreach($rows as $row){
            $dish_name = str_replace(" ", "_", $row->dish_name);
            $amount = $_GET["$dish_name"];
            if(!is_numeric($amount)){
                throw new Exception("NOT A NUMBER");
            }
            if ($amount > $_GET["$dish_name" . "_amount"]){
                throw new Exception("max amount exceeded");
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

        foreach($rows as $row){
            $dish_name = str_replace(" ", "_", $row->dish_name);
            $amount = $_GET["$dish_name"];
            $price = $_GET["$dish_name" . "_price"];
            if($amount > 0){
                $dish_name = str_replace(" ", "_", $row->dish_name);
                $amount = $_GET["$dish_name"];
                $price = $_GET["$dish_name" . "_price"];
                $stmt = $conn->prepare("INSERT INTO order_detail(OID, foodname, price, amount)
                                        VALUES($OID, '$dish_name', $price, $amount)");
                $stmt->execute();
                $stmt = $conn->prepare("UPDATE dish SET amount = amount - $amount WHERE dish_name = '$dish_name' AND name = '$storename'");
                $stmt->execute();
            }
        }
        $stmt = $conn->prepare("INSERT INTO orders(status, start, storename, price, username, OID, delievery_fee) 
                                VALUES('Not Finished', '$date', '$storename', $total, '$username', $OID, $delivery_fee)");
        $stmt->execute();


        $stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance - $total WHERE username = '$username'");
        $stmt->execute();

        $stmt = $conn->prepare("SELECT username FROM store WHERE name = '$storename'");
        $stmt->execute();
        $owner_username = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->username;
        $stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + $total WHERE username = '$owner_username'");
        $stmt->execute();

        $recode_time = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("SELECT max(RID) + 1 as new_RID FROM transaction_record");
        $stmt->execute();
        $RID = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->new_RID;
        if(is_null($RID))
            $RID = 0;
        $stmt = $conn->prepare("SELECT username FROM store WHERE name = '$storename'");
        $stmt->execute();
        $store_username = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->username;
    
        $stmt = $conn->prepare("INSERT INTO transaction_record(action, date, trader, username, amount, RID)
                                VALUES ('RECEIVE', '$recode_time', '$username', '$store_username', $total, $RID )");
        $stmt->execute();

        $RID += 1;
        $total = -$total;
        $stmt = $conn->prepare("INSERT INTO transaction_record(RID, action, date, trader, username, amount)
                                VALUES ($RID, 'PAYMENT', '$recode_time', '$storename', '$username', $total)");
        $stmt->execute();
        echo "successfully order";
    }
    catch(Exception $e){
        $msg=$e->getMessage();
        echo $msg;
    }
?>