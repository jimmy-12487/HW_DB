<?php
session_start();
$dbservername = 'localhost';
$dbname = 'examdb';
$dbusername = 'examdb';
$dbpassword = '';
$conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try{
    $oid = $_POST['cancel'];
    $stmt=$conn->prepare("select status from orders where OID=:oid");
    $stmt->execute(array('oid' => $oid));
    $row = $stmt->fetch();
    if($row['status']=='Not Finished'){
        $stmt=$conn->prepare("Update orders set status='Cancel' where OID=:oid");
        $stmt->execute(array('oid' => $oid));
        $stmt=$conn->prepare("select * from order_detail where OID=:oid");
        $stmt->execute(array('oid' => $oid));

        if ($stmt->rowCount()>0){
            $result=$stmt->fetchAll();
            foreach($result as $row){   
                $foodname = $row['foodname'];
                $quan = $row['amount'];
                $stmt=$conn->prepare("select amount from dish where dish_name=:food");
                $stmt->execute(array('food' => $foodname));
                $ori = $stmt->fetch();
                $ori_quan = $ori[0];
                $new_quan=$ori_quan+$quan;

                $stmt=$conn->prepare("Update dish set amount=:quan where dish_name=:food");
                $stmt->execute(array('quan' => $new_quan, 'food' => $foodname));
            }
        }
        //users得到退費

        $stmt=$conn->prepare("select username, storename, price, delievery_fee from orders where OID=:oid");
        $stmt->execute(array('oid' => $oid));
        $result=$stmt->fetch();
        $username=$result[0];
        $shop=$result[1];
        $price=$result[2];
        $fee = $result[3];
        $stmt=$conn->prepare("select wallet_balance from users where username=:username");
        $stmt->execute(array('username' => $username));
        $wallet=$stmt->fetch();
        $stmt=$conn->prepare("update users set wallet_balance=:num where username=:username");
        $stmt->execute(array('num' => $wallet[0] + $price + $fee, 'username' => $username));
        //店長退費
        
        $stmt=$conn->prepare("select account from store where name=:shopname");
        $stmt->execute(array('shopname' => $shop));
        $r=$stmt->fetch();
        $account=$r[0];
        $stmt=$conn->prepare("select wallet_balance, username from users where account=:account");
        $stmt->execute(array('account' => $account));
        $r=$stmt->fetch();
        $wallet=$r[0];
        $shopusername=$r[1];
        $stmt=$conn->prepare("update users set wallet_balance=:num where account=:account");
        $stmt->execute(array('num' => $wallet - $price - $fee, 'account' => $account));

        //transaction record(user)

        $stmt=$conn->prepare("select max(RID) from transaction_record");
        $stmt->execute();
        $r=$stmt->fetch();
        $rid = $r[0];
        if($rid==null)
            $rid=1;
        else $rid+=1;
        $date = date("Y-m-d H:i");
        $total = $price + $fee;
        $stmt=$conn->prepare("insert into transaction_record (RID, action, amount, date, trader, username) values (:RID, :action, :amount, :date, :trader, :username)");
        $stmt->execute(array('RID' => $rid, 'action' => "Received", 'amount'=> "+".$total, 'date' => $date, 'trader' => $shop, 'username' => $username));

        //transaction record(store)

        $stmt=$conn->prepare("select max(RID) from transaction_record");
        $stmt->execute();
        $rid=$stmt->fetch()[0];
        if($rid==null)
            $rid=1;
        else $rid+=1;
        $date = date("Y-m-d H:i:s");
        $stmt=$conn->prepare("insert into transaction_record (RID, action, amount, date, trader, username) values (:RID, :action, :amount, :date, :trader, :username)");
        $stmt->execute(array('RID' => $rid, 'action' => "Payment", 'amount'=> -$total, 'date' => $date, 'trader' => $username, 'username' => $shopusername));

        $stmt = $conn->prepare("UPDATE orders SET end = '$date' WHERE OID = $oid");
        $stmt->execute();

        echo <<<EOT
            <!DOCTYPE html>
            <html>
                <body>
                    <script>
                    alert("The order has been canceled");
                    window.location.replace("../home_page/home.php");
                    </script> 
                </body> 
            </html>
            EOT;
            exit();
    }
    else{
        throw new Exception('Cancellation Failure.');
    }
    
}

catch(Exception $e)
{
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