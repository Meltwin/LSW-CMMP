<?php
    $config = file_get_contents('admin/private/config.json');
    $config = json_decode($config,true);
    include 'admin/private/bddIni.php';
?>
<head>
    <meta charset="UTF-8">
    <title>Liste</title>
    <link rel="stylesheet" href="css/list_style.css">
</head>
<body>
    <div class="header">
    <h1>Liste des vers  :</h1>
    </div>
    <div class="box">
    <?php
        $req = $bdd->prepare('SELECT cmmp_vers.contenu 
        FROM cmmp_vers 
        INNER JOIN cmmp_code ON cmmp_code.code = cmmp_vers.code
        INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id
        WHERE cmmp_vers.n_vers = ? AND cmmp_code.state = 3 AND cmmp_groups.state = 1');
        for ($i=1;$i<=$config["nbV"];$i++) {
            $req->execute(array($i));
            $donnee = $req->fetchAll();
            echo '<h2 class="titre">Vers n°'.$i.'</h2>';
            foreach ($donnee as $value) {
                echo "<span class='vers'>\"".$value["contenu"]."\"</span><br>";
            }
        }
    ?>
    </div>
</body>