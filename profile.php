<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Document</title>
    <style>
    .card {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
  max-width: 300px;
  margin: auto;
  text-align: center;
}

.title {
  color: grey;
  font-size: 18px;
}

button {
  border: none;
  outline: 0;
  display: inline-block;
  padding: 8px;
  color: white;
  background-color: #000;
  text-align: center;
  cursor: pointer;
  width: 100%;
  font-size: 18px;
}

a {
  text-decoration: none;
  font-size: 22px;
  color: black;
}

button:hover, a:hover {
  opacity: 0.7;
}
    </style>
</head>
<body>
    <!-- Add icon library -->
 <?php
 session_start();
    $myPDO = new PDO('mysql:host=localhost;dbname=biblio_v2', 'root', '');
    $sql="SELECT nom_client,email, nationalite, type_livre, age FROM client WHERE id_client =?";
    $queryx=$myPDO->prepare($sql);
    $idClient = $_SESSION['id_client'];
    $queryx->execute(array($idClient));
    $profile = $queryx->fetch();
  ?>
<div class="card">
  <img src="img/profile.png" alt="John" style="width:100%">
  <h1><?php echo $profile['nom_client'] ?></h1>
  <h3 class="title"><?php echo $profile['email'] ?></h3>
  <h3><strong>Langue Préférée:</strong><?php echo $profile['nationalite'] ?></h3>
  <h3><strong>Type de livre Préférée:</strong><?php echo $profile['type_livre'] ?></h3>
  <h3><strong>Catégorie d'age Préférée:</strong><?php echo $profile['age'] ?></h3>
  <a href="#"><i class="fa fa-dribbble"></i></a>
  <a href="#"><i class="fa fa-twitter"></i></a>
  <a href="#"><i class="fa fa-linkedin"></i></a>
  <a href="#"><i class="fa fa-facebook"></i></a>
  <p><button><a href="myprofil.php" class="btn btn-info" role="button" style="color:white">Retour</a> </button></p>
</div>
</body>
</html>