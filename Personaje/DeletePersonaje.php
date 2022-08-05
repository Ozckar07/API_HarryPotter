<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    
        $ID_PERSONAJE = $_GET['ID_PERSONAJE'];
        
        $sql = "SELECT ID_PERSONAJE FROM personaje WHERE ID_PERSONAJE='$ID_PERSONAJE'";
        $query = $mysqli->query($sql);
        $resultado = $query->num_rows;
        // echo($resultado);
        
        if($resultado==0){
            
            echo "No existe el personaje";
        }else{
            
            $sql = "DELETE FROM personaje WHERE ID_PERSONAJE='$ID_PERSONAJE'";
            $query = $mysqli->query($sql);
            echo "Personaje eliminado correctamente";
        }

    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>
