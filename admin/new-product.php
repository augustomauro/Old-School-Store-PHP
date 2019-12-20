<?php

// Leer json correspondiente a products.json
$products = get_json('./json/products.json');

// Genera un codigo aleatorio y no repetitivo para el producto
$cod = random_id('OSS',$products);

// Leer json correspondiente a categories.json
$categories = get_json('./json/categories.json');

if ($_POST) {

    // Determinar categoria correspondiente
    $id = $_POST['cod'];
    $category = $_POST['category'];
    $name = $_POST['name'];
    $code = $_POST['cod'];
    //$file = $_FILES['image'];
    $colour = $_POST['colour'];
    $currency = $_POST['currency'];
    $price = (float)$_POST['price'];
    $model = $_POST['model'];
    $quality = $_POST['quality'];
    $status = $_POST['status'];
    $stock = (double)filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $description_detail = $_POST['description_detail'];
    $description_general = $_POST['description_general'];
    $description_title = $_POST['description_title'];
    $description_model = $_POST['description_model'];
    $description_quality = $_POST['description_quality'];

    // cuenta cantidad de articulos previos
    $id = count($products);

    // Establece el formato del array guardado
    $product = [
        'id' => $id++,
        'cat_id' => (int)$category,
        'name' => $name,
        'code' => $code,
        'image' => upload_file('image', 'img/Productos', null, $code.'_'.$name),
        'colour' => $colour,
        'currency' => $currency,
        'price' => $price,
        'model' => $model,
        'quality' => $quality,
        'status' => $status,
        'stock' => $stock,
        'description_detail' => $description_detail,
        'description_general' => $description_general,
        'description_title' => $description_title,
        'description_model' => $description_model,
        'description_quality' => $description_quality,
        'created_at' => time(),
        'updated_at' => null
    ];

    // Agregar al array.
    $products[] = $product;

    // Guarda los productos en products.json.
    save_json($products,'./json/products.json');

    header('location: ?admin=new-product');
}

?>

<html>
<head>
    <title>Carga Productos</title>
    <style>
        .container {
            padding: 0 50px;
        }
    </style>
</head>

<div class="container">
    <div class="row">
        <div class="col">

            <h1>Nuevo Producto</h1>
            <h5>Cod Producto Nº <?= $cod = random_id('OSS',$products) ?></h5>    <!-- Genera un codigo aleatorio y no repetitivo para el producto -->

                <form action="" method="post" enctype="multipart/form-data">

                <h5 style="text-align: left">Datos - Detalle General de Producto</h5>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="name">Título :</label>
                            <input type="text" class="form-control" placeholder="Título" name="name" required autofocus>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="category">Categoría :</label>
                            <select name="category" id="categoria" class="form-control" required>
                                <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="quality">Calidad :</label>
                            <select name="quality" id="calidad" class="form-control" required>
                                <option value="Nuevo">Nuevo</option>
                                <option value="Usado">Usado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="status">Estado :</label>
                            <select name="status" id="estado" class="form-control" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-group col-md-1">
                            <label for="stock">Stock :</label>
                            <input type="number" step="1" class="form-control" placeholder="XX" name="stock">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="colour">Color :</label>
                            <input type="text" class="form-control" placeholder="Color" name="colour" required autofocus>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="model">Modelo :</label>
                            <input type="text" class="form-control" placeholder="Modelo" name="model" required>
                        </div>
                        <div class="form-group col-md-1">
                            <label for="currency">Moneda :</label>
                            <select name="currency" id="moneda" class="form-control" required>
                                <option value="$">$</option>
                                <option value="U$S">U$S</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="price">Precio :</label>
                            <input type="text" class="form-control" placeholder="Precio" name="price" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="image">Imagen :</label>
                            <input type="file" name="image" accept="image/*" class="" 
                            id="imagen" aria-describedby="inputGroupFileAddon01" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción Detallada :</label>
                        <textarea type="text" rows="8" cols="" class="form-control" placeholder="Descripcion" name="description_detail" required></textarea>
                    </div>

                    <h5 style="text-align: left">Datos - Página Detalle Productos</h5>

                    <div class="form-group">
                        <label for="descripcion">Descripción General :</label>
                        <input type="text" class="form-control" placeholder="Descripcion" name="description_general" required>
                    </div>

                    <h5 style="text-align: left">Datos - Página Articulo</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="descripcion">Descripción Modelo :</label>
                            <input type="text" class="form-control" placeholder="Descripcion" name="description_model" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="descripcion">Descripción Calidad :</label>
                            <input type="text" class="form-control" placeholder="Descripcion" name="description_quality" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción Venta :</label>
                        <input type="text" class="form-control" placeholder="Descripcion" name="description_title" required>
                    </div>

                    <hr>

                    <div class="form-group">
                        <input type="hidden" name="cod" value="<?= $cod ?>">
                        <button class="btn btn-success">Guardar</button>
                    </div>
                </form>

                <br>
                <a href="?view=home">Ir a Home</a>

        </div>
    </div>
</div>
</html>