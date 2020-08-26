<?php
    $redir = "false";
    if (isset($_POST["name"])) {
        $grp_id = $_GET["group"];
        $entryUsr = 0;
        if (isset($_POST["usrEntry"])) {
            var_dump($_POST);
            if($_POST["usrEntry"] == "on") {
                $entryUsr = 1;
            }
        }
        $nameUp = $bdd->prepare('UPDATE cmmp_groups SET name = :name, usrEntry = :usrEntry WHERE id = :id');
        $nameUp->execute(array("name"=>$_POST["name"],'usrEntry'=>$entryUsr,'id'=>$grp_id));
        unset($_POST['name']);
        unset($_POST['usrEntry']);
        $resetAll = $bdd->prepare('UPDATE cmmp_code SET group_id = 0 WHERE group_id = ?');
        $resetAll->execute(array($grp_id));
        $setGroup = $bdd->prepare('UPDATE cmmp_code SET group_id = :groupId WHERE code = :code');
        foreach($_POST as $key => $value) {
            $setGroup->execute(array("groupId"=>$grp_id,"code"=>$key));
        }
        
        $redir = "true";
    }
?>
<h1>Vue du groupe :</h1>
<form action='?page=seegrp&group=<?=$_GET["group"]?>' method="post">
<label>Nom du groupe :  </label>
<?php
$info = $bdd->prepare('SELECT name, usrEntry FROM cmmp_groups WHERE id = ?');
$info->execute(array($_GET["group"]));
$info = $info->fetchAll();
?>
<input type="text" name="name" value="<?=$info[0]["name"]?>"><br>
<label>Autoriser les utilisateurs a entrer leurs nom : </label> <input type="checkbox" name="usrEntry"
<?php
    if ($info[0]["usrEntry"] == 1) {
        echo "checked ";
    }
    if ($config["settings"]["entryGrp"] == ""){
        echo 'disabled title="Le paramètre a été désactivé"';
    }
?>>
<br><br>
<?php
$code = $bdd->query('SELECT cmmp_code.code, cmmp_code.membres, cmmp_code.state, cmmp_code.group_id, cmmp_groups.name
                    FROM cmmp_code
                    INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id 
                    ORDER BY cmmp_code.state DESC, cmmp_code.code ASC');
$code = $code->fetchAll();
$a = count($code);
if ($a > 1) {
    ?>
    <table style="width:100%;" class="tStyle">
        <tr>
            <td class="check"></td>
            <td class="code">Code</td>
            <td class="membre">Membres</td>
            <td class="grp">Groupe</td>
        </tr>
    <?php
    for($i=1;$i<$a;$i++) {
        ?>
        <tr>
            <td><input type="checkbox" name="<?=$code[$i-1]['code']?>"
            <?php
                if ($code[$i-1]["group_id"] === $_GET["group"]) {
                    echo 'checked';
                }
            ?>
            ></td>
            <?php
            switch ($code[$i-1]["state"]) {
                case '0':
                    echo '<td class="unused"">'.$code[$i-1]["code"].'</td>';
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
            <?php
                if ($code[$i-1]["name"] == $info[0]["name"]) {
                    echo '<td class="integre">'.$code[$i-1]["name"];
                }
                else {
                    echo '<td>'.$code[$i-1]["name"];
                }
            ?>
            </td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <td><input type="checkbox" name="<?=$code[$a-1]['code']?>"
        <?php
                if ($code[$a-1]["group_id"] === $_GET["group"]) {
                    echo 'checked';
                }
            ?>></td>
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
        <?php
                if ($code[$a-1]["name"] == $info[0]["name"]) {
                    echo '<td class="integre">'.$code[$a-1]["name"];
                }
                else {
                    echo '<td>'.$code[$a-1]["name"];
                }
            ?>
            </td>
    </tr>
    <?php
}
else {
    echo 'Il n\'y a aucun code d\'enregistré';
}
?>
</table>
<input type="submit" value="Sauvegarder">
</form>
<script>
    var redir = <?=$redir?>;
    if (redir == true) {
        document.location.href="?page=group";
    }
</script>