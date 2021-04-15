<?php
/**
 * Classe de recommandation 
 */
class Recommend {
    /**
    * Permet de calculer la similarité des achats d'une personne avec les achats des autres clients.\n
    *  Une fois que le calcul a fini la fonction retourn une liste de livres.\n
    * Chaque livre est associé à une note qui determine sa popularité. \n
    */ 
    public function connect(){
        $myPDO = new PDO('mysql:host=localhost;dbname=biblio_v2', 'root', '');
        return $myPDO;
    }    
    public function similarityDistance($preferences, $person1, $person2)
    {
        $similar = array(); // on declare un tableau 
        $sum = 0; // on initialise une somme
        
        // pour chaque client dans la liste de clients
        foreach($preferences[$person1] as $key=>$value)
        {
           // on verifie si la personne a les mêmes livre que chaque autre client
            if(array_key_exists($key, $preferences[$person2]))
                $similar[$key] = 1; // si oui a chaque existence de met la clé 
        }
        
        if(count($similar) == 0)
            return 0;
        
        foreach($preferences[$person1] as $key=>$value)
        {
            if(array_key_exists($key, $preferences[$person2]))
                $sum = $sum + pow($value - $preferences[$person2][$key], 2);
        }
        
        return  1/(1 + sqrt($sum));     
    }

    
    
   /**
    *cette fonction fait la recomandation.\n
    * Elle returne une liste de livres à recommander, ordonnés de façon décroissant sur la note de popularité.\n
    */
    public function getRecommendations($person)
    {
        $total = array(); // on declare un tableau pour mettre
        $simSums = array(); // on declare un tableau pour mettre
        global $ranks;
        $ranks = array(); // on declare un tableau pour mettre
        $sim = 0; //on initialise la similarité a 0
        $preferences = $this->getAllAchats();
        // on fait une loop sur la liste des personnes et on le met sous forme key et value
        foreach($preferences as $otherPerson=>$values)
        {
            
            if($otherPerson != $person) // on substrait le nom de la personne à suggerer dans la liste et on met dans otherPersonne 
            {
                // on fait appel a la fonction similarité et on passe en paramètre la liste de toute de les personnes, la personne à suggerer et les autres personnes
                $sim = $this->similarityDistance($preferences, $person, $otherPerson); 
            }
            
            if($sim > 0)
            {
                foreach($preferences[$otherPerson] as $key=>$value)
                {
                    if(!array_key_exists($key, $preferences[$person]))
                    {
                        if(!array_key_exists($key, $total)) {
                            $total[$key] = 0;
                        }
                        $total[$key] += $preferences[$otherPerson][$key] * $sim;
                        
                        if(!array_key_exists($key, $simSums)) {
                            $simSums[$key] = 0;
                        }
                        $simSums[$key] += $sim;
                    }
                }
                
            }
        }
        // sort the recommendation
        foreach($total as $key=>$value)
        {
            $ranks[$key] = $value / $simSums[$key];
        }
        
       array_multisort($ranks, SORT_DESC); 
        $books = $this->filterBooks($ranks);
    return $books;
        
    }
    /**
     * Cette fonction renvoie la liste des achats.\n 
     * Elle cree deux liste: une liste de client et une liste de livre acheté par chaque client.
     */
    public function getAllAchats()
    {
        $myPDO = $this->connect();
        $livreAchat ="SELECT client.nom_client, group_concat(livre.titre_livre SEPARATOR ';'), 
                        group_concat(achat.evaluer SEPARATOR ';') FROM achat INNER JOIN 
                        client ON achat.id_client = client.id_client INNER JOIN 
                        livre ON livre.ISBN = achat.ISBN GROUP BY client.nom_client";
        $query=$myPDO->prepare($livreAchat);
        $query->execute(array());
        $bookarray = array();
        $ratearray = array();
        $outerarray = array();
        $users = array();
        while($row = $query->fetch()){
            $bookarray = explode(';',$row[1]);
            $ratearray= explode(';',$row[2]);
            $inner = array_combine ($bookarray ,$ratearray);
            $outerarray += array($row[0]=>$inner);
        }
        return $outerarray;
    }
     /**
    * Cette fonction renvoie les categories de livre que le client préfère. Ces critères ont été renseigner lors de l'inscription du client\n
    * O enlève toujour les livre que le client a déja acheté
    */
    public function getClientInfo(){
        $myPDO = $this->connect();
        $sql="SELECT nom_client,email, nationalite, type_livre, age FROM client WHERE id_client =?";
        $queryx=$myPDO->prepare($sql);
        $clientBook = $this->getClientBook();
        $idClient = $_SESSION['id_client'];
        $queryx->execute(array($idClient));
        $profile = $queryx->fetch();
        $innerBooks = array();
        $sql="SELECT l.titre_livre , l.note FROM livre l JOIN ecrire e ON e.ISBN = l.ISBN 
        JOIN auteur a ON a.id_auteur = e.id_auteur 
        WHERE l.type_livre =? OR l.cat_pay=? OR l.cat_age=? AND NOT FIND_IN_SET(`titre_livre`,?) ORDER BY l.note DESC LIMIT 15";
                $query1=$myPDO->prepare($sql);
                $query1->execute([$profile['nationalite'],$profile['type_livre'],$profile['age'],implode(",",$clientBook)]);
                $livres = $query1->fetchAll();
                foreach($livres as $livre){
                    $innerBooks += array($livre['titre_livre']=>$livre['note']);
                    //print_r($innerBooks);
                }
        return $innerBooks;
    }
    /**
     * Cette fonction renvoie la liste des achats d'un seul client.
     */
    public function getClientBook(){
        $myPDO = $this->connect();
        $livres = array();
        $idClient = $_SESSION['id_client'];
        $sql="SELECT group_concat(livre.titre_livre) FROM achat INNER JOIN livre ON livre.ISBN = achat.ISBN Where achat.id_client =?";
        $query=$myPDO->prepare($sql);
        $query->execute(array($idClient));
        while($data = $query->fetch()){
            $livreAcheter = explode(',',$data['group_concat(livre.titre_livre)']);
            $livres[]=$livreAcheter;
        }
        //print_r($livres[0]);
        return $livres[0];

    }
 
    /**
     * Cette fonction permet de filter notre liste de livre à renvoyer en fonction des critere de prefence déja défini.\n
     * Elle prend chaque critère puis verifie son existance dans la liste de livres renvoyer par le fonction getRecommendations.\n 
     * Si le livre existe déja la fonction ne fqis rien sinon le livre est ajouté dans une autre liste contenant tout les livres concernant les critères choisi.\n
     * Si aucun critère n'est choisi la fonction renvoie la meme liste de livre que celle de la fonction getRecommendations.\n
     */
    public function filterBooks($bookRecommend)
    {
        $myPDO = $this->connect();
        $books = array();
        if(isset($_POST['recommend']))
        {
            if($_SESSION['choix'] !=NULL)
            {
                $choix = $_SESSION['choix'];
                if(count($choix)>1)
                {
                    echo "2";
                    
                    $bookRecommend = array_merge($bookRecommend,$this->getClientInfo());
                    $bookRecommend=array_reverse($bookRecommend);
                }
                elseif($choix[0] == 0)
                {
                    //echo "profile";
                    $bookRecommend = $this->getClientInfo();
                    array_multisort($bookRecommend, SORT_DESC);
                }
            }
        }
        foreach ($bookRecommend as $recommend=>$values) { 
            if(count($books) <15)
            {
                $sql="SELECT * 
                FROM livre l
                JOIN ecrire e 
                ON e.ISBN = l.ISBN 
                JOIN auteur a 
                ON a.id_auteur = e.id_auteur 
                WHERE l.titre_livre = ? GROUP BY l.titre_livre ORDER BY l.note DESC";
                $query1=$myPDO->prepare($sql);
                $query1->execute(array($recommend));
                $livres = $query1->fetch();
                if(isset($_POST['preferences']))
                {
                    if( $_SESSION['cat_age'] !=NULL || $_SESSION['type_livre'] !=NULL 
                    || $_SESSION['type_auteur'] !=NULL || $_SESSION['cat_pay'] !=NULL 
                    || $_SESSION['livre_format'] || $_SESSION['condition_livre']
                    || $_SESSION['annee_publication'] !=NULL)
                    {
                        if( $_SESSION['cat_age'] !=NULL){
                            foreach($_SESSION['cat_age'] as $cat_age)
                            {
                                if($livres['cat_age'] == $cat_age){
                                    if(array_search($livres['titre_livre'],$books,true) == false)
                                    {
                                        $books[] = $livres;
                                    }
                                }
                            }
                        }
                        if($_SESSION['type_livre'] !=NULL){
                            foreach($_SESSION['type_livre'] as $type_livre)
                            {
                                if($livres['type_livre'] == $type_livre){
                                    if(array_search($livres['titre_livre'],$books,true) == false){
                                        $books[] = $livres;
                                    }
                                }
                            }
                        }
                        if($_SESSION['type_auteur'] !=NULL){
                            foreach($_SESSION['type_auteur'] as $type_auteur)
                            {
                                if($livres['nom_auteur'] == $type_auteur){
                                    if(array_search($livres['titre_livre'],$books,true) == false){
                                        $books[] = $livres;
                                    }
                                }
                            }
                        }
                        if($_SESSION['cat_pay'] !=NULL){
                            foreach($_SESSION['cat_pay'] as $cat_pay)
                            {
                                if($livres['cat_pay'] == $cat_pay){
                                    if(array_search($livres['titre_livre'],$books,true) == false){
                                        $books[] = $livres;
                                    }
                                }
                            }
                        }
                        if($_SESSION['livre_format'] !=NULL){
                            foreach($_SESSION['livre_format'] as $livre_format)
                            {
                                if($livres['livre_format'] == $livre_format){
                                    if(array_search($livres['titre_livre'],$books,true) ==false){
                                        $books[] = $livres;
                                    }
                                }
                            }
                        }
                        if($_SESSION['condition_livre'] !=NULL)
                        {
                            foreach($_SESSION['condition_livre'] as $condition_livre){
                                if($livres['condition_livre'] == $condition_livre){
                                    if(array_search($livres['titre_livre'],$books,true) ==false){
                                        $books[] = $livres;
                                    }
                                }
                            }
                        }
                        if($_SESSION['annee_publication'] !=NULL)
                        {
                            foreach($_SESSION['annee_publication'] as $annee_publication){
                                if($livres['annee_publication'] == $annee_publication){
                                    if(array_search($livres['titre_livre'],$books,true) ==false){
                                        $books[] = $livres;
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        //echo "Not IN";
                        $books[] = $livres;
                    }
                }
                else
                {
                    $books[] = $livres;
                }
           }
       }
       //print_r($books);
       return $books;
          
    }

    
}

?>