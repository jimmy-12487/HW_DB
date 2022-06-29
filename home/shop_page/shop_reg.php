<?php
    $dbservername = 'localhost';
    $dbname = 'examdb';
    $dbusername = 'examdb';
    $dbpassword = '';

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

try{
    if (empty($_POST['sname']) || empty($_POST['scat']) || is_null($_POST['sla']) || is_null($_POST['slo']))
        throw new Exception('欄位空白');
    if ($_POST['slo']<-180 || $_POST['slo']>180)
        throw new Exception('wrong longtitude');
    if ($_POST['sla']<-90 || $_POST['sla']>90)
        throw new Exception('wrong latitude');
    $sname=$_POST['sname'];
    $scat=$_POST['scat'];
    $sla=$_POST['sla'];
    $slo=$_POST['slo'];
    $account = $_POST['account'];

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt=$conn->prepare("select name from store where name =:storename");
    $stmt->execute(array('storename' => $sname));


    if ($stmt->rowCount()==0){
        $stmt=$conn->prepare("insert into store (name, location, category, account) values (:sname, ST_GeomFromText(:pos,2154), :scat, :account)");
        $stmt->execute(array('sname' => $sname, 'pos' => "POINT($slo $sla)", 'scat' => $scat, 'account' => $account));
        $_SESSION['sname']= $sname;
        $_SESSION['scat'] = $scat;
        $_SESSION['slo'] = $slo;
        $_SESSION['sla'] = $sla;
        echo <<<EOT
        <!DOCTYPE html>
        <html>
            <body>
                <script>
                alert("You've registered a shop successfully.");
                window.location.replace("../home_page/home.php");
                </script> 
            </body> 
        </html>
        EOT;
        exit();
    }
    else 
        throw new Exception('shop name has been used');
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