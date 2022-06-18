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
    $dbservername='localhost';
    $dbname='examdb';
    $dbusername='examdb';
    $dbpassword='';
    try{
        if (empty($_POST['epr']) and $_POST['epr']!=0)
            throw new Exception('price is empty');
        if ($_POST['epr']<0)
            throw new Exception('price should >=0');
        if (empty($_POST['equ']) and $_POST['equ']!=0)
            throw new Exception('quantity is empty');
        if ($_POST['equ']<0)
            throw new Exception('quantity should >=0');
        $price = $_POST['epr'];
        $quan = $_POST['equ'];

        $conn = new PDO("mysql:host = $dbservername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt=$conn->prepare("UPDATE dish SET price=:price, amount=:quan WHERE dish_name= :dish_name and name = :storename");
        $stmt->execute(array('price' => $price, 'quan' => $quan, 'dish_name' => $_POST['meal'], 'storename' => $_POST['storename']));
        

        echo <<<EOT
        <!DOCTYPE html>
        <html>
            <body>
                <script>
                alert("edit successfully.");
                window.location.replace("../home_page/home.php");
                </script> 
            </body> 
        </html>
        EOT;
        exit();
        
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
?>