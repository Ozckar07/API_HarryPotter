<?php
include "../config.php";
include "../utils.php";

$dbConn = connect($db);

/*
REALIZA BUSQUEDA ESPECIFICA
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //VALIDA LA BUSQUEDA
    if (isset($_GET['ID_MONEDA'])) {
        $sql = $dbConn->prepare("SELECT * FROM moneda where ID_MONEDA=:ID_MONEDA");
        $sql->bindValue(':ID_MONEDA', $_GET['ID_MONEDA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
        if ($row_count == 0) {
            header("HTTP/1.1 204 No Content");
            echo "No existe el registro de la moneda con id = ", $_GET['ID_MONEDA'];

        } else {
            //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
            echo "Si existe el registro  ";
            $sql = $dbConn->prepare("SELECT * FROM moneda where ID_MONEDA=:ID_MONEDA");
            $sql->bindValue(':ID_MONEDA', $_GET['ID_MONEDA']);
            $sql->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
            exit();
        }

    } else {
        //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
        $sql = $dbConn->prepare("SELECT * FROM moneda");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit();
    }

}

// CREA UN NUEVO ELEMENTO EN LA BASE DE DATOS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ID_MONEDA'])) {
        $sql = $dbConn->prepare("SELECT * FROM moneda where ID_MONEDA=:ID_MONEDA");
        $sql->bindValue(':ID_MONEDA', $_POST['ID_MONEDA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            header("HTTP/1.1 204 No Content");
            echo "Ya existe la ID_MONEDA ", $_POST['ID_MONEDA'];
        } else {
            echo "Guardado Exitosamente";
            $input = $_POST;
            $sql = "INSERT INTO moneda (ID_MONEDA, ID_UNIVERSO, NOMBRE_MONEDA, CAMBIO_MONEDA, MATERIAL_MONEDA)
            VALUES
           (:ID_MONEDA, :ID_UNIVERSO, :NOMBRE_MONEDA, :CAMBIO_MONEDA, :MATERIAL_MONEDA)";
            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);
            $statement->execute();
            $postId = $dbConn->lastInsertId();
            if ($postId) {
                $input['ID_MONEDA'] = $postId;
                header("HTTP/1.1 200 OK");
                echo json_encode($input);
                exit();
            }
        }
    } else {
        echo "EL campo ID_MONEDA es obligatorio para insertar";
    }

}

//BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['ID_MONEDA'])) {
        $sql = $dbConn->prepare("SELECT COUNT(*) FROM moneda where ID_MONEDA=:ID_MONEDA");
        $sql->bindValue(':ID_MONEDA', $_GET['ID_MONEDA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        // echo $row_count;
        if ($row_count == 0) {
            echo "No existe el registro ", $_GET['ID_MONEDA'];
            header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

        } else {
            $ID_MONEDA = $_GET['ID_MONEDA'];
            $statement = $dbConn->prepare("DELETE FROM moneda where ID_MONEDA=:ID_MONEDA");
            $statement->bindValue(':ID_MONEDA', $ID_MONEDA);
            $statement->execute();
            echo "Eliminado el registro ", $_GET['ID_MONEDA'];
            header("HTTP/1.1 200 OK");
            exit();
        }
    } else {
        echo "El parametro ID_MONEDA es obligatorio para poder eliminar";
    }

}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['ID_MONEDA'])) {
        $sql = $dbConn->prepare("SELECT * FROM moneda where ID_MONEDA=:ID_MONEDA");
        $sql->bindValue(':ID_MONEDA', $_GET['ID_MONEDA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            $input = $_GET;
            $postId = $input['ID_MONEDA'];
            $fields = getParams($input);

            $sql = "UPDATE moneda
            SET $fields
            WHERE ID_MONEDA='$postId'";

            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);

            $statement->execute();
            header("HTTP/1.1 200 OK");
            echo "Actualizada exitosamente la moneda ", $_GET['ID_MONEDA'];
            exit();
        } else {
            header("HTTP/1.1 204 No Content");
            echo "No existe la ID_MONEDA ", $_GET['ID_MONEDA'];
        }
    } else {
        echo "El parametro ID_MONEDA es obligatorio para poder actualizar";
    }
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
