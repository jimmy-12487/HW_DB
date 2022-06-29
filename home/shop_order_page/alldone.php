<?php
    SESSION_START();
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
    $OIDS = $_POST['OID'];
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($OIDS as $OID){
        $stmt = $conn->prepare("SELECT status FROM orders WHERE OID = $OID");
        $stmt->execute();
        if($stmt->fetchAll(PDO::FETCH_CLASS)[0]->status != "Not Finished"){
            echo 'FAILED';
            exit();
        }
    }

    foreach($OIDS as $OID){

        $stmt = $conn->prepare("SELECT status FROM orders WHERE OID = $OID");
        $stmt->execute();

        $date = date("Y-m-d H:i:s");
        $stmt = $conn->prepare("UPDATE orders SET end = '$date' WHERE OID = $OID");
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE orders 
                                SET status = 'Finished' 
                                WHERE OID = $OID");
        $stmt->execute();
    }
    echo 'success';
?>
