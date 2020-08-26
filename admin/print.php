<head>
    <link rel="stylesheet" href="../css/print.css">
</head>
<body>
    <div class="printInner">
    <?php
        $redir = 'false';
        unset($_POST["actSel"]);
        $method = $_POST["printSel"];
        unset($_POST["printSel"]);
        if (!empty($_POST)) {
            switch ($method) {
                case "list":
                    echo '<table class="tStyle"><tr><td style="width:10vh">Code</td><td style="width:30vh">Membres</td><td style="width:30vh">Groupe</td></tr>';
                    $req = $bdd->prepare('SELECT cmmp_code.code,cmmp_code.membres,cmmp_groups.name FROM cmmp_code INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id WHERE cmmp_code.code = ?');
                    $nbT = count($_POST);
                    $nb = 0;
                    foreach($_POST as $key => $value) {
                        $req->execute(array($key));
                        $info = $req->fetchAll()[0];
                        if ($nb == ($nbT-1)) {
                            echo '<tr><td>'.$info["code"].'</td><td>'.$info["membres"].'</td><td>'.$info["name"].'</td></tr>';
                        }
                        else {
                            echo '<tr><td>'.$info["code"].'</td><td>'.$info["membres"].'</td><td>'.$info["name"].'</td></tr>';
                        }
                        $nb +=1;
                    }
                    echo '</table>';
                    break;
                case "bon":
                    foreach($_POST as $key => $value) {
                        echo '<div class="bon"><div>Projet LSW42 - Collège P&M Curie</div><div><strong>Lien :</strong>www.meltwin.fr/sites/Haiku/</div><div><strong>Code :</strong> '.$key.'</div></div>';
                    }
                    break;
            }
        } else {
            $redir = 'true';
        }
    ?>
    </div>
    <p style="margin-top:10px;">>> Vous pouvez imprimer ces codes en appuyant sur le bouton impression <span class="glyphicon glyphicon-print"></span></p>
    <script>
        var redir = <?=$redir?>;
        if (redir == true) {
            document.location.href="./";
        }
    </script>
</body>