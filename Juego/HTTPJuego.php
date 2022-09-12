<?php
include "../config.php";
include "../utils.php";

$dbConn = connect($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //=========================================================REALIZA UN A BUSQUEDA A TRACES DEL NOMBRE DEL JUEGO
    if (isset($_GET['NOMBRE_JUEGO'])) {
        $sql = $dbConn->prepare("SELECT * FROM `juego`
        WHERE juego.NOMBRE_JUEGO LIKE '%' :NOMBRE_JUEGO '%'
        ORDER BY juego.NOMBRE_JUEGO");
        $sql->bindValue(':NOMBRE_JUEGO', $_GET['NOMBRE_JUEGO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
        if ($row_count == 0) {
            header("HTTP/1.1 204 No Content");
            echo "No existe el registro de la juego con id = ", $_GET['NOMBRE_JUEGO'];
        } else {
            //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
            echo "Si existe el registro  ";
            $sql = $dbConn->prepare("SELECT juego.NOMBRE_JUEGO, juego.INSTRUMENTO_JUEGO, juego.DESCRIPCION_JUEGO, juego.CATEGORIA_JUEGO FROM `juego`
            WHERE juego.NOMBRE_JUEGO LIKE '%' :NOMBRE_JUEGO '%'
            ORDER BY juego.NOMBRE_JUEGO");
            $sql->bindValue(':NOMBRE_JUEGO', $_GET['NOMBRE_JUEGO']);
            $sql->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
            exit();
        }
    } else {
        //MUESTRA TODOS LOS REGISTRO DE LA TABLA JUEGO
        $sql = $dbConn->prepare("SELECT * FROM juego");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit();
    }
}

// ========================================================================CREA UN NUEVO ELEMENTO EN LA BASE DE DATOS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ID_JUEGO'])) {
        $sql = $dbConn->prepare("SELECT * FROM juego where ID_JUEGO=:ID_JUEGO");
        $sql->bindValue(':ID_JUEGO', $_POST['ID_JUEGO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            header("HTTP/1.1 204 No Content");
            echo "Ya existe la ID_JUEGO ", $_POST['ID_JUEGO'];
        } else {
            echo "Guardado Exitosamente";
            $input = $_POST;
            $sql = "INSERT INTO juego (ID_JUEGO, ID_UNIVERSO, NOMBRE_JUEGO, INSTRUMENTO_JUEGO, CATEGORIA_JUEGO)
            VALUES (:ID_JUEGO, :ID_UNIVERSO, :NOMBRE_JUEGO, :INSTRUMENTO_JUEGO, :CATEGORIA_JUEGO)";
            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);
            $statement->execute();
            $postId = $dbConn->lastInsertId();
            if ($postId) {
                $input['ID_JUEGO'] = $postId;
                header("HTTP/1.1 200 OK");
                echo json_encode($input);
                exit();
            }
        }
    } else {
        echo "EL campo ID_JUEGO es obligatorio para insertar";
    }
}

//==========================================================================BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['ID_JUEGO'])) {
        $sql = $dbConn->prepare("SELECT COUNT(*) FROM juego where ID_JUEGO=:ID_JUEGO ");
        $sql->bindValue(':ID_JUEGO', $_GET['ID_JUEGO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        // echo $row_count;
        if ($row_count == 0) {
            echo "No existe el ID = ", $_GET['ID_JUEGO'];
            header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

        } else {
            $ID_JUEGO = $_GET['ID_JUEGO'];
            $statement = $dbConn->prepare("DELETE FROM juego where ID_JUEGO=:ID_JUEGO");
            $statement->bindValue(':ID_JUEGO', $ID_JUEGO);
            $statement->execute();
            echo "Eliminado el registro ", $_GET['ID_JUEGO'];
            header("HTTP/1.1 200 OK");
            exit();
        }
    } else {
        echo "El parametro ID_JUEGO es obligatorio para poder eliminar";
    }

}

//=======================================================================ACTUALIZA LOS DATOS DE UN REGISTRO
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['ID_JUEGO'])) {
        $sql = $dbConn->prepare("SELECT * FROM juego where ID_JUEGO=:ID_JUEGO");
        $sql->bindValue(':ID_JUEGO', $_GET['ID_JUEGO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            $input = $_GET;
            $postId = $input['ID_JUEGO'];
            $fields = getParams($input);

            $sql = "UPDATE juego
            SET $fields
            WHERE ID_JUEGO='$postId'";

            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);

            $statement->execute();
            header("HTTP/1.1 200 OK");
            echo "Actualizada exitosamente la juego ", $_GET['ID_JUEGO'];
            exit();
        } else {
            header("HTTP/1.1 204 No Content");
            echo "No existe la ID_JUEGO ", $_GET['ID_JUEGO'];
        }
    } else {
        echo "El parametro ID_JUEGO es obligatorio para poder actualizar";
    }
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
