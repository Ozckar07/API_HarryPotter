<?php
include "../config.php";
include "../utils.php";

$dbConn = connect($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //=============================================================BUSQUEDA MEDIANTE EL NOMBRE DE LA CASA
    if (isset($_GET['NOMBRE_CASA'])) {
        $sql = $dbConn->prepare("SELECT * FROM `casa` 
        RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA 
        WHERE NOMBRE_CASA LIKE '%' :NOMBRE_CASA '%'");
        $sql->bindValue(':NOMBRE_CASA', $_GET['NOMBRE_CASA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
        if ($row_count == 0) {
            header("HTTP/1.1 204 No Content");
            echo "No existe el registro de la casa con id = ", $_GET['NOMBRE_CASA'];

        } else {
            //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
            echo "Si existe el registro  ";
            $sql = $dbConn->prepare("SELECT casa.NOMBRE_CASA AS 'CASA: ', 
            casa.COLOR_CASA AS 'EMBLEMAS', casa.VIRTUD_CASA AS 'VIRTUDES', 
            casa.NOMBRE_FANTASMA_CASA AS 'FANTAS O GUARDIAN', 
            escuela.NOMBRE_ESCUELA AS 'ESCUELA' 
            FROM `casa` 
            RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA 
            WHERE NOMBRE_CASA LIKE '%' :NOMBRE_CASA '%' 
            ORDER BY escuela.NOMBRE_ESCUELA");
            $sql->bindValue(':NOMBRE_CASA', $_GET['NOMBRE_CASA']);
            $sql->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
            exit();
        }
    } else {
        //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
        $sql = $dbConn->prepare("SELECT * FROM casa");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit();
    }

}

//========================================================= METODO PARA INSERTAR UN NUEVO REGISTRO
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ID_CASA'])) {
        $sql = $dbConn->prepare("SELECT * FROM casa where ID_CASA=:ID_CASA");
        $sql->bindValue(':ID_CASA', $_POST['ID_CASA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            header("HTTP/1.1 204 No Content");
            echo "Ya existe la ID_CASA ", $_POST['ID_CASA'];
        } else {
            echo "Guardado Exitosamente";
            $input = $_POST;
            $sql = "INSERT INTO casa (ID_ESCUELA, ID_CASA, NOMBRE_CASA, COLOR_CASA, VIRTUD_CASA, NOMBRE_FANTASMA_CASA)
            VALUES (:ID_ESCUELA, :ID_CASA, :NOMBRE_CASA, :COLOR_CASA, :VIRTUD_CASA, :NOMBRE_FANTASMA_CASA)";
            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);
            $statement->execute();
            $postId = $dbConn->lastInsertId();
            if ($postId) {
                $input['ID_CASA'] = $postId;
                header("HTTP/1.1 200 OK");
                echo json_encode($input);
                exit();
            }
        }
    } else {
        echo "EL campo ID_CASA es obligatorio para insertar";
    }

}
//=========================================================================BORRAR ELEMENTOS SEGUN EL ID_CASA
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['ID_CASA'])) {
        $sql = $dbConn->prepare("SELECT COUNT(*) FROM casa where ID_CASA=:ID_CASA");
        $sql->bindValue(':ID_CASA', $_GET['ID_CASA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        // echo $row_count;
        if ($row_count == 0) {
            echo "No existe el registro ", $_GET['ID_CASA'];
            header("HTTP/1.1 400 Bad Request"); //error 400 al no encontrar coincidencias
        } else {
            //==================================Al encontrar el elemento que coincide, lo elimina
            $ID_CASA = $_GET['ID_CASA'];
            $statement = $dbConn->prepare("DELETE FROM casa where ID_CASA=:ID_CASA");
            $statement->bindValue(':ID_CASA', $ID_CASA);
            $statement->execute();
            echo "Eliminado el registro ", $_GET['ID_CASA'];
            header("HTTP/1.1 200 OK");
            exit();
        }
    } else {
        echo "El parametro ID_CASA es obligatorio para poder eliminar";
    }

}

//===================================================================METODO PARA LA MODIFICIACION DE UN REGISTRO
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['ID_CASA'])) {
        $sql = $dbConn->prepare("SELECT * FROM casa where ID_CASA=:ID_CASA");
        $sql->bindValue(':ID_CASA', $_GET['ID_CASA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            $input = $_GET;
            $postId = $input['ID_CASA'];
            $fields = getParams($input);

            $sql = "UPDATE casa
            SET $fields
            WHERE ID_CASA='$postId'";

            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);

            $statement->execute();
            header("HTTP/1.1 200 OK");
            echo "Actualizada exitosamente la casa= ", $_GET['ID_CASA'];
            exit();
        } else {
            header("HTTP/1.1 204 No Content");
            echo "No existe la ID_CASA= ", $_GET['ID_CASA'];
        }
    } else {
        echo "El parametro ID_CASA es obligatorio para poder actualizar";
    }
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
