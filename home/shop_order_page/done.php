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
    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    $OID = $_GET['OID'];
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

    $stmt = $conn->prepare("UPDATE orders 
                            SET status = 'Finished' 
                            WHERE OID = $OID");
    $stmt->execute();
    echo "Finish!";
?>
