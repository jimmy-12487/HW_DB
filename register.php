<?php
    session_start();

    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';
    
    try{

        $account = $_POST['Account'];
        $username = $_POST['Username'];
        $phonenumber = $_POST['Phonenumber'];
        $password = $_POST['Password'];
        $retypepassword = $_POST['RetypePassword'];
        $longitude = $_POST['Longitude'];
        $latitude = $_POST['Latitude'];

        if(is_null($account)) 
            throw new Exception("account cannot be empty");
        if(is_null($username)) throw new Exception("username cannot be empty");
        if(is_null($phonenumber)) throw new Exception("phonenumber cannot be empty");
        if(is_null($password)) throw new Exception("password cannot be empty");
        if(is_null($retypepassword)) throw new Exception('retype password');
        if(is_null($longitude)) throw new Exception("longitude cannot be empty");
        if(is_null($latitude)) throw new Exception("latitude cannot be empty");

        if(strlen($username) > 20) throw new Exception("username must be less than 20 characters");
        if(substr_count($username, ' ') > 1) throw new Exception("username can only contain at most 1 space");
        if(!ctype_alpha(str_replace(' ', '', $username))) throw new Exception("username can only contain a-z, A-Z");
        if(!ctype_alnum($account)) throw new Exception("account can only contain 0-9, a-z, A-Z");  
        if(!ctype_alnum($password)) throw new Exception("password can only contain 0-9, a-z, A-Z");
        if($password != $retypepassword) throw new Exception("Password confirmation not pass");
        if(strlen($phonenumber) != 10) throw new Exception("phone number does not meet requirement");
        if($longitude < -180 || $longitude > 180) throw new Exception("longitude error");
        if($latitude < -90 || $latitude > 90) throw new Exception("latitude error");

        $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("select username from users where username =:username or account =:account");
        $stmt->execute(array('username' => $username, 'account' => $account));

        if($stmt->rowCount() == 0){
            $salt = strval(rand(1000, 9999));
            $hashvalue = hash('sha256', $salt.$password);
            $stmt = $conn->prepare("insert into users(username, password, salt, account, PhoneNumber, Location) values(:username, :password, :salt, :account, :PhoneNumber, ST_GeomFromText(:Location,2154))");
            $stmt->execute(array('username' => $username, 'password' => $hashvalue, 'salt' => $salt,
                                'account' => $account, 'PhoneNumber' => $phonenumber, 'Location' => "POINT($longitude $latitude)"));
            $_SESSION['username'] = $username;
            echo <<< EOT
                <!DOCTYPE html>
                <html>
                    <body>
                        <script>
                            alert("account created successfully!");
                            window.location.replace("./index.php");
                        </script>
                    </body>
                </html>
            EOT;
            exit();
        }
        else
            throw new Exception("Account already exists!");
    }
    catch(Exception $e){
        $msg = $e->getMessage();
        session_unset();
        session_destroy();

        echo <<< EOT
            <!DOCTYPE html>
            <html>
                <body>
                    <script>
                        alert("$msg");
                        window.location.replace("./register_page.php");
                    </script>
                </body>
            </html>
        EOT;
        exit();
    }
?>