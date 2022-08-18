<?php
include "../config.php";
include "../utils.php";


$dbConn =  connect($db);

/*
  REALIZA BUSQUEDA ESPECIFICAS
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    //VALIDA LA BUSQUEDA  
  if (isset($_GET['ID_LIBRO']) && isset ($_GET['ID_UNIVERSO']))
  {
    $sql = $dbConn->prepare("SELECT * FROM libros where ID_LIBRO=:ID_LIBRO AND ID_UNIVERSO=:ID_UNIVERSO");
    $sql->bindValue(':ID_LIBRO', $_GET['ID_LIBRO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro de la libros con id = ",$_GET['ID_LIBRO'], " o la escuela con id =  ", $_GET['ID_UNIVERSO'] ;
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      echo "Si existe el registro  ";
      $sql = $dbConn->prepare("SELECT * FROM libros where ID_LIBRO=:ID_LIBRO AND ID_UNIVERSO=:ID_UNIVERSO");
      $sql->bindValue(':ID_LIBRO', $_GET['ID_LIBRO']);
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
    $sql = $dbConn->prepare("SELECT * FROM libros");
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
  if (isset($_POST['ID_LIBRO'])){
    $sql = $dbConn->prepare("SELECT * FROM libros where ID_LIBRO=:ID_LIBRO");
    $sql->bindValue(':ID_LIBRO', $_POST['ID_LIBRO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_LIBRO ", $_POST['ID_LIBRO'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO libros (ID_UNIVERSO, ID_LIBRO, TITULO_LIBRO, AUTOR_LIBRO, SINOPSIS_LIBRO, ANO_PUBLICACION_LIBRO, EDITORIAL_LIBRO, CODIGO_ISBN_LIBRO, CRONOLOGIA_LIBRO)
            VALUES
           (:ID_UNIVERSO, :ID_LIBRO, :TITULO_LIBRO, :AUTOR_LIBRO, :SINOPSIS_LIBRO, :ANO_PUBLICACION_LIBRO, :EDITORIAL_LIBRO, :CODIGO_ISBN_LIBRO, :CRONOLOGIA_LIBRO)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['ID_LIBRO'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_LIBRO es obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_LIBRO']) && isset ($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM libros where ID_LIBRO=:ID_LIBRO AND ID_UNIVERSO=:ID_UNIVERSO ");
    $sql->bindValue(':ID_LIBRO', $_GET['ID_LIBRO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['ID_LIBRO'], " en la escuela ", $_GET['ID_UNIVERSO'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_CASA = $_GET['ID_LIBRO'];
      $ID_ESCUELA = $_GET['ID_UNIVERSO'];
      $statement = $dbConn->prepare("DELETE FROM libros where ID_LIBRO=:ID_LIBRO AND ID_UNIVERSO=:ID_UNIVERSO");
      $statement->bindValue(':ID_LIBRO', $ID_CASA);
      $statement->bindValue(':ID_UNIVERSO', $ID_UNIVERSO);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['ID_LIBRO'], " de la escuela ", $_GET['ID_UNIVERSO'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_LIBRO y ID_UNIVERSO es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_LIBRO']) && isset($_GET['ID_UNIVERSO'])){
    $sql = $dbConn->prepare("SELECT * FROM libros where ID_LIBRO=:ID_LIBRO AND ID_UNIVERSO=:ID_UNIVERSO ");
    $sql->bindValue(':ID_LIBRO', $_GET['ID_LIBRO']);
    $sql->bindValue(':ID_UNIVERSO', $_GET['ID_UNIVERSO']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_LIBRO'];
      $postId2 = $input['ID_UNIVERSO'];
      $fields = getParams($input);

      $sql = "UPDATE libros
            SET $fields
            WHERE ID_LIBRO='$postId' AND ID_UNIVERSO='$postId2'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente la libros ", $_GET['ID_LIBRO'], " deL universo ", $_GET['ID_UNIVERSO'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la ID_LIBRO ", $_GET['ID_LIBRO'], " o no se encuentra en la escuela ", $_GET['ID_UNIVERSO'];
    }
  }else{
    echo "El parametro ID_LIBRO y ID_UNIVERSO es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>