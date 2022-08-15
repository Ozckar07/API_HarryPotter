<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){

        $ID_ESCUELA = $_GET['ID_ESCUELA'];
        $ID_CASA = $_GET['ID_CASA'];
        $NOMBRE_CASA = $_GET['NOMBRE_CASA'];
        $COLOR_CASA = $_GET['COLOR_CASA'];
        $VIRTUD_CASA = $_GET['VIRTUD_CASA'];
        $NOMBRE_FANTASMA_CASA = $_GET['NOMBRE_FANTASMA_CASA'];
        
        $sql = "UPDATE casa SET ID_ESCUELA ='$ID_ESCUELA', NOMBRE_CASA='$NOMBRE_CASA', COLOR_CASA='$COLOR_CASA', VIRTUD_CASA='$VIRTUD_CASA', NOMBRE_FANTASMA_CASA='$NOMBRE_FANTASMA_CASA' WHERE ID_CASA='$ID_CASA'";
        
        $query = $mysqli->query($sql);
        echo "Casa actualizada correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }

?>
