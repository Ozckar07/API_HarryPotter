<?php
include "../config.php";
include "../utils.php";

$dbConn = connect($db);

/*
REALIZA BUSQUEDA ESPECIFICA
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //===================================================BUSQUEDA MEDIANTE EL APELLIDO DEL PERSONAJE
    if (isset($_GET['APELLIDO_PERSONAJE'])) {
        $sql = $dbConn->prepare("SELECT * FROM `pelicula_personaje` 
        RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA 
        LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE 
        LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE 
        LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA 
        RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA 
        WHERE personaje.APELLIDO_PERSONAJE LIKE '%' :APELLIDO_PERSONAJE '%'");
        $sql->bindValue(':APELLIDO_PERSONAJE', $_GET['APELLIDO_PERSONAJE']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
        if ($row_count == 0) {
            header("HTTP/1.1 204 No Content");
            echo "No existe la consulta con id =  ", $_GET['APELLIDO_PERSONAJE'];
        } else {
            //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
            $sql = $dbConn->prepare("SELECT personaje.NOMBRE_PERSONAJE AS 'PERSONAJE: ', personaje.APELLIDO_PERSONAJE AS 'APELLIDO: ', personaje.FOTO_PERSONAJE AS 'LINK FOTO', casa.NOMBRE_CASA AS 'CASA: ', escuela.NOMBRE_ESCUELA AS 'COLEGIO: ', libros.AUTOR_LIBRO AS 'CREADORA: ' 
            FROM `pelicula_personaje` RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA 
            LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE 
            LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE 
            LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO 
            RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA 
            RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA 
            WHERE personaje.APELLIDO_PERSONAJE 
            LIKE '%' :APELLIDO_PERSONAJE '%'
            GROUP BY personaje.NOMBRE_PERSONAJE
            ORDER BY personaje.FECHA_NACIMIENTO_PERSONAJE");
            $sql->bindValue(':APELLIDO_PERSONAJE', $_GET['APELLIDO_PERSONAJE']);
            $sql->execute();
            header("HTTP/1.1 200 OK");
            echo json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
            echo "Si existe el registro  ";
            exit();
        }
    } else {
        //=============================================================BUSCAR POR LINAJE DE SANGRE
        if (isset($_GET['RAZA_PERSONAJE'])) {
            $sql = $dbConn->prepare("SELECT * FROM `pelicula_personaje`
            RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA
            LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE
            LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE
            LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO
            RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
            RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA
            WHERE personaje.RAZA_PERSONAJE LIKE '%' :RAZA_PERSONAJE '%'");
            $sql->bindValue(':RAZA_PERSONAJE', $_GET['RAZA_PERSONAJE']);
            $sql->execute();
            $row_count = $sql->fetchColumn();
            //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
            if ($row_count == 0) {
                header("HTTP/1.1 204 No Content");
                echo "No existe la consulta con id =  ", $_GET['RAZA_PERSONAJE'];
            } else {
                //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
                $sql = $dbConn->prepare("SELECT  personaje.NOMBRE_PERSONAJE AS 'PERSONAJE: ',personaje.APELLIDO_PERSONAJE AS 'APELLIDO: ', casa.NOMBRE_CASA AS 'CASA: ', personaje.RAZA_PERSONAJE AS 'SANGRE', escuela.NOMBRE_ESCUELA AS 'COLEGIO: ', libros.AUTOR_LIBRO AS 'CREADORA:' FROM `pelicula_personaje`
                RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA
                LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE
                LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE
                LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO
                RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
                RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA
                WHERE personaje.RAZA_PERSONAJE LIKE '%' :RAZA_PERSONAJE '%'
                GROUP BY personaje.NOMBRE_PERSONAJE");
                $sql->bindValue(':RAZA_PERSONAJE', $_GET['RAZA_PERSONAJE']);
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                header("HTTP/1.1 200 OK");
                echo json_encode($sql->fetchAll());
                echo "Si existe el registro ";
                exit();
            }
        } else {
            //===================================================BUSCAMOS LAS OBCIONES EDUCATIVAS POR EL NOMBRE DE LA CASA
            if (isset($_GET['NOMBRE_CASA'])) {
                $sql = $dbConn->prepare("SELECT * FROM `pelicula_personaje`
                RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA
                LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE
                LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE
                LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO
                RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
                RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA
                LEFT JOIN universomagico ON universomagico.ID_UNIVERSO=personaje.ID_UNIVERSO
                WHERE casa.NOMBRE_CASA LIKE '%' :NOMBRE_CASA '%'");
                $sql->bindValue(':NOMBRE_CASA', $_GET['NOMBRE_CASA']);
                $sql->execute();
                $row_count = $sql->fetchColumn();
                //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
                if ($row_count == 0) {
                    header("HTTP/1.1 204 No Content");
                    echo "No existe la casa =  ", $_GET['NOMBRE_CASA'];
                } else {
                    //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
                    $sql = $dbConn->prepare("SELECT  casa.NOMBRE_CASA AS 'CASA: ', escuela.NOMBRE_ESCUELA AS 'COLEGIO: ', casa.VIRTUD_CASA AS 'VIRTUDES', casa.COLOR_CASA 'EMBLEMATICO: ', casa.NOMBRE_FANTASMA_CASA 'FANTASMA: ' FROM `pelicula_personaje`
                    RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA
                    LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE
                    LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE
                    LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO
                    RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
                    RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA
                    LEFT JOIN universomagico ON universomagico.ID_UNIVERSO=personaje.ID_UNIVERSO
                    WHERE casa.NOMBRE_CASA LIKE '%' :NOMBRE_CASA '%'
                    GROUP BY casa.NOMBRE_CASA
                    ORDER BY escuela.NOMBRE_ESCUELA");
                    $sql->bindValue(':NOMBRE_CASA', $_GET['NOMBRE_CASA']);
                    $sql->execute();
                    $sql->setFetchMode(PDO::FETCH_ASSOC);
                    header("HTTP/1.1 200 OK");
                    echo json_encode($sql->fetchAll());
                    echo "Si existe el registro  ";
                    exit();
                }
            } else {
                //=================================================================BUQUEDA POR NOMBRE DEL LIBRO
                if (isset($_GET['TITULO_LIBRO'])) {
                    $sql = $dbConn->prepare("SELECT * FROM `pelicula_personaje`
                    RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA
                    LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE
                    LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE
                    LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO
                    RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
                    RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA
                    LEFT JOIN universomagico ON universomagico.ID_UNIVERSO=personaje.ID_UNIVERSO
                    WHERE libros.TITULO_LIBRO LIKE '%' :TITULO_LIBRO '%'
                    GROUP BY libros.TITULO_LIBRO");
                    $sql->bindValue(':TITULO_LIBRO', $_GET['TITULO_LIBRO']);
                    $sql->execute();
                    $row_count = $sql->fetchColumn();
                    //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
                    if ($row_count == 0) {
                        header("HTTP/1.1 204 No Content");
                        echo "No existe la casa =  ", $_GET['TITULO_LIBRO'];
                    } else {
                        //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
                        $sql = $dbConn->prepare("SELECT  libros.TITULO_LIBRO AS 'TITULO: ', libros.SINOPSIS_LIBRO AS 'SINÓPSIS: ', universomagico.NOMBRE_UNIVERSO AS 'UNIVERSO', libros.AUTOR_LIBRO AS 'AUTORA: ', libros.ANO_PUBLICACION_LIBRO AS 'AÑO DE PUBLICACIÓN', libros.EDITORIAL_LIBRO AS 'EDITORIAL'  FROM `pelicula_personaje`
                        RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA
                        LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE
                        LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE
                        LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO
                        RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
                        RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA
                        LEFT JOIN universomagico ON universomagico.ID_UNIVERSO=personaje.ID_UNIVERSO
                        WHERE libros.TITULO_LIBRO LIKE '%' :TITULO_LIBRO '%'
                        GROUP BY libros.TITULO_LIBRO
                        ORDER BY libros.ANO_PUBLICACION_LIBRO");
                        $sql->bindValue(':TITULO_LIBRO', $_GET['TITULO_LIBRO']);
                        $sql->execute();
                        $sql->setFetchMode(PDO::FETCH_ASSOC);
                        header("HTTP/1.1 200 OK");
                        echo json_encode($sql->fetchAll());
                        echo "Si existe el registro  ";
                        exit();
                    }
                } else {
                    //==============================================================BUSQUEDA POR EL TITULO DE LA PELICULA
                    if (isset($_GET['TITULO_PELICULA'])) {
                        $sql = $dbConn->prepare("SELECT  * FROM `pelicula_personaje`
                        RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA
                        LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE
                        LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE
                        LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO
                        RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
                        RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA
                        LEFT JOIN universomagico ON universomagico.ID_UNIVERSO=personaje.ID_UNIVERSO
                        WHERE pelicula.TITULO_PELICULA LIKE '%' :TITULO_PELICULA '%'");
                        $sql->bindValue(':TITULO_PELICULA', $_GET['TITULO_PELICULA']);
                        $sql->execute();
                        $row_count = $sql->fetchColumn();
                        //VALIDA SI SE ENCUENTRAN O NO LOS DATOS
                        if ($row_count == 0) {
                            header("HTTP/1.1 204 No Content");
                            echo "No existe la casa =  ", $_GET['TITULO_PELICULA'];
                        } else {
                            //REALIZA LA BUSQUEDA Y OBTIENE LOS DATOS
                            $sql = $dbConn->prepare("SELECT  pelicula.TITULO_PELICULA AS 'TITULO: ', pelicula.SINOPSIS_PELICULA AS 'RESUMEN', universomagico.NOMBRE_UNIVERSO, libros.AUTOR_LIBRO AS 'AUTORA', pelicula.ANO_ESTRENO_PELICULA AS 'AÑO DE ESTRENO', pelicula.PRODUCTORA_PELICULA AS 'PRODUCTORA', pelicula.DIRECTOR_PELICULA FROM `pelicula_personaje`
                            RIGHT JOIN pelicula ON pelicula_personaje.ID_PELICULA=pelicula.ID_PELICULA
                            LEFT JOIN personaje ON pelicula_personaje.ID_PERSONAJE=personaje.ID_PERSONAJE
                            LEFT JOIN libro_personajes ON personaje.ID_PERSONAJE=libro_personajes.ID_PERSONAJE
                            LEFT JOIN libros ON libros.ID_LIBRO=libro_personajes.ID_LIBRO
                            RIGHT JOIN casa ON casa.ID_CASA=personaje.ID_CASA
                            RIGHT JOIN escuela ON escuela.ID_ESCUELA=casa.ID_ESCUELA
                            LEFT JOIN universomagico ON universomagico.ID_UNIVERSO=personaje.ID_UNIVERSO
                            WHERE pelicula.TITULO_PELICULA LIKE '%' :TITULO_PELICULA '%'
                            GROUP BY pelicula.TITULO_PELICULA
                            ORDER BY pelicula.ANO_ESTRENO_PELICULA");
                            $sql->bindValue(':TITULO_PELICULA', $_GET['TITULO_PELICULA']);
                            $sql->execute();
                            $sql->setFetchMode(PDO::FETCH_ASSOC);
                            header("HTTP/1.1 200 OK");
                            echo json_encode($sql->fetchAll());
                            echo "Si existe el registro  ";
                            exit();
                        }
                    } else {
                        //MUESTRA TODOS LOS ELEMENTOS DE LA TABLA
                        $sql = $dbConn->prepare("SELECT * FROM pelicula_personaje");
                        $sql->execute();
                        $sql->setFetchMode(PDO::FETCH_ASSOC);
                        header("HTTP/1.1 200 OK");
                        echo json_encode($sql->fetchAll());
                        exit();
                    }
                }
            }
        }
    }
}
// ======================================================================================================CREA UN NUEVO ELEMENTO EN LA BASE DE DATOS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ID_PELICULA']) && isset($_POST['ID_PERSONAJE'])) {
        $sql = $dbConn->prepare("SELECT * FROM pelicula_personaje WHERE ID_PELICULA=:ID_PELICULA AND ID_PERSONAJE=:ID_PERSONAJE");
        $sql->bindValue(':ID_PERSONAJE', $_POST['ID_PERSONAJE']);
        $sql->bindValue(':ID_PELICULA', $_POST['ID_PELICULA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            header("HTTP/1.1 204 No Content");
            echo "Ya existe la ID_PERSONAJE ", $_POST['ID_PERSONAJE'], "con relacion a la pelicula", $_POST['ID_PELICULA'];
        } else {
            $input = $_POST;
            $sql = "INSERT INTO pelicula_personaje (ID_PERSONAJE, ID_PELICULA) VALUES (:ID_PERSONAJE, :ID_PELICULA)";
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
        echo "EL campo ID_PERSONAJE y ID_PELICULA son obligatorio para insertar";
    }
}

//==============================================================================================================BORRA EL ELEMENTO SEGUN EL ID
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['ID_PERSONAJE']) && isset($_GET['ID_PERSONAJE'])) {
        $sql = $dbConn->prepare("SELECT COUNT(*) FROM pelicula_personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_PELICULA = :ID_PELICULA");
        $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
        $sql->bindValue(':ID_PELICULA', $_GET['ID_PELICULA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        // echo $row_count;
        if ($row_count == 0) {
            echo "No existe el registro en la pelicula_personaje con id = ", $_GET['ID_PERSONAJE'];
            header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

        } else {
            $ID_PERSONAJE = $_GET['ID_PERSONAJE'];
            $ID_PELICULA = $_GET['ID_PELICULA'];
            $statement = $dbConn->prepare("DELETE FROM pelicula_personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_PELICULA = :ID_PELICULA");
            $statement->bindValue(':ID_PERSONAJE', $ID_PERSONAJE);
            $statement->bindValue(':ID_PELICULA', $ID_PELICULA);
            $statement->execute();
            echo "Eliminado el registro de la pelicula_personaje con id = ", $_GET['ID_PERSONAJE'];
            header("HTTP/1.1 200 OK");
            exit();
        }
    } else {
        echo "El parametro ID_PERSONAJE es obligatorio para poder eliminar";
    }
}

//=================================================================================================ACTUALIZA Y MODIFICA LAS RELACIONES DENTRE PELICULA Y PERSONAJE
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['ID_PERSONAJE']) && isset($_GET['ID_PELICULA'])) {
        $sql = $dbConn->prepare("SELECT * FROM pelicula_personaje where ID_PERSONAJE=:ID_PERSONAJE AND ID_PELICULA=:ID_PELICULA");
        $sql->bindValue(':ID_PERSONAJE', $_GET['ID_PERSONAJE']);
        $sql->bindValue(':ID_PELICULA', $_GET['ID_PELICULA']);
        $sql->execute();
        $row_count = $sql->fetchColumn();
        if ($row_count > 0) {
            $input = $_GET;
            $postId = $input['ID_PERSONAJE'];
            $postId2 = $input['ID_PELICULA'];
            $fields = getParams($input);
            $sql = "UPDATE pelicula_personaje SET $fields WHERE ID_PERSONAJE='$postId'AND ID_PELICULA='$postId2'";
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
