<?php

// Si no esta seteada la categoria y el id producto, da ERROR
if (! $_GET['cat'] && ! $_GET['id']) {
	header('location: ?view=403');
	die();
}

// Verifica que Categoria exista con al menos 1 item mediante active_categories())
// Si no existen articulos de esa categoria, da ERROR Not Found
if (isset($_GET['cat']) && $_GET['cat'] != "") {

    if (! in_array($_GET['cat'], active_categories('QTY_CAT'), true) && $_GET['cat'] != 'Todas') {
      header('location: ?view=404');
      die();
    }

}

if (isset($_GET)) {

	$articulos = get_json('./json/products.json');

	// Selecciona el articulo a mostrar segun GET, determinando la posicion
	$i = 0;
	foreach ($articulos as $articulo => $value) {
		if ($value['id'] == $_GET['id']) {
			$cat_all = $value['cat_id'];	// Guarda la categoria a la que pertenece
			$pos = $i;	// Guarda la posicion en la que está el articulo dentro de productos.json
		}
		$i++;
	}

	// Establece la categoria correspondiente segun GET
	if ($_GET['cat'] == 'Todas') {
		$cat = $cat_all;
	} else {
		$cat = $_GET['cat'];
	}

	// Compone un link para volver atras en la misma categoria
	$cat_link = str_replace(" ","%20",$cat); // Para reemplazar los espacios por %20 en hipervinculos

	// Si el producto no existe, da ERROR
	if (! $articulos[$pos] ) {
		header('location: ?view=404');
		//header('location: index.php?msg=No se encontró el producto&alert=danger');
		die();
	}

	$is_in_fav = null;
	$is_in_cart = null;
	// Si esta iniciada la sesion, verifica si el articulo esta seleccionado como favorito para el usuario
	if ( isset($_SESSION['usuario']) ) {
		$is_in_fav = is_item_added('./json/favorites.json', $_SESSION['usuario']['id'], $articulos[$pos]['id']);
		$is_in_cart = is_item_added('./json/carts.json', $_SESSION['usuario']['id'], $articulos[$pos]['id']);
	}


	$max_str = strlen($articulos[$pos]['stock']);
	$min_stk = (int)($articulos[$pos]['stock'] > 0) ?  1 : 0;

}
?>

<div class="container" id="producto">
	<header>

		<?php	require 'require/header.php';	?>

		<section class="categorias-inicio">
			<div class="ruta-categorias">
				<nav class="navBuscador">
					<ul>
						<li>Detalle <a href="?view=detail&cat=Todas">Todas</a></li>
						<li>/</li>
						<li>Categoría <a href=<?= '?view=detail&cat='. $cat_link ?> ><?= $cat ?></a></li>
						<li>/</li>
						<li>Producto <a href="#"><?= $articulos[$pos]['name'] ?></a></li>
					</ul>
				</nav>
			</div>
		</section>
	</header>

	<div class="content-wrap">

		<h3><mark><?= $cat ?></mark></h3>

		<section class="section-content bg padding-y-sm">

			<div class="row">
				<div class="col-xl-12 col-md-12 col-sm-12">
					<main class="card">
						<div class="row no-gutters">
							<aside class="col-sm-6 border-right"> <!-- Columna Izquierda -->
								<img src="<?= $articulos[$pos]['image'] ?>" style="width: 99%">
							</aside> <!-- Fin Columna Izquierda -->

							<aside class="col-sm-6"> <!-- Columna Derecha -->

								<article class="card-body">

									<div class="row" id="titulo-articulo">
										<!--<a href="#" class="title mt-2 h5">Titulo Producto</a>-->
										<div class="col-md-10">
											<h3><?= $articulos[$pos]['name'] ?></h3>
										</div>
										<div class="col-md-2">
											<form action="?action=favorites-modify" method="post" id="add-favoritos">
												<input type="hidden" name="prod_id" value="<?= $articulos[$pos]['id'] ?>">
												<input type="hidden" name="prod_cat" value="<?= $cat ?>">
												<button type="submit" name="articulo-a-favoritos" class="btn-link float-right" title="Agregar a Mis Favoritos" style="<?= $is_in_fav ? 'color: crimson' : '' ?>"> <i class="fa fa-heart fa-2x"></i> </button>
											</form>
										</div>
									</div>

									<div class="d-flex mb-3">
										<div class="price-wrap mr-4">
											<span class="price h5"><?= $articulos[$pos]['currency'] . $articulos[$pos]['price'] ?></span>
											<span> / Aceptamos hasta 6 cuotas sin interes.</span>
										</div> <!-- price-dewrap // -->


										<div class="rating-wrap">
											<ul class="rating-stars" style="">
												<li style="width:80%" class="stars-active">
													<i class="fa fa-star"></i> <i class="fa fa-star"></i>
													<i class="fa fa-star"></i> <i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</li>
												<li>
													<i class="fa fa-star"></i> <i class="fa fa-star"></i>
													<i class="fa fa-star"></i> <i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</li>
											</ul>
											<small class="label-rating text-muted">7/10</small>
										</div> <!-- rating-wrap.// -->
									</div>

									
									<dl>
										<dt>Descripcion</dt>
										<p><?= $articulos[$pos]['description_title'] ?></p>
									</dl>
									<dl class="row">

										<dt class="col-sm-3">Código</dt>
										<dd class="col-sm-9"><?= $articulos[$pos]['code'] ?></dd>

										<dt class="col-sm-3">Modelo</dt>
										<dd class="col-sm-9"><?= $articulos[$pos]['description_model'] . ' ' . $articulos[$pos]['model'] ?></dd>

										<dt class="col-sm-3">Color</dt>
										<dd class="col-sm-9"><?= $articulos[$pos]['colour'] ?></dd>

										<dt class="col-sm-3">Calidad</dt>
										<dd class="col-sm-9"><?= $articulos[$pos]['quality'] . ', ' . $articulos[$pos]['description_quality'] ?></dd>
									</dl>
									<div class="rating-wrap">
										<div class="label-rating">10 productos ya fueron vendidos!</div>
									</div>

									<hr>
									
									<div class="row">
										<div class="col-sm-3">
											<dl class="dlist-inline">
												<dt>En Stock </dt>
												<dd>
													<span><?= $articulos[$pos]['stock'] ?></span>
												</dd>
											</dl>
										</div>

										<div class="col-sm-9">
											<dl class="dlist-inline">
												<dt>¿Te enviamos el producto a tu domicilio? </dt>
												<dd>
													<label class="form-check form-check-inline">
														<input class="form-check-input" name="inlineRadioOptions"
															id="inlineRadio2" value="option2" type="radio">
														<span class="form-check-label">SI</span>
													</label>
													<label class="form-check form-check-inline">
														<input class="form-check-input" name="inlineRadioOptions"
															id="inlineRadio2" value="option2" type="radio">
														<span class="form-check-label">NO, voy al local.</span>
													</label>
												</dd>
											</dl>
										</div>
									</div>

									<section id="cart-form">

										<form class="form-inline" action="?action=cart-modify" method="post" id="add-carrito">
											<div class="form-group mx-sm-3 mb-2">
												<dl>
													<dt>Cantidad </dt>
													<dd id="input-qty">
														<input type="hidden" name="prod_id" value="<?= $articulos[$pos]['id'] ?>">
														<input type="hidden" name="prod_cat" value="<?= $cat ?>">
														<input type="hidden" name="articulo-a-carrito" value="">
														<input type="number" name="qty" class="form-control" rows="1" min="0" max="<?= $articulos[$pos]['stock'] ?>" maxlenght="<?= $max_str ?>" style="">
													</dd>
												</dl>
											</div>
											<section id="cart-buttons">
												<button name="agregar" id="cart-btn" type="submit" class="btn btn-primary"> 
													<span class="text">Agregar al Carrito</span> <i class="fas fa-shopping-cart"></i> 
												</button>
												<button name="eliminar" title="Eliminar del Carrito" id="cart-btn" type="submit" class="btn btn-danger <?= $is_in_cart ? null : 'isDisabled' ?>"> 
													<span class="text"></span> <i class="fas fa-cart-arrow-down"></i> 
												</button>
											</section>
										</form>

										<!-- MUESTRA LOS MENSAJES DE ERROR o ADVERTENCIA !!! -->
										<p class="text-danger" style="font-style: italic; font-weight: bold"><?= $_GET['msg'] ?? '' ?></p>

									</section>
									
									<hr>

									<a href="#" class="btn btn-primary mb-2" style="width: 100%; border-width: 0"> Comprar </a>

								</article>
							</aside> <!-- Fin Columna Derecha -->
						</div>
					</main>

					<!-- DETALLE DEL PRODUCTO -->
					<article class="card mt-3">
						<div class="card-body">
							<?php if ($articulos[$pos]['description_detail'] != '') : ?>
								<h4><?= 'Detalles de ' . $articulos[$pos]['name'] ?></h4>
								<p><?= $articulos[$pos]['description_detail'] ?></p>
							<?php else : ?>
								<h4>Sin Detalle</h4>
							<?php endif; ?>
						</div>
					</article>

				</div>
			</div>

		</section>

	</div>

	<?php require 'require/footer.php';	?>

</div>