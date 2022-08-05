<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){

        $ID_ESCUELA = $_GET['ID_ESCUELA'];
        $NOMBRE_ESCUELA = $_GET['NOMBRE_ESCUELA'];
        $UBICACION_ESCUELA = $_GET['UBICACION_ESCUELA'];
        $DESCRIPCION_ESCUELA = $_GET['DESCRIPCION_ESCUELA'];
        $DIRECTOR_ESCUELA = $_GET['DIRECTOR_ESCUELA'];
        
        $sql = "UPDATE escuela SET NOMBRE_ESCUELA='$NOMBRE_ESCUELA', UBICACION_ESCUELA='$UBICACION_ESCUELA', DESCRIPCION_ESCUELA='$DESCRIPCION_ESCUELA', DIRECTOR_ESCUELA='$DIRECTOR_ESCUELA' WHERE ID_ESCUELA='$ID_ESCUELA'";
        
        $query = $mysqli->query($sql);
        echo "Escuela actualizada correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }

?>
