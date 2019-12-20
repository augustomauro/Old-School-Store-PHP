<?php

if (! $_SESSION['usuario']) {
    header('location: ?view=403');
    //header('location: ./?view=home');
    die();
}

$carts = [];
$rom_qty = 0;
$nº = 1;
$moneda = null;
$subtot = 0;

$carts = get_json('json/carts.json');

$products = get_json('json/products.json');

// Guarda datos del usuario actual
//$userId = $_SESSION['usuario']['code'];
$userId = $_SESSION['usuario']['id'];
$qty_cart = $_SESSION['usuario'][0]['qty_cart'];
// Verifica si el articulo esta seleccionado como favorito para el usuario
$is_in_fav = null;
$is_in_cart = null;


// Verifica primero en toda la base de datos del carrito si el usuario ya agrego algo anteriormente
$posUser = null; // Inicializa variable Posicion Usuario como NULA

foreach ($carts as $index => $value) {
    if ($value['user_id'] == $userId) {
        $posUser = $index; // Guarda posicion del usuario
    }
}


if ($_POST && ! isset($_POST['carrito-a-favoritos'])) {

    // Si se presiona el boton 'ver (detalle producto)'
    if (isset($_POST['ver'])) {
        header('location: ?view=product&cat='.$_POST['prod_cat'].'&id='.$_POST['ver']);
        die;
    }

    // Si se presiona el boton 'mas similares (categorias)'
    if (isset($_POST['mas'])) {
        header('location: ?view=detail&cat='.$_POST['prod_cat']);
        die;
    }

    if (isset($_POST['arrow-up'])) {

        // Busca la posicion del Articulo sobre el cual operar
        $posArt = pos_art('json/carts.json', $posUser, $_POST['arrow-up']);

        if ( (int)$_POST['input_qty'] < (int)$_POST['stock_qty'] ) {
            // Si es menor al maximo de stock, Suma de a uno
            $carts[$posUser]['products'][$posArt]['prod_qty']++;
            $qty_cart++;
            $_SESSION['usuario'][0]['qty_cart'] = $qty_cart;
        }
        
    }

    if (isset($_POST['arrow-down'])) {

        // Busca la posicion del Articulo sobre el cual operar
        $posArt = pos_art('json/carts.json', $posUser, $_POST['arrow-down']);

        if ( (int)$_POST['input_qty'] > 1 ) {
            // Si es mayor a 1, Resta de a uno
            $carts[$posUser]['products'][$posArt]['prod_qty']--;
            $qty_cart--;
            $_SESSION['usuario'][0]['qty_cart'] = $qty_cart;
        }
        
    }

    if (isset($_POST['eliminar'])) {

        // Busca la posicion del Articulo sobre el cual operar
        $posArt = pos_art('json/carts.json', $posUser, $_POST['eliminar']);

        // Borra TODA la clave del producto
        unset($carts[$posUser]['products'][$posArt]);

        // Guarda la cantidad que habia originalmente en el carrito
        if (isset($_POST['input_qty'])) {
            $rom_qty = $_POST['input_qty'];
        }

        // Sobrescribe la cantidad en SESSION
        $qty_cart = $qty_cart - $rom_qty;
        $_SESSION['usuario'][0]['qty_cart'] = $qty_cart;

    }

    $carts[$posUser]['updated_at'] = time();    // Guarda el timestamp de la modificacion

    // Graba la nueva base
    save_json($carts,'./json/carts.json');

    // Recarga la pagina y evita reenvio de formulario
    header('location: ?view=cart');

}

?>

<div class="container" id="mi-carrito">

    <?php require 'require/header.php'; ?>

    <div class="row">
        <div class="col">
            <h4>Mi Carrito <i class="fa fa-shopping-cart" aria-hidden="true"></i></h4>
            <h6>Cod Usuario (<?= $userId ?>) | Cantidad Items (<?= $posUser ? count($carts[$posUser]['products']) : 0 ?>)</h6>

            <output>
                <article class="card">
                <header class="card-header"> Mi Carrito </header>
                    <div class="row">

                        <aside class="col-lg-9">

                            <div class="card">
                    
                                <div class="table-responsive">
            
                                    <table class="table table-borderless table-shopping-cart">
                                        <thead class="text-muted">
                                        <tr class="small text-uppercase">
                                        <th scope="col">Producto</th>
                                        <th scope="col" width="120">Cantidad</th>
                                        <th scope="col" width="120">Precio</th>
                                        <th scope="col" class="text-right d-none d-md-block" width="200"> </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ( $posUser !== null ) : ?>
                                                <?php foreach ($carts[$posUser]['products'] as $key => $articulo): ?>
                                                    <?php foreach ($products as $id => $product): ?> 
                                                        <?php if ($articulo['prod_id'] == $product['id']): ?>
                                                        <?php $is_in_fav = is_item_added('./json/favorites.json', $_SESSION['usuario']['id'], $articulo['prod_id']); ?>
                                                            <tr>
                                                                <td>
                                                                    <figure class="itemside align-items-center">
                                                                        <div class="aside"> <img style="width: 80px; height: 80px" src="<?= $product['image'] ?>" class="img-sm"> </div>
                                                                        <figcaption class="info">
                                                                            <a href="?view=product&cat=<?= cat_name($product['cat_id']) ?>&id=<?= $product['id'] ?>" class="title text-dark"><?= $product['name'] ?></a>
                                                                            <p class="small text-muted">
                                                                                Codigo: <?= $product['code'] ?>
                                                                                <br>
                                                                                Categoria: <a href="?view=detail&cat=<?= cat_name($product['cat_id']) ?>"><?= cat_name($product['cat_id']) ?></a>
                                                                                <br>
                                                                                Modelo: <?= $product['model'] ?>
                                                                                <br>
                                                                                Calidad: <?= $product['quality'] ?></p>
                                                                        </figcaption>
                                                                    </figure>
                                                                </td>
                                                                <!--<td>
                                                                    <select class="form-control">
                                                                        <option>1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                    </select>
                                                                </td>-->
                                                                <td>
                                                                    <form action="" method="post">
                                                                    <input type="hidden" name="input_qty" value="<?= $articulo['prod_qty'] ?>">
                                                                    <input type="hidden" name="stock_qty" value="<?= $product['stock'] ?>">
                                                                        <div class="arrow-input">
                                                                            <input type="text" id="prod-qty" class="form-control" name="input_qty" value="<?= $articulo['prod_qty'] ?>" autocomplete="off">
                                                                            <div id="arrows">
                                                                                <button class="arrow" name="arrow-up" value="<?= $product['id'] ?>">▲</button>
                                                                                <button class="arrow" name="arrow-down" value="<?= $product['id'] ?>">▼</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                    <br>
                                                                    <br>
                                                                    <div>
                                                                        Stock: <?= $product['stock'] ?> uni
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="price-wrap">
                                                                        <var class="price"><?= $product['currency'] . number_format($product['price'] * $articulo['prod_qty'], 2, ',', '') ?> </var>
                                                                        <?php $subtot = $subtot + ($product['price'] * $articulo['prod_qty']) ?> <!-- Hace la cuenta parcial -->
                                                                        <?php $moneda = $product['currency'] ?>
                                                                        <br>
                                                                        <small class="text-muted"> <?= $product['currency'] . number_format($product['price'], 2, ',', '') ?> c/u</small>
                                                                    </div> <!-- price-wrap .// -->
                                                                </td>
                                                                
                                                                    <td class="text-right d-none d-md-block">

                                                                    <form action="?action=favorites-modify" method="post">
                                                                        <input type="hidden" name="prod_id" value="<?= $articulo['prod_id'] ?>">
                                                                        <input type="hidden" name="prod_cat" value="<?= cat_name($product['cat_id']) ?>">
                                                                        <!--<button class="btn btn-sm btn-primary" title="Ver" name="ver" value="<?= $product['id'] ?>"> <i class="fas fa-eye"></i> </button>
                                                                        <button class="btn btn-sm btn-warning" title="Mas Similares" name="mas" value="<?= $product['id'] ?>"> <i class="fas fa-cart-plus" style="color: white;"></i> </button>
                                                                        <button class="btn btn-sm btn-success" title="Guardar" name="guardar" value="<?= $product['id'] ?>"> <i class="far fa-save" style="color: white;"></i> </button>
                                                                        <button class="btn btn-sm btn-danger" title="Eliminar" name="eliminar" value="<?= $product['id'] ?>"> <i class="fas fa-cart-arrow-down"></i> </button>-->
                                                                        
                                                                        <button type="submit" id="cart-fav-button" name="carrito-a-favoritos" title="Agregar a Mis Favoritos" class="btn btn-sm btn-light"> 
                                                                            <i class="fa fa-heart" style="<?= $is_in_fav ? 'color: crimson' : '' ?>"></i> 
                                                                        </button>
                                                                    </form>

                                                                    <form action="" method="post">
                                                                        <input type="hidden" name="input_qty" value="<?= $articulo['prod_qty'] ?>">
                                                                        <input type="hidden" name="stock_qty" value="<?= $product['stock'] ?>">
                                                                        <input type="hidden" name="prod_id" value="<?= $articulo['prod_id'] ?>">
                                                                        <!--<input type="hidden" name="prod_cat" value="<?= cat_name($product['cat_id']) ?>">-->
                                                                        <button class="btn btn-sm btn-danger" title="Eliminar del Carrito" name="eliminar" value="<?= $product['id'] ?>"> 
                                                                            <i class="fas fa-times"></i> 
                                                                        </button>
                                                                    </form>

                                                                    </td>
                                                                    <!--<td><input type="checkbox" name="checkbox" id="" value="<?= $product['id'] ?>"></td>-->
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
            
                                </div> <!-- table-responsive.// -->
            
                                <div class="card-body border-top">
                                    <p class="icontext"><i class="icon text-success fa fa-truck"></i> Envío Gratis dentro de la semana 1-2</p>
                                </div> <!-- card-body.// -->
            
                            </div> <!-- card.// -->
            
                        </aside> <!-- col.// -->

                        <aside class="col-lg-3">
            
                        <div class="card mb-3">
                            <div class="card-body">
                                <form>
                                    <div class="form-group">
                                        <label>Tiene un Cupon?</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="" placeholder="Codigo">
                                            <span class="input-group-append">
                                                <button class="btn btn-primary">Aplicar</button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div> <!-- card-body.// -->
                        </div> <!-- card.// -->
            
                        <div class="card">
                            <div class="card-body">
                                    <dl class="dlist-align">
                                    <dt>Precio Total:</dt>
                                    <dd class="text-right"><?= $moneda . number_format($subtot, 2, ',', '') ?></dd>
                                    </dl>
                                    <dl class="dlist-align">
                                    <dt>Descuento:</dt>
                                    <dd class="text-right text-danger">- $0.00</dd>
                                    </dl>
                                    <dl class="dlist-align">
                                    <dt>Total (s/imp):</dt>
                                    <dd class="text-right text-dark b"><strong><?= $moneda . number_format($subtot, 2, ',', '') ?></strong></dd>
                                    </dl>
                                    <hr>
                                    <p class="text-center mb-3">
                                        <img src="img/payments.png" height="26">
                                    </p>
                                    <form action="?action=payment" method="post" id="buy-button">
                                        <input type="hidden" name="cart_pos_user" value="<?= $posUser ?>">
                                        <button type="submit" name="buy" class="btn btn-primary btn-block" style="<?= $qty_cart == 0 ? 'background-color: gray':null ?>" <?= $qty_cart == 0 ? 'disabled':null ?>> Realizar Compra </button>
                                    </form>
                                    <a href="?view=home" class="btn btn-warning btn-block">Continuar Navegando</a>
                            </div> <!-- card-body.// -->
                        </div> <!-- card.// -->
                    
                        </aside> <!-- col.// -->
                    </div> <!-- row.// -->
                </article>
            </output>

            <hr>
            <!--<div id="erase-selected">
                <button class="btn btn-sm btn-danger" title="Eliminar Seleccionados" name="erase-selected" value=""> <i class="fas fa-cart-arrow-down"></i> </button>
            </div>-->
        </div>
    </div>

    <?php require 'require/footer.php'; ?>

</div>