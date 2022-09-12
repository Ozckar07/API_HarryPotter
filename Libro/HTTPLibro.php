<?php
include "../config.php";
include "../utils.php";

$dbConn = connect($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //========================================================================BUSQUEDA MEDIANTE EL TITULO DEL LIBRO
    if (isset($_GET['TITULO_LIBRO'])) {
        $sql = $dbConn->prepare("SELECT libros.CRONOLOGIA_LIBRO AS 'CRONOLOGÍA', libros.TITULO_LIBRO AS 'TÍTULO', libros.SINOPSIS_LIBRO AS 'SINÓPSIS', libros.EDITORIAL_LIBRO AS 'EDITORIAL', libros.ANO_PUBLICACION_LIBRO AS 'AÑO DE PUBLICACIÓN' FROM `libros` 
        WHERE libros.TITULO_LIBRO LIKE '%' :TITULO_LIBRO '%'");
        $sql->bindValue(':TITULO_LIBRO', $_GET['TITULO_LIBRO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
        if ($row_count == 0) {
            header("HTTP/1.1 204 No Content");
            echo "No existe el registro de la libros con nombre = ", $_GET['TITULO_LIBRO'];
        } else {
            //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
            echo "Si existe el registro  ";
            $sql = $dbConn->prepare("SELECT libros.CRONOLOGIA_LIBRO AS 'CRONOLOGÍA', libros.TITULO_LIBRO AS 'TÍTULO', libros.SINOPSIS_LIBRO AS 'SINÓPSIS', libros.EDITORIAL_LIBRO AS 'EDITORIAL', libros.ANO_PUBLICACION_LIBRO AS 'AÑO DE PUBLICACIÓN' FROM `libros` 
            WHERE libros.TITULO_LIBRO LIKE '%' :TITULO_LIBRO '%'
            ORDER BY libros.ANO_PUBLICACION_LIBRO");
            $sql->bindValue(':TITULO_LIBRO', $_GET['TITULO_LIBRO']);
            $sql->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
            exit();
        }
    } else {
        //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
        $sql = $dbConn->prepare("SELECT * FROM libros");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit();
    }

}

//========================================================================CREA UN NUEVO REGISTRO EN LA BASE DE DATOS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ID_LIBRO'])) {
        $sql = $dbConn->prepare("SELECT * FROM libros where ID_LIBRO=:ID_LIBRO");
        $sql->bindValue(':ID_LIBRO', $_POST['ID_LIBRO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            header("HTTP/1.1 204 No Content");
            echo "Ya existe la ID_LIBRO ", $_POST['ID_LIBRO'];
        } else {
            echo "Guardado Exitosamente";
            $input = $_POST;
            $sql = "INSERT INTO libros (ID_LIBRO, TITULO_LIBRO, AUTOR_LIBRO, SINOPSIS_LIBRO, ANO_PUBLICACION_LIBRO, EDITORIAL_LIBRO, CODIGO_ISBN_LIBRO, CRONOLOGIA_LIBRO)
            VALUES (:ID_LIBRO, :TITULO_LIBRO, :AUTOR_LIBRO, :SINOPSIS_LIBRO, :ANO_PUBLICACION_LIBRO, :EDITORIAL_LIBRO, :CODIGO_ISBN_LIBRO, :CRONOLOGIA_LIBRO)";
            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);
            $statement->execute();
            $postId = $dbConn->lastInsertId();
            if ($postId) {
                $input['ID_LIBRO'] = $postId;
                header("HTTP/1.1 200 OK");
                echo json_encode($input);
                exit();
            }
        }
    } else {
        echo "EL campo ID_LIBRO es obligatorio para insertar";
    }

}

//===========================================================================BORRA EL ELEMENTO SEGUN EL ID EN LA TABALA JUEGO
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['ID_LIBRO'])) {
        $sql = $dbConn->prepare("SELECT COUNT(*) FROM libros where ID_LIBRO=:ID_LIBRO");
        $sql->bindValue(':ID_LIBRO', $_GET['ID_LIBRO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        // echo $row_count;
        if ($row_count == 0) {
            echo "No existe el registro ", $_GET['ID_LIBRO'];
            header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete
        } else {
            $ID_CASA = $_GET['ID_LIBRO'];
            $ID_ESCUELA = $_GET['ID_UNIVERSO'];
            $statement = $dbConn->prepare("DELETE FROM libros where ID_LIBRO=:ID_LIBRO");
            $statement->bindValue(':ID_LIBRO', $ID_LIBRO);
            $statement->execute();
            echo "Eliminado el registro ", $_GET['ID_LIBRO'];
            header("HTTP/1.1 200 OK");
            exit();
        }
    } else {
        echo "El parametro ID_LIBRO es obligatorio para poder eliminar";
    }
}

//=============================================================MODIFICAR UN RESGISTRO DE LAS BASE DE DATOS EN LA TABALA JUEGO
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['ID_LIBRO'])) {
        $sql = $dbConn->prepare("SELECT * FROM libros where ID_LIBRO=:ID_LIBRO");
        $sql->bindValue(':ID_LIBRO', $_GET['ID_LIBRO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            $input = $_GET;
            $postId = $input['ID_LIBRO'];
            $fields = getParams($input);
            $sql = "UPDATE libros SET $fields WHERE ID_LIBRO='$postId'";
            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);
            $statement->execute();
            header("HTTP/1.1 200 OK");
            echo "Actualizada exitosamente la libros ", $_GET['ID_LIBRO'];
            exit();
        } else {
            header("HTTP/1.1 204 No Content");
            echo "No existe la ID_LIBRO ", $_GET['ID_LIBRO'];
        }
    } else {
        echo "El parametro ID_LIBRO es obligatorio para poder actualizar";
    }
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
