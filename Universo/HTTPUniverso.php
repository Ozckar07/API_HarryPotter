<?php
include "../config.php";
include "../utils.php";


$dbConn =  connect($db);

/*
  REALIZA BUSQUEDA ESPECIFICA DE UN OBJETO DE LA BASE
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    //VALIDA LA BUSQUEDA  
  if (isset($_GET['ID_UNIVERSO']))
  {
    $sql = $dbConn->prepare("SELECT * FROM universomagico WHERE ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro del universo con id =  ", $_GET['ID_UNIVERSO'] ;
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM universomagico WHERE ID_UNIVERSO=:ID_UNIVERSO");
      $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC));
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    //"SELECT * FROM escuela.ID_ESCUELA LEFT JOIN casa ON escuela.ID_ESCUELA = casa.ID_CASA"
    $sql = $dbConn->prepare("SELECT * FROM universomagico");
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
  if (isset($_POST['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT * FROM universomagico where ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_UNIVERSO', $_POST['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_UNIVERSO ", $_POST['ID_UNIVERSO'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO universomagico (ID_UNIVERSO, HISTORIA_UNIVERSO, TEMA_UNIVERSO, AUTOR_UNIVERSO, TIPO_UNIVERSO) VALUES(:ID_UNIVERSO, :HISTORIA_UNIVERSO, :TEMA_UNIVERSO, :AUTOR_UNIVERSO, :TIPO_UNIVERSO)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['ID_UNIVERSO'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_UNIVERSO es obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM universomagico where ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro en la escuela con id = ", $_GET['ID_UNIVERSO'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_ESCUELA = $_GET['ID_UNIVERSO'];
      $statement = $dbConn->prepare("DELETE FROM universomagico where ID_UNIVERSO=:ID_UNIVERSO");
      $statement->bindValue(':ID_UNIVERSO', $ID_UNIVERSO);
      $statement->execute();
      echo "Eliminado el registro del universo con id = ", $_GET['ID_UNIVERSO'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_UNIVERSO es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT * FROM universomagico where ID_UNIVERSO=:ID_UNIVERSO ");
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_UNIVERSO'];
      $fields = getParams($input);

      $sql = "UPDATE universomagico
            SET $fields
            WHERE ID_UNIVERSO='$postId'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente del universo ", $_GET['ID_UNIVERSO'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No se encuentra el universo con id = ", $_GET['ID_UNIVERSO'];
    }
  }else{
    echo "El parametro ID_UNIVERSO es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>