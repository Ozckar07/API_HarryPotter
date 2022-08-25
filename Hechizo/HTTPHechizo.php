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
  if (isset($_GET['ID_HECHIZO']) && isset ($_GET['ID_UNIVERSO']))
  {
    $sql = $dbConn->prepare("SELECT * FROM hechizo where ID_HECHIZO=:ID_HECHIZO AND ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_HECHIZO', $_GET['ID_HECHIZO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro de la hechizo con id = ",$_GET['ID_HECHIZO'], " o la universomagico con id =  ", $_GET['ID_UNIVERSO'] ;
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM hechizo WHERE ID_HECHIZO=:ID_HECHIZO AND ID_UNIVERSO=:ID_UNIVERSO ");
      $sql->bindValue(':ID_HECHIZO', $_GET['ID_HECHIZO']);
      $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC));
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    //"SELECT * FROM universomagico.ID_UNIVERSO LEFT JOIN hechizo ON universomagico.ID_UNIVERSO = hechizo.ID_HECHIZO"
    $sql = $dbConn->prepare("SELECT hechizo.NOMBRE_HECHIZO, hechizo.MOVIMIENTO_HECHIZO, hechizo.EFECTO_HECHIZO, hechizo.DURACION_HECHIZO, universomagico.TIPO_UNIVERSO, universomagico.TEMA_UNIVERSO FROM hechizo INNER JOIN universomagico ON hechizo.ID_UNIVERSO= universomagico.ID_UNIVERSO");
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
      $sql = "INSERT INTO hechizo (ID_UNIVERSO, ID_HECHIZO, NOMBRE_HECHIZO, EFECTO_HECHIZO, DURACION_HECHIZO, CURA_HECHIZO, MOVIMIENTO_HECHIZO)
            VALUES
           (:ID_UNIVERSO, :ID_HECHIZO, :NOMBRE_HECHIZO, :EFECTO_HECHIZO, :DURACION_HECHIZO, :CURA_HECHIZO, :MOVIMIENTO_HECHIZO)";
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
  if (isset($_GET['ID_HECHIZO']) && isset ($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM hechizo where ID_HECHIZO=:ID_HECHIZO AND ID_UNIVERSO=:ID_UNIVERSO ");
    $sql->bindValue(':ID_HECHIZO', $_GET['ID_HECHIZO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['ID_HECHIZO'], " en la universomagico ", $_GET['ID_UNIVERSO'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_HECHIZO = $_GET['ID_HECHIZO'];
      $ID_UNIVERSO = $_GET['ID_UNIVERSO'];
      $statement = $dbConn->prepare("DELETE FROM hechizo where ID_HECHIZO=:ID_HECHIZO AND ID_UNIVERSO=:ID_UNIVERSO");
      $statement->bindValue(':ID_HECHIZO', $ID_HECHIZO);
      $statement->bindValue(':ID_UNIVERSO', $ID_UNIVERSO);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['ID_HECHIZO'], " de la universomagico ", $_GET['ID_UNIVERSO'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_HECHIZO y ID_UNIVERSO es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_HECHIZO']) && isset($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT * FROM hechizo where ID_HECHIZO=:ID_HECHIZO AND ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_HECHIZO', $_GET['ID_HECHIZO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_HECHIZO'];
      $postId2 = $input['ID_UNIVERSO'];
      $fields = getParams($input);

      $sql = "UPDATE hechizo
            SET $fields
            WHERE ID_HECHIZO='$postId' AND ID_UNIVERSO='$postId2'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente la hechizo ", $_GET['ID_HECHIZO'], " de la universomagico ", $_GET['ID_UNIVERSO'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la ID_HECHIZO ", $_GET['ID_HECHIZO'], " o no se encuentra en la universomagico ", $_GET['ID_UNIVERSO'];
    }
  }else{
    echo "El parametro ID_HECHIZO y ID_UNIVERSO es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>