<?php
session_start();
session_name('SESSION');
//session_start();

include('modele/connexion.php');


//la verfication de variable session et et verfier resevoir la variable b
if(isset($_SESSION['connecter'])){ 

  if(isset($_GET['d'])){
      session_destroy();
      $_SESSION['connecter']=false;
    }
}

//si n'y a pas la variable de connecter alors connecter est false
if(!isset($_SESSION['connecter'])) 
  $_SESSION['connecter']=false; 
   
//pour lad conexion je dois verfier dans la premier temps est ce que j'ai les variable 
if(!empty($_POST['email']) AND !empty($_POST['pwd'])) 
{
  
  $sql="SELECT * FROM client WHERE Email=? AND MDP=?";
  $query=$myPDO->prepare($sql);
  $query->execute(array($_POST['email'],$_POST['pwd']));
  $count = $query->rowCount();
  if($count > 0)
  {
    if($_POST['email'] =="admin@biblio.com" AND $_POST['pwd']== "adminDiallo"){
      $_SESSION['connecter']=true;
      header("location:admin.php");
    }else{
      $_SESSION['connecter']=true;
      if($client_connecter=$query->fetch()) {
        //stocke tt les variable dans session pour travaille dans tous les page
        $_SESSION['id_client']=$client_connecter['id_client'];
        $_SESSION['nom_client']=$client_connecter['nom_client'];
        $_SESSION['Email']=$client_connecter['Email'];
        $_SESSION['adresse']=$client_connecter['adresse'];
        $_SESSION['MDP']=$client_connecter['MDP'];
        $_SESSION['Date_inscription']=$client_connecter['Date_inscription'];
      }
      header("location: myprofil.php");
    }
  }
  else
  {
    header("location:index.php"); 
  }
   
  
}