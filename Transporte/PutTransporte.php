<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){

        $ID_TRANSPORTE  = $_GET['ID_TRANSPORTE'];
        $NOMBRE_TRANSPORTE = $_GET['NOMBRE_TRANSPORTE'];
        $MEDIO_TRANSPORTE = $_GET['MEDIO_TRANSPORTE'];
        $DESCRIPCION_TRANSPORTE = $_GET ['DESCRIPCION_TRANSPORTE'];
        
        $sql = "UPDATE transporte SET NOMBRE_TRANSPORTE='$NOMBRE_TRANSPORTE', MEDIO_TRANSPORTE='$MEDIO_TRANSPORTE', DESCRIPCION_TRANSPORTE='$DESCRIPCION_TRANSPORTE' WHERE ID_TRANSPORTE='$ID_TRANSPORTE'";
        
        $query = $mysqli->query($sql);
        echo "Transporte actualizado correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }

?>
