<?php
    $config = file_get_contents("private/config.json");
    $config = json_decode($config,true);
    if (!empty($_POST)) {
        foreach ($config["settings"] as $key => $value) {
            $config["settings"][$key] = "";
        }
        foreach ($_POST as $key => $value) {
            $config["settings"][$key] = $value;
        }
        $config = json_encode($config);
        file_put_contents("private/config.json",$config);
    }

    function check($value) {
        $config = file_get_contents("private/config.json");
        $config = json_decode($config,true);
        if ($config["settings"][$value] == "on") {
            return "checked";
        }
    }
    function sel($select,$id) {
        $config = file_get_contents("private/config.json");
        $config = json_decode($config,true);
        if ($config["settings"][$select] == $id) {
            return "selected";
        }
    }
?>
<form id="form1" action="?page=settings" method="post">
    <input type="hidden" value="0" name="hidden">
    <ul>
    <!--Paramètres concernant les entrées -->
    <li><h1>Entrées :</h1>
        <ul>
            <li><input type="checkbox" name="entryUser" <?=check("entryUser")?> id="entryGlobal"> <label>Autoriser les utilisateurs à entrer leurs noms lors de l'entrée du sonnet</label></li>
            <li><input type="checkbox" name="entryGrp" <?=check("entryGrp")?> id="entryGrp" onchange="entryAllDisabled();"> <label>Gérer l'autorisation de l'entrée des noms depuis les groupes</label></li>
        </ul>
    </li>

    </ul>
</form>
<button form="form1"><span class="glyphicon glyphicon-floppy-disk"></span> Sauvegarder</button>
<script>
    function entryAllDisabled() {
        var checkGlobal = document.getElementById("entryGlobal");
        var checkGrp = document.getElementById("entryGrp");
        console.log(checkGrp.checked);
        if (checkGrp.checked == true) {
            checkGlobal.disabled = true;
            checkGlobal.checked = false;
        }
        else {
            checkGlobal.disabled = false;
        }
    }
    entryAllDisabled();
</script>
