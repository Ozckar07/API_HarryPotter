<?php

    require "conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    
        $id = $_GET['Id_Transporte'];
        
        $sql = "SELECT Id_Transporte FROM harrypotter WHERE id='$id_transporte'";
        $query = $mysqli->query($sql);
        $resultado = $query->num_rows;
        // echo($resultado);
        
        if($resultado==0){
            
            echo "No existe el Transporte";
        }else{
            
            $sql = "DELETE FROM harrypotter WHERE id='$id_transporte'";
            $query = $mysqli->query($sql);
            echo "Transporte eliminado correctamente";
        }

    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>
