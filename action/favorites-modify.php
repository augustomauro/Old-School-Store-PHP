<?php

// PAGINA PARA AÑADIR PRODUCTOS A FAVORITOS !!!

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

    // Cantidad por defecto = 1 (integer)
    $prodQty = 1;

    // Guarda datos del usuario actual
    $userId = $_SESSION['usuario']['id'];
    $userCode = $_SESSION['usuario']['code'];
    $qty_fav = $_SESSION['usuario'][0]['qty_fav'];


    if ( ! isset($_SESSION['usuario']) ) {
        // Si NO hay sesion iniciada, redirige a index.php primero, luego login, luego al articulo

        header('location: ?action=login&cat='.$prodCat.'&id='.$prodId);

    } else {
        // Si hay sesion iniciada

        $favorites = [];

        $favorites = get_json('./json/favorites.json');

        // Verifica primero en toda la base de datos si el usuario ya agrego algo anteriormente
        $posUser = null; // Inicializa variable Posicion Usuario como NULA
        $posArt = null; // Inicializa variable Posicion Articulo como NULA
        foreach ($favorites as $key => $value) {
            if ($value['user_id'] == $userId) {
                $posUser = $key; // Guarda posicion del usuario
                foreach ($value['products'] as $key2 => $product) {
                    if ($product['prod_id'] == $prodId) {
                        $posArt = $key2;
                    }
                }  
            }
        }

        // Verifica Codigo del Articulo en Productos
        $products = [];

        $products = get_json('./json/products.json');

        $prodCode = null;
        foreach ($products as $key => $product) {
            if ($product['id'] == $prodId) {
                $prodCode = $product['code'];
            }
        }
    
        // Si Usuario existe (distinto de NULL), agrega un array con el articulo a la cadena ya existente del usuario
        if ($posUser !== null) {

            // Si NO existe el articulo, lo agrega
            if ($posArt === null) {

                // Agrega un nuevo array de producto al usuario
                $art = array(
                    'prod_id' => (int)$prodId, 
                    'prod_code' => $prodCode, 
                    'prod_qty' => $prodQty
                );
                $favorites[$posUser]['products'][] = $art;	// Lo agrega en el proximo vacio
                $favorites[$posUser]['updated_at'] = time();    // Guarda el timestamp de la modificacion

                // Actualiza en SESSION la cantidad cuantificada de items
                $qty_fav = $qty_fav + $prodQty;

                // Mensaje para mostrar
                $msg = '&msg=Articulo añadido a favoritos !!!';

            } else {

                // Si existe articulo, lo elimina
                unset($favorites[$posUser]['products'][$posArt]);
                $favorites[$posUser]['updated_at'] = time();    // Guarda el timestamp de la modificacion

                // Actualiza en SESSION la cantidad cuantificada de items
                $qty_fav = $qty_fav - 1;
                
                // Mensaje para mostrar
                $msg = '&msg=Articulo eliminado de favoritos !!!';
            }
    
        } else {

            // Si NO existe el usuario, agrega un nuevo usuario con los items deseados
            $fav1 = [
                'id' => (int)$posUser,
                'user_id' => $userId,
                'user_code' => $userCode,
                'products' => array(
                array(
                    'prod_id' => (int)$prodId,
                    'prod_code' => $prodCode,
                    'prod_qty' => $prodQty)
                ),
                'created_at' => time(),
                'updated_at' => null
            ];
    
            // Agrega al proximo vacio dentro del array favoritos
            $favorites[] = $fav1;

            // Actualiza en SESSION la cantidad cuantificada de items
            $qty_fav = $qty_fav + $prodQty;

            // Mensaje para mostrar
            $msg = '&msg=Articulo añadido a favoritos !!!';
    
        }

        // Sobrescribe la cantidad en SESSION
        $_SESSION['usuario'][0]['qty_fav'] = $qty_fav;
    
        // Graba la nueva base
        save_json($favorites,'./json/favorites.json');

        if (isset($_POST['articulo-a-favoritos'])) {
            // Redirige a la pagina de articulo
            header('location: ?view=product&cat='.$prodCat.'&id='.$prodId.$msg); 
        }

        if (isset($_POST['carrito-a-favoritos'])) {
            // Redirige a la pagina de articulo
            header('location: ?view=cart&cat='.$prodCat.'&id='.$prodId); 
        }
        
    }

}

?>