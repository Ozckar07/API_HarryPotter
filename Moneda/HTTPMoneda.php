<?php
include "../config.php";
include "../utils.php";


$dbConn =  connect($db);

/*
  REALIZA BUSQUEDA ESPECIFICA
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    //VALIDA LA BUSQUEDA  
  if (isset($_GET['ID_MONEDA']) && isset ($_GET['ID_UNIVERSO']))
  {
    $sql = $dbConn->prepare("SELECT * FROM moneda where ID_MONEDA=:ID_MONEDA AND ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_MONEDA', $_GET['ID_MONEDA']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro de la moneda con id = ",$_GET['ID_MONEDA'], " o la universomagico con id =  ", $_GET['ID_UNIVERSO'] ;
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM moneda where ID_MONEDA=:ID_MONEDA AND ID_UNIVERSO=:ID_UNIVERSO");
      $sql->bindValue(':ID_MONEDA', $_GET['ID_MONEDA']);
      $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC));
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    //"SELECT * FROM universomagico.ID_UNIVERSO LEFT JOIN moneda ON universomagico.ID_UNIVERSO = moneda.ID_MONEDA"
    $sql = $dbConn->prepare("SELECT * FROM moneda LEFT JOIN universomagico ON universomagico.ID_UNIVERSO = moneda.ID_MONEDA;");
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
  if (isset($_POST['ID_MONEDA'])){
    $sql = $dbConn->prepare("SELECT * FROM moneda where ID_MONEDA=:ID_MONEDA");
    $sql->bindValue(':ID_MONEDA', $_POST['ID_MONEDA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_MONEDA ", $_POST['ID_MONEDA'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO moneda (ID_MONEDA, ID_UNIVERSO, NOMBRE_MONEDA, CAMBIO_MONEDA)
            VALUES
           (:ID_MONEDA, :ID_UNIVERSO, :NOMBRE_MONEDA, :CAMBIO_MONEDA)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['ID_MONEDA'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_MONEDA es obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_MONEDA']) && isset ($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM moneda where ID_MONEDA=:ID_MONEDA AND ID_UNIVERSO=:ID_UNIVERSO ");
    $sql->bindValue(':ID_MONEDA', $_GET['ID_MONEDA']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['ID_MONEDA'], " en la universomagico ", $_GET['ID_UNIVERSO'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_MONEDA = $_GET['ID_MONEDA'];
      $ID_UNIVERSO = $_GET['ID_UNIVERSO'];
      $statement = $dbConn->prepare("DELETE FROM moneda where ID_MONEDA=:ID_MONEDA AND ID_UNIVERSO=:ID_UNIVERSO");
      $statement->bindValue(':ID_MONEDA', $ID_MONEDA);
      $statement->bindValue(':ID_UNIVERSO', $ID_UNIVERSO);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['ID_MONEDA'], " de la universomagico ", $_GET['ID_UNIVERSO'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_MONEDA y ID_UNIVERSO es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_MONEDA']) && isset($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT * FROM moneda where ID_MONEDA=:ID_MONEDA AND ID_UNIVERSO=:ID_UNIVERSO ");
    $sql->bindValue(':ID_MONEDA', $_GET['ID_MONEDA']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_MONEDA'];
      $postId2 = $input['ID_UNIVERSO'];
      $fields = getParams($input);

      $sql = "UPDATE moneda
            SET $fields
            WHERE ID_MONEDA='$postId' AND ID_UNIVERSO='$postId2'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente la moneda ", $_GET['ID_MONEDA'], " de la universomagico ", $_GET['ID_UNIVERSO'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la ID_MONEDA ", $_GET['ID_MONEDA'], " o no se encuentra en la universomagico ", $_GET['ID_UNIVERSO'];
    }
  }else{
    echo "El parametro ID_MONEDA y ID_UNIVERSO es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>