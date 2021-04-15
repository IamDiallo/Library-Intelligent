<?php
  if(isset($_SESSION['connecter'])){
    if (isset($_SESSION['nom_client'])) {
      $nom=$_SESSION['nom_client'];
      $id_client = $_SESSION['id_client'];
    }else{
      $nom = "";
    }
  }
  require_once("recommend.php");
  require_once('modele/connexion.php');
  //$result = $link->query("select users.firstname, group_concat(book.bookName), group_concat(book.bookRate) from ownerBook INNER JOIN users ON ownerBook.user_id = users.id INNER JOIN book ON book.bookId = ownerBook.book_id group by users.firstname");
  //On determine sur quelle page on se trouve
  if (isset($_GET['page']) && !empty($_GET['page'])) {
    $currentPage = (int) strip_tags($_GET['page']);

  }else{
    $currentPage= 1;
  }
  //On determine le nombre de livre par page
  $parpage = 12;
  //Calcul du prmier article de la page
  $premier= ($currentPage * $parpage) - $parpage;

?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
  
  <div class="col-md-12">
  <header class="text-center text-white py-5">
      <h1 class="display-4 font-weight-bold mb-4" style="color: #F39539; margin-bottom:15px">Livre Recommender</h1>
  </header>
    <?php 
    $recommendation = new Recommend(); 
    $clientInfo = $recommendation->getClientInfo();
    //print_r($clientInfo);
    //filterBooks(0);
    $verificationAchat = "SELECT id_client from achat WHERE id_client =? ";
    $query3=$myPDO->prepare($verificationAchat);
    $query3->execute(array($id_client));
    $count = $query3->rowCount(); 
    //  $count !=0 changement recommend
    if($count !=0 & $_SESSION['choix'][0] == 1){
      $recommends = $recommendation->getRecommendations($nom);
      foreach ($recommends as $recommend) { 
    ?>
  
      <div class="col-md-4">
        <div class="thumbnail" style="height:50vh;">
          <a href="consultation.php?ISBN=<?php echo  $recommend['ISBN'] ?>">
          <?php if($recommend['img_livre'] != "") {?>
          <img src="<?php echo $recommend['img_livre']; ?>" alt="Lights" style="height:100%;">
          <?php }else {?>
            <img src="img/default_book_cover.jpeg" alt="Lights" style="height:100%;">
            <?php } ?>
          </a>
        </div>
      </div>
    <?php 
      }
  }elseif($_SESSION['choix'][0] == 0){
    $books = $recommendation->getClientInfo();
    $recommends = $recommendation->filterBooks($books);
    $livreAcheter = $recommendation->getClientBook();
    foreach ($recommends as $recommend) { 
      ?>
        <div class="col-md-4">
          <div class="thumbnail" style="height:50vh;">
            <a href="consultation.php?ISBN=<?php echo  $recommend['ISBN'] ?>">
            <?php if($recommend['img_livre'] != "") {?>
            <img src="<?php echo $recommend['img_livre']; ?>" alt="Lights" style="height:100%;">
            <?php }else {?>
              <img src="img/default_book_cover.jpeg" alt="Lights" style="height:100%;">
              <?php } ?>
            </a>
          </div>
        </div>
      <?php 
        }
  }
  else{
     ?>
      <p><a href="#" class="btn btn-info" role="button" style="margin-left:400px">Nouveau Client Commence A faire Des Achats</a><p>
     <?php 
  }
     ?>
     <!-- <div class="col-md-12 text-center" style="margin:20px">
       <p><a href="#" class="btn btn-primary" role="button">Voir plus</a><p>
     </div> -->
  </div>
</body>
</html>