<?php
session_start();
$dbservername='localhost';
$dbname='examdb';
$dbusername='examdb';
$dbpassword='';
try
{
    if (is_null($_SESSION['storename']))
        throw new Exception('You have not start a business');
    if (empty($_POST['mname']) and $_POST['mname']!=0)
        throw new Exception('meal name is empty');
    if (empty($_POST['price']) and $_POST['price']!=0)
        throw new Exception('price is empty');
    if ($_POST['price']<0)
        throw new Exception('price should >=0');
    if (empty($_POST['quan']) and $_POST['quan']!=0)
        throw new Exception('quantity is empty');
    if ($_POST['quan']<0)
        throw new Exception('quantity should >=0');
    if (!is_uploaded_file($_FILES['myFile']["tmp_name"]))
        throw new Exception('you have not uploaded any picture.');
    
    $file = fopen($_FILES["myFile"]["tmp_name"], "rb");
    $fileContents = fread($file, filesize($_FILES["myFile"]["tmp_name"]));
    fclose($file);
    $fileContents = base64_encode($fileContents);

    $conn = new PDO("mysql:host=$dbservername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt=$conn->prepare("insert into dish (dish_name, picture, name, price, amount) values (:mname, :picture, :sname, :price, :quan)");
    $stmt->execute(array('mname' => $_POST['mname'], 'picture' => $fileContents, 'price' => $_POST['price'], 'quan' => $_POST['quan'], 'sname' => $_SESSION['storename']));

    echo <<<EOT
    <!DOCTYPE html>
    <html>
        <body>
            <script>
            alert("You've added a commodity successfully.");
            window.location.replace("../home_page/home.php");
            </script> 
        </body> 
    </html>
    EOT;
    exit();
    
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