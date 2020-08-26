<?php
    include 'admin/private/bddIni.php';
    $code = $_COOKIE["code"];
    $req = $bdd->prepare('SELECT * FROM cmmp_code WHERE code = ?');
    $req->execute(array($code));
    $donnee = $req->fetchAll();
    if (isset($_POST["ligne1"]) && isset($_COOKIE["code"]) && ($donnee[0]["state"] ==1)) {
        setcookie("reset","y",time()+3600, null, null, false, true);
        var_dump($_POST);
        $req = $bdd->prepare('INSERT INTO cmmp_vers(code,contenu,n_vers) VALUES(:code,:contenu,:ligne)');
        for ($i=1;$i<=14;$i++) {
            $contenu = $_POST["ligne".$i];
            $req->execute(array('code'=>$code,'contenu'=>$contenu,'ligne'=>$i));
        }
        $reqA = $bdd->prepare('UPDATE cmmp_code SET state = 2 WHERE code = :code ');
        $reqA->execute(array("code"=>$code));
        if (isset($_POST["membre"])) {
            $reqA = $bdd->prepare('UPDATE cmmp_code SET membres = :membres WHERE code = :code ');
            $reqA->execute(array("membres"=>$_POST["membre"],"code"=>$code));
        }
    }
?>
<meta charset="UTF-8">
<script>
    document.location.href="./";
</script>