<form class="page">
        <?php
            // Génération des champs
            foreach ($config["forme"] as $key =>$value) {
                ?><div class="strophe"><?php
                for ($i = 0;$i<$value;$i++) {
                    echo '<input type="text" id="ligne'.$ligne.'" class="ligne cursor" value="" readonly>';
                    $ligne++;
                    
                }
                ?></div><?php
            }
        ?>
</form>
<div id="menu">
    <div title="Générer un nouveau code" id="gen" onclick="genR();"><span class="glyphicon glyphicon-repeat sizeMoins"></span></div>
    <div title="Voir les vers enregistrés" onclick="listWindow();"><span class="glyphicon glyphicon-list sizeMoins"></span></div>
    <div title="Ajouter des vers"><a href="?page=pass"><span class="glyphicon glyphicon-plus sizeMoins"></span></a></div>
    <div title="Ajouter des vers"><a href="?page=qr"><span class="glyphicon glyphicon-qrcode sizeMoins"></span></a></div>
</div>
<script>
    <?php
        $req = $bdd->prepare('SELECT cmmp_vers.contenu as contenu
                            FROM cmmp_vers
                            INNER JOIN cmmp_code ON cmmp_code.code = cmmp_vers.code
                            INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id
                            WHERE cmmp_vers.n_vers = ? AND cmmp_code.state >= 3 AND cmmp_groups.state = 1');
        $json = [];
        $launch = true;
        for ($i=1;$i<=$config["nbV"];$i++) {
            $req->execute(array($i));
            $donnee = $req->fetchAll();
            if (count($donnee) != 0) {
                array_push($json,$donnee);
            }
            else {
                $launch = false;
                break;
            }
            
        }

        if ($launch === true) {
            ?>
            genR();
            function genR() {
                <?php
                    echo "var t = ".json_encode($json).";";
                    for ($i=1;$i<=$config["nbV"];$i++) {
                        echo 'document.getElementById("ligne'.$i.'").value = t['.($i-1).'][Math.floor((Math.random() * t['.($i-1).'].length) + 1)-1]["contenu"];';
                    }
                ?>
            }
            <?php
        }
        ?>
        function listWindow() {
            console.log("hello");
            var listW = window.open("list.php","","width=450px,height=600px;");
        }
    </script>
    