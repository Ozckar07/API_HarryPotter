<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        $ID_UNIVERSO = $_POST['ID_UNIVERSO'];
        $ID_LIBRO = $_POST['ID_LIBRO'];
        $ID_PELICULA = $_POST['ID_PELICULA'];
        $TITULO_PELICULA = $_POST['TITULO_PELICULA'];
        $DIRECTOR_PELICULA = $_POST['DIRECTOR_PELICULA'];
        $PRODUCTORA_PELICULA = $_POST['PRODUCTORA_PELICULA'];
        $ANO_ESTRENO_PELICULA = $_POST['ANO_ESTRENO_PELICULA'];
        $SINOPSIS_PELICULA = $_POST['SINOPSIS_PELICULA'];
       
        $sql = "INSERT INTO pelicula VALUES('$ID_UNIVERSO', '$ID_LIBRO', '$ID_PELICULA', '$TITULO_PELICULA', '$DIRECTOR_PELICULA', '$PRODUCTORA_PELICULA', '$ANO_ESTRENO_PELICULA', '$SINOPSIS_PELICULA')";
        
        $query = $mysqli->query($sql);
        echo "Pelicula guardada correctamente";   
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
    
?>
