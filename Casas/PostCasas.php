<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        $ID_ESCUELA = $_POST['ID_ESCUELA'];
        $NOMBRE_ESCUELA = $_POST['NOMBRE_ESCUELA'];
        $UBICACION_ESCUELA = $_POST['UBICACION_ESCUELA'];
        $DESCRIPCION_ESCUELA = $_POST['DESCRIPCION_ESCUELA'];
        $DIRECTOR_ESCUELA = $_POST['DIRECTOR_ESCUELA'];
       
        $sql = "INSERT INTO escuela VALUES('$ID_ESCUELA', '$NOMBRE_ESCUELA', '$UBICACION_ESCUELA', '$DESCRIPCION_ESCUELA', '$DIRECTOR_ESCUELA')";
        
        $query = $mysqli->query($sql);
        echo "Escuela guardada correctamente";   
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
    
?>
