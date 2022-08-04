<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){

        $ID_LIBRO = $_GET['ID_LIBRO'];
        $AUTOR_LIBRO = $_GET['AUTOR_LIBRO'];
        $TITULO_LIBRO = $_GET['TITULO_LIBRO'];
        $SINOPSIS_LIBRO = $_GET['SINOPSIS_LIBRO'];
        $ANO_PUBLICACION_LIBRO = $_GET['ANO_PUBLICACION_LIBRO'];
        $EDITORIAL_LIBRO = $_GET['EDITORIAL_LIBRO'];
        $CODIGO_ISBN_LIBRO = $_GET['CODIGO_ISBN_LIBRO'];
        $CRONOLOGIA_LIBRO = $_GET['CRONOLOGIA_LIBRO'];
        
        $sql = "UPDATE libros SET AUTOR_LIBRO='$AUTOR_LIBRO', TITULO_LIBRO='$TITULO_LIBRO' , SINOPSIS_LIBRO='$SINOPSIS_LIBRO', ANO_PUBLICACION_LIBRO='$ANO_PUBLICACION_LIBRO', EDITORIAL_LIBRO='$EDITORIAL_LIBRO', CODIGO_ISBN_LIBRO='$CODIGO_ISBN_LIBRO', CRONOLOGIA_LIBRO='$CRONOLOGIA_LIBRO' WHERE ID_LIBRO='$ID_LIBRO'";
        
        $query = $mysqli->query($sql);
        echo "Libro actualizado correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }

?>
