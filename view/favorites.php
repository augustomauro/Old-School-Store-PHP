<?php

if (! $_SESSION['usuario']) {
    header('location: ?view=403');
    //header('location: ./?view=home');
    die();
}

$favorites = [];
$rom_qty = 0;
$nº = 1;

$favorites = get_json('json/favorites.json');

$products = get_json('json/products.json');

// Guarda datos del usuario actual
//$userId = $_SESSION['usuario']['code'];
$userId = $_SESSION['usuario']['id'];
$qty_fav = $_SESSION['usuario'][0]['qty_fav'];

// Verifica primero en toda la base de datos del favoritos si el usuario ya agrego algo anteriormente
$posUser = null; // Inicializa variable Posicion Usuario como NULA

foreach ($favorites as $index => $value) {
    if ($value['user_id'] == $userId) {
        $posUser = $index; // Guarda posicion del usuario
    }
}

if ($_POST) {

    if (isset($_POST['ver'])) {
        header('location: ?view=product&cat='.$_POST['cat'].'&id='.$_POST['ver']);
        die;
    }

    if (isset($_POST['mas'])) {
        header('location: ?view=detail&cat='.$_POST['cat']);
        die;
    }


    if (isset($_POST['eliminar'])) {

        // Busca la posicion del Articulo sobre el cual operar
        $posArt = pos_art('json/favorites.json', $posUser, $_POST['eliminar']);

        // Borra TODA la clave del producto
        unset($favorites[$posUser]['products'][$posArt]);

        // Sobrescribe la cantidad en SESSION
        $qty_fav = $qty_fav - 1;
        $_SESSION['usuario'][0]['qty_fav'] = $qty_fav;

    }

    $favorites[$posUser]['updated_at'] = time();    // Guarda el timestamp de la modificacion

    // Graba la nueva base
    save_json($favorites,'./json/favorites.json');

    // Recarga la pagina y evita reenvio de formulario
    header('location: ?view=favorites');

}

?>

<div class="container" id="mis-favoritos">

    <?php require 'require/header.php'; ?>

    <div class="row">
        <div class="col">
            <h4>Mis Favoritos <i class="fa fa-heart" aria-hidden="true"></i></h4>
            <h6>Cod Usuario (<?= $userId ?>) | Cantidad Items (<?= $posUser ? count($favorites[$posUser]['products']) : 0 ?>)</h6>
        
            <output>
                <!-- =========================  COMPONENT WISHLIST ========================= -->
                <article class="card">
                    <header class="card-header"> Mis Favoritos </header>
                    <div class="card-body">
                        <div class="row">
                
                            <?php if ( $posUser !== null ) : ?>
                                <?php foreach ($favorites[$posUser]['products'] as $key => $articulo): ?>
                                    <?php foreach ($products as $id => $product): ?> 
                                        <?php if ($articulo['prod_id'] == $product['id']): ?>  
                                            <div class="col-md-4">
                                                <figure class="itemside mb-4">
                                                    <div class="aside"><img style="width: 140px; height: 140px" src="<?= $product['image'] ?>" class="border img-md"></div>
                                                    <figcaption class="info">
                                                        <a href="#" class="title"><?= $product['name'] ?></a>
                                                        <p class="price mb-2"><?= $product['currency'].$product['price'] ?></p>

                                                        <form action="?action=cart-modify" method="post">
                                                            <input type="hidden" name="qty" value=1>
                                                            <input type="hidden" name="prod_id" value="<?= $articulo['prod_id'] ?>">
                                                            <input type="hidden" name="prod_cat" value="<?= cat_name($product['cat_id']) ?>">
                                                            <input type="hidden" name="favoritos-a-carrito" value="">
                                                            <button type="submit" name="agregar" id="cart-btn" class="btn btn-primary btn-sm"> <span class="text">Agregar al Carrito</span> <i class="fas fa-shopping-cart"></i> </button>
                                                        </form>

                                                        <form action="" method="post">                                       
                                                            <button class="btn btn-danger btn-sm" data-toggle="tooltip" title="Eliminar de Favoritos" name="eliminar" value="<?= $product['id'] ?>" data-original-title="Remove from wishlist"> <i class="fa fa-times"></i> </button>
                                                        </form>

                                                    </figcaption>
                                                </figure>
                                            </div> <!-- col.// -->
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div> <!-- row .//  -->
                    </div> <!-- card-body.// -->
                </article>
                <!-- =========================  COMPONENT WISHLIST END.// ========================= -->
            </output>

        </div>
    </div>

    <?php require 'require/footer.php'; ?>

</div>