<?php
    $redir = "false";

    // Del des groupes
    if (isset($_GET["act"])) {
        switch ($_GET["act"]) {
            case "del":
                $del = $bdd->prepare('UPDATE cmmp_code SET group_id = 0 WHERE group_id = ?');
                $del->execute(array($_GET["group"]));
                $del = $bdd->prepare('DELETE FROM cmmp_groups WHERE id = ?');
                $del->execute(array($_GET["group"]));
                $redir = "true";
                break;
            case 'actv':
                $grpState = $bdd->prepare('SELECT state FROM cmmp_groups WHERE id = ?');
                $grpState->execute(array($_GET["group"]));
                $grpState = $grpState->fetchAll();
                if ($grpState[0]["state"] === '0') {
                    $actv = $bdd->prepare('UPDATE cmmp_groups SET state = 1 WHERE id = ?');
                }
                else {
                    $actv = $bdd->prepare('UPDATE cmmp_groups SET state = 0 WHERE id = ?');
                }
                $actv->execute(array($_GET["group"]));
                $redir = "true";
                break;
            case 'print':
                $grpCode = $bdd->prepare('SELECT code FROM cmmp_code WHERE group_id = ?');
                $grpCode->execute(array($_GET["group"]));
                $grpCode = $grpCode->fetchAll();
                ?>
                <form id="printGrp" action="?page=print" method="post">
                    <?php
                    foreach($grpCode as $value) {
                        ?>
                        <input type="hidden" name="<?=$value[0]?>" value="on">
                        <?php
                    }
                    include 'modules/print.html';
                    ?>
                </form>
                <script>
                    document.getElementById("popupPrintSel").style.display = "flex";
                    document.getElementById("fondNoir").style.display = "block";
                </script>
                <?php
        }
    }
    // Ajout d'un groupe
    if (isset($_POST["name"])) {
        $add = $bdd->prepare('INSERT INTO cmmp_groups(name,state) VALUES(:name,1)');
        $add->execute(array("name"=>$_POST["name"]));
        $idGroup = $bdd->prepare('SELECT id FROM cmmp_groups WHERE name = :name');
        $idGroup->execute(array("name"=>$_POST["name"]));
        $idGroup = $idGroup->fetchAll();
        $changeGroup = $bdd->prepare('UPDATE cmmp_code SET group_id = :groupId WHERE code = :code');
        foreach ($_POST as $key => $value) {
            if (preg_match("#^[0-9]{6}#",$key)) {
                $changeGroup->execute(array("groupId"=>$idGroup[0]["id"],"code"=>$key));
            }
        }
        $redir="true";
    }

    // Recupération des données et affichage
?><h1>Liste des groupes</h1><?php
    $group = $bdd->query('SELECT id, name, state FROM cmmp_groups WHERE id != 0');
    $group = $group->fetchAll();
    $a = count($group);
    if ($a > 0) {
        ?>
        <table class="tStyle">
            <tr>
                <td class="code">Groupe</td>
                <td class="state">Etat</td>
                <td class="membre">Nombre de codes associés</td>
                <td class="action"></td>
            </tr>
        <?php
        for($i=1;$i<$a;$i++) {
            $element = $bdd->prepare('SELECT COUNT(*) FROM cmmp_code WHERE group_id = :grp');
            $element->execute(array("grp"=>$group[$i-1]["id"]));
            $nb = $element->fetchAll()[0][0];
            ?>
            <tr>
                <td><?=$group[$i-1]["name"]?></td>
                    <?php
                    switch ($group[$i-1]["state"]) {
                        case "0":
                            echo '<td class="desactive">Désactivé</td>';
                            break;
                        case "1":
                            echo '<td class="integre">Activé</td>';
                            break;
                    }
                    ?>
                <td><?=$nb?></td>
                <td>
                    <a onclick="delConfirm(<?=$group[$i-1]['id']?>);" class="anormal" title="Suprimer ce groupe"><span class="glyphicon glyphicon-remove"></span></a>
                    <a title="Voir les codes de ce groupe" href="?page=seegrp&group=<?=$group[$i-1]['id']?>"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a title="Activer / Désactiver ce groupe" href="?page=group&act=actv&group=<?=$group[$i-1]['id']?>"><span class=" glyphicon glyphicon-off"></span></a>
                    <a title="Imprimer les codes de ce groupe" href="?page=group&act=print&group=<?=$group[$i-1]['id']?>"><span class=" glyphicon glyphicon-print"></a>
                </td>
            </tr>
            <?php
        }
        $element = $bdd->prepare('SELECT COUNT(*) FROM cmmp_code WHERE group_id = :grp');
        $element->execute(array("grp"=>$group[$a-1]["id"]));
        $nb = $element->fetchAll()[0][0];
        ?>
        <tr>
            <td><?=$group[$a-1]["name"]?></td>
             <?php
                    switch ($group[$a-1]["state"]) {
                        case "0":
                            echo '<td class="desactive">Désactivé</td>';
                            break;
                        case "1":
                            echo '<td class="integre">Activé</td>';
                            break;
                    }
                    ?>
            <td><?=$nb?></td>
            <td>
                <a onclick="delConfirm(<?=$group[$a-1]['id']?>);" class="anormal" title="Suprimer ce groupe"><span class="glyphicon glyphicon-remove"></span></a>
                <a title="Voir les codes de ce groupe" href="?page=seegrp&group=<?=$group[$a-1]['id']?>"><span class="glyphicon glyphicon-eye-open"></span></a>
                <a title="Activer / Désactiver ce groupe" href="?page=group&act=actv&group=<?=$group[$a-1]['id']?>"><span class=" glyphicon glyphicon-off"></span></a>
                <a title="Imprimer les codes de ce groupe" href="?page=group&act=print&group=<?=$group[$a-1]['id']?>"><span class=" glyphicon glyphicon-print"></a>
            </td>
        </tr>
        <?php
    }

    // Si rien =>

    else {
        echo 'Il n\'y a aucun code d\'enregistré';
    }
    ?>
    </table>

    <!-- Formulaire pour un nouveau groupe -->
    <button onclick="addElement();"><span class="glyphicon glyphicon-plus"></span> Créer un nouveau groupe</button>
    
    <div class="fondNoir" id="fondNoir" style='display:none;'></div>
    <div id="grpAdd" style='display:none;' class="popupLarge">
        <h1>Créer un nouveau groupe : </h1>
        <a class="closePopup"><span class="glyphicon glyphicon-remove" id="close" onclick="addElement();"></span></a>
        <form method="post" action="?page=group">

            <!-- Affichage des codes -->
            <p>Ajouter des codes déjà existant :</p>
            <div class="grpInner">                        
                    <?php
                    $code = $bdd->query('SELECT * FROM cmmp_code ORDER BY state DESC');
                    $code = $code->fetchAll();
                    $a = count($code);
                    if ($a > 1) {
                        ?>
                        <table style="width:100%;" class="tStyle">
                            <tr>
                                <td class="check"></td>
                                <td class="code">Code</td>
                                <td class="membre">Membres</td>
                            </tr>
                        <?php
                        for($i=1;$i<$a;$i++) {
                            ?>
                            <tr>
                                <td><input type="checkbox" name="<?=$code[$i-1]['code']?>"><label></label></td>
                                <?php
                                switch ($code[$i-1]["state"]) {
                                    case '0':
                                        echo '<td class="unused">'.$code[$i-1]["code"].'</td>';
                                        break;
                                    case '1':
                                        echo '<td class="edition">'.$code[$i-1]["code"].'</td>';
                                        break;
                                    case '2':
                                        echo '<td class="attente">'.$code[$i-1]["code"].'</td>';
                                        break;
                                    case '3':
                                        echo '<td class="integre">'.$code[$i-1]["code"].'</td>';
                                        break;
                                }
                                ?>
                                <td><?=$code[$i-1]["membres"]?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td><input type="checkbox" name="<?=$code[$i-1]['code']?>"><label></label></td>
                            <?php
                                switch ($code[$a-1]["state"]) {
                                    case '0':
                                        echo '<td class="unused">'.$code[$a-1]["code"].'</td>';
                                        break;
                                    case '1':
                                        echo '<td class="edition">'.$code[$a-1]["code"].'</td>';
                                        break;
                                    case '2':
                                        echo '<td class="attente">'.$code[$a-1]["code"].'</td>';
                                        break;
                                    case '3':
                                        echo '<td class="integre">'.$code[$a-1]["code"].'</td>';
                                        break;
                                }
                                ?>
                            <td><?=$code[$a-1]["membres"]?></td>
                        </tr>
                        <?php
                    }
                    else {
                        echo 'Il n\'y a aucun code d\'enregistré';
                    }
                    ?>
                </table>
            </div>

            <label style="font-weight:normal;">Nom du groupe :</label>
            <input type="text" name="name"><br>
            <input type="submit" value="Créer">
        </form>
    </div>

    
<script>
    function delConfirm(id) {
        var r = confirm("Etes vous sur de vouloir suprimer ce groupe ? \r\n         (Les codes ne seront pas détruits)");
        if (r == true) {
            document.location.href="?page=group&act=del&group="+id;
        }
    }
    function addElement() {
        var x = document.getElementById('grpAdd');
        if (x.style.display === 'none') {
            x.style.display = 'block';
            document.getElementById('fondNoir').style.display="block";
        } else {
            x.style.display = 'none';
            document.getElementById('fondNoir').style.display='none';
        }
    }

    var redir = <?=$redir?>;
    if (redir == true) {
        document.location.href="?page=group";
    }
</script>