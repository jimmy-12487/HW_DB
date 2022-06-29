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

    $dbservername='localhost';
    $dbname='examdb';
    $dbusername='examdb';
    $dbpassword='';
    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $storename = $_POST['storename'];
    $dish_name = $_POST['meal'];
    $stmt = $conn->prepare("SELECT OID FROM orders WHERE storename = '$storename' AND status = 'Not Finished'");
    $stmt->execute();

    $OIDS = $stmt->fetchAll(PDO::FETCH_CLASS);

    foreach ($OIDS as $OID){
        $tmp_oid = $OID->OID;
        $stmt = $conn->prepare("SELECT * FROM order_detail WHERE OID = $tmp_oid AND foodname = '$dish_name'");
        $stmt->execute();
        if(count($stmt->fetchAll()) != 0){
            echo <<<EOT
            <!DOCTYPE html>
            <html>
                <body>
                    <script>
                    alert("Deletion failed");
                    window.location.replace("../home_page/home.php");
                    </script> 
                </body> 
            </html>
            EOT;
            exit();
        }
    }

    
    $stmt=$conn->prepare("delete from dish where dish_name=:dish_name and name = :storename");
    $stmt->execute(array('dish_name' => $_POST['meal'], 'storename' => $_POST['storename']));

    echo <<<EOT
    <!DOCTYPE html>
    <html>
        <body>
            <script>
            alert("$dish_name has been deleted.");
            window.location.replace("../home_page/home.php");
            </script> 
        </body> 
    </html>
    EOT;
    exit();
    
?>