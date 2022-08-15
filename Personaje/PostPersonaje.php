<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        $ID_PERSONAJE = $_POST['ID_PERSONAJE'];
        $ID_ESCUELA = $_POST['ID_ESCUELA'];
        $ID_CASA = $_POST['ID_CASA'];
        $NOMBRE_PERSONAJE = $_POST['NOMBRE_PERSONAJE'];
        $APELLIDO_PERSONAJE = $_POST['APELLIDO_PERSONAJE'];
        $FECHA_NACIMIENTO_PERSONAJE = $_POST['FECHA_NACIMIENTO_PERSONAJE'];
        $RAZA_PERSONAJE = $_POST['RAZA_PERSONAJE'];
        $PAPEL_PERSONAJE = $_POST['PAPEL_PERSONAJE'];
        $FOTO_PERSONAJE = $_POST['FOTO_PERSONAJE'];
        $ACTOR_PERSONAJE = $_POST['ACTOR_PERSONAJE'];
       
        $sql = "INSERT INTO personaje VALUES('$ID_PERSONAJE', '$ID_ESCUELA', '$ID_CASA ', '$NOMBRE_PERSONAJE', '$APELLIDO_PERSONAJE', '$FECHA_NACIMIENTO_PERSONAJE', '$RAZA_PERSONAJE', '$PAPEL_PERSONAJE', '$FOTO_PERSONAJE', '$ACTOR_PERSONAJE')";
        
        $query = $mysqli->query($sql);
        echo "Pelicula guardada correctamente";   
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
    
?>
