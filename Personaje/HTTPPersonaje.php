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
  if (isset($_GET['ID_PERSONAJE']) && isset ($_GET['ID_CASA']) && isset ($_GET['ID_ESCUELA']))
  {
    $sql = $dbConn->prepare("SELECT * FROM personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_CASA=:ID_CASA AND ID_ESCUELA=:ID_ESCUELA");
    $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
    $sql->bindValue(':ID_CASA', $_GET['ID_CASA']);
    $sql->bindValue(':ID_ESCUELA', $_GET['ID_ESCUELA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro de la personaje con id = ",$_GET['ID_PERSONAJE'], " de la casa con id =  ", $_GET['ID_CASA'], " perteneciente a la escuela con id= ", $_GET['ID_ESCUELA'];
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_CASA=:ID_CASA AND ID_ESCUELA=:ID_ESCUELA");
      $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
      $sql->bindValue(':ID_CASA', $_GET['ID_CASA']);
      $sql->bindValue(':ID_ESCUELA', $_GET['ID_ESCUELA']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC));
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    //"SELECT * FROM escuela.ID_ESCUELA LEFT JOIN casa ON escuela.ID_ESCUELA = casa.ID_CASA"
    $sql = $dbConn->prepare("SELECT * FROM personaje");
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
  if (isset($_POST['ID_PERSONAJE'])){
    $sql = $dbConn->prepare("SELECT * FROM personaje where ID_PERSONAJE=:ID_PERSONAJE");
    $sql->bindValue(':ID_PERSONAJE', $_POST['ID_PERSONAJE']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_PERSONAJE ", $_POST['ID_PERSONAJE'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO personaje (ID_PERSONAJE, ID_ESCUELA, ID_CASA, NOMBRE_PERSONAJE, APELLIDO_PERSONAJE, FECHA_NACIMIENTO_PERSONAJE, RAZA_PERSONAJE, PAPEL_PERSONAJE, FOTO_PERSONAJE, ACTOR_PERSONAJE)
            VALUES
           (:ID_PERSONAJE, :ID_ESCUELA, :ID_CASA, :NOMBRE_PERSONAJE, :APELLIDO_PERSONAJE, :FECHA_NACIMIENTO_PERSONAJE, :RAZA_PERSONAJE, :PAPEL_PERSONAJE, :FOTO_PERSONAJE, :ACTOR_PERSONAJE)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['ID_PERSONAJE'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_PERSONAJE es obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_PERSONAJE']) && isset ($_GET['ID_CASA']) && isset ($_GET['ID_ESCUELA'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_CASA=:ID_CASA AND ID_ESCUELA=:ID_ESCUELA ");
    $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
    $sql->bindValue(':ID_CASA', $_GET['ID_CASA']);
    $sql->bindValue(':ID_ESCUELA', $_GET['ID_ESCUELA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['ID_PERSONAJE'], " del libro", $_GET['ID_CASA'], " en el universo ", $_GET['ID_ESCUELA'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_PERSONAJE = $_GET['ID_PERSONAJE'];
      $ID_CASA = $_GET['ID_CASA'];
      $ID_ESCUELA = $_GET['ID_ESCUELA'];
      $statement = $dbConn->prepare("DELETE FROM personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_CASA=:ID_CASA AND ID_ESCUELA=:ID_ESCUELA");
      $statement->bindValue(':ID_PERSONAJE', $ID_PERSONAJE);
      $statement->bindValue(':ID_CASA', $ID_CASA);
      $statement->bindValue(':ID_ESCUELA', $ID_ESCUELA);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['ID_PERSONAJE'], " de la casa ", $_GET['ID_CASA'], " de la escuela ", $_GET['ID_ESCUELA'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_PERSONAJE, ID_CASA y ID_ESCUELA es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_PERSONAJE']) && isset($_GET['ID_CASA']) && isset($_GET['ID_ESCUELA'])){
    $sql = $dbConn->prepare("SELECT * FROM personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_CASA=:ID_CASA AND ID_ESCUELA=:ID_ESCUELA ");
    $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
    $sql->bindValue(':ID_CASA', $_GET['ID_CASA']);
    $sql->bindValue(':ID_ESCUELA', $_GET['ID_ESCUELA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_PERSONAJE'];
      $postId2 = $input['ID_CASA'];
      $postId3 = $input['ID_ESCUELA'];
      $fields = getParams($input);

      $sql = "UPDATE personaje
            SET $fields
            WHERE ID_PERSONAJE='$postId' AND ID_CASA='$postId2' AND ID_ESCUELA='$postId3'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente la personaje ", $_GET['ID_PERSONAJE'], " del libro ", $_GET['ID_CASA'], " del universo ", $_GET['ID_ESCUELA'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la personaje ", $_GET['ID_PERSONAJE'], " no hacer parte del libro ", $_GET['ID_CASA'], " y/o del universo ", $_GET['ID_ESCUELA'] ;
    }
  }else{
    echo "El parametro ID_PERSONAJE, ID_CASA y ID_ESCUELA es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>