<?php 
    $myPDO = new PDO('mysql:host=localhost;dbname=biblio_v1', 'root', '');
    $id_achat = $_GET['id'];
    $stmt = $myPDO->prepare( "DELETE FROM achat WHERE id_achat = :id_achat" );
    $stmt->bindValue(':id_achat', $id_achat);
    $stmt->execute();
    if($stmt){
        $_SESSION['supprimer'] = "Votre email ou mot de passe est incorrect!";
        header("location:index.php");  
        ob_end_flush();
        exit;
    }else{
        echo "something went wrong";
    }
                   
?>