<?php
    session_start();
    $state = true;
    if(isset($_POST["username"])) {
        $handle = file("private/user.txt");
        $state = false;
        foreach ($handle as $key => $value) {
            $handle[$key] = explode(":",$value);
            $handle[$key][1] = preg_replace("#(\r\n)#","", $handle[$key][1]);
            if (($_POST["username"] == $handle[$key][0]) && ($_POST["password"] == $handle[$key][1])) {
                $_SESSION["connect"] = "set";
                $_SESSION["user"] = $_POST["username"];
                $state = true;
                header("Location:./");
            }
        }
    }
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Connection au panneau d'administration</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body class="coBody">
    <div class="coBox">
        <h1>Connection au panel admin :</h1>
        <?php
            if ($state === false) {
                echo '<div class="coError"><p style="margin:0px;">La combinaison identifiant / mot de passe ne correspond pas.</p></div>';
            }
        ?>
        <form action="" method="post">
        <div class="coInner">
                <label>Username :</label><br>
                <input type="text" name="username" class="coInput" required autocomplete="off"><br>
                <label>Password :</label><br>
                <input type="password" name="password" class="coInput" required autocomplete="off"><br>                
        </div>
        <input type="submit" class="coSubmit">
        </form>
    </div>
</body>