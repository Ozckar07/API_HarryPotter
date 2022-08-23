<?php
include "../config.php";
include "../utils.php";


$dbConn =  connect($db);

/*
  REALIZA BUSQUEDA ESPECIFICA DE LA CASA
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    //VALIDA LA BUSQUEDA  
  if (isset($_GET['ID_PELICULA']))
  {
    $sql = $dbConn->prepare("SELECT * FROM pelicula where ID_PELICULA=:ID_PELICULA");
    $sql->bindValue(':ID_PELICULA', $_GET['ID_PELICULA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro de la pelicula con id = ",$_GET['ID_PELICULA'];
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM pelicula where ID_PELICULA=:ID_PELICULA");
      $sql->bindValue(':ID_PELICULA', $_GET['ID_PELICULA']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC));
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    //"SELECT * FROM escuela.ID_ESCUELA LEFT JOIN casa ON escuela.ID_ESCUELA = casa.ID_CASA"
    $sql = $dbConn->prepare("SELECT pelicula.TITULO_PELICULA, pelicula.DIRECTOR_PELICULA, pelicula.ANO_ESTRENO_PELICULA, pelicula.SINOPSIS_PELICULA FROM pelicula ");
    $sql->execute();
    $sql->setFetchMode(PDO::FETCH_ASSOC);
    header("HTTP/1.1 200 OK");
    echo json_encode( $sql->fetchAll()  );
    exit();
  }

}

// CREA UN NUEVO ELEMENTO EN LA BASE DE DATOS
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (isset($_POST['ID_PELICULA'])){
    $sql = $dbConn->prepare("SELECT * FROM pelicula where ID_PELICULA=:ID_PELICULA");
    $sql->bindValue(':ID_PELICULA', $_POST['ID_PELICULA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_PELICULA ", $_POST['ID_PELICULA'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO pelicula (ID_PELICULA, TITULO_PELICULA, DIRECTOR_PELICULA, PRODUCTORA_PELICULA, ANO_ESTRENO_PELICULA, SINOPSIS_PELICULA)
            VALUES
           (:ID_PELICULA, :TITULO_PELICULA, :DIRECTOR_PELICULA, :PRODUCTORA_PELICULA, :ANO_ESTRENO_PELICULA, :SINOPSIS_PELICULA)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['ID_PELICULA'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_PELICULA es obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_PELICULA'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM pelicula where ID_PELICULA=:ID_PELICULA");
    $sql->bindValue(':ID_PELICULA', $_GET['ID_PELICULA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['ID_PELICULA'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_PELICULA = $_GET['ID_PELICULA'];
      $statement = $dbConn->prepare("DELETE FROM pelicula where ID_PELICULA=:ID_PELICULA");
      $statement->bindValue(':ID_PELICULA', $ID_PELICULA);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['ID_PELICULA'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_PELICULA es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_PELICULA'])){
    $sql = $dbConn->prepare("SELECT * FROM pelicula where ID_PELICULA=:ID_PELICULA");
    $sql->bindValue(':ID_PELICULA', $_GET['ID_PELICULA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_PELICULA'];
      $fields = getParams($input);

      $sql = "UPDATE pelicula
            SET $fields
            WHERE ID_PELICULA='$postId'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente la pelicula ", $_GET['ID_PELICULA'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la pelicula ", $_GET['ID_PELICULA'];
    }
  }else{
    echo "El parametro ID_PELICULA es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>