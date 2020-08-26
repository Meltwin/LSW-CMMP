<?php
    $donnee = $bdd->query('SELECT cmmp_code.code, cmmp_code.membres,cmmp_code.state,cmmp_groups.name FROM cmmp_code INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id WHERE cmmp_code.state = 2');
    $donnee = $donnee->fetchAll();
    $redir = 'false';
    if (isset($_GET["act"])) {
        switch ($_GET["act"]) {
            case "del":
                $del = $bdd->prepare('DELETE FROM cmmp_vers WHERE code = ?');
                $del->execute(array($_GET["code"]));
                $del = $bdd->prepare('DELETE FROM cmmp_code WHERE code = ?');
                $del->execute(array($_GET["code"]));
                $redir = "true";
                break;
        }
    }
    elseif (isset($_POST["actSel"])) {
        if ($_POST["actSel"] == "delMulti") {
            $delV = $bdd->prepare('DELETE FROM cmmp_vers WHERE code = ?');
            $delC = $bdd->prepare('DELETE FROM cmmp_code WHERE code = ?');
            foreach ($_POST as $key => $value) {
                if ($value != "delMulti") {
                    $delV->execute(array($key));
                    $delC->execute(array($key));
                }
            }
            $redir = "true";
        }
    }
?>

<?php
/* 
    ##################################################
    ###################            ###################
    ################### En attente ###################
    ###################            ###################
    ##################################################
*/
?>

<h1>Propositions en attente : </h1>
<?php
$a = count($donnee);
if ($a > 0) {
    ?>
    <table class="tStyle">
        <tr>
            <td class="code">Code</td>
            <td class="membre">Membres</td>
            <td class="grp">Groupe</td>
            <td class="state">Etat</td>
            <td class="action"></td>
        </tr>
    <?php
    for($i=1;$i<$a;$i++) {
        ?>
        <tr>
            <td class="cellmid"><?=$donnee[$i-1]["code"]?></td>
            <td class="cellmid"><?=$donnee[$i-1]["membres"]?></td>
            <?php
            if ($donnee[$i-1]["name"] == "Import") {
                echo '<td class="cellbas import">'.$donnee[$i-1]["name"].'</td>';
            }
            else {
                echo '<td class="cellbas">'.$donnee[$i-1]["name"].'</td>';
            }
        ?>
            <td>
                <?php
                switch ($donnee[$i-1]["state"]) {
                    case "0":
                        echo 'Non Utilisé';
                        break;
                    case "1":
                        echo 'En édition';
                        break;
                    case "2":
                        echo 'En attente';
                        break;
                    case "3":
                        echo 'Intégré';
                        break;
                    case "4":
                        echo 'Import';
                        break;

                }
                ?>
            </td>
            <td>
                <a href="?page=see&code=<?=$donnee[$i-1]['code']?>" class="anormal"><span class="glyphicon glyphicon-share-alt"></span></a>
            </td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td><?=$donnee[$a-1]["code"]?></td>
        <td><?=$donnee[$a-1]["membres"]?></td>
        <?php
            if ($donnee[$a-1]["name"] == "Import") {
                echo '<td class="import">'.$donnee[$a-1]["name"].'</td>';
            }
            else {
                echo '<td>'.$donnee[$a-1]["name"].'</td>';
            }
        ?>
        <td>
            <?php
            switch ($donnee[$a-1]["state"]) {
                case "0":
                    echo 'Non Utilisé';
                    break;
                case "1":
                    echo 'En édition';
                    break;
                case "2":
                    echo 'En attente';
                    break;
                case "3":
                    echo 'Intégré';
                    break;
            }
            ?>
        </td>
        <td>
            <a href="?page=see&code=<?=$donnee[$a-1]['code']?>" class="anormal"><span class="glyphicon glyphicon-share-alt"></span></a>
        </td>
    </tr>
    <?php
}
else {
    echo 'Il n\'y a rien a afficher';
}
?>
</table>

<?php
/*
    ##################################################
    ###################            ###################
    ###################     All    ###################
    ###################            ###################
    ##################################################
*/
?>

<h2>Données :</h2>
    <form action="?page=verif" method="post" id="codeMulti">
    <?php
    $donnee = $bdd->query('SELECT cmmp_code.code, cmmp_code.membres,cmmp_code.state,cmmp_groups.name FROM cmmp_code INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id WHERE cmmp_code.state > 0 ORDER BY cmmp_code.state DESC');
    $donnee = $donnee->fetchAll();
    $a = count($donnee);
    if ($a > 0) {
        ?>
        <table class="tStyle">
            <tr>
                <td class="check"><input type="checkbox" id="checkAll" onchange="checkAllF();"></td>
                <td class="code">Code</td>
                <td class="membre">Membres</td>
                <td class="grp">Groupe</td>
                <td class="state">Etat</td>
                <td class="action"></td>
            </tr>
        <?php
        for($i=1;$i<$a;$i++) {
            ?>
            <tr>            
                <td><input type="checkbox" name="<?=$donnee[$i-1]["code"]?>" id="<?=$donnee[$i-1]["code"]?>"></td>
                <td><?=$donnee[$i-1]["code"]?></td>
                <td><?=$donnee[$i-1]["membres"]?></td>
                <?php
                    if ($donnee[$i-1]["name"] == "Import") {
                        echo '<td class="import">'.$donnee[$i-1]["name"].'</td>';
                    }
                    else {
                        echo '<td>'.$donnee[$i-1]["name"].'</td>';
                    }
                    switch ($donnee[$i-1]["state"]) {
                        case "0":
                            echo '<td class="unused">Non Utilisé</td>';
                            break;
                        case "1":
                            echo '<td class="edition">En édition</td>';
                            break;
                        case "2":
                            echo '<td class="attente">En attente</td>';
                            break;
                        case "3":
                            echo '<td class="integre">Intégré</td>';
                            break;
                    }
                    ?>
                <td>
                <a href="?page=see&code=<?=$donnee[$i-1]['code']?>" class="anormal"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a onclick="delConfirm(<?=$donnee[$i-1]['code']?>);" class="anormal"><span class="glyphicon glyphicon-remove"></span></a>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td><input type="checkbox" name="<?=$donnee[$a-1]["code"]?>" id="<?=$donnee[$a-1]["code"]?>"></td>
            <td><?=$donnee[$a-1]["code"]?></td>
            <td><?=$donnee[$a-1]["membres"]?></td>
            <?php
                    if ($donnee[$a-1]["name"] == "Import") {
                        echo '<td class="import">'.$donnee[$a-1]["name"].'</td>';
                    }
                    else {
                        echo '<td>'.$donnee[$a-1]["name"].'</td>';
                    }
                ?>
                <?php
                switch ($donnee[$a-1]["state"]) {
                    case "0":
                        echo '<td class="unused">Non Utilisé</td>';
                        break;
                    case "1":
                        echo '<td class="edition">En édition</td>';
                        break;
                    case "2":
                        echo '<td class="attente">En attente</td>';
                        break;
                    case "3":
                        echo '<td class="integre">Intégré</td>';
                        break;
                }
                ?>
            <td>
                <a href="?page=see&code=<?=$donnee[$a-1]['code']?>" class="anormal"><span class="glyphicon glyphicon-pencil"></span></a>
                <a onclick="delConfirm(<?=$donnee[$a-1]['code']?>);" class="anormal"><span class="glyphicon glyphicon-remove"></span></a>
            </td>
        </tr>
    </table>
    <label style="margin-left:11px;"><span style="font-size:23px;">&#8627;</span> Action :</label>
    <select id="actSel" name="actSel">
        <option value="null">Choisir =></option>
        <option value="delMulti">Suprimer</option>
        <option value="print">Imprimer</option>
    </select>
    <a class="anormal" onclick="formSub();"><span class="glyphicon glyphicon-ok"></span></a>
    <?php include 'modules/print.html'; ?>
    </form>
    <?php
    }
    else {
        echo 'Il n\'y a aucun code d\'enregistré';
    }
    ?>

<?php
    /*
    ##################################################
    ###################   Scripts  ###################
    ##################################################
    */
?>

<script>
    function delConfirm(id) {
        var r = confirm("Etes vous sur de vouloir suprimer ce code ?");
        if (r == true) {
            document.location.href="?page=verif&act=del&code="+id;
        }
    }
    var redir = <?=$redir?>;
    if (redir == true) {
        document.location.href="?page=verif";
    }

    function formSub() {
        var actSelect = document.getElementById("actSel");
        var form = document.getElementById("codeMulti");
        if (actSelect.value == "null") {
            var r = alert("Veuillez selectionner une action");
        }
        else if (actSelect.value == "delMulti") {
            var r = confirm("Etes vous sur de vouloir suprimer ces codes ?");
            if (r == true) {
                form.submit();
            }
        }
        else if (actSelect.value == "print") {
            document.getElementById("codeMulti").action = "?page=print";
            document.getElementById("popupPrintSel").style.display = "flex";
            document.getElementById("fondNoir").style.display = "block";
        }
        else {
            form.submit();
        }
    }

    function checkAllF() {
        var strCheckList = <?=json_encode($donnee,true)?>;
        var checkBox = document.getElementById("checkAll");
        if (checkBox.checked === true) {
            for (i = 0; i < strCheckList.length; i++) {
                document.getElementById(strCheckList[i]["code"]).checked = true;
            }
        } else {
            for (i = 0; i < strCheckList.length; i++) {
                document.getElementById(strCheckList[i]["code"]).checked = false;
            }
        }
    }
</script>