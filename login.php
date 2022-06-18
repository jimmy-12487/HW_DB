<?php
    session_start();
    $_SESSION['Auth'] = false;

    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

    try{
        if(!isset($_POST['Account']) || !isset($_POST['Password'])){
            header("Location: ./index.php");
            exit();
        }
        if(empty($_POST['Account']) || empty($_POST['Password']))
            throw new Exception("username and password cannot be empty!");
        

        $account = $_POST['Account'];
        $password = $_POST['Password'];
        $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select account, password, salt, username, PhoneNumber, ST_AsText(Location) AS Location, wallet_balance from users where account =:account");
        $stmt->execute(array('account' => $account));
        if($stmt->rowCount() == 1){
            $row = $stmt->fetch();
            if ($row['password'] == hash('sha256', $row['salt'].$password)){
                $_SESSION['Auth'] = true;
                $_SESSION['account'] = $row['account'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['phonenumber'] = $row['PhoneNumber'];
                $_SESSION['location'] = $row['Location'];
                $_SESSION['balance'] = $row['wallet_balance'];

                $stmt = $conn->prepare("SELECT name, ST_AsText(location) as new_location, category FROM store WHERE account = :account");
                $stmt->execute(array('account' => $account));
                if($stmt->rowCount() == 1){
                    $row = $stmt->fetch();
                    $_SESSION['storename'] = $row['name'];
                    $_SESSION['storelocation'] = str_replace('POINT', "", $row['new_location']);
                    $_SESSION['storecategory'] = $row['category'];
                }
                else
                    $_SESSION['storename'] = NULL;
                header("Location: ./home/home_page/home.php");
                exit();
            }
            else{
                throw new Exception("username or password incorrect.");
            }
        }
        else
            throw new Exception("Account does not exist.");
    }
    catch(Exception $e){
        $msg = $e->getMessage();
        session_unset();
        session_destroy();
        echo <<< EOT
        <!DOCTYPE html>
        <html>
            <head>
                <title> redirecting </title>
                <link href="css/style.css" rel="stylesheet" type="text/css">
            </head>
            <body>
                <h1 align = "center"> Fail? </h1>
                <script>
                    alert("$msg");
                    window.location.replace("./index.php");
                </script>
            </body>
        </html>
        EOT;
    }

?>