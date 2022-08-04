<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    
        $ID_PELICULA = $_GET['ID_ESCUELA'];
        
        $sql = "SELECT ID_ESCUELA FROM escuela WHERE ID_ESCUELA='$ID_ESCUELA'";
        $query = $mysqli->query($sql);
        $resultado = $query->num_rows;
        // echo($resultado);
        
        if($resultado==0){
            
            echo "No existe la escuela";
        }else{
            
            $sql = "DELETE FROM escuela WHERE ID_ESCUELA='$ID_ESCUELA'";
            $query = $mysqli->query($sql);
            echo "Escuela eliminada correctamente";
        }

    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>