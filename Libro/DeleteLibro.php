<?php

    require "../conexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    
        $ID_LIBRO = $_GET['ID_LIBRO'];
        
        $sql = "SELECT ID_LIBRO FROM libros WHERE ID_LIBRO='$ID_LIBRO'";
        $query = $mysqli->query($sql);
        $resultado = $query->num_rows;
        // echo($resultado);
        
        if($resultado==0){
            
            echo "No existe el libro";
        }else{
            
            $sql = "DELETE FROM libros WHERE ID_LIBRO='$ID_LIBRO'";
            $query = $mysqli->query($sql);
            echo "Libro eliminado correctamente";
        }

    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>
