<?php

    SESSION_START();
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
    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt=$conn->prepare("delete from dish where dish_name=:dish_name and name = :storename");
    $stmt->execute(array('dish_name' => $_POST['meal'], 'storename' => $_POST['storename']));

    $temp=$_POST['meal'];
    echo <<<EOT
    <!DOCTYPE html>
    <html>
        <body>
            <script>
            alert("$temp has been deleted.");
            window.location.replace("../home_page/home.php");
            </script> 
        </body> 
    </html>
    EOT;
    exit()
?>