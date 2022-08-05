<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){

        $ID_UNIVERSO = $_GET['ID_UNIVERSO'];
        $HISTORIA_UNIVERSO = $_GET['HISTORIA_UNIVERSO'];
        $TEMA_UNIVERSO = $_GET['TEMA_UNIVERSO'];
        $AUTOR_UNIVERSO = $_GET['AUTOR_UNIVERSO'];
        $TIPO_UNIVERSO = $_GET['TIPO_UNIVERSO'];
        
        $sql = "UPDATE universomagico SET HISTORIA_UNIVERSO='$HISTORIA_UNIVERSO', TEMA_UNIVERSO='$TEMA_UNIVERSO', AUTOR_UNIVERSO='$AUTOR_UNIVERSO', TIPO_UNIVERSO='$TIPO_UNIVERSO' WHERE ID_UNIVERSO='$ID_UNIVERSO'";
        
        $query = $mysqli->query($sql);
        echo "Universo actualizado correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }

?>
