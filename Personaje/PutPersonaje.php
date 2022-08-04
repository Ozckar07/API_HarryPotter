<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){

        $ID_PELICULA = $_GET ['ID_PELICULA'];
        $TITULO_PELICULA = $_GET ['TITULO_PELICULA'];
        $DIRECTOR_PELICULA = $_GET ['DIRECTOR_PELICULA'];
        $PRODUCTORA_PELICULA = $_GET ['PRODUCTORA_PELICULA'];
        $ANO_ESTRENO_PELICULA = $_GET ['ANO_ESTRENO_PELICULA'];
        $SINOPSIS_PELICULA = $_GET ['SINOPSIS_PELICULA'];
        
        $sql = "UPDATE pelicula SET TITULO_PELICULA='$TITULO_PELICULA', DIRECTOR_PELICULA='$DIRECTOR_PELICULA', PRODUCTORA_PELICULA='$PRODUCTORA_PELICULA', ANO_ESTRENO_PELICULA='$ANO_ESTRENO_PELICULA', SINOPSIS_PELICULA='$SINOPSIS_PELICULA' WHERE ID_PELICULA='$ID_PELICULA'";
        
        $query = $mysqli->query($sql);
        echo "Pelicula actualizada correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }

?>
