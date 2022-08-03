<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        $ID_UNIVERSO = $_POST['ID_UNIVERSO'];
        $ID_LIBRO = $_POST['ID_LIBRO'];
        $AUTOR_LIBRO = $_POST['AUTOR_LIBRO'];
        $SINOPSIS_LIBRO = $_POST['SINOPSIS_LIBRO'];
        $ANO_PUBLICACION_LIBRO = $_POST['ANO_PUBLICACION_LIBRO'];
        $EDITORIAL_LIBRO = $_POST['EDITORIAL_LIBRO'];
        $CODIGO_ISBN_LIBRO = $_POST['CODIGO_ISBN_LIBRO'];
        $CRONOLOGIA_LIBRO = $_POST['CRONOLOGIA_LIBRO'];

        $sql = "INSERT INTO libros VALUES('$ID_UNIVERSO', '$ID_LIBRO', '$TITULO_LIBRO', '$AUTOR_LIBRO', '$SINOPSIS_LIBRO', '$ANO_PUBLICACION_LIBRO', '$EDITORIAL_LIBRO', '$CODIGO_ISBN_LIBRO', '$CRONOLOGIA_LIBRO')";
        
        $query = $mysqli->query($sql);
        echo "Libro guardado correctamente";   
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
    
?>
