<?php
    session_start();
    if (isset($_SESSION["connect"])) {
        unset($_SESSION["connect"]);
        unset($_SESSION["username"]);
        session_destroy();
    }
    $config = file_get_contents('admin/private/config.json');
    $config = json_decode($config,true);
    $ligne = 1;
    include 'admin/private/bddIni.php';

    if (isset($_COOKIE["reset"])) {
        setcookie("code" ,null);
        unset($_COOKIE["code"]);
        setcookie("reset",null);
        unset($_COOKIE["reset"]);
    }
    $newRedi = "false";
    $passRedi = "false";
    if (isset($_POST["code"])) {
        $code = $_POST["code"];
        $req = $bdd->prepare('SELECT * FROM cmmp_code WHERE code = ?');
        $req->execute(array($code));
        $donnee = $req->fetchAll();
        if (!empty($donnee)) {
            if ($donnee[0]["state"] == 0) {
                $req = $bdd->prepare("UPDATE cmmp_code SET state = 1 WHERE id = ? ");
                $req->execute(array($donnee[0]["id"]));
                setcookie("code",$code, time()+2*3600, null, null, false, true);
                $passRedi = "true";
            }
        }
        else {
        }
    }
?>
<!DOCTYPE html>
<head>
    <title>Cent mille milliards de poèmes</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/public_style.css">
    <link rel="stylesheet" href="css/public_mobile_style.css">
</head>
<body>
    
    <div class="box">
        <div class="titre">
            <h1>Cent Mille Milliards de Poèmes</h1>
            <p style="color:#9b9898; margin:0px;">Projet Lycée Simone Weil - Collège Pierre et Marie Curie</p>
            <p style="color:#4c4c4c;">Site par G.Côte - Version : 2.2.0</p>
        </div>
        <?php

            // Insertion des pages

            
            if (isset($_GET["page"])) {
                switch ($_GET["page"]) {
                    case "new":
                        if (isset($_COOKIE["code"])) {
                            include 'new.php';
                        }
                        else {
                            $newRedi = "true";
                        }
                        
                        break;
                    case "pass":
                    if (!isset($_COOKIE["code"])) {
                        include 'access.php';
                    }
                    else {
                        $passRedi = "true";
                    }
                        break;
                }
            }
            else {
                include 'main.php';
            }
        ?>
    </div>
    <script>
        var redirectNew = <?=$newRedi?>;
        var redirectPass = <?=$passRedi?>;
        if (redirectNew == true) {
            document.location.href = "?page=pass";
        }
        if (redirectPass == true) {
            document.location.href = "?page=new";
        }
    </script>
</body>