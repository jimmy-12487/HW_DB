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
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $shopname = $_GET['shopname'];
    $sel1 = $_GET['sel1'];
    $lowerbound = $_GET['lowerbound'];
    $upperbound = $_GET['upperbound'];
    $meals = $_GET['meals'];
    $categories = $_GET['categories'];
    $self_location = $_GET['self_location'];
    
    $stmt = $conn->prepare("SELECT name FROM store");
    $stmt->execute();
    $result = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'name');

    if(!empty($_GET['shopname'])){
        $stmt = $conn->prepare("SELECT name FROM store WHERE name LIKE '%" . $shopname . "%'");
        $stmt->execute();
        $shopname_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'name');
        $result = array_intersect($result, $shopname_set);
        $result = array_values($result);
    }

    if(!empty($_GET['categories'])){
        $stmt = $conn->prepare("SELECT name, category FROM store WHERE category = :categories");
        $stmt->execute(array("categories" => $categories));
        $category_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'name');
        $result = array_intersect($result, $category_set);
        $result = array_values($result);
    }
    
    if(!empty($_GET['meals'])){
        $stmt = $conn->prepare("SELECT name, dish_name FROM dish WHERE dish_name = :meals");
        $stmt->execute(array("meals" => $meals));
        $meals_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'name');
        $result = array_intersect($result, $meals_set);
        $result = array_values($result);
    }
    
    if(!empty($_GET['sel1'])){
        if($sel1 == 'near')
            $stmt = $conn->prepare("SELECT name, ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) AS distance FROM store WHERE ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) < 1500 order by distance" );
        else if ($sel1 == 'medium')
            $stmt = $conn->prepare("SELECT name, ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) AS distance FROM store 
                                    WHERE ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) between 1500 and 5000 order by distance" );
        else
            $stmt = $conn->prepare("SELECT name, ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) AS distance FROM store WHERE ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) > 5000 order by distance" );
            
        $stmt->execute(array("self_location" => $self_location));
        $dis_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'name');

        $result = array_intersect($result, $dis_set);
        $result = array_values($result);
    }
    
    if(!empty($_GET['lowerbound']) && !empty($_GET['upperbound'])) {
        $stmt = $conn->prepare("SELECT *
                                FROM (SELECT *, COUNT(name) as num_name
                                        FROM store NATURAL JOIN dish
                                        WHERE price > :lowerbound and 
                                              price < :upperbound
                                        GROUP BY name
                                        )tmp
                                WHERE num_name != 0");
        $stmt->execute(array("upperbound" => $upperbound, "lowerbound" => $lowerbound));
        $money_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'name'); 
        $result = array_intersect($result, $money_set);
        $result = array_values($result);
    }
    else if (!empty($_GET['lowerbound'])){
        $stmt = $conn->prepare("SELECT *
                                FROM (SELECT *, COUNT(name) as num_name
                                        FROM store NATURAL JOIN dish
                                        WHERE price > :lowerbound
                                        GROUP BY name
                                        )tmp
                                WHERE num_name != 0");
        $stmt->execute(array("lowerbound" => $lowerbound));
        $money_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'name');
        $result = array_intersect($result, $money_set);
        $result = array_values($result);
    }
    else if (!empty($_GET['upperbound'])){
        $stmt = $conn->prepare("SELECT *
                                FROM (SELECT *, COUNT(name) as num_name
                                        FROM store NATURAL JOIN dish
                                        WHERE price < :upperbound
                                        GROUP BY name
                                        )tmp
                                WHERE num_name != 0");
        $stmt->execute(array("upperbound" => $upperbound));
        $money_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'name');
        $result = array_intersect($result, $money_set);
        $result = array_values($result);
    }

    
    $final_names = get_str($result);
    $final_category = array();
    $final_distance = array();

    $command = "SELECT name, category,  ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) AS distance 
                FROM store WHERE ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) < 1500 
                and name IN " . $final_names . " order by name";
    $stmt = $conn->prepare($command);
    $stmt->execute(array("self_location" => $self_location));
    $tmp_category = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'category');
    for($i = 0; $i < count($tmp_category); $i++){
        $final_category[] = $tmp_category[$i];
        $final_distance[] = 'near';
    }

    $command = "SELECT name, category,  ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) AS distance 
                FROM store WHERE ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) between 1500 and 5000
                and name IN " . $final_names . " order by name";
    $stmt = $conn->prepare($command);
    $stmt->execute(array("self_location" => $self_location));
    $tmp_category = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'category');
    for($i = 0; $i < count($tmp_category); $i++){
        $final_category[] = $tmp_category[$i];
        $final_distance[] = 'medium';
    }

    $command = "SELECT name, category,  ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) AS distance 
    FROM store WHERE ST_Distance_Sphere( ST_GeomFromText(:self_location, 2154), location) > 5000
    and name IN " . $final_names . " order by name";

    $stmt = $conn->prepare($command);
    $stmt->execute(array("self_location" => $self_location));
    $tmp_category = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'category');
    for($i = 0; $i < count($tmp_category); $i++){
        $final_category[] = $tmp_category[$i];
        $final_distance[] = 'far';
    }



    $show = "";

    for($i = 0; $i < count($result); $i++){
        $show = $show . '<tr>' .
                            '<th scope="row">' . ($i+1) . '</th>' .
                            '<td>' . $result[$i] . '</td>' .
                            '<td>' . $final_category[$i] . '</td>' .
                            '<td>' . $final_distance[$i] . '</td>' .
                            '<td> <button type="button" class="btn btn-info " data-toggle="modal" data-target="#' . $result[$i] . '"> Open menu</button></td>' .
                        '</tr>';
    }


    echo $show;
    echo "|";
    echo $final_names;

    function get_attr_arr($arr, $attr){
        $name = array();
        for($i = 0; $i < count($arr); $i++)
            $name[] = $arr[$i]->$attr;
        return $name;
    }
    function get_str($arr){

        if(count($arr) > 0){
            $name = "(";
            for($i = 0; $i < count($arr) - 1; $i++)
                $name = $name . "'" . $arr[$i] . "'" . ",";
            
            $name = $name . "'" . $arr[count($arr) - 1] . "'" . ")";
        }
        else{
            $name = "('')";
        }
        return $name;
    }
?>