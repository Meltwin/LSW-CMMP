<?php 
    $redir = "false";
    if (isset($_POST["import"])) {
        if ($handle = opendir('./files')) {
            $t = [[],[],[],[],[],[],[],[],[],[],[],[],[],[]];
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $vers =explode("_",explode(".",$entry)[0])[1];
                    $file = file("./files/".$entry);
                    foreach($file as $value) {
                        array_push($t[$vers - 1],$value);
                    }
                }
            }
            closedir($handle);
        }
        $nb = count($t[0]);

        $usedData = $bdd->query('SELECT code, membres, state FROM cmmp_code WHERE code < 100000 ORDER BY state DESC');
        $usedData = $usedData->fetchAll();
        $used = [];
        foreach ($usedData as $value)  {
            array_push($used,$value["code"]);
        }
        $code = [];
        $addC = $bdd->prepare('INSERT INTO cmmp_code(code,state,group_id) VALUES(:code,3,:group)');
        $addV = $bdd->prepare('INSERT INTO cmmp_vers(code,contenu,n_vers) VALUES(:code,:contenu,:ligne)');
        for($i = 0; $i < $nb; $i++) {
            $maked = 0;
            $cToUse = 0;
            while ($maked < 1) {
                $c = strval(rand(0,100000));
                if ((false === array_search($c, $used)) && (false === array_search($c, $code))) {
                    array_push($code,$c);
                    $cToUse = intval($c);
                    $addC->execute(array("code"=>$c,"group"=>1));
                    $maked++;
                }
            }
            for($a = 1;$a <=14;$a++) {
                $envoie = $t[$a-1][$i];
                $addV->execute(array("code"=>$cToUse,"contenu"=>$envoie,"ligne"=>$a));
            }
        }
        $redir = "true";
    }
?>
<form action="?page=import" method="post">
    <input type="submit" name="import" value="Importer les données">
</form>
<script>
    var redir = <?=$redir?>;
    if (redir == true) {
        document.location.href="?page=verif";
    }
</script>