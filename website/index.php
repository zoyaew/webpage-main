<?php
    include("database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <h2>Welcome to Fakebook!</h2>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
        <label for="username">Username: </label>
        <input type="text" id="username" name="username" required maxlength=20> <br>
        <label for="password">Password: </label>
        <input type="password" id="password" name="password" required maxlength=20> <br>
        <input type="submit" name="login" value="Log In">
        <input type="submit" name="register" value="Register"> <br>
    </form>
</head>
<body>
        
</body>
</html>

<?php
    // foreach($_POST as $k => $v) {
    //     echo "$k => $v <br>";
    // }
    if($_SERVER["REQUEST_METHOD"]=="POST") {
        $action = null;
        if(isset($_POST["register"])) {
            $action = "R";
        } elseif(isset($_POST["login"])) {
            $action = "L";
        }
        // check if the field(s) are empty
        if(empty($_POST["username"]) || empty($_POST["password"])) {
            echo "Please fill in your username and password.<br>";
        } else {
            //check the validity of username and password
            $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
            $invalid_username = strcmp($username, $_POST["username"]);
            $invalid_password = strcmp($password, $_POST["password"]);
            if($invalid_username && $invalid_password) {
                echo "Do not use special characters for your username and password. <br>";
            }
            elseif($invalid_username) {
                echo "Do not use special characters for your username. <br>";
            }
            elseif($invalid_password) {
                echo "Do not use special characters for your password. <br>";
            }
            else {
                // check if the username is already on the database
                // try {
                    $sql = "SELECT * FROM users WHERE user='$username';";
                    $result = mysqli_query($conn, $sql);
                    // case 1: name is on the database
                    if(mysqli_num_rows($result)>0) {
                        switch($action){
                            case "R":
                                echo "The username {$username} is taken. Please select another username. <br>";
                                break;
                            case "L":
                                $row = mysqli_fetch_assoc($result);
                                // verify password
                                if(password_verify($password, $row["password"])) {
                                    echo "You may log in<br>";
                                } else {
                                    echo "Incorrect password. <br>";
                                    echo "<a href= {$_SERVER["PHP_SELF"]}>Forget password. <br></a>";
                                }
                        }
                    }
                    // case 2: username is not on the database
                    else {
                        switch($action){
                            case "R":
                                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                                $sql = "INSERT INTO users(user, password)
                                    VALUES('{$username}', '{$password_hash}');";
                                mysqli_query($conn, $sql);
                                echo "Congratulations, you have successfully registered on Fakebook. <br>";
                                break;
                            case "L":
                                echo "Username is incorrect. <br>";
                        }
                        
                        
                    }

                // }
                // catch(mysqli_sql_exception) {
                //     echo "Could not register user at this moment. Please try again later.<br>";
                // }
            }


        }
    }



    mysqli_close($conn);
?>  