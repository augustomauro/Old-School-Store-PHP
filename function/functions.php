<?php
// Sube Archivos y los encripta o no
function upload_file($_file_index, $dir, $crypt, $filename) {
    // Si hay imagen enviada (size != 0), realizar el codigo...
    if ( $_FILES[$_file_index]['size'] != 0 ) {

        if ($crypt == 'MD5') {
            $hash = md5(time() . $_FILES[$_file_index]['tmp_name']);
        } elseif ($crypt == 'DEFAULT') {
            if ($filename == null || $filename == '') {
                $hash = time();
            } else {
                $hash = time() . '_' . $filename;
            }
        } elseif ($crypt == '' || $crypt == null) {
            if ($filename == null || $filename == '') {
                $hash = $_FILES[$_file_index]['name'];
            } else {
                $hash = $filename;
            }
        }

        $ext = pathinfo($_FILES[$_file_index]['name'], PATHINFO_EXTENSION);
        $path = "$dir/$hash.$ext";

        // Verificar si existe el directorio, sino crearlo
        if ( !file_exists($dir) ) {
            mkdir($dir, 0777, true);
        }

        move_uploaded_file($_FILES[$_file_index]['tmp_name'], $path);

        return $path;
    }
    // Sino hay imagen, retornar
    return null;
}

// Lee Archivos json
function get_json($json_file) {
    // string (json)
    $json = file_get_contents($json_file);

    // Retorna un array ordenado
    return json_decode($json, true);
}

// Graba Archivos json
function save_json($array,$json_name) {
    // Convierte Array ordenado $array en json ordenado
    $json = json_encode($array, JSON_PRETTY_PRINT);
    // Guarda lo convertido en archivo json
    file_put_contents($json_name, $json);
}

// Selecciona el usuario que se loguea (desde users.json)
function select_user($usuarios, $email, $password) {
    // Verifico para cada clave email y password dentro del array usuarios si existe el email y password de entrada (post).
    foreach ($usuarios as $usuario) {
        // Verifica si el email ingresado coincide con la base de datos de usuarios y el password verifica correctamente.
        if ($usuario['email1'] === $email && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
    }
    // Si no encuentra, retorna falso
    return false;
}

// Verifica si el email a registrar ya existe en users.json
function verify_registered_email($usuarios, $email) {
    // Verifico para cada clave email dentro del array usuarios si existe el email que desea registrarse.
    foreach ($usuarios as $usuario) {
        // Verifica si el email ingresado coincide con la base de datos de usuarios.
        if ($usuario['email1'] === $email) {
            return true;
        }
    }
    // Si no encuentra, retorna falso
    return false;
}

// Cuantifica los ITEMS en el Carrito o en Favoritos
function items_cart_fav($base_array, $userId) {
    $products = get_json($base_array);
    $sum = 0;
    // Verifica primero en toda la base de datos si el usuario ya agrego algo anteriormente
    foreach ($products as $key => $value) {
        // Verifica los datos con los del usuario logueado
        if ($value['user_id'] == $userId) {
            //$posUser = $key; // Guarda posicion del usuario
            foreach ($value['products'] as $product) {
                    $sum = $sum + $product['prod_qty'];
            }
        }
    }
    return $sum;
}

// Verifica si EXISTE un articulo en favoritos o en carrito para un usuario dado
function is_item_added($base_array, $userId, $artId) {
    $favorites = get_json($base_array);
    // Verifica primero en toda la base de datos si el usuario ya agrego algo anteriormente
    foreach ($favorites as $key => $value) {
        // Verifica los datos con los del usuario logueado
        if ($value['user_id'] == $userId) {
            //$posUser = $key; // Guarda posicion del usuario
            foreach ($value['products'] as $product) {
                if ($product['prod_id'] == $artId) {
                    return true;
                }
            }
            return false;
        }
    }
    return false;
}

// Funcion Recursiva para generar los codigos de productos y/o usuarios de forma aleatoria y no repetitivos
function random_id($string, $array) {
    $id1 = mt_rand(0000,9999);  // Entre el 0000 y el 9999
    $id = str_pad($id1, 4, "0", STR_PAD_LEFT);  // Siempre 4 digitos, sino completa con ceros (0)
        foreach ($array as $key => $value) {
            if ($value['code'] == $string.$id) {
                random_id($string,$array);
            }
        }
        return $string.$id;
}

// Funcion para redireccionar a pagina concatenando con 'action'
function redirect($action) {
    header('location: ./index.php?action=' . $action);
}

// Devuelve Cantidad de Categorias o Productos Existentes
function active_categories($type) {
    // Determinar la cantidad de categorias que EXISTEN en la base de datos--------------------//
    $products = get_json('./json/products.json');
    $categories = get_json('./json/categories.json');

    $count_cat = [];
    foreach ($products as $product => $prod) {
        foreach ($categories as $key => $category) {
            if ($prod['cat_id'] == $category['id']) {
                $count_cat[] = $category['name'];
            }
        }
    }

    asort($count_cat);  //ordena el array alfabeticamente (internamente), antes de ser usado

    // Devuelve un array con la cant. de veces que aparecen valores en la clave 'categorias'.
    $count_qty_prod = array_count_values($count_cat);

    // Devuelve un array con TODAS las categorias indexadas y de manera UNICA y ordenada alfabeticamente
    $exist_cat = array_keys($count_qty_prod);

    if ($type == 'QTY_PROD') {

        return $count_qty_prod;

    } elseif ($type == 'QTY_CAT') {

        return $exist_cat;

    }
}

// Determina categoria correspondiente segun codigo cat_id
function cat_name($cat_id) {
    $categories = get_json('./json/categories.json');
    $cat_name = null;
    // Determina el ID de categoria que viene por GET
    foreach ($categories as $category) {
        if ($cat_id == $category['id']) {
        //$cat_id = $category['id'];
        $cat_name = $category['name'];
        }
    }
    return $cat_name;
}

// Determina posicion del articulo dentro de la base del usuario (p/Carrito y/o Favoritos)
function pos_art($base_array,$pos_user,$art_id) {
$articulos = get_json($base_array);
$posArt = null; // Inicializa variable Posicion Articulo como NULA
    foreach ($articulos[$pos_user]['products'] as $key => $articulo) {
        if ($articulo['prod_id'] == $art_id) {
            $posArt = $key;
        }
    }

    return $posArt;

}


// ELIMINAR ESTA FUNCION SI SE PUEDE RESOLVER CON UNSET() !!!
// Elimina un producto del Carrito o Favoritos
function delete_article($base_array, $userId, $artId) {
    $articulos = get_json('./json/'.$base_array.'.json');
    // Verifica posicion original del favorito/carrito dentro del json
    $i = 0;
    $us_pos = 0;
    $art_pos = 0;
    foreach ($articulos as $key => $value) {
        if ( $value[0]['user_id'] === $userId ) {
            for ($a=1; $a < count($value); $a++) { 
                if ( $value[$a]['prod_id'] === $artId ) {
                    $art_pos = $a;
                }
            }
        $us_pos = $i;
        }
        $i++;
    }

    $clear_art = [];
    $clear_us = [];
    $fav1 = [];
    $fav2 = [];
    $res1 = [];
    $res2 = [];

    // Elimina producto dentro del array del usuario
    // El producto favorito/carrito esta en el medio.
    $fav1 = $articulos[$us_pos];
    $fav2 = $articulos[$us_pos];
    // Corta y guarda desde $pos+1 hasta el ultimo
    $res1 = array_splice($fav1, $art_pos+1, (count($articulos[$us_pos])-1));
    // Corta y guarda desde 0 hasta $pos
    $res2 = array_splice($fav2, 0, $art_pos);
    // Hace merge del array final resultante
    $clear_art = array_merge($res2, $res1);

    // Corta el usuario completo con articulos y hace merge al final
    if ($us_pos == 0) {
        // El favorito esta en posicion inicial. Selecciona desde 1 hasta el ultimo
        $clear_us = array_splice($articulos, 1, (count($articulos)-1));
        //var_dump($clear_us); die();
    } elseif ($us_pos == (count($articulos)-1)) {
        // El favorito esta en posicion final. Selecciona desde 0 hasta anteultimo
        $clear_us = array_splice($articulos, 0, -1);
    } else {
        // El favorito esta en el medio.
        $fav1 = $articulos;
        $fav2 = $articulos;
        // Corta y guarda desde $pos+1 hasta el ultimo
        $res1 = array_splice($fav1, $us_pos+1, (count($articulos)-1));
        // Corta y guarda desde 0 hasta $pos
        $res2 = array_splice($fav2, 0, $us_pos);
        // Hace merge del array final resultante
        $clear_us = array_merge($res2, $res1);
    }

    //var_dump($clear_us); die();
    // Guarda nuevamente en el array editado el usuario sin el favorito.
    $clear_us[] = $clear_art;

    //var_dump($clear_art); die();
    //var_dump($clear_us); die();
    //return $clear_us;
    save_json($clear_us,'./json/'.$base_array.'.json');
}

?>