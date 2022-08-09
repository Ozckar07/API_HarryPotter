<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        $ID_ESCUELA = $_POST['ID_ESCUELA'];
        $ID_CASA = $_POST['ID_CASA'];
        $NOMBRE_CASA = $_POST['NOMBRE_CASA'];
        $COLOR_CASA = $_POST['COLOR_CASA'];
        $VIRTUD_CASA = $_POST['VIRTUD_CASA'];
        $NOMBRE_FANTASMA_CASA = $_POST['NOMBRE_FANTASMA_CASA'];

       
        $sql = "INSERT INTO casa VALUES('$ID_ESCUELA', '$ID_CASA', '$NOMBRE_CASA', '$COLOR_CASA', '$VIRTUD_CASA', '$NOMBRE_FANTASMA_CASA')";
        
        $query = $mysqli->query($sql);
        echo "Casas guardada correctamente";   
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
    
?>
