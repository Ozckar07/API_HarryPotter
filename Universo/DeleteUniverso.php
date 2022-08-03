<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    
        $ID_UNIVERSO = $_GET['ID_UNIVERSO'];
        
        $sql = "SELECT ID_UNIVERSO FROM universomagico WHERE ID_UNIVERSO='$ID_UNIVERSO'";
        $query = $mysqli->query($sql);
        $resultado = $query->num_rows;
        // echo($resultado);
        
        if($resultado==0){
            
            echo "No existe el Universo";
        }else{
            
            $sql = "DELETE FROM universomagico WHERE ID_UNIVERSO='$ID_UNIVERSO'";
            $query = $mysqli->query($sql);
            echo "Universo eliminado correctamente";
        }

    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>
