<?php

    require "../conexion.php";
    

    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    
        $sql = "SELECT * FROM libros ";
        $query = $mysqli->query($sql);
        
        $datos = array();
        
        while($resultado = $query->fetch_assoc()) {
            $datos[] = $resultado;
        }
        
        echo json_encode($datos);
        //echo json_encode(array("usuarios" => $datos));
    }else{
        header("HTTP/1.1 501 Not Implemented");
        echo "Error de metodo";
    }
?>
