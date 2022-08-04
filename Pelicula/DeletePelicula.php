<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    
        $ID_PELICULA = $_GET['ID_PELICULA'];
        
        $sql = "SELECT ID_PELICULA FROM pelicula WHERE ID_PELICULA='$ID_PELICULA'";
        $query = $mysqli->query($sql);
        $resultado = $query->num_rows;
        // echo($resultado);
        
        if($resultado==0){
            
            echo "No existe la pelicula";
        }else{
            
            $sql = "DELETE FROM pelicula WHERE ID_PELICULA='$ID_PELICULA'";
            $query = $mysqli->query($sql);
            echo "Pelicula eliminado correctamente";
        }

    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>
