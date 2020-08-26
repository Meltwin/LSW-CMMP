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
</div>
<script>
    <?php
        $req = $bdd->prepare('SELECT cmmp_vers.contenu as contenu, cmmp_code.state as state 
                            FROM cmmp_vers
                            INNER JOIN cmmp_code ON cmmp_code.code = cmmp_vers.code
                            INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id
                            WHERE cmmp_vers.n_vers = ? AND cmmp_code.state >= 3 AND cmmp_groups.state = 1');
        for ($i=1;$i<=$config["nbV"];$i++) {
            $req->execute(array($i));
            $donnee = $req->fetchAll();
            echo'var t'.$i.' ='.json_encode($donnee,true).';';
            echo 'var l'.$i.' = t'.$i.'.length;';
        }
    ?>
    genR();
    function genR() {
        <?php
            for ($i=1;$i<=$config["nbV"];$i++) {
                echo 'document.getElementById("ligne'.$i.'").value = t'.$i.'[Math.floor((Math.random() * l'.$i.') + 1)-1]["contenu"];';
            }
        ?>
    }
    function listWindow() {
        var listW = window.open("list.php","","width=450px,height=600px;");
    }
</script>