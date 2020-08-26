<?php
    $handle = json_decode(file_get_contents("admin/private/config.json"),true);
    $bdd = new PDO('mysql:host=localhost;dbname='.$handle['bdName'].';charset=utf8',$handle['bdUser'],$handle['bdPass']);
?>