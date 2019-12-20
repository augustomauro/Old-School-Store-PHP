<?php

if (! $_SESSION['usuario']) {
    header('location: ?view=403');
    //header('location: ./?view=home');
    die();
}

$carts = get_json('json/carts.json');

$products = get_json('json/products.json');

// Valores iniciales
$sub1 = 0;
$sub2 = 0;
$sub3 = 0;
$subtot = 0;
$items = 0;

// Impuestos y envio
$envio = 120;
$iva = 21;  // 21%
$oferta = 10; // 10%

// Guarda datos del usuario actual
$userId = $_SESSION['usuario']['id'];
$qty_cart = $_SESSION['usuario'][0]['qty_cart'];

// Verifica primero en toda la base de datos del carrito si el usuario ya agrego algo anteriormente
$posUser = null; // Inicializa variable Posicion Usuario como NULA

foreach ($carts as $index => $value) {
    if ($value['user_id'] == $userId) {
        $posUser = $index; // Guarda posicion del usuario
    }
}


if ( ! isset($_POST['shipping']) ) {

    if (! isset($_POST['eliminar']) ) {
        header('location: ?view=403');
        //header('location: ./?view=home');
        die();

    } else {

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

        $carts[$posUser]['updated_at'] = time();    // Guarda el timestamp de la modificacion
    
        // Graba la nueva base
        save_json($carts,'./json/carts.json');
    
        // Recarga la pagina y evita reenvio de formulario
        //header('location: ?view=review-cart');

    }
    
}


if ($_SESSION['usuario'][0]['qty_cart'] == 0) {
    header('location: ?view=cart');
    //header('location: ./?view=home');
    die();
}


?>

<div class="container" id="review-cart">

    <?php require 'require/header.php'; ?>

    <h4>Confirmacion de Compra 3</h4>
    <!--<div class="row">-->
        <div class="col">
        <!--<h4>Confirmacion de Pago </h4>-->
        <!--<h6>Usuario ID (<?= $userId ?>) | Cantidad Items (<?= $posUser ? count($carts[$posUser]['products']) : 0 ?>)</h6>-->

        <div class="row">
        <aside class="col-md-9">
            <output id="review">
                <div class="card">
                    <article class="card-body">
                        <header class="mb-4">
                            <h4 class="card-title">Revisión de Compra</h4>
                        </header>
                        <div class="row">
                            <?php if ( $posUser !== null ) : ?>
                                <?php foreach ($carts[$posUser]['products'] as $key => $articulo): ?>
                                    <?php foreach ($products as $id => $product): ?> 
                                        <?php if ($articulo['prod_id'] == $product['id']): ?>

                                            <div class="col-md-6">
                                                <figure class="itemside  mb-3">
                                                    <div class="aside"><img style="width: 40px; height: 40px" src="<?= $product['image'] ?>"
                                                            class="border img-xs"></div>
                                                    <figcaption class="info">
                                                        <p> <?= $product['name'] ?> </p>
                                                        <span><?= $articulo['prod_qty'] ?>
                                                        x <?= $product['currency'] . number_format($product['price'], 2, ',', '') ?> 
                                                        = Total: <?= $product['currency'] . number_format($product['price'] * $articulo['prod_qty'], 2, ',', '') ?> </span>
                                                    </figcaption>
                                                </figure>
                                            </div> <!-- col.// -->

                                            <?php $sub1 = $sub1 + ($product['price'] * $articulo['prod_qty']) ?> <!-- Hace la cuenta parcial -->
                                            <?php $items++ ?> <!-- Hace la cuenta parcial -->

                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div> <!-- row.// -->
                    </article> <!-- card-body.// -->
                    <article class="card-body border-top">

                        <dl class="row">
                            <dt class="col-sm-10">Subtotal: <span class="float-right text-muted"><?= $items ?> items</span>
                            </dt>
                            <dd class="col-sm-2 text-right">
                                <strong>
                                    <?= $product['currency'] . number_format(round($sub1, 2 , PHP_ROUND_HALF_DOWN), 2, ',', '') ?>
                                </strong></dd>

                            <dt class="col-sm-10">Descuento: <span class="float-right text-muted">Oferta -<?= $oferta ?>%</span></dt>
                            <dd class="col-sm-2 text-danger text-right">
                                <strong>
                                    <?= $product['currency'] . number_format(round($sub2 = ($sub1 / (($oferta / 100) + 1)), 2 , PHP_ROUND_HALF_DOWN), 2, ',', '') ?>
                                </strong></dd>

                            <dt class="col-sm-10">Cargo por Envío: <span class="float-right text-muted">Envío express</span></dt>
                            <dd class="col-sm-2 text-right"><strong>$<?= $envio ?></strong></dd>

                            <dt class="col-sm-10">Impuesto: <span class="float-right text-muted"><?= $iva ?>%</span></dt>
                            <dd class="col-sm-2 text-right text-success">
                                <strong>
                                    <?= $product['currency'] . number_format(round($subtot = ($sub2 + $envio) * (($iva / 100) + 1), 2 , PHP_ROUND_HALF_DOWN), 2 , ',', '') ?>
                                </strong></dd>

                            <dt class="col-sm-9">Total:</dt>
                            <dd class="col-sm-3 text-right" id="total-price">
                                <strong class="h5 text-dark">
                                    <?= $product['currency'] . number_format(round($subtot, 2, PHP_ROUND_HALF_DOWN), 2, ',', '') ?>
                                </strong></dd>
                        </dl>
                    </article> <!-- card-body.// -->
                </div>
            </output>
        </aside>
        <aside class="col-md-3">
            <output id="dropdown">
                <div class="card">
                    <div class="card-body">
                        <p>Confirmar Compra</p>
                        <div class="dropdown">
                            <a href="#" class="btn btn-primary btn-block dropdown-toggle" data-toggle="dropdown">
                                Mostrar <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            </a>
                            <div class="dropdown-menu p-3 dropdown-menu-right" style="min-width:280px;">
                                <?php if ( $posUser !== null ) : ?>
                                    <?php foreach ($carts[$posUser]['products'] as $key => $articulo): ?>
                                        <?php foreach ($products as $id => $product): ?> 
                                            <?php if ($articulo['prod_id'] == $product['id']): ?>

                                                <?php $sub3 = $sub3 + $product['price'] * $articulo['prod_qty'] ?> <!-- Hace la cuenta parcial -->

                                                <figure class="itemside mb-3">
                                                    <div class="aside"><img style="" src="<?= $product['image'] ?>"
                                                            class="img-sm border"></div>
                                                    <figcaption class="info align-self-center" id="dropdown">
                                                        <p class="title"> <?= $product['name'] ?> </p>
                                                        <div class="price-delete">
                                                            <div class="price"> 
                                                                <?= $product['currency'] . number_format($product['price'] * $articulo['prod_qty'], 2, ',', '') ?> 
                                                                <small><?= '(' . $articulo['prod_qty'] . ' item)' ?></small> 
                                                            </div> <!-- price-wrap.// -->
                                                            <form action="" method="post">
                                                                <input type="hidden" name="input_qty" value="<?= $articulo['prod_qty'] ?>">
                                                                <input type="hidden" name="stock_qty" value="<?= $product['stock'] ?>">
                                                                <input type="hidden" name="prod_id" value="<?= $articulo['prod_id'] ?>">
                                                                <button class="btn btn-sm float right" title="Eliminar del Carrito" name="eliminar" value="<?= $product['id'] ?>"> 
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div> 
                                                    </figcaption>
                                                </figure>

                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <div class="price-wrap text-center py-3 border-top">Subtotal: <strong class="h5 price">$<?= number_format($sub3, 2, ',', '') ?></strong></div>
                                <div class="price-wrap text-center py-3">Final: <strong class="h5 price">$<?= number_format($subtot, 2, ',', '') ?></strong></div>
                                <a href="" class="btn btn-primary btn-block"> Confirmar </a>

                            </div> <!-- drowpdown-menu.// -->
                        </div> <!-- dropdown.// -->

                    </div> <!-- card-body.// -->
                </div> <!-- card.// -->
            </output>
        </aside>

        
        </div>

        </div>
    <!--</div>-->

    <?php require 'require/footer.php'; ?>

</div>