<?php
include "../config.php";
include "../utils.php";

$dbConn = connect($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //====================================================================================BUSQUEDA MEDIANTE EL NOMBRE DE LA ESCUELA
    if (isset($_GET['NOMBRE_ESCUELA'])) {
        $sql = $dbConn->prepare("SELECT * FROM `escuela` 
        WHERE escuela.NOMBRE_ESCUELA LIKE '%' :NOMBRE_ESCUELA '%'
        ORDER BY escuela.NOMBRE_ESCUELA");
        $sql->bindValue(':NOMBRE_ESCUELA', $_GET['NOMBRE_ESCUELA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
        if ($row_count == 0) {
            header("HTTP/1.1 204 No Content");
            echo "No existe el registro de la escuela con id =  ", $_GET['NOMBRE_ESCUELA'];
        } else {
            //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
            echo "Si existe el registro  ";
            $sql = $dbConn->prepare("SELECT escuela.NOMBRE_ESCUELA AS 'COLEGIO: ', escuela.DESCRIPCION_ESCUELA AS 'DESCRIPCIÓN', escuela.UBICACION_ESCUELA, escuela.DIRECTOR_ESCUELA AS 'DIRECTOR' FROM `escuela` 
            WHERE escuela.NOMBRE_ESCUELA LIKE '%' :NOMBRE_ESCUELA '%'
            ORDER BY escuela.NOMBRE_ESCUELA");
            $sql->bindValue(':NOMBRE_ESCUELA', $_GET['NOMBRE_ESCUELA']);
            $sql->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
            exit();
        }
    } else {
        //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
        $sql = $dbConn->prepare("SELECT * FROM escuela");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit();
    }

}

//=============================================================================================METODO PARA REAR UN NUEVO REGISTRO DE ESCUELA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ID_ESCUELA'])) {
        $sql = $dbConn->prepare("SELECT * FROM escuela where ID_ESCUELA=:ID_ESCUELA");
        $sql->bindValue(':ID_ESCUELA', $_POST['ID_ESCUELA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            header("HTTP/1.1 204 No Content");
            echo "Ya existe la ID_ESCUELA ", $_POST['ID_ESCUELA'];
        } else {
            echo "Guardado Exitosamente";
            $input = $_POST;
            $sql = "INSERT INTO escuela (ID_ESCUELA, NOMBRE_ESCUELA, UBICACION_ESCUELA, DESCRIPCION_ESCUELA, DIRECTOR_ESCUELA) VALUES(:ID_ESCUELA, :NOMBRE_ESCUELA, :UBICACION_ESCUELA, :DESCRIPCION_ESCUELA, :DIRECTOR_ESCUELA)";
            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);
            $statement->execute();
            $postId = $dbConn->lastInsertId();
            if ($postId) {
                $input['ID_ESCUELA'] = $postId;
                header("HTTP/1.1 200 OK");
                echo json_encode($input);
                exit();
            }
        }
    } else {
        echo "EL campo ID_ESCUELA es obligatorio para insertar";
    }

}

//====================================================================================================BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['ID_ESCUELA'])) {
        $sql = $dbConn->prepare("SELECT COUNT(*) FROM escuela where ID_ESCUELA=:ID_ESCUELA");
        $sql->bindValue(':ID_ESCUELA', $_GET['ID_ESCUELA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        // echo $row_count;
        if ($row_count == 0) {
            echo "No existe el registro en la escuela con id = ", $_GET['ID_ESCUELA'];
            header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

        } else {
            $ID_ESCUELA = $_GET['ID_ESCUELA'];
            $statement = $dbConn->prepare("DELETE FROM escuela where ID_ESCUELA=:ID_ESCUELA");
            $statement->bindValue(':ID_ESCUELA', $ID_ESCUELA);
            $statement->execute();
            echo "Eliminado el registro de la escuela con id = ", $_GET['ID_ESCUELA'];
            header("HTTP/1.1 200 OK");
            exit();
        }
    } else {
        echo "El parametro ID_ESCUELA es obligatorio para poder eliminar";
    }

}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['ID_ESCUELA'])) {
        $sql = $dbConn->prepare("SELECT * FROM escuela where ID_ESCUELA=:ID_ESCUELA ");
        $sql->bindValue(':ID_ESCUELA', $_GET['ID_ESCUELA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            $input = $_GET;
            $postId = $input['ID_ESCUELA'];
            $fields = getParams($input);

            $sql = "UPDATE escuela
            SET $fields
            WHERE ID_ESCUELA='$postId'";

            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);

            $statement->execute();
            header("HTTP/1.1 200 OK");
            echo "Actualizada exitosamente de la escuela ", $_GET['ID_ESCUELA'];
            exit();
        } else {
            header("HTTP/1.1 204 No Content");
            echo "No se encuentra la escuela con id = ", $_GET['ID_ESCUELA'];
        }
    } else {
        echo "El parametro ID_ESCUELA es obligatorio para poder actualizar";
    }
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
