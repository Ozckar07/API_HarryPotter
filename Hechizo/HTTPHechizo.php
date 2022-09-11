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
  if (isset($_GET['ID_HECHIZO']))
  {
    $sql = $dbConn->prepare("SELECT * FROM hechizo where ID_HECHIZO=:ID_HECHIZO");
    $sql->bindValue(':ID_HECHIZO', $_GET['ID_HECHIZO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro de la hechizo con id = ",$_GET['ID_HECHIZO'];
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM hechizo WHERE ID_HECHIZO=:ID_HECHIZO");
      $sql->bindValue(':ID_HECHIZO', $_GET['ID_HECHIZO']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC));
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    $sql = $dbConn->prepare("SELECT * FROM hechizo");
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
  if (isset($_POST['ID_HECHIZO'])){
    $sql = $dbConn->prepare("SELECT * FROM hechizo where ID_HECHIZO=:ID_HECHIZO");
    $sql->bindValue(':ID_HECHIZO', $_POST['ID_HECHIZO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_HECHIZO ", $_POST['ID_HECHIZO'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO hechizo (ID_UNIVERSO, ID_HECHIZO, NOMBRE_HECHIZO, EFECTO_HECHIZO, DURACION_HECHIZO, CURA_HECHIZO)
            VALUES
           (:ID_UNIVERSO, :ID_HECHIZO, :NOMBRE_HECHIZO, :EFECTO_HECHIZO, :DURACION_HECHIZO, :CURA_HECHIZO)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['ID_HECHIZO'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_HECHIZO es obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_HECHIZO'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM hechizo where ID_HECHIZO=:ID_HECHIZO");
    $sql->bindValue(':ID_HECHIZO', $_GET['ID_HECHIZO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['ID_HECHIZO'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_HECHIZO = $_GET['ID_HECHIZO'];
      $statement = $dbConn->prepare("DELETE FROM hechizo where ID_HECHIZO=:ID_HECHIZO");
      $statement->bindValue(':ID_HECHIZO', $ID_HECHIZO);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['ID_HECHIZO'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_HECHIZO es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_HECHIZO'])){
    $sql = $dbConn->prepare("SELECT * FROM hechizo where ID_HECHIZO=:ID_HECHIZO");
    $sql->bindValue(':ID_HECHIZO', $_GET['ID_HECHIZO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_HECHIZO'];
      $fields = getParams($input);

      $sql = "UPDATE hechizo
            SET $fields
            WHERE ID_HECHIZO='$postId'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente la hechizo ", $_GET['ID_HECHIZO'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la ID_HECHIZO ", $_GET['ID_HECHIZO'];
    }
  }else{
    echo "El parametro ID_HECHIZO es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>