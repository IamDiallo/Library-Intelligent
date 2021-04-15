<?php 
require_once("recommend.php");
require_once("sample_list.php");
//require_once 'inputData.php';
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'biblio_v2');

// Create connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
// Check connection
if($link === false){
  die("ERROR: Could not connect. " . mysqli_connect_error());
}else{
  //echo "connected";
}

$isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '';

$request = 'https://www.googleapis.com/books/v1/volumes?q=Ecology&maxResults=40&langRestrict=fr&orderBy=newest&key=AIzaSyB4wF3fh4UmO2-lVTlZO3gjCYlHGMUYIRQ';
$response = file_get_contents($request);
$results = json_decode($response, true);
$books = array();
$info = array();
echo "<pre>";
print_r($results);
echo "</pre>";
if($results['totalItems'] >0)
{

  $i=0;
  foreach($results['items'] as $book)
  {
    $books['title'] = $results['items'][$i]['volumeInfo']['title'];
    if (strlen($books['title']) > 300)
    $books['title'] = substr($books['title'], 0, 90) . '...';
    $books['author'] = $results['items'][$i]['volumeInfo']['authors'][0];
    $books['description'] = $results['items'][$i]['volumeInfo']['description'];
    if (strlen($books['description']) > 300)
        $books['description'] = substr($books['description'], 0, 300) . '...';
    $books['categories'] = $results['items'][$i]['volumeInfo']['categories'][0];
    $books['country'] = $results['items'][$i]['saleInfo']['country'];
    $books['imageLinks'] = $results['items'][$i]['volumeInfo']['imageLinks']['thumbnail'];
    $i++;
    
    $title = $books['title'];
    $author = $books['author'];
    $namesAut = preg_split('/\s+/', $author, -1, PREG_SPLIT_NO_EMPTY);
    $description  = $books['description'];
    $categories =  $books['categories'];
    $country =  $books['country'];
    $imageLinks =  $books['imageLinks'];
    echo "<pre>";
    print_r($books);
    echo "</pre>";
    $title = mysqli_real_escape_string($link,$title);
    $description = mysqli_real_escape_string($link,$description);
    $namesAut[1] = mysqli_real_escape_string($link,$namesAut[1]);
    $namesAut[0] = mysqli_real_escape_string($link,$namesAut[0]);
    $query = "SELECT * FROM livre WHERE titre_livre = '$title'";
    $result = mysqli_query($link,$query); //$link is the connection
    if(mysqli_num_rows($result) > 0 )
    {
      echo "Records Already exist.".$i;
      echo "<br/>";
    }
    else
    {
      echo "New Records";
      $sql1 ="INSERT INTO livre (titre_livre,Paragraphe,type_livre,img_livre,cat_pay) VALUES('$title', '$description','$categories','$imageLinks', '$country')";
      if(mysqli_query($link, $sql1)){
        echo "Records Sql1 added successfully.";
      }
      else
      {
        echo "Cannot process query $sql1. " . mysqli_error($link);
      }
      $book_id = $link->insert_id;
      $sql2 = "INSERT INTO auteur (nom_auteur, prenom_auteur) VALUES('$namesAut[1]','$namesAut[0]' )";
      if(mysqli_query($link, $sql2))
      {
        echo "Records added successfully.";
      } 
      else
      {
        echo "Cannot process query $sql2. " . mysqli_error($link);
      }
      $auth_id = $link->insert_id;
      $sql3 = "INSERT INTO ecrire(id_auteur, isbn) Values('$auth_id', '$book_id')";

      if(mysqli_query($link, $sql3)){
        echo "Records added successfully.";
      }
      else
      {
        echo "Cannot process query $sql3. " . mysqli_error($link);
      }
   
    //   $sql4 = "SELECT isbn FROM livre";
    //   $result = mysqli_query($link,$sql4); //$link is the connection
    //   while($row = mysqli_fetch_array($result)){
    //     $cat_pay = array(
    //       'FR',
    //       'ES',
    //       'IT',
    //       'PT',
    //       'JP',
    //       'GB',
    //       'CA'
    //   );
    //   $cat_age = array(
    //     '0', //jeune
    //     '1', //adulte
    //     '2', //jeune-adulte
    //     '3', // Majeur
    // );
    // $type_livre = array(
    //   'Manga', //jeune
    //   'Roman', //adulte
    //   'Bandesdessinees', //jeune-adulte
    //   'Informatique', // Majeur
    //   'Sciencessociales', //jeune
    //   'Cosmography', //adulte
    //   'Education', //jeune-adulte
    //   'Mathematics',// Majeur
    //   'Religion', // Majeur
    //   'Performing Arts', //jeune-adulte
    //   'Medical',// Majeur
    //   'Travel' // Majeur
    // );
    // $annee_publication = array(
    //   '2020', //jeune
    //   '2019', //adulte
    //   '2018', //jeune-adulte
    //   '2017', // Majeur
    //   '2016', //jeune
    // );
    // $livre_format = array(
    //   'Papier', //jeune
    //   'Electronic', //adulte
    //   'Papier-Electronic', //jeune-adulte
    //   'CD', // Majeur
    // );
    // $condition_livre = array(
    //   'Nouveau', //jeune
    //   'Utiliser', //adulte
    //   'Collectable', //jeune-adulte
    // );
    //     $annee_publication = $annee_publication[rand ( 0 , count($annee_publication) -1)];
    //     $livre_format = $livre_format[rand ( 0 , count($livre_format) -1)];
    //     $condition_livre = $condition_livre[rand ( 0 , count($condition_livre) -1)];
    //     $isbn = $row['isbn'];
    //    // echo 'pay:'.$cat_pay.'------------' .'age:'.$cat_age.'--------';
    //    $sql5 = "UPDATE livre SET condition_livre='$condition_livre', livre_format= '$livre_format',annee_publication='$annee_publication' WHERE isbn = '$isbn'";
    //     if(mysqli_query($link, $sql5))
    //     {
    //       echo 'Updated';
    //     }
    //     else
    //     {
    //       echo "Cannot process query $sql5. " . mysqli_error($link);
    //     }
    //   }
    //   }
    //   $sql6 = "SELECT id_auteur FROM auteur";
    //   $result = mysqli_query($link,$sql6); //$link is the connection
    //   while($row = mysqli_fetch_array($result)){
    //     $nom_auteur = array(
    //       'Masashi Kishimoto', //jeune
    //       'Victor Hugo', //adulte
    //       'Akira Toriama', //jeune-adulte
    //       'Paulo Coelho', // Majeur
    //       'Oda Eiichiro', //jeune
    //       'Tite Saint-Exupéry', //adulte
    //       'Antoine Grenand', //jeune-adulte
    //       'Vincent Nietzsche',// Majeur
    //       'Pierre Sapir', // Majeur
    //       'Friedrich Stoczkowski', //jeune-adulte
    //       'Edward Anouilh',// Majeur
    //       'Wiktor Platon' // Majeur
    //   );
    //     $nom_auteur = $nom_auteur[rand ( 0 , count($nom_auteur) -1)];
    //     $id_auteur = $row['id_auteur'];
    //     //echo 'id:'.$id_auteur .'--------------------';
    //    $sql7 = "UPDATE auteur SET nom_auteur='$nom_auteur' WHERE id_auteur = '$id_auteur' ";
    //     if(mysqli_query($link, $sql7))
    //     {
    //       echo 'Updated';
    //     }
    //     else
    //     {
    //       echo "Cannot process query $sql7. " . mysqli_error($link);
    //     }
    //   }

   }
    
    

// /** select of all users's id **/
// $DateAchat = date("Y-m-d");
// $sql6= "SELECT id_client FROM client LIMIT 70 OFFSET 142";
// $id_clients = mysqli_query($link,$sql6); //$link is the connection
// while($row = mysqli_fetch_array($id_clients)){
//  $id_c = $row['id_client'];
//  $sql6= "SELECT isbn FROM livre LIMIT 347 OFFSET 696";
//  $id_livres = mysqli_query($link,$sql6); //$link is the connection
//  while($row = mysqli_fetch_array($id_livres)){
//   $evaluer = array(1.5,1,2,2.5,3,3.5,4,4.5,5,3,4,5,3.5,4.5);
//   $evaluer = $evaluer[rand ( 0 , count($evaluer) -1)];
//   $id_l = $row['isbn'];
//   $sql7= "INSERT INTO achat (id_client,ISBN,date_achat, evaluer) VALUES('$id_c','$id_l','$DateAchat','$evaluer')";

//   if(mysqli_query($link, $sql7))
//      {
//        echo 'Inserted';
//      }
//      else
//      {
//        echo "Cannot process query $sql7. " . mysqli_error($link);
//      }
//  }
}
}

?>
