<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){

        $Historia_universo = $_POST['Historia_universo'];
        $Tema_universo = $_POST['Tema_universo'];
        $Autor_universo = $_POST['Autor_universo'];
        $Tipo_universo = $_POST['Tipo_universo'];

        $sql = "INSERT INTO harrypotter VALUES ('', '$Historia_universo', '$Tema_universo', '$Autor_universo', '$Tipo_universo')";
        
        $query = $mysqli->query($sql);
        echo "Universo guardado correctamente";
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
    
?>
