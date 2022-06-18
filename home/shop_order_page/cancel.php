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
    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    $OID = $_GET['OID'];
    $username = $_SESSION['username'];
    $account = $_SESSION['account'];
    
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("UPDATE orders 
                            SET status = 'Cancel' 
                            WHERE OID = $OID");
    $stmt->execute();

    $stmt = $conn->prepare("SELECT price
                            FROM orders
                            WHERE OID = $OID");
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_CLASS);

    $price = $row[0]->price;

    $stmt = $conn->prepare("UPDATE users
                            SET wallet_balance = wallet_balance + $price
                            WHERE username = '$username' AND
                                  account = '$account'");
    $stmt->execute();

    echo "Cancel";
?>
