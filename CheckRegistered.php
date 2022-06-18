<?php

    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    $account = $_GET['account'];

    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("select account from users where account =:account");
    $stmt->execute(array('account' => $account));

    if($stmt->rowCount() != 0)
        echo "registered!";
    else
        echo "OK";
?>