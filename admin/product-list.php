<?php

$products = get_json('json/products.json');
$categories = get_json('./json/categories.json');

if ($_POST) {
    // Usuario a borrar:
    $art_delete = (int)$_POST['borrar'];

    // Verifica posicion original del usuario dentro de users.json
    $i = 0;
    foreach ($products as $articulo) {
        if ( $articulo['id'] === $art_delete ) {
        $pos = $i;
        }
        $i++;
    }

    // Elimina la imagen asociada si existe
    if ($products[$pos]['image'] != null) {
        unlink($products[$pos]['image']);
    }

    $resultado = [];
    $art1 = [];
    $art2 = [];
    $res1 = [];
    $res2 = [];
    if ($pos == 0) {
        // El producto esta en posicion inicial. Selecciona desde 1 hasta el ultimo
        $resultado = array_splice($products, 1, (count($products)-1));
    } elseif ($pos == (count($products)-1)) {
        // El producto esta en posicion final. Selecciona desde 0 hasta anteultimo
        $resultado = array_splice($products, 0, -1);
    } else {
        // El producto esta en el medio.
        $art1 = $products;
        $art2 = $products;
        // Corta y guarda desde $pos+1 hasta el ultimo
        $res1 = array_splice($art1, $pos+1, (count($products)-1));
        // Corta y guarda desde 0 hasta $pos
        $res2 = array_splice($art2, 0, $pos);
        // Hace merge del array final resultante
        $resultado = array_merge($res2, $res1);
    }

    // Grabamos la nueva base a json (reemplaza la anterior)
    save_json($resultado,'./json/products.json');
    // Recarga la pagina
    header('location: ?admin=product-list');

    //var_dump($resultado);
}

?>

<div class="container">
    <div class="row">
        <div class="col">
            <h1>Articulos Registrados (<?= count($products) ?>)</h1>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Cod</th>
                        <th>Titulo</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Calidad</th>
                        <th>Estado</th>
                        <th>Imagen</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($products as $id => $articulo): ?>
                        <tr>
                            <td><?= $id ?></td>
                            <td><?= $articulo['code'] ?></td>
                            <td><?= $articulo['name'] ?></td>
                            <?php foreach ($categories as $category): ?>
                            <?php if ($articulo['cat_id'] == $category['id']): ?>
                            <td><?= $category['name'] ?></td>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <td><?= $articulo['currency'] . $articulo['price'] ?></td>
                            <td><?= $articulo['stock'] ?></td>
                            <td><?= $articulo['quality'] ?></td>
                            <td><?= $articulo['status'] ?></td>
                            <td>
                                <img src="<?= $articulo['image'] ?>" style="width: 25px;">
                            </td>
                            <td>
                                <form action="" method="post">
                                    <button class="btn btn-sm btn-success" name="detalle" value="<?= $articulo['id'] ?>">Detalles</button>
                                    <button class="btn btn-sm btn-primary" name="editar" value="<?= $articulo['id'] ?>">Editar</button>
                                    <button class="btn btn-sm btn-danger" name="borrar" value="<?= $articulo['id'] ?>">Borrar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <br>
            <a href="?view=home">Ir a Home</a>

        </div>
    </div>
</div>