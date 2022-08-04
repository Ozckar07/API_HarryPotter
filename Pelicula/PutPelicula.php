<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){

        $ID_UNIVERSO = $_PUT ['ID_UNIVERSO'];
        $ID_LIBRO = $_PUT ['ID_LIBRO'];
        $ID_PELICULA = $_PUT ['ID_PELICULA'];
        $TITULO_PELICULA = $_PUT ['TITULO_PELICULA'];
        $DIRECTOR_PELICULA = $_PUT ['DIRECTOR_PELICULA'];
        $PRODUCTORA_PELICULA = $_PUT ['PRODUCTORA_PELICULA'];
        $ANO_ESTRENO_PELICULA = $_PUT ['ANO_ESTRENO_PELICULA'];
        $SINOPSIS_PELICULA = $_PUT ['SINOPSIS_PELICULA'];
        
        $sql = "UPDATE pelicula SET TITULO_PELICULA='$TITULO_PELICULA', DIRECTOR_PELICULA='$DIRECTOR_PELICULA', PRODUCTORA_PELICULA='$PRODUCTORA_PELICULA', ANO_ESTRENO_PELICULA='$ANO_ESTRENO_PELICULA', SINOPSIS_PELICULA='$SINOPSIS_PELICULA' WHERE ID_PELICULA='$ID_PELICULA'";
        
        $query = $mysqli->query($sql);
        echo "Libro actualizado correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }

?>
