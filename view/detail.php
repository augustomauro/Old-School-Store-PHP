<?php

// Si no esta seteada la categoria, da ERROR
if (! $_GET['cat']) {
	header('location: ?view=403');
  die();
}

// Verifica que Categoria exista con al menos 1 item mediante active_categories()
// Si no existen articulos de esa categoria, da ERROR Not Found
if (isset($_GET['cat']) && $_GET['cat'] != "") {

    if (! in_array($_GET['cat'], active_categories('QTY_CAT'), true) && $_GET['cat'] != 'Todas') {
      header('location: ?view=404');
      die();
    }

  // Lee la base correspondiente a productos.
  $products = get_json('./json/products.json');

}

?>

<div class="container" id="detalleproductos">
  <header>

    <?php require 'require/header.php'; ?>

    <section class="categorias-inicio">
      <div class="ruta-categorias">
        <nav class="navBuscador">
          <ul>
            <li>Detalle <a href="?view=detail&cat=Todas">Todas</a></li>
            <li>/</li>
            <li>Categoría <a href="#"><?= $_GET['cat'] ?></a></li>
          </ul>
        </nav>
      </div>
    </section>
  </header>

  <div class="content-wrap">

    <h3><mark><?= $_GET['cat'] ?></mark></h3>

    <div class="detalleproductos">

      <?php foreach ($products as $product) : ?>
      <?php if ( $_GET['cat'] == cat_name($product['cat_id']) ) : ?>
      <div class="card">
        <img src="<?= $product['image'] ?>" title="Envios a todo el pais" class="card-img-top" alt="...">
        <div class="card-body">
          <h5 class="card-title"><?= $product['name'] ?></h5>
          <p class="precio"><?= $product['currency'] . $product['price'] ?></p>
          <p class="card-text"><?= $product['description_general'] ?></p>
          <button type="button" name="button"> <a href="<?= '?view=product&cat='.$_GET['cat'].'&id='.$product['id'] ?>">Ver Producto</a></button>
        </div>
      </div>
      <?php elseif ( $_GET['cat'] == 'Todas' ) : ?>
      <div class="card">
        <img src="<?= $product['image'] ?>" title="Envios a todo el pais" class="card-img-top" alt="...">
        <div class="card-body">
          <h5 class="card-title"><?= $product['name'] ?></h5>
          <p class="precio"><?= $product['currency'] . $product['price'] ?></p>
          <p class="card-text"><?= $product['description_general'] ?></p>
          <button type="button" name="button"> <a href="<?= '?view=product&cat='.cat_name($product['cat_id']).'&id='.$product['id'] ?>">Ver Producto</a></button>
        </div>
      </div>
      <?php endif; ?>
      <?php endforeach; ?>

    </div>

  </div>

  <?php require 'require/footer.php'; ?>

</div>