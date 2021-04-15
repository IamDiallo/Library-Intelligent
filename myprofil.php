<?php
session_start();
/// la verfication de variable session et et verfier resevoir la variable b
if(isset($_SESSION['connecter'])){ 

  if(isset($_GET['d'])){
      session_destroy();
      $_SESSION['connecter']=false;
    }
}
include('modele/connexion.php');
require_once("recommendation/recommend.php");
//si n'y a pas la variable de connecter alors connecter est false
if(!isset($_SESSION['connecter'])){
  $_SESSION['connecter']=false; 
}
if(isset($_POST['preferences']))
{
  if(isset($_POST['type_livre'])){
    $_SESSION['type_livre'] = $_POST['type_livre'];
   }else $_SESSION['type_livre'] = NULL; 
   if(isset($_POST['cat_age'])){
    $_SESSION['cat_age'] = $_POST['cat_age'];
  }else $_SESSION['cat_age'] =NULL; 
  if(isset($_POST['type_auteur'])){
    $_SESSION['type_auteur'] = $_POST['type_auteur'];
  }else $_SESSION['type_auteur'] = NULL;
  if(isset($_POST['cat_pay'])){
    $_SESSION['cat_pay'] = $_POST['cat_pay'];
  }else $_SESSION['cat_pay'] = NULL;
  if(isset($_POST['livre_format'])){
    $_SESSION['livre_format'] = $_POST['livre_format'];
  }else  $_SESSION['livre_format'] = NULL;
  if(isset($_POST['condition_livre'])){
    $_SESSION['condition_livre'] = $_POST['condition_livre'];
  }else $_SESSION['condition_livre'] = NULL;
  if(isset($_POST['annee_publication'])){
    $_SESSION['annee_publication']= $_POST['annee_publication'];
  }else{
    $_SESSION['annee_publication'] = NULL;
  }
}
if(isset($_POST['recommend']) || isset($_POST['preferences']))
{
  if(isset($_POST['choix'])){
    $_SESSION['choix'] = $_POST['choix'];
    //print_r($_SESSION['choix']);
   }else $_SESSION['choix'] = NULL; 
}else{
  $_SESSION['choix'] = NULL;
}
/**Cette requête envoie toute les catégories. de livre*/
$sql="SELECT DISTINCT type_livre FROM livre WHERE type_livre != ''";

$query=$myPDO->prepare($sql);
$query->execute(array());

/**Cette requête envoie toute les noms d'auteurs*/
$sql1="SELECT DISTINCT nom_auteur FROM auteur";
$query1=$myPDO->prepare($sql1);
$query1->execute(array());

/**Cette requête envoie toute les catégories d'age*/
$sql2="SELECT DISTINCT cat_age FROM livre";
$query2=$myPDO->prepare($sql2);
$query2->execute(array());

/**Cette requête envoie toute les catégories de pays*/
$sql3="SELECT DISTINCT cat_pay FROM livre";
$query3=$myPDO->prepare($sql3);
$query3->execute(array());

/**Cette requête envoie toute les années de publication*/
$sql4="SELECT DISTINCT annee_publication FROM livre";
$query4=$myPDO->prepare($sql4);
$query4->execute(array());

/**Cette requête envoie toute les format de livre*/
$sql5="SELECT DISTINCT livre_format FROM livre";
$query5=$myPDO->prepare($sql5);
$query5->execute(array());

$sql6="SELECT DISTINCT condition_livre FROM livre";
$query6=$myPDO->prepare($sql6);
$query6->execute(array());
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="CSS/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="CSS/theme.css"/>
    <link rel="stylesheet" href="CSS/profile1.css"/>
    <script type="text/javascript" src="JQ/jquery-2.2.1.min.js"></script>
    <title>biblioweb</title>
</head>

<body>
<div class="jumbotron">
      <div class="col-lg-8">
          <span class="biblio-logo">Biblioweb<span class="dot">.CYU</span></span>
      </div>
      <div class="col-lg-4">
          <div id="logoright">Bibliotheque du web</div>
      </div>
</div>

<div class="col-lg-6">
  <ul class="nav nav-pills nav1">
           <li class="active"><a href="#">Acceuil</a></li>
   <?php 
 
  if(!$_SESSION['connecter']){//si connecter il n,affiche pas else il affiche
  ?>
           <li><a href="#">Inscription</a></li>
    <?php
  }
    ?> 
           <li><a href="profile.php" >Profile</a></li>
          
  </ul>
</div>
<div class="col-lg-6">
 <div id="iscri">
  <?php 
  if(!$_SESSION['connecter']){
  ?>
        <form method="post" action="" > 
            <input type="text" name="email" placeholder="Email ou Username" required />
            <input type="password" name="pwd" placeholder="Password" required />
            <input type="submit" value="Login"/>
        </form>  
      
    <?php
  }else{
    ?> 

 <div class="col-lg-7">
 </div>
 <div class="col-lg-5">
 <ul class="nav nav-pills">
      <li class="active" id="myModal"><a href="myprofil.php" >Livre Acheter</a></li>
      <li><a href="index.php?d=true" >Deconnecter</a></li>
 </ul>

</div>

    <?php
    }
    ?>

  </div> 
</div>
<div class="row">

<!-- <?php
//include('composant/menu.php');
?> -->

<div class="col-lg-3">
    <div class="col-lg-12">
    <form method="post" action="#">
      <div id="menu1" style="height: auto;">
            <h3>Recommendations en Fonction</h3>
              <ul style="list-style-type:none;">
                <li style="list-style-type:none;">
                <div class="row">
                  <div class="col-lg-9">
                    <a><h4>Profile</h4></a>
                  </div>
                  <div class="col-lg-3">
                      <input class="form-check-input" type="checkbox" name="choix[]" value="0">
                  </div>
                  </div>
                </li>
                <li style="list-style-type:none;">
                <div class="row">
                  <div class="col-lg-9">
                    <a><h4>Achats</h4></a>
                  </div>
                  <div class="col-lg-3">
                      <input class="form-check-input" type="checkbox" name="choix[]" value="1">
                  </div>
                  </div>
                </li>
              <ul>
        </div>
        <input class="btn btn-info" name="recommend" type="submit" style="margin-left:90px; margin-top:20px;" value="Enregistrer">
    </form>
    <form method="post" action="#">
      <div id="menu1" style="height: auto;">
          <h3>Themes</h3>
            <ul>
              <?php while($donnees=$query->fetch()){ ?>
                <li style="list-style-type:none;">
                  <div class="row">
                    <div class="col-lg-9">
                      <a><?php echo $donnees['type_livre']; ?></a>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="type_livre[]" value="<?php echo $donnees['type_livre'] ?>">
                    </div>
                    </div>
                  </div>
                </li>
              <?php }?>
            <ul>
        </div>
        <div id="menu2" style="height: auto;">
            <h3>Auteurs</h3>
                <ul>
                  <?php while ($donnees1=$query1->fetch()) {?>
                    <li style="list-style-type:none;">
                      <div class="row">
                      <div class="col-lg-9">
                          <a><?php echo $donnees1['nom_auteur']; ?></a>
                      </div>
                      <div class="col-lg-3">
                      <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="type_auteur[]" value="<?php echo $donnees1['nom_auteur'] ?>" >
                      </div>
                      </div>
                      </li>
                  <?php 
                  }
                ?>
            </ul>
        </div>
        <div id="menu2" style="height: auto;">
            <h3>Age</h3>
                <ul>
                  <?php while ($donnees2=$query2->fetch()) {?>
                    <li style="list-style-type:none;">
                      <div class="row">
                      <div class="col-lg-9">
                          <a><?php if($donnees2['cat_age'] == 0) { 
                            echo "Jeune";
                            }elseif($donnees2['cat_age'] == 1)
                            { echo "Adulte";}
                            elseif($donnees2['cat_age'] == 2){
                              echo "Jeune-Adulte" ; 
                            }
                            elseif ($donnees2['cat_age'] == 3){
                              echo "Majeur" ; 
                            }
                            ?>
                            </a>
                            
                      </div>
                      <div class="col-lg-3">
                      <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="cat_age[]" value="<?php echo $donnees2['cat_age'] ?>" >
                      </div>
                      </div>
                      </li>
                  <?php 
                  }
                ?>
            </ul>
        </div>
        <div id="menu2" style="height: auto;">
            <h3>Langue</h3>
                <ul>
                  <?php while ($donnees3=$query3->fetch()) {?>
                    <li style="list-style-type:none;">
                      <div class="row">
                      <div class="col-lg-9">
                          <a><?php if($donnees3['cat_pay'] == "ES") { 
                            echo "Espagne";
                            }elseif($donnees3['cat_pay'] == "PT")
                            { echo "Portugal";}
                            elseif($donnees3['cat_pay'] == "FR"){
                              echo "France" ; 
                            }
                            elseif ($donnees3['cat_pay'] == "GB"){
                              echo "Grande Bretagane" ; 
                            }
                            elseif ($donnees3['cat_pay'] == "CA"){
                              echo "Canada" ; 
                            }
                            elseif ($donnees3['cat_pay'] == "IT"){
                              echo "Italie" ; 
                            }
                            elseif ($donnees3['cat_pay'] == "JP"){
                              echo "Japon" ; 
                            }
                            ?>
                            </a>
                            
                      </div>
                      <div class="col-lg-3">
                      <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="cat_pay[]" value="<?php echo $donnees3['cat_pay'] ?>" >
                      </div>
                      </div>
                      </li>
                  <?php 
                  }
                ?>
            </ul>
        </div>
        <div id="menu1" style="height: auto;">
          <h3>Année de Publication</h3>
            <ul>
              <?php while($donnees4=$query4->fetch()){ ?>
                <li style="list-style-type:none;">
                  <div class="row">
                    <div class="col-lg-9">
                      <a><?php echo $donnees4['annee_publication']; ?></a>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="annee_publication[]" value="<?php echo $donnees4['annee_publication'] ?>" >
                    </div>
                    </div>
                  </div>
                </li>
              <?php }?>
            <ul>
        </div>
        <div id="menu1" style="height: auto;">
          <h3>Livre Format</h3>
            <ul>
              <?php while($donnees5=$query5->fetch()){ ?>
                <li style="list-style-type:none;">
                  <div class="row">
                    <div class="col-lg-9">
                      <a><?php echo $donnees5['livre_format']; ?></a>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="livre_format[]" value="<?php echo $donnees5['livre_format'] ?>" >
                    </div>
                    </div>
                  </div>
                </li>
              <?php }?>
            <ul>
        </div>
        <div id="menu1" style="height: auto;">
          <h3>Condition du livre</h3>
            <ul>
              <?php while($donnees6=$query6->fetch()){ ?>
                <li style="list-style-type:none;">
                  <div class="row">
                    <div class="col-lg-9">
                      <a><?php echo $donnees6['condition_livre']; ?></a>
                    </div>
                    <div class="col-lg-3">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="condition_livre[]" value="<?php echo $donnees6['condition_livre'] ?>" >
                    </div>
                    </div>
                  </div>
                </li>
              <?php }?>
            <ul>
        </div>
      </div>
      <input class="btn btn-info" name="preferences" type="submit" style="margin-left:90px; margin-top:20px;" value="Filtre">
    </form>
        
    </div>
  <div class="col-lg-9">
  <h2> Acceuil</h2>
    <div class="panel panel-default panel2">
      <div class="panel-heading">Page d'Acceuil</div>
      <div class="panel-body">
        <div class="row">
          
            <?php include('recommendation/recommandation.php'); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
include('composant/footer.php');?>
<script>

</script>
</body>
</html>










