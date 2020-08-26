<?php
    session_start();
    if (!isset($_SESSION["connect"])) {
        header("Location:connection.php");
    }
    include 'private/bddIni_private.php';
    $config = file_get_contents('private/config.json');
    $config = json_decode($config,true);
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Page d'administration</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    <nav>
        <div class="home" title="Revenir au site"><a href="../?logout=true"><span class="glyphicon glyphicon-off"></span></a></div>
        <table class="menu">
            <tr>
                <td class="in mid"><a href="./" title="Retourner a l'accueil"><span class="glyphicon glyphicon-home"></span></a></td>
            </tr>
            <tr>
                <td class="in mid"><a href="?page=gen" title="Générer des codes"><span class="glyphicon glyphicon-qrcode"></span></a></td>
            </tr>
            <tr>
                <td class="in mid"><a href="?page=verif" title="Gérer les codes et entrées"><span class="glyphicon glyphicon-saved"></span></a></td>
            </tr>
            <tr>
                <td class="in"><a href="?page=group" title="Gérer les groupes"><span class="glyphicon glyphicon-folder-open"></span></a></td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="in"><a href="?page=import" title="Importer des données"><span class="glyphicon glyphicon-import"></span></a></td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td class="in mid"><a href="?page=changelog" title="Changelog"><span class="glyphicon glyphicon-time"></span></a></td>
            </tr>
            <tr>
                <td class="in"><a href="?page=settings" title="Paramètres"><span class="glyphicon glyphicon-cog"></span></a></td>
            </tr>
        </table>
    </nav>
    <header class="head">
        <?php
        if (isset($_GET["page"])) {
            switch ($_GET["page"]) {
                case 'gen':
                    echo '<h1 class="titre">Code > Inutilisés</h1>';
                    break;
                case 'verif':
                    echo '<h1 class="titre">Code > Utilisés</h1>';
                    break;
                case 'see':
                    echo '<h1 class="titre">Code > Utilisés > Voir</h1>';
                    break;
                case 'changelog':
                    echo '<h1 class="titre">Changelog</h1>';
                    break;
                case 'group':
                    echo '<h1 class="titre">Groupes</h1>';
                    break;
                case 'seegrp':
                    echo '<h1 class="titre">Groupes > Voir</h1>';
                    break;
                case 'settings':
                    echo '<h1 class="titre">Paramètres</h1>';
                    break;
                case 'print':
                    echo '<h1 class="titre">Impression</h1>';
                    break;
                case 'import':
                    echo '<h1 class="titre">Importer</h1>';
                    break;
            }
        }else {
            echo '<h1 class="titre">Accueil du panneau d\'administration</h1>';
        }
        ?>
    </header>
    <div class="page">
    <?php
        if (isset($_GET["page"])) {
            switch ($_GET["page"]) {
                case 'gen':
                    include 'genCode.php';
                    break;
                case 'verif':
                    include 'verif.php';
                    break;
                case 'see':
                    include 'see.php';
                    break;
                case 'changelog':
                    include 'changelog.php';
                    break;
                case 'group':
                    include 'group.php';
                    break;
                case 'seegrp':
                    include 'see_group.php';
                    break;
                case 'settings':
                    include 'parametre.php';
                    break;
                case 'print':
                    include 'print.php';
                    break;
                case 'import':
                    include 'import.php';
                    break;
            }
        }
        else {
            ?>
            <h1>Statistiques :</h1>
            <div class="stat">
                <?php
                $nbCode= $bdd->query('SELECT COUNT(*) FROM cmmp_code'); $nbCode = $nbCode->fetchAll()[0][0];
                $nbCodeL = $bdd->query('SELECT COUNT(*) FROM cmmp_code WHERE state = 0'); $nbCodeL = $nbCodeL->fetchAll()[0][0];
                $nbCodeE = $bdd->query('SELECT COUNT(*) FROM cmmp_code WHERE state = 1'); $nbCodeE = $nbCodeE->fetchAll()[0][0];
                $nbCodeA = $bdd->query('SELECT COUNT(*) FROM cmmp_code WHERE state = 2'); $nbCodeA = $nbCodeA->fetchAll()[0][0];
                $nbCodeI = $bdd->query('SELECT COUNT(*) FROM cmmp_code WHERE state >= 3'); $nbCodeI = $nbCodeI->fetchAll()[0][0];
                $nbVers= $bdd->query('SELECT COUNT(*) FROM cmmp_vers'); $nbVers = $nbVers->fetchAll()[0][0];
                $nbGrp = $bdd->query('SELECT COUNT(*) FROM cmmp_groups'); $nbGrp = $nbGrp->fetchAll()[0][0] - 2;

                $combi = pow($nbCodeA+$nbCodeI,12);
                $combiText = $combi;
                if ($combi >= pow(10,60)) {
                    $combi = number_format($combi/pow(10,60),3)."x10<sup>60</sup>";
                } else if ($combi >= pow(10,57)) {
                    $combi = number_format($combi/pow(10,57),3)."x10<sup>57</sup>";
                }else if ($combi >= pow(10,54)) {
                    $combi = number_format($combi/pow(10,54),3)."x10<sup>54</sup>";
                }else if ($combi >= pow(10,51)) {
                    $combi = number_format($combi/pow(10,51),3)."x10<sup>51</sup>";
                }else if ($combi >= pow(10,48)) {
                    $combi = number_format($combi/pow(10,48),3)."x10<sup>5748</sup>";
                }else if ($combi >= pow(10,45)) {
                    $combi = number_format($combi/pow(10,45),3)."x10<sup>45</sup>";
                }else if ($combi >= pow(10,42)) {
                    $combi = number_format($combi/pow(10,42),3)."x10<sup>42</sup>";
                }else if ($combi >= pow(10,39)) {
                    $combi = number_format($combi/pow(10,39),3)."x10<sup>39</sup>";
                }else if ($combi >= pow(10,36)) {
                    $combi = number_format($combi/pow(10,36),3)."x10<sup>36</sup>";
                }else if ($combi >= pow(10,33)) {
                    $combi = number_format($combi/pow(10,33),3)."x10<sup>33</sup>";
                }else if ($combi >= pow(10,30)) {
                    $combi = number_format($combi/pow(10,30),3)."x10<sup>30</sup>";
                }else if ($combi >= pow(10,27)) {
                    $combi = number_format($combi/pow(10,27),3)."x10<sup>27</sup>";
                }else if ($combi >= pow(10,24)) {
                    $combi = number_format($combi/pow(10,24),3)."x10<sup>24</sup>";
                }else if ($combi >= pow(10,21)) {
                    $combi = number_format($combi/pow(10,21),3)."x10<sup>21</sup>";
                }else if ($combi >= pow(10,18)) {
                    $combi = number_format($combi/pow(10,18),3)."x10<sup>18</sup>";
                }else if ($combi >= pow(10,15)) {
                    $combi = number_format($combi/pow(10,15),3)."x10<sup>15</sup>";
                }else if ($combi >= pow(10,12)) {
                    $combi = number_format($combi/pow(10,12),3)."x10<sup>12</sup>";
                }else if ($combi >= pow(10,9)) {
                    $combi = number_format($combi/pow(10,9),3)."x10<sup>9</sup>";
                }else if ($combi >= pow(10,6)) {
                    $combi = number_format($combi/pow(10,6),3)."x10<sup>6</sup>";
                }else if ($combi >= pow(10,3)) {
                    $combi = number_format($combi/pow(10,3),3)."x10<sup>3</sup>";
                }
                $text = "";
                while ($combiText >= pow(10,3)) {
                    if ($combiText >= pow(10,9)) {
                        $combiText = $combiText/pow(10,9);
                        if (strlen($text) == 0) {
                            $text = "milliards".$text;
                        }
                        else {
                            $text = "milliards de ".$text;
                        }
                    }else if ($combiText >= pow(10,6)) {
                        $combiText = $combiText/pow(10,6);
                        if (strlen($text) == 0) {
                            $text = "millions".$text;
                        }
                        else {
                            $text = "millions de ".$text;
                        }
                    }else if ($combiText >= pow(10,3)) {
                        $combiText = $combiText/pow(10,3);
                        if (strlen($text) == 0) {
                            $text = "milliards".$text;
                        }
                        else {
                            $text = "milliers de ".$text;
                        }
                    }
                }
                $text = number_format($combiText,3)." ".$text;
                ?>
                <ul>
                    <li>Nombre de vers : <?=$nbVers?></li>
                    <li>Nombre de combinaisons : <?=$nbCodeA+$nbCodeI?><sup>12</sup> = <?=$combi?><br>Soit : <strong><i><?=$text?></i></strong> de combinaisons</li>
                    <li style="float:none;">Nombre de groupes : <?=$nbGrp?></li>
                    <li style="float:left;width:42%;">Nombre de code : 
                        <table class="tStyle">
                            <tr style="background-color:rgba(0,0,0,0);">
                                <td class="statC">Libres</td>
                                <td class="unused statNb"><?=$nbCodeL?></td>
                            </tr>
                            <tr>
                                <td class="statC">En édition</td>
                                <td class="edition"><?=$nbCodeE?></td>
                            </tr>
                            <tr>
                                <td class="statC">En attente</td>
                                <td class="attente"><?=$nbCodeA?></td>
                            </tr>
                            <tr>
                                <td class="statc">Intégrés</td>
                                <td class="integre"><?=$nbCodeI?></td>
                            </tr>
                            <tr>
                                <td class="statC">Total</td>
                                <td><?=$nbCode?></td>
                            </tr>
                        </table>
                        
                    </li>
                    <canvas id="stat" width="150px" height="150px" style="left:10px;position:relative;"></canvas>
                </ul>
                
            </div>
            <h1>Informations :</h1>
            <p>Depuis le panneau d'administration vous pouvez gérer le site.</p>
            <p>Vous avez plusieurs pages : </p>
            <ul>
                <li>Le bouton <span class="glyphicon glyphicon-off"></span> vous permet de sortir du panneau d'administration.</li>
                <li>Le bouton <span class="glyphicon glyphicon-home"></span> vous permet de revenir sur cette page.</li>
                <li>Le bouton <span class="glyphicon glyphicon-qrcode"></span> vous redirige sur une page où vous pouvez générer des codes et voir ceux actuellement libres.</li>
                <li>Le bouton <span class="glyphicon glyphicon-folder-open"></span> vous permet de gérer votre groupes de codes.<br><span style="font-style:italic;font-size:13px;">(Les codes n'étant pas dans un groupe sont affiché de base)</span></li>
                <li>Le bouton <span class="glyphicon glyphicon-saved"></span> vous ammène sur une page où vous pouvez : </li>
                <ul>
                    <li>Valider les entrées faites par les élèves.</li>
                    <li>Voir les états des différents codes.</li>
                    <li>Modifier les propriétés d'une entrée.</li>
                    <li>Suprimer une entrée.</li>
                </ul>
                <li>Le bouton <span class="glyphicon glyphicon-time"></span> vous renvoie sur le changelog de développement du site.</li>
                <li>Le bouton <span class="glyphicon glyphicon-cog"></span> vous enmenne sur la page des paramètres.</li>
                
            </ul>
            <br>
            <p>Le site est actuellement en version 2.2.0</p>
            </div>
            <?php
            if ($nbCode != 0) {
            ?>
            <script>
            var free = <?=$nbCodeL?>;
            var edit = <?=$nbCodeE?>;
            var wait = <?=$nbCodeA?>;
            var inter = <?=$nbCodeI?>;
            var total = <?=$nbCode?>;
        
            var angleLast = 1.5*Math.PI;
            var c=document.getElementById("stat");
            var r = 50;
            
            
            function angleNew(alpha) {
                var a = (alpha * (2*Math.PI))/total;
                console.log(a);
                return a;
            }
            function drawArc(value,color) {
                var ctx=c.getContext("2d");
                var angleN = angleNew(value)
                ctx.beginPath();
                ctx.arc(75,75,r,angleLast,(angleLast+angleN), false);
                ctx.lineTo(75,75);
                ctx.closePath();
                ctx.fillStyle = color;
                ctx.fill();
                angleLast += angleN;
            }
        
            drawArc(free, "#e2e2e2");
            drawArc(edit, "#1798d4");
            drawArc(wait,"#cad417");
            drawArc(inter,"#38cc1b");
            </script>
            <?php
            }
        }
        ?>
    
</body>