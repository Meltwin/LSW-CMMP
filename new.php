<div class="page">
    <form action="save.php" method="POST" id="sonnet">
    <?php
        // Génération des champs
        foreach ($config["forme"] as $key =>$value) {
            ?><div class="strophe"><?php
            for ($i = 0;$i<$value;$i++) {
                echo '<input type="text" id="ligne'.$ligne.'" class="ligne" placeholder="Entrez votre vers" name="ligne'.$ligne.'" required autocomplete="off">';
                $ligne++;
            }
            ?></div><?php
        }
    ?>
    </form>
</div>
<div id="menu">
    <?php
    if ($config["settings"]["entryUser"] == "on") {
        ?><div><input type="text" form="sonnet" name="membre" placeholder="Nom des membres" required></div><?php
    } elseif ($config["settings"]["entryGrp"] == "on") {
        $grpEntry = $bdd->prepare('SELECT cmmp_groups.usrEntry FROM cmmp_groups INNER JOIN cmmp_code ON cmmp_code.group_id = cmmp_groups.id WHERE cmmp_code.code = :code');
        $grpEntry->execute(array("code"=>$_COOKIE["code"]));
        $grpEntry = $grpEntry->fetchAll()[0][0];
        if ($grpEntry == 1) {
            ?><div><input type="text" form="sonnet" name="membre" placeholder="Nom des membres" required></div><?php
        }
    }
    ?>
    <div><button form="sonnet">Envoyer le sonnet</button></div>
</div>