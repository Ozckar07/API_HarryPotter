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
  if (isset($_GET['ID_TRANSPORTE']) && isset ($_GET['ID_UNIVERSO']))
  {
    $sql = $dbConn->prepare("SELECT * FROM transporte where ID_TRANSPORTE=:ID_TRANSPORTE AND ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_TRANSPORTE', $_GET['ID_TRANSPORTE']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro de la transporte con id = ",$_GET['ID_TRANSPORTE'], " o la universomagico con id =  ", $_GET['ID_UNIVERSO'] ;
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM transporte where ID_TRANSPORTE=:ID_TRANSPORTE AND ID_UNIVERSO=:ID_UNIVERSO");
      $sql->bindValue(':ID_TRANSPORTE', $_GET['ID_TRANSPORTE']);
      $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC));
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    //"SELECT * FROM universomagico.ID_UNIVERSO LEFT JOIN transporte ON universomagico.ID_UNIVERSO = transporte.ID_TRANSPORTE"
    $sql = $dbConn->prepare("SELECT * FROM transporte LEFT JOIN universomagico ON universomagico.ID_UNIVERSO = transporte.ID_TRANSPORTE;");
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
  if (isset($_POST['ID_TRANSPORTE'])){
    $sql = $dbConn->prepare("SELECT * FROM transporte where ID_TRANSPORTE=:ID_TRANSPORTE");
    $sql->bindValue(':ID_TRANSPORTE', $_POST['ID_TRANSPORTE']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_TRANSPORTE ", $_POST['ID_TRANSPORTE'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO transporte (ID_UNIVERSO, ID_TRANSPORTE, NOMBRE_TRANSPORTE, MEDIO_TRANSPORTE, DESCRIPCION_TRANSPORTE)
            VALUES
           (:ID_UNIVERSO, :ID_TRANSPORTE, :NOMBRE_TRANSPORTE, :MEDIO_TRANSPORTE, :DESCRIPCION_TRANSPORTE)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['ID_TRANSPORTE'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_TRANSPORTE es obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_TRANSPORTE']) && isset ($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM transporte where ID_TRANSPORTE=:ID_TRANSPORTE AND ID_UNIVERSO=:ID_UNIVERSO ");
    $sql->bindValue(':ID_TRANSPORTE', $_GET['ID_TRANSPORTE']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['ID_TRANSPORTE'], " en la universomagico ", $_GET['ID_UNIVERSO'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_TRANSPORTE = $_GET['ID_TRANSPORTE'];
      $ID_UNIVERSO = $_GET['ID_UNIVERSO'];
      $statement = $dbConn->prepare("DELETE FROM transporte where ID_TRANSPORTE=:ID_TRANSPORTE AND ID_UNIVERSO=:ID_UNIVERSO");
      $statement->bindValue(':ID_TRANSPORTE', $ID_TRANSPORTE);
      $statement->bindValue(':ID_UNIVERSO', $ID_UNIVERSO);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['ID_TRANSPORTE'], " de la universomagico ", $_GET['ID_UNIVERSO'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_TRANSPORTE y ID_UNIVERSO es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_TRANSPORTE']) && isset($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT * FROM transporte where ID_TRANSPORTE=:ID_TRANSPORTE AND ID_UNIVERSO=:ID_UNIVERSO ");
    $sql->bindValue(':ID_TRANSPORTE', $_GET['ID_TRANSPORTE']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_TRANSPORTE'];
      $postId2 = $input['ID_UNIVERSO'];
      $fields = getParams($input);

      $sql = "UPDATE transporte
            SET $fields
            WHERE ID_TRANSPORTE='$postId' AND ID_UNIVERSO='$postId2'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente la transporte ", $_GET['ID_TRANSPORTE'], " de la universomagico ", $_GET['ID_UNIVERSO'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la ID_TRANSPORTE ", $_GET['ID_TRANSPORTE'], " o no se encuentra en la universomagico ", $_GET['ID_UNIVERSO'];
    }
  }else{
    echo "El parametro ID_TRANSPORTE y ID_UNIVERSO es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>