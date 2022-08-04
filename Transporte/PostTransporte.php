<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        $ID_UNIVERSO = $_POST['ID_UNIVERSO'];
        $ID_TRANSPORTE  = $_POST['ID_TRANSPORTE'];
        $NOMBRE_TRANSPORTE = $_POST['NOMBRE_TRANSPORTE'];
        $MEDIO_TRANSPORTE = $_POST['MEDIO_TRANSPORTE'];
        $DESCRIPCION_TRANSPORTE = $_POST ['DESCRIPCION_TRANSPORTE'];

        $sql = "INSERT INTO transporte VALUES ('$ID_UNIVERSO', '$ID_TRANSPORTE', '$NOMBRE_TRANSPORTE', '$MEDIO_TRANSPORTE', '$DESCRIPCION_TRANSPORTE')";
        
        $query = $mysqli->query($sql);
        echo "Transporte guardado correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
    
?>
