<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    
        $ID_PELICULA = $_GET['ID_CASA'];
        
        $sql = "SELECT ID_CASA FROM casa WHERE ID_CASA='$ID_CASA'";
        $query = $mysqli->query($sql);
        $resultado = $query->num_rows;
        // echo($resultado);
        
        if($resultado==0){
            
            echo "No existe la casa";
        }else{
            
            $sql = "DELETE FROM casa WHERE ID_CASA='$ID_CASA'";
            $query = $mysqli->query($sql);
            echo "Escuela eliminada correctamente";
        }

    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>