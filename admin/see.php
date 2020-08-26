<?php
    $code = $_GET["code"];
    $req =  $bdd->prepare('SELECT * FROM cmmp_vers WHERE code = :code ORDER BY n_vers ASC');
    $req->execute(array('code'=>$code));
    $donnee = $req->fetchAll();

    $config = file_get_contents('private/config.json');
    $config = json_decode($config, true);

    $ligne = 1;
    $verifRedi = false;

    if (isset($_GET["act"])) {
        if (isset($_POST["add"])) {
            $req = $bdd->prepare('UPDATE cmmp_code SET state = 3, membres = :membres WHERE code = :code');
            $req->execute(array('membres'=>$_POST['membres'],'code'=>$code));
            for($i=1;$i<=$config["nbV"];$i++) {
                $req = $bdd->prepare('UPDATE cmmp_vers SET contenu = :contenu WHERE code = :code AND n_vers= :nvers');
                $req->execute(array('contenu'=>$_POST["ligne".$i],'code'=>$code,'nvers'=>$i));
            }
            $verifRedi = "true";
        }
        elseif (isset($_POST["no"])) {
            $req = $bdd->prepare('DELETE FROM cmmp_vers WHERE code = ?');
            $req->execute(array($code));
            $reqA = $bdd->prepare('UPDATE cmmp_code SET state = 0 WHERE code = ?');
            $reqA->execute(array($code));
            $verifRedi = "true";
        }
    }
?>
<h1>Poème code <?=$code?> :</h1>
<form style="margin-right:90px;" method="post" action="?page=see&code=<?=$code?>&act=y">
<div class="seeLine">
<?php
     foreach ($config["forme"] as $key =>$value) {
        for ($i = 0;$i<$value;$i++) {
            echo '<input class="seeVers" type="text" value="'.$donnee[$ligne-1]["contenu"].'" name="ligne'.$ligne.'"><br>';
            $ligne++;
            
        }
        ?><br><?php
    }
?>
</div>
<?php
    $donnee = $bdd->prepare('SELECT state,membres FROM cmmp_code WHERE code = ?');
    $donnee->execute(array($code));
    $donnee = $donnee->fetchAll();
?>
<label>Membres : </label>
<input type="text" name="membres" value="<?=$donnee[0]["membres"]?>">
<br>
<br>
<?php

if ($donnee[0]["state"] == 2) {
    ?>
    <button name="add"><span class="glyphicon glyphicon-ok"></span> Valider</button>
    <button name="no"><span class="glyphicon glyphicon-remove"></span> Refuser</button>
    </form>
    <?php
} else {
    ?>
    <button name="add"><span class="glyphicon glyphicon-floppy-disk"></span> Sauvegarder</button>
    <?php
}
?>
<script>
    var verifRedi = <?=$verifRedi?>;
    if (verifRedi == true) {
        document.location.href = "?page=verif";
    }
</script>