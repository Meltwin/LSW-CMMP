<?php
    $redir = "false";
    $usedData = $bdd->query('SELECT code, membres, state FROM cmmp_code ORDER BY state DESC');
    $usedData = $usedData->fetchAll();
    
    if (isset($_POST["nb"])) {
        if ($_POST["option"] == "null") {
            $grp = 0;
        }
        elseif ($_POST["option"] == "new") {
            $grpN = $bdd->prepare('INSERT INTO cmmp_groups(name,state) VALUES(?,1)');
            $grpN->execute(array($_POST["grpName"]));
            $grp = $bdd->prepare('SELECT id FROM cmmp_groups WHERE name = ?');
            $grp->execute(array($_POST["grpName"]));
            $grp = intval($grp->fetchAll()[0]["id"]);
        }
        else {
            $grp = intval($_POST["option"]);
        }

        $add = $bdd->prepare('INSERT INTO cmmp_code(code,state,group_id) VALUES(:code,0,:grp)');
        $used = [];
        foreach ($usedData as $value)  {
            array_push($used,$value["code"]);
        }
        $code = [];
        $maked = 0;
        while ($maked != $_POST["nb"]) {
            $c = strval(rand(100000,999999));
            if ((false === array_search($c, $used)) && (false === array_search($c, $code))) {
                array_push($code,$c);
                $add->execute(array("code"=>$c,"grp"=>$grp));
                $maked++;
            }
        }
    }
    elseif (isset($_GET["act"])) {
        switch ($_GET["act"]) {
            case "del":
                $del = $bdd->prepare('DELETE FROM cmmp_code WHERE code = ?');
                $del->execute(array($_GET["code"]));
                $redir = "true";
                break;
        }
    }
    elseif (isset($_POST["actSel"])) {
        if ($_POST["actSel"] == "delMulti") {
            $delC = $bdd->prepare('DELETE FROM cmmp_code WHERE code = ?');
            foreach ($_POST as $key => $value) {
                if ($value != "delMulti") {
                    $delC->execute(array($key));
                }
            }
        }
    }
?>
<h1>Générer un code</h1>
<form method="post" action="?page=gen">
    <label>Nombre de codes a génerer :</label>
    <input type="number" name="nb" value="1" min="1"><br>

    <label>Ajouter au groupe : </label>
    <select name="option" id="grpSelect" onchange="grpChange();">
        <option value="null">Aucun groupe</option>
        <option disabled>-------------------</option>
        <?php
        $grp = $bdd->query('SELECT name,id FROM cmmp_groups WHERE id !=0');
        $grp = $grp->fetchAll();
        foreach ($grp as $key => $value) {
            echo '<option value="'.$value["id"].'">'.$value["name"].'</option>';
        }
        ?>
        <option disabled>-------------------</option>
        <option value="new"> + Nouveau groupe</option>
    </select><br>

    <div style="display:none;" id="grpName">
        <label>Nom du groupe : </label>
        <input type="text" name="grpName">
    </div>
    <input type="submit" value="Générer">
</form>
<?php
    if (isset($_POST["nb"])) {
        ?>
        <h3>Résultat : </h3>
        <?php
        foreach($code as $value) {
            echo '- '.$value.'<br>';
        }
    }

?>
<h1>Codes libres :</h1>
    <?php
    $usedData = $bdd->query('SELECT cmmp_code.code, cmmp_code.membres, cmmp_code.state, cmmp_groups.name FROM cmmp_code INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id WHERE cmmp_code.state = 0 ORDER BY cmmp_code.code ASC');
    $usedData = $usedData->fetchAll();
    $a = count($usedData);
    if ($a > 0) {
        ?>
        <form action="?page=gen" method="post" id="codeMulti">
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
                <td><input type="checkbox" name="<?=$usedData[$i-1]["code"]?>" id="<?=$usedData[$i-1]["code"]?>"></td>
                <td><?=$usedData[$i-1]["code"]?></td>
                <td><?=$usedData[$i-1]["membres"]?></td>
                <td><?=$usedData[$i-1]["name"]?></td>
                <td class="unused">Non Utilisé</td>
                <td>
                    <a onclick="delConfirm(<?=$usedData[$i-1]['code']?>);" class="anormal"><span class="glyphicon glyphicon-remove"></span></a>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td><input type="checkbox" name="<?=$usedData[$a-1]["code"]?>" id="<?=$usedData[$a-1]["code"]?>"></td>
            <td><?=$usedData[$a-1]["code"]?></td>
            <td><?=$usedData[$a-1]["membres"]?></td>
            <td><?=$usedData[$a-1]["name"]?></td>
            <td class="unused">Non Utilisé</td>
            <td>
            <a onclick="delConfirm(<?=$usedData[$a-1]['code']?>);" class="anormal"><span class="glyphicon glyphicon-remove"></span></a>
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
<script>
    function delConfirm(id) {
        var r = confirm("Etes vous sur de vouloir suprimer ce code ?");
        if (r == true) {
            document.location.href="?page=gen&act=del&code="+id;
        }
    }
    var redir = <?=$redir?>;
    if (redir == true) {
        document.location.href="?page=gen";
    }
    var grpSelect = document.getElementById("grpSelect");
    function grpChange() {
        var grpDiv = document.getElementById("grpName");
        console.log("test");
        if (grpSelect.value == "new") {
            grpDiv.style.display = "block";
        }
        else {
            grpDiv.style.display = "none";
        }
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
            document.getElementById("codeMulti").action = "?page=print";
            document.getElementById("popupPrintSel").style.display = "flex";
            document.getElementById("fondNoir").style.display = "block";
        }
        else {
            form.submit();
        }
    }
    function checkAllF() {
        var strCheckList = <?=json_encode($usedData,true)?>;
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