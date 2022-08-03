<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){

        $Historia_universo = $_GET['Historia_universo'];
        $Tema_universo = $_GET['Tema_universo'];
        $Autor_universo = $_GET['Autor_universo'];
        $Tipo_universo = $_GET['Tipo_universo'];
        
        $sql = "UPDATE harrypotter SET '$Historia_universo', '$Tema_universo', '$Autor_universo', '$Tipo_universo' WHERE id='$id'";
        
        $query = $mysqli->query($sql);
        echo "Universo actualizado correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }

?>
