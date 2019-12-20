<?php

// PAGINA PARA AÑADIR PRODUCTOS A CARRITO !!!

// Si no hay pedido por POST da ERROR
if (! $_POST ) {
    header('location: ?view=403');
    //header('location: ./?view=home');
    die();
}


if ( isset($_POST) ) {

    // Lee la base correspondiente segun categoria por POST
    $prodCat = $_POST['prod_cat'];

    // Selecciona el articulo a mostrar segun POST
    $prodId = $_POST['prod_id'];

    // Guarda en variables la cantidad seleccionada (integer)
    $prodQty = (int) $_POST['qty'];

    // Guarda datos del usuario actual
    $userId = $_SESSION['usuario']['id'];
    $userCode = $_SESSION['usuario']['code'];
    $qty_cart = $_SESSION['usuario'][0]['qty_cart'];


    if ( ! isset($_SESSION['usuario']) ) {
        // Si NO hay sesion iniciada, redirige a index.php primero, luego login, luego al articulo

        header('location: ?action=login&cat='.$prodCat.'&id='.$prodId);

    } else {
        // Si hay sesion iniciada

        // Lee la cantidad elegida segun POST, si es nula, devuelve a la misma pagina con un error
        if ($_POST['qty'] == 0 && isset($_POST['agregar'])) {
            header('location: ?view=product&cat='.$prodCat.'&id='.$prodId.'&msg=Debe seleccionar una cantidad !!!');
            die();
        }

        // Verifica en Todo el Carrito
        $carts = [];

        $carts = get_json('./json/carts.json');

        // Verifica primero en toda la base de datos si el usuario ya agrego algo anteriormente
        $posUser = null; // Inicializa variable Posicion Usuario como NULA
        $posArt = null; // Inicializa variable Posicion Articulo como NULA
        foreach ($carts as $key => $value) {
            if ($value['user_id'] == $userId) {
                $posUser = $key; // Guarda posicion del usuario o cart_id
                foreach ($value['products'] as $key2 => $product) {
                    if ($product['prod_id'] == $prodId) {
                        $posArt = $key2;
                    }
                }  
            }
        }

        // Verifica Stock, Moneda, Precio del Articulo en Productos
        $products = [];

        $products = get_json('./json/products.json');

        $prodCode = null;
        $prodStock = null;
        $prodCurrency = null;
        $prodPrice = null;
        foreach ($products as $key => $product) {
            if ($product['id'] == $prodId) {
                $prodCode = $product['code'];
                $prodStock = $product['stock'];
                $prodCurrency = $product['currency'];
                $prodPrice = $product['price'];
            }
        }
    
        // Si Usuario existe (distinto de NULL), agrega un array con el articulo a la cadena ya existente del usuario
        if ($posUser !== null) {

            // Si NO existe el articulo
            if ($posArt === null) {

                // Verifica que la cantidad agregada sea menor o igual al Stock del Articulo
                if (isset($_POST['agregar']) && $prodQty <= $prodStock) {

                    // Agrega un nuevo array de producto al usuario
                    $art = array(
                        'prod_id' => (int)$prodId,
                        'prod_code' => $prodCode,
                        'currency' => $prodCurrency, 
                        'price' => $prodPrice, 
                        'prod_qty' => $prodQty
                    );
                    $carts[$posUser]['products'][] = $art;	// Lo agrega en el proximo vacio
                    $carts[$posUser]['updated_at'] = time();    // Guarda el timestamp de la modificacion

                    // Actualiza en SESSION la cantidad cuantificada de items
                    $qty_cart = $qty_cart + $prodQty;

                }

                if ($_POST['eliminar']) {
                    $msg = '&msg=Nada para eliminar';
                }
                
            } else {
                // Si EXISTE el articulo

                // Verifica que la cantidad agregada + Lo que ya está en el carrito, sea menor o igual al Stock del Articulo
                if (isset($_POST['agregar']) && $carts[$posUser]['products'][$posArt]['prod_qty'] + $prodQty <= $prodStock) {

                    // Agrega repetida cantidad de items a lo anterior segun lo elegido (SOLO carrito)
                    $tmp = $carts[$posUser]['products'][$posArt]['prod_qty'] + $prodQty;
                    $carts[$posUser]['products'][$posArt]['prod_qty'] = $tmp;
                    $carts[$posUser]['updated_at'] = time();    // Guarda el timestamp de la modificacion

                    // Actualiza en SESSION la cantidad cuantificada de items
                    $qty_cart = $qty_cart + $prodQty;

                }

                if (isset($_POST['eliminar'])) {

                    // Guarda la cantidad que va a ser eliminada
                    $qty_del = $carts[$posUser]['products'][$posArt]['prod_qty'];
                    
                    // Borra TODA la clave del producto
                    unset($carts[$posUser]['products'][$posArt]);
                    $carts[$posUser]['updated_at'] = time();    // Guarda el timestamp de la modificacion

                    // Actualiza carrito segun cantidad eliminada
                    $qty_cart = $qty_cart - $qty_del;

                }
                
            }
    
        } else {

            // Si NO existe el usuario, agrega un nuevo usuario con los items deseados
            $cart1 = [
                'id' => (int)$posUser,
                'purchase_id' => null,
                'user_id' => $userId,
                'user_code' => $userCode,
                'products' => array(
                array(
                    'prod_id' => (int)$prodId,
                    'prod_code' => $prodCode,
                    'currency' => $prodCurrency, 
                    'price' => $prodPrice, 
                    'prod_qty' => $prodQty)
                ),
                'created_at' => time(),
                'updated_at' => null
            ];
    
            // Agrega al proximo vacio dentro del array carrito
            $carts[] = $cart1;

            // Actualiza en SESSION la cantidad cuantificada de items
            $qty_cart = $qty_cart + $prodQty;
    
        }

        // MENSAJES DE AVISO //
        if (isset($_POST['agregar'])) {

            if ($prodQty == 1) {
                // Mensaje para mostrar
                $msg = '&msg=Se añadió '.$prodQty.' producto al carrito !!!';
            } else {
                // Mensaje para mostrar
                $msg = '&msg=Se añadieron '.$prodQty.' productos al carrito !!!';
            }

        } elseif (isset($_POST['agregar'])) {
        
            if ($qty_del == 1) {
                // Mensaje para mostrar
                $msg = '&msg=Se ha eliminado '.$qty_del.' producto del carrito !!!';
            } else {
                // Mensaje para mostrar
                $msg = '&msg=Se han eliminado '.$qty_del.' productos del carrito !!!';
            }
        }

        // Sobrescribe la cantidad en SESSION
        $_SESSION['usuario'][0]['qty_cart'] = $qty_cart;
    
        // Graba la nueva base
        save_json($carts,'./json/carts.json');

        if (isset($_POST['favoritos-a-carrito'])) {
            // Redirige a la pagina de articulo
            header('location: ?view=favorites');
        }

        if (isset($_POST['articulo-a-carrito'])) {
            // Redirige a la pagina de articulo
            header('location: ?view=product&cat='.$prodCat.'&id='.$prodId.$msg); 
        }
        
    }

}

?>