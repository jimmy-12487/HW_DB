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
    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    $OID = $_GET['OID'];
    $username = $_SESSION['username'];
    $account = $_SESSION['account'];
    
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT status FROM orders WHERE OID = $OID");
    $stmt->execute();
    if($stmt->fetchAll(PDO::FETCH_CLASS)[0]->status != 'Not Finished'){
        echo "FAILED";
        exit();
    }


    $stmt = $conn->prepare("UPDATE orders 
                            SET status = 'Cancel' 
                            WHERE OID = $OID");
    $stmt->execute();

    $stmt = $conn->prepare("SELECT price, storename
                            FROM orders
                            WHERE OID = $OID");
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_CLASS);
    $price = $row[0]->price;
    $storename = $row[0]->storename;

    $stmt = $conn->prepare("UPDATE users
                            SET wallet_balance = wallet_balance + $price
                            WHERE username = '$username' AND
                                  account = '$account'");
    $stmt->execute();

    $stmt = $conn->prepare("SELECT username, account
                            FROM store
                            WHERE name = '$storename'
                            ");
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_CLASS);
    $owner_username = $row[0]->username;
    $owner_account = $row[0]->account;

    $date = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("UPDATE orders SET end = '$date' WHERE OID = $OID");
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE users 
                            SET wallet_balance = wallet_balance - $price
                            WHERE username = '$owner_username' AND
                                  account = '$owner_account'
    ");
    $stmt->execute();
    echo "Cancel";
?>
