<?php
    session_start();
    $username = $_SESSION['username'];
    $account = $_SESSION['account'];

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

    try{

        $add_value = $_GET['add_value'];

        if(!ctype_digit($add_value))
            throw new Exception("must be a positive integer");
        

        $dbservername = 'localhost';
        $dbname = 'examdb';
        $dbusername = 'examdb';
        $dbpassword = '';

        $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $recode_time = date("Y-m-d H:i:s");
        $stmt = $conn->prepare("SELECT max(RID) + 1 as new_RID FROM transaction_record");
        $stmt->execute();
        $RID = $stmt->fetchAll(PDO::FETCH_CLASS)[0]->new_RID;
        if(is_null($RID))
            $RID = 0;

        $stmt = $conn->prepare("UPDATE users 
                                SET wallet_balance = wallet_balance + $add_value
                                WHERE account = '$account' AND username = '$username'");
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO transaction_record(action, date, trader, username, amount, RID)
                                    VALUES ('RECHARGE', '$recode_time', '$username', '$username', $add_value, $RID )");
        $stmt->execute();
        echo "recharge success!";
    }

    catch (Exception $e){
        $msg = $e->getMessage();
        echo $msg;
    }
?>