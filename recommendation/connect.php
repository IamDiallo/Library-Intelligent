<?php
  include('../modele/connexion.php');
  $sql="SELECT users.firstname, group_concat(book.bookName), group_concat(book.bookRate) FROM ownerBook INNER JOIN users ON ownerBook.user_id = users.id INNER JOIN book ON book.bookId = ownerBook.book_id GROUP BY users.firstname";

  $query=$myPDO->prepare($sql);
  $query->execute(array());

  $bookarray = array();
  $ratearray = array();
  $rows = array();
  $user = array();

  while($row = $query->fetch()){
  
    $bookarray = explode(',',$row['group_concat(book.bookName)']);
    $ratearray= explode(',',$row['group_concat(book.bookRate)']);
    $namearray = $row[0];
    $inner = array_combine ($bookarray ,$ratearray);
  
    $outerarray = array($row[0]=>$inner);
    $user = json_encode($outerarray, JSON_NUMERIC_CHECK);

    //this will return a nested array
    echo "<pre>";
    print_r($user);
    echo "</pre>";

 }
 require_once('../modele/close.php');
?>