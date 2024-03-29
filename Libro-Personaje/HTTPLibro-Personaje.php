<?php
include "../config.php";
include "../utils.php";

$dbConn = connect($db);

/*
REALIZA BUSQUEDA ESPECIFICA
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //=================================================================VALIDA LA BUSQUEDA MEDIANTE EL NOMBRE DEL PERSONAJE Y NOS MOSTRARA EN QUE LIBROS PARTICIPA
    if (isset($_GET['NOMBRE_PERSONAJE'])) {
        $sql = $dbConn->prepare("SELECT * FROM `libro_personajes`
        RIGHT JOIN personaje ON libro_personajes.ID_PERSONAJE=personaje.ID_PERSONAJE
        LEFT JOIN libros ON libro_personajes.ID_LIBRO=libros.ID_LIBRO
        LEFT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
        WHERE personaje.NOMBRE_PERSONAJE LIKE '%' :NOMBRE_PERSONAJE '%'");
        $sql->bindValue(':NOMBRE_PERSONAJE', $_GET['NOMBRE_PERSONAJE']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
        if ($row_count == 0) {
            header("HTTP/1.1 204 No Content");
            echo "No existe la consulta con id =  ", $_GET['NOMBRE_PERSONAJE'];

        } else {
            //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS

            $sql = $dbConn->prepare("SELECT personaje.NOMBRE_PERSONAJE, personaje.APELLIDO_PERSONAJE, personaje.PAPEL_PERSONAJE, libros.TITULO_LIBRO, libros.CRONOLOGIA_LIBRO , libros.SINOPSIS_LIBRO, libros.ANO_PUBLICACION_LIBRO 
            FROM `libro_personajes`
            RIGHT JOIN personaje ON libro_personajes.ID_PERSONAJE=personaje.ID_PERSONAJE
            LEFT JOIN libros ON libro_personajes.ID_LIBRO=libros.ID_LIBRO
            LEFT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
            WHERE personaje.NOMBRE_PERSONAJE LIKE '%' :NOMBRE_PERSONAJE '%'
            ORDER BY libros.CRONOLOGIA_LIBRO");
            $sql->bindValue(':NOMBRE_PERSONAJE', $_GET['NOMBRE_PERSONAJE']);
            $sql->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
            echo "Si existe el registro  ";
            exit();
        }

    } else {
        //===============================================================BUSUQEDA MEDIANTE EL NOMNBRE DEL LIBRO PARA SABER LOS PERSONAJES QUE APARECEN EN EL LIBRO
        if (isset($_GET['TITULO_LIBRO'])) {
            $sql = $dbConn->prepare("SELECT libros.CRONOLOGIA_LIBRO AS 'CRONOLOGIA', libros.TITULO_LIBRO AS 'TITULO', personaje.NOMBRE_PERSONAJE AS 'NOMBRE', personaje.APELLIDO_PERSONAJE AS 'APELLIDO',  personaje.PAPEL_PERSONAJE AS 'PAPEL'
            FROM `libro_personajes`
            RIGHT JOIN personaje ON libro_personajes.ID_PERSONAJE=personaje.ID_PERSONAJE
            LEFT JOIN libros ON libro_personajes.ID_LIBRO=libros.ID_LIBRO
            LEFT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
            WHERE libros.TITULO_LIBRO LIKE '%' :TITULO_LIBRO '%'");
            $sql->bindValue(':TITULO_LIBRO', $_GET['TITULO_LIBRO']);
            $sql->execute();
            $row_count = $sql->fetchColumn();
            //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
            if ($row_count == 0) {
                header("HTTP/1.1 204 No Content");
                echo "No existe la consulta con id =  ", $_GET['TITULO_LIBRO'];
    
            } else {
                //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
                $sql = $dbConn->prepare("SELECT libros.CRONOLOGIA_LIBRO AS 'CRONOLOGIA', libros.TITULO_LIBRO AS 'TITULO', personaje.NOMBRE_PERSONAJE AS 'NOMBRE', personaje.APELLIDO_PERSONAJE AS 'APELLIDO',  personaje.PAPEL_PERSONAJE AS 'PAPEL'
                FROM `libro_personajes`
                RIGHT JOIN personaje ON libro_personajes.ID_PERSONAJE=personaje.ID_PERSONAJE
                LEFT JOIN libros ON libro_personajes.ID_LIBRO=libros.ID_LIBRO
                LEFT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
                WHERE libros.TITULO_LIBRO LIKE '%' :TITULO_LIBRO '%'
                ORDER BY libros.CRONOLOGIA_LIBRO");
                $sql->bindValue(':TITULO_LIBRO', $_GET['TITULO_LIBRO']);
                $sql->execute();
                header("HTTP/1.1 200 OK");
                echo json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
                echo "Si existe el registro  ";
                exit();
            }
    
        } else {
            //MUESTRA TODOS LOS ELEMENTOS DE LA BASE
            $sql = $dbConn->prepare("SELECT * FROM libro_personajes");
            $sql->execute();
            $sql->setFetchMode(PDO::FETCH_ASSOC);
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll());
            exit();
        }
    }
}

// =======================================================================================CREA UN NUEVO ELEMENTO EN LA BASE DE DATOS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ID_LIBRO']) && isset($_POST['ID_PERSONAJE'])) {
        $sql = $dbConn->prepare("SELECT * FROM libro_personajes WHERE ID_LIBRO=:ID_LIBRO AND ID_PERSONAJE=:ID_PERSONAJE");
        $sql->bindValue(':ID_PERSONAJE', $_POST['ID_PERSONAJE']);
        $sql->bindValue(':ID_LIBRO', $_POST['ID_LIBRO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            header("HTTP/1.1 204 No Content");
            echo "Ya existe la ID_PERSONAJE ", $_POST['ID_PERSONAJE'];
        } else {
            $input = $_POST;
            $sql = "INSERT INTO libro_personajes (ID_PERSONAJE, ID_LIBRO)
            VALUES
            (:ID_PERSONAJE, :ID_LIBRO)";
            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);
            $statement->execute();
            $postId = $dbConn->lastInsertId();
            echo "Guardado Exitosamente";
            if ($postId) {
                $input['ID_PERSONAJE'] = $postId;
                header("HTTP/1.1 200 OK");
                echo json_encode($input);
                exit();
            }
        }
    } else {
        echo "EL campo ID_PERSONAJE y ID_LIBRO son obligatorio para insertar";
    }
}

//==================================================================================================BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['ID_PERSONAJE']) && isset($_GET['ID_PERSONAJE'])) {
        $sql = $dbConn->prepare("SELECT COUNT(*) FROM libro_personajes where ID_PERSONAJE=:ID_PERSONAJE AND ID_LIBRO = :ID_LIBRO");
        $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
        $sql->bindValue(':ID_LIBRO', $_GET['ID_LIBRO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        // echo $row_count;
        if ($row_count == 0) {
            echo "No existe el registro en la libro_personajes con id = ", $_GET['ID_PERSONAJE'];
            header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

        } else {
            $ID_PERSONAJE = $_GET['ID_PERSONAJE'];
            $ID_LIBRO = $_GET['ID_LIBRO'];
            $statement = $dbConn->prepare("DELETE FROM libro_personajes where ID_PERSONAJE=:ID_PERSONAJE AND ID_LIBRO = :ID_LIBRO");
            $statement->bindValue(':ID_PERSONAJE', $ID_PERSONAJE);
            $statement->bindValue(':ID_LIBRO', $ID_LIBRO);
            $statement->execute();
            echo "Eliminado el registro de la libro_personajes con id = ", $_GET['ID_PERSONAJE'];
            header("HTTP/1.1 200 OK");
            exit();
        }
    } else {
        echo "El parametro ID_PERSONAJE es obligatorio para poder eliminar";
    }

}

//===========================================================================ACTUALIZA LA RELACI[ON QUE EXISTE ENTRE PERSONAJE Y LIBRO
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['ID_PERSONAJE']) && isset($_GET['ID_LIBRO'])) {
        $sql = $dbConn->prepare("SELECT * FROM libro_personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_LIBRO=:ID_LIBRO");
        $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
        $sql->bindValue(':ID_LIBRO', $_GET['ID_LIBRO']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            $input = $_GET;
            $postId = $input['ID_PERSONAJE'];
            $postId2 = $input['ID_LIBRO'];
            $fields = getParams($input);

            $sql = "UPDATE libro_personaje
            SET $fields
            WHERE ID_PERSONAJE='$postId'AND ID_LIBRO='$postId2'";

            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);

            $statement->execute();
            header("HTTP/1.1 200 OK");
            echo "Actualizada exitosamente el contenido de la consulta con id ", $_GET['ID_PERSONAJE'];
            exit();
        } else {
            header("HTTP/1.1 204 No Content");
            echo "No existe la consulta con id= ", $_GET['ID_PERSONAJE'];
        }
    } else {
        echo "El parametro ID_PERSONAJE es obligatorio para poder actualizar";
    }
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
