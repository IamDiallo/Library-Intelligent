<?php
   session_start();
   require_once("modele/connexion.php");
   $erreur = array();
   if (isset($_POST['inscription'])) {
       // Verifier si le champs ne sont pas vide
       if(!empty($_POST['username']) AND !empty($_POST['password']) AND !empty($_POST['nom'])){
           try{

               $sql = "SELECT count(*) as nbClient FROM client c WHERE c.Email = ?";
               $query1=$myPDO->prepare($sql);
               $query1->execute(array($_POST['username']));
               $resulat = $query1->fetch();
               $nbClient = (int) $resulat['nbClient'];
               if ($nbClient != 1) {
                   $sql = "INSERT INTO client(nom_client, Email, MDP, nationalite, type_livre,age) VALUES(?, ?, ?, ?, ?,?)";
                   $query=$myPDO->prepare($sql);
                   $query->execute(array($_POST['nom'],$_POST['username'],$_POST['password'],$_POST['nationalite'],$_POST['type_livre'],$_POST['age']));
                   $erreur['success'] ="inscription reussi !!! Connectez-vous...";
                   echo $_POST['nom'],$_POST['username'],$_POST['password'],$_POST['nationalite'],$_POST['type_livre'];
               }
               //echo "existe deja";
               if($nbClient >= 1){
                   $erreur['erreur'] ="Mot de pass ou nom d'utilisateur incorrect !!!";

               }


           }
           catch(Exception $e)
           {
               echo $e->getMessage();
           }
       }
   }  
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="CSS/bootstrap/css/bootstrap.min.css"/>
<link rel="stylesheet" href="CSS/theme.css"/>
<script type="text/javascript"></script>
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
  <ul class="nav nav-pills">
      <li><a href="index.php">Acceuil</a></li>
       
      <li class="active"><a href="inscription.php">Inscription</a></li>
  </ul>
</div>
<div class="col-lg-6">
    <div id="iscri">
      <form method="post" action="loginCheck.php" > 
          <input type="text" name="email" placeholder="Email ou Username" required />
          <input type="password" name="pwd" placeholder="Password" required />
          <input type="submit" value="Login"/>
      </form>  
    </div> 
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-success panel1" >
      <div class="panel-heading">Inscription</div>
      <div class="panel-body">
        <fieldset>
    	     <legend><b>Inscripton Individuelle</b></legend>
            <form action="inscription.php" method="post" enctype="multipart/form-data">
        		<table class="login_table">
        		<tr>
        		<td>Email<span>*</span></td>
        		<td><input type="text" name="username" id="username" placeholder="email or username" required></td>
        		</tr>
        		<tr>
        		<td>Pasword<span>*</span></td>
        		<td><input type="password" name="password" id="password" placeholder="password" required></td>
        		</tr>
        		<tr>
        		<td>Nom<span>*</span></td>
        		<td><input type="text" name="nom" id="nom" placeholder="Nom" required></td>
        		</tr>
            <tr>
            <td>Age Category<span>*</span></td>
            <td>
              <select class="form-control" id="age" name="age">
                  <option value="0">Jeune</option>
                  <option value="1">Adulte</option>
                  <option value="2">Jeune-Adulte</option>
                  <option value="3">Majeur</option>
              </select>
            </td>
            <td>
            <tr>
            <td>Nationalite<span>*</span></td>
            <td>
              <select class="form-control" id="country" name="nationalite">
                  <option value="FR">France</option>
                  <option value="IT">Italy</option>
                  <option value="PT">Portugal</option>
                  <option value="ES">Espagne</option>
                  <option value="JPN">Japon</option>
                  <option value="GB"> Grande Bretagane</option>
                  <option value="CA">Canada</option>
              </select>
            </td>
            </tr>
            <tr>
            <td>Type de de livre<span>*</span></td>
            <td><select class="form-control" id="type_livre" name="type_livre">
                <option value="Sciencessociales">Sciencessociales</option>
                <option value="Education">Education</option>
                <option value="Informatique">Informatique</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Religion">Religion</option>
                <option value="Roman">Roman</option>
                <option value="Performing Arts">Performing Arts</option>
                <option value="Travel">Travel</option>
                <option value="Medical">Medical</option>
                <option value="Bandesdessinees">Bandesdessinees</option>
                <option value="Manga">Manga</option>
            </select></td>
            </tr>
    		    <tr>
    		      <td></td>
    		      <td><input type="submit" name="inscription" value="inscription"/></td>
    		    </tr>
    		  </table>
    	   </form>

        </fieldset>  
      </div>
    </div>
  </div>
</div>
<?php
include('composant/footer.php')
?>
</body>
</html>