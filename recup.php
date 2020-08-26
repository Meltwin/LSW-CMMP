<?php
    header("Content-type:application/json");
    include 'admin/private/bddIni.php';
    $req = $bdd->prepare('SELECT cmmp_vers.contenu 
        FROM cmmp_vers 
        INNER JOIN cmmp_code ON cmmp_code.code = cmmp_vers.code
        INNER JOIN cmmp_groups ON cmmp_code.group_id = cmmp_groups.id
        WHERE cmmp_vers.n_vers = ? AND cmmp_code.state = 2 AND cmmp_groups.state = 1');
    $json = [];
    for ($i=1;$i<=12;$i++) {
        $req->execute(array($i));
        $donnee = $req->fetchAll();
        $a = [];
        foreach ($donnee as $value) {
            array_push($a,$value["contenu"]);
        }
        array_push($json,$a);
    }
    echo json_encode($json);