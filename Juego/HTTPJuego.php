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
  if (isset($_GET['ID_JUEGO']) && isset ($_GET['ID_UNIVERSO']))
  {
    $sql = $dbConn->prepare("SELECT * FROM juego where ID_JUEGO=:ID_JUEGO AND ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_JUEGO', $_GET['ID_JUEGO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro de la juego con id = ",$_GET['ID_JUEGO'], " o la universomagico con id =  ", $_GET['ID_UNIVERSO'] ;
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM juego where ID_JUEGO=:ID_JUEGO AND ID_UNIVERSO=:ID_UNIVERSO");
      $sql->bindValue(':ID_JUEGO', $_GET['ID_JUEGO']);
      $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC));
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    //"SELECT * FROM universomagico.ID_UNIVERSO LEFT JOIN juego ON universomagico.ID_UNIVERSO = juego.ID_JUEGO"
    $sql = $dbConn->prepare("SELECT juego.NOMBRE_JUEGO, juego.CATEGORIA_JUEGO, juego.INSTRUMENTO_JUEGO, universomagico.TIPO_UNIVERSO, universomagico.TEMA_UNIVERSO FROM juego INNER JOIN universomagico ON juego.ID_UNIVERSO= universomagico.ID_UNIVERSO");
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
  if (isset($_POST['ID_JUEGO'])){
    $sql = $dbConn->prepare("SELECT * FROM juego where ID_JUEGO=:ID_JUEGO");
    $sql->bindValue(':ID_JUEGO', $_POST['ID_JUEGO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_JUEGO ", $_POST['ID_JUEGO'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO juego (ID_JUEGO, ID_UNIVERSO, NOMBRE_JUEGO, INSTRUMENTO_JUEGO, CATEGORIA_JUEGO)
            VALUES
           (:ID_JUEGO, :ID_UNIVERSO, :NOMBRE_JUEGO, :INSTRUMENTO_JUEGO, :CATEGORIA_JUEGO)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['ID_JUEGO'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_JUEGO es obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_JUEGO']) && isset ($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM juego where ID_JUEGO=:ID_JUEGO AND ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_JUEGO', $_GET['ID_JUEGO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el ID = ",$_GET['ID_JUEGO'], " en la universomagico ", $_GET['ID_UNIVERSO'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_JUEGO = $_GET['ID_JUEGO'];
      $ID_UNIVERSO = $_GET['ID_UNIVERSO'];
      $statement = $dbConn->prepare("DELETE FROM juego where ID_JUEGO=:ID_JUEGO AND ID_UNIVERSO=:ID_UNIVERSO");
      $statement->bindValue(':ID_JUEGO', $ID_JUEGO);
      $statement->bindValue(':ID_UNIVERSO', $ID_UNIVERSO);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['ID_JUEGO'], " de la universomagico ", $_GET['ID_UNIVERSO'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_JUEGO y ID_UNIVERSO es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_JUEGO']) && isset($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT * FROM juego where ID_JUEGO=:ID_JUEGO AND ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_JUEGO', $_GET['ID_JUEGO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_JUEGO'];
      $postId2 = $input['ID_UNIVERSO'];
      $fields = getParams($input);

      $sql = "UPDATE juego
            SET $fields
            WHERE ID_JUEGO='$postId' AND ID_UNIVERSO='$postId2'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente la juego ", $_GET['ID_JUEGO'], " de la universomagico ", $_GET['ID_UNIVERSO'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la ID_JUEGO ", $_GET['ID_JUEGO'], " o no se encuentra en la universomagico ", $_GET['ID_UNIVERSO'];
    }
  }else{
    echo "El parametro ID_JUEGO y ID_UNIVERSO es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>