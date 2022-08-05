<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    
        $id = $_GET['ID_TRANSPORTE'];
        
        $sql = "SELECT ID_TRANSPORTE FROM transporte WHERE ID_TRANSPORTE='$ID_TRANSPORTE'";
        $query = $mysqli->query($sql);
        $resultado = $query->num_rows;
        // echo($resultado);
        
        if($resultado==0){
            
            echo "No existe el Transporte";
        }else{
            
            $sql = "DELETE FROM transporte WHERE ID_TRANSPORTE='$ID_TRANSPORTE'";
            $query = $mysqli->query($sql);
            echo "Transporte eliminado correctamente";
        }

    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>
