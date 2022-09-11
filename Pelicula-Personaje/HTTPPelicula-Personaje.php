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
  if (isset ($_GET['APELLIDO_PERSONAJE']))
  {
    $sql = $dbConn->prepare("SELECT * FROM `pelicula_personaje` LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA RIGHT JOIN escuela ON casa.ID_ESCUELA=escuela.ID_ESCUELA LEFT JOIN libro_personajes ON libro_personajes.ID_PERSONAJE=personaje.ID_PERSONAJE LEFT JOIN libros ON libro_personajes.ID_LIBRO=libros.ID_LIBRO WHERE APELLIDO_PERSONAJE LIKE '%' :APELLIDO_PERSONAJE '%'");
    $sql->bindValue(':APELLIDO_PERSONAJE', $_GET['APELLIDO_PERSONAJE']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe la consulta con id =  ", $_GET['APELLIDO_PERSONAJE'];
      
    }else{
    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
      
      $sql = $dbConn->prepare("SELECT personaje.NOMBRE_PERSONAJE, casa.NOMBRE_CASA, escuela.NOMBRE_ESCUELA, pelicula.TITULO_PELICULA, libros.TITULO_LIBRO, libros.AUTOR_LIBRO FROM pelicula_personaje LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA RIGHT JOIN escuela ON casa.ID_ESCUELA=escuela.ID_ESCUELA LEFT JOIN libro_personajes ON libro_personajes.ID_PERSONAJE=personaje.ID_PERSONAJE LEFT JOIN libros ON libro_personajes.ID_LIBRO=libros.ID_LIBRO WHERE APELLIDO_PERSONAJE LIKE '%' :APELLIDO_PERSONAJE '%'");
      $sql->bindValue(':APELLIDO_PERSONAJE', $_GET['APELLIDO_PERSONAJE']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetchAll(PDO::FETCH_ASSOC));
      echo "Si existe el registro  ";
      exit();
    }

  }
  else {
    //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
    //SELECT pelicula.TITULO_PELICULA, pelicula.ID_PELICULA, personaje.NOMBRE_PERSONAJE, personaje.ID_PERSONAJE FROM `pelicula_personaje` RIGHT JOIN personaje ON personaje.ID_PERSONAJE=pelicula_personaje.ID_PERSONAJE LEFT JOIN pelicula on pelicula.ID_PELICULA=pelicula_personaje.ID_PELICULA ORDER BY pelicula.TITULO_PELICULA;
    $sql = $dbConn->prepare("SELECT * FROM pelicula_personaje");
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
  if (isset ($_POST['ID_PELICULA']) && isset ($_POST['ID_PERSONAJE'])){
    $sql = $dbConn->prepare("SELECT * FROM pelicula_personaje WHERE ID_PELICULA=:ID_PELICULA AND ID_PERSONAJE=:ID_PERSONAJE");
    $sql->bindValue(':ID_PERSONAJE', $_POST['ID_PERSONAJE']);
    $sql->bindValue(':ID_PELICULA', $_POST['ID_PELICULA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la ID_PERSONAJE ", $_POST['ID_PERSONAJE'], "con relacion a la pelicula",$_POST['ID_PELICULA'] ;
    }else{
      $input = $_POST;
      $sql = "INSERT INTO pelicula_personaje (ID_PERSONAJE, ID_PELICULA)
            VALUES
            (:ID_PERSONAJE, :ID_PELICULA)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      echo "Guardado Exitosamente";

      if($postId)
      {
        $input['ID_PERSONAJE'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo ID_PERSONAJE y ID_PELICULA son obligatorio para insertar";
  }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['ID_PERSONAJE']) && isset($_GET['ID_PERSONAJE'])){
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM pelicula_personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_PELICULA = :ID_PELICULA");
    $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
    $sql->bindValue(':ID_PELICULA', $_GET['ID_PELICULA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro en la pelicula_personaje con id = ", $_GET['ID_PERSONAJE'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $ID_PERSONAJE = $_GET['ID_PERSONAJE'];
      $ID_PELICULA = $_GET['ID_PELICULA'];
      $statement = $dbConn->prepare("DELETE FROM pelicula_personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_PELICULA = :ID_PELICULA");
      $statement->bindValue(':ID_PERSONAJE', $ID_PERSONAJE);
      $statement->bindValue(':ID_PELICULA', $ID_PELICULA);
      $statement->execute();
      echo "Eliminado el registro de la pelicula_personaje con id = ", $_GET['ID_PERSONAJE'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro ID_PERSONAJE es obligatorio para poder eliminar";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  if (isset($_GET['ID_PERSONAJE']) && isset($_GET['ID_PELICULA'])){
    $sql = $dbConn->prepare("SELECT * FROM pelicula_personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_PELICULA=:ID_PELICULA");
    $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
    $sql->bindValue(':ID_PELICULA', $_GET['ID_PELICULA']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['ID_PERSONAJE'];
      $postId2 = $input['ID_PELICULA'];
      $fields = getParams($input);

      $sql = "UPDATE pelicula_personaje
            SET $fields
            WHERE ID_PERSONAJE='$postId'AND ID_PELICULA='$postId2'";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizada exitosamente el contenido de la consulta con id ", $_GET['ID_PERSONAJE'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la consulta con id= ", $_GET['ID_PERSONAJE'];
    }
  }else{
    echo "El parametro ID_PERSONAJE es obligatorio para poder actualizar";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>