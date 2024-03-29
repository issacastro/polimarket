<?php
include '../CLases/producto.class.php';
include '../CLases/perfil.class.php';
include '../CLases/usuario.class.php';
include 'm_product.php';
//Comprobamos que el valor no venga vacío
if(isset($_POST['function']) && !empty($_POST['function'])) {
    $funcion = $_POST['function'];
$ID=$_POST['ID'];
//En función del parámetro que nos llegue ejecutamos una función u otra
    switch($funcion) {
        case 'f1': 
//Todas las compras de usuarios
        $a = action1($ID);
            echo json_encode($a);
            break;
        case 'f2': 
//Todas las Ventas del Usuario
        $b = action2($ID);
            echo json_encode($b);
            break;
        case 'f3': 
        $c = action3($ID);
        echo json_encode($c);
            break;
        case 'f4': 
//Mostrar datos de Usuario
        $d = action4($ID);
            echo json_encode($d);
            break;
        case 'f5': 
//Actualizar datos de usuario
        $d = action5($ID);
            echo json_encode($d);
            break;
         case 'f6': 
//Cancelar compra
        $e = action6();
            echo json_encode($e);
            break;
          case 'f7': 
//Actualizar Perfil
        $f = action7();
            echo json_encode($f);
            break;

    }
}
function action1($ID){
    $stmt="SELECT T3.ID_COMPRAS,T.FK_USER AS USR,T2.NOMBRE,T3.CANTIDAD,T2.PRECIO,T3.TOTAL,T2.IMG,T2.FK_CATEGORIA AS CAT,T2.ID_PRODUCTO FROM Vendedor AS T
    INNER JOIN Producto AS T2 
    ON T.ID_VENDEDOR = T2.FK_VENDEDOR
    INNER JOIN Compras AS T3
    ON T3.FK_PRODUCTO =T2.ID_PRODUCTO
    INNER JOIN Comprador AS T4
    ON T3.FK_COMPRADOR=T4.ID_COMPRADOR
    WHERE T4.FK_USER = ? ORDER BY T3.ID_COMPRAS;
    ";
    $params=array($ID);
    $compras=array();
    $i=1;
    for($j=1;$j<3;$j++){
    $conn =conexion($j);
    $query = sqlsrv_query($conn, $stmt,$params);
    while($result = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
        $pr=new Perfil();
        $pr->id_Compra = $result['ID_COMPRAS'];
        $pr->usr = $result['USR']; 
        $pr->nombre  = $result['NOMBRE'];
        $pr->cantidad = $result['CANTIDAD'];
        $pr->precio = $result['PRECIO'];
        $pr->total = $result['TOTAL']; 
        $pr->img = $result['IMG']; 
        $pr->cat = $result['CAT']; 
        $pr->id_Producto = $result['ID_PRODUCTO']; 
        $compras+=[ "$i" => $pr ];
        $i++;
    }
    sqlsrv_close($conn); 
}
  return $compras; 
}
function action2($ID){
    $stmt="SELECT T3.ID_COMPRAS,T4.FK_USER AS USR,T2.NOMBRE,T3.CANTIDAD,T2.PRECIO,T3.TOTAL,T2.IMG,T2.FK_CATEGORIA AS CAT,T2.ID_PRODUCTO FROM Vendedor AS T
    INNER JOIN Producto AS T2 
    ON T.ID_VENDEDOR = T2.FK_VENDEDOR
    INNER JOIN Compras AS T3
    ON T3.FK_PRODUCTO =T2.ID_PRODUCTO
    INNER JOIN Comprador AS T4
    ON T3.FK_COMPRADOR=T4.ID_COMPRADOR
    WHERE T.FK_USER = ? ORDER BY T3.ID_COMPRAS;
    ";
    $params=array($ID);
    $compras=array();
    $i=1;
    for($j=1;$j<3;$j++){
    $conn =conexion($j);
    $query = sqlsrv_query($conn, $stmt,$params);
    while($result = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
        $pr=new Perfil();
        $pr->id_Compra = $result['ID_COMPRAS'];
        $pr->usr = $result['USR']; 
        $pr->nombre  = $result['NOMBRE'];
        $pr->cantidad = $result['CANTIDAD'];
        $pr->precio = $result['PRECIO'];
        $pr->total = $result['TOTAL']; 
        $pr->img = $result['IMG'];
        $pr->cat = $result['CAT'];  
        $pr->id_Producto = $result['ID_PRODUCTO']; 
        $compras+=[ "$i" => $pr ];
        $i++;
    }
    sqlsrv_close($conn); 
}
  return $compras; 
}
function action3($ID){
    $stmt ="SELECT ID_PRODUCTO, T1.FK_USER AS FK_VENDEDOR, T2.NOMBRE AS FK_ESCUELA,FK_CATEGORIA,T.NOMBRE,DESCRIPCION,IMG,STOCK,STATUS,PRECIO 
    FROM Producto AS T
    INNER JOIN
    Vendedor AS T1
    ON T.FK_VENDEDOR = T1.ID_VENDEDOR
    INNER JOIN Escuela AS T2 
    ON T.FK_ESCUELA = T2.ID_ESCUELA
WHERE T1.FK_USER= ?";
$params=array($ID);
return getProductsF($stmt,$params);
}

function action4($ID){
    for($i=1;$i<3;$i++){
    $stmt="SELECT * FROM Usuarios WHERE ID_USER= ?";
    $params=array($ID);
    $conn=conexion($i);
    $query = sqlsrv_query($conn, $stmt,$params);
    if($result = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
    $USR = new Usuario();
    $USR->ID_Nick=$result['ID_USER'];
    $USR->email=$result['EMAIL'];
    $USR->password=$result['PASSWORD'];
    }
}
    return $USR;
}
function action5($ID){
    $stmt="UPDATE Usuarios SET EMAIL= ?, PASSWORD = ? WHERE ID_USER= ?";
    $params=array($_POST['Email'],$_POST['Password'],$ID);
    $conn=conexion(1);
    $query = sqlsrv_query($conn, $stmt,$params);
    $stmt="SELECT * FROM Usuarios WHERE ID_USER= ?";
    $params=array($ID);
    $query = sqlsrv_query($conn, $stmt,$params);
    if($result = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)){
    $USR = new Usuario();
    $USR->ID_Nick=$result['ID_USER'];
    $USR->email=$result['EMAIL'];
    $USR->password=$result['PASSWORD'];
    }
    return $USR;
}
function action6(){
    $stmt="UPDATE Compras SET TOTAL = 0 WHERE FK_PRODUCTO= ? AND ID_COMPRAS = ?";
    $params=array($_POST['ID_P'],$_POST['ID']);
    $conn=conexion(SRV($_POST['CAT']));
    $query = sqlsrv_query($conn, $stmt,$params);
    $producto = getProduct($_POST['ID_P'],$_POST['CAT']);
    $stmt="UPDATE Producto SET STOCK = ? WHERE ID_PRODUCTO= ?";
    $newstock= $producto->stock + $_POST['ITEMS'];
    $params=array($newstock,$_POST['ID_P']);
    $query = sqlsrv_query($conn, $stmt,$params);
    return $producto;
}
function action7(){
    $stmt= "
    EXEC acualizar_publicacion
    @SRV =?,
    @ID_PRODUCTO = ?,
    @FK_ESCUELA = ?,
    @FK_CATEGORIA= ?,
    @NOMBRE = ?,
    @DESCRIPCION = ?,
    @STOCK = ?,
    @STATUS = 1,
    @PRECIO = ?
        ";
        $SRV=SRV($_POST['CAT']);
        $params=array(
            SRV($_POST['FK_CATEGORIA']),
            $_POST['ID'],
            $_POST['FK_ESCUELA'],
            $_POST['FK_CATEGORIA'],
            $_POST['NOMBRE'],
            $_POST['DESCRIPCION'],
            $_POST['STOCK'],
            $_POST['PRECIO']
        );
        $conn = conexion($SRV);
        $query = $query = sqlsrv_query($conn, $stmt,$params);
        if($query){
            echo $SRV;
        }else {
            echo "Mal";
        }
}
?>