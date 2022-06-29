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
    $action = $_GET['action'];
    $username = $_SESSION['username'];
    $storename = $_SESSION['storename'];
    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if($action == "ALL")
        $stmt = $conn->prepare("SELECT * FROM transaction_record WHERE username = '$username'");
    else 
        $stmt = $conn->prepare("SELECT * FROM transaction_record WHERE username = '$username' AND action = '$action'");

    $result = "";
    
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_CLASS);

    foreach($rows as $row){
        $result = $result . '
            <tr>
                <td> ' . $row->RID . '</td>
                <td> ' . $row->action . '</td>
                <td> ' . $row->date . '</td>
                <td> ' . $row->trader . '</td>
                <td> ' . $row->amount . '</td>
            </tr>
        ';
    }
    echo $result;
?>
