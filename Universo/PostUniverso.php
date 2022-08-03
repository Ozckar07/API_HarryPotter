<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
      

        $ID_UNIVERSO = $_POST['ID_UNIVERSO'];
        $HISTORIA_UNIVERSO = $_POST['HISTORIA_UNIVERSO'];
        $TEMA_UNIVERSO = $_POST['TEMA_UNIVERSO'];
        $AUTOR_UNIVERSO = $_POST['AUTOR_UNIVERSO'];
        $TIPO_UNIVERSO = $_POST['TIPO_UNIVERSO'];

        $sql = "INSERT INTO universomagico VALUES ('$ID_UNIVERSO', '$HISTORIA_UNIVERSO', '$TEMA_UNIVERSO', '$AUTOR_UNIVERSO', '$TIPO_UNIVERSO')";
        $query = $mysqli->query($sql);
        echo "Universo guardado correctamente";
    }else{
        echo("algo salio mal final");
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
    
?>
