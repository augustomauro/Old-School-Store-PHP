<?php

$users = get_json('json/users.json');

$users_type = get_json('json/users_type.json');

if ($_POST) {
    // Usuario a borrar:
    $us_delete = (int)$_POST['eliminar'];

    // Verifica posicion original del usuario dentro de users.json
    $i = 0;
    foreach ($users as $user) {
        if ( $user['id'] === $us_delete ) {
        $pos = $i;
        }
        $i++;
    }

    // Elimina la imagen asociada (avatar) si existe
    if ($users[$pos]['avatar'] != null) {
        unlink($users[$pos]['avatar']);
    }

    $resultado = [];
    $us1 = [];
    $us2 = [];
    $res1 = [];
    $res2 = [];
    if ($pos == 0) {
        // El usuario esta en posicion inicial. Selecciona desde 1 hasta el ultimo
        $resultado = array_splice($users, 1, (count($users)-1));
    } elseif ($pos == (count($users)-1)) {
        // El usuario esta en posicion final. Selecciona desde 0 hasta anteultimo
        $resultado = array_splice($users, 0, -1);
    } else {
        // El usuario esta en el medio.
        $us1 = $users;
        $us2 = $users;
        // Corta y guarda desde $pos+1 hasta el ultimo
        $res1 = array_splice($us1, $pos+1, (count($users)-1));
        // Corta y guarda desde 0 hasta $pos
        $res2 = array_splice($us2, 0, $pos);
        // Hace merge del array final resultante
        $resultado = array_merge($res2, $res1);
    }

    // Grabamos la nueva base a json (reemplaza la anterior)
    save_json($resultado,'./json/users.json');

    // Recarga la pagina y evita reenvio de formulario
    header('location: ?admin=users-list');

}

?>

<div class="container">
    <div class="row">
        <div class="col">
            <h1>Usuarios Registrados (<?= count($users) ?>)</h1>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Cod</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email Principal</th>
                        <th>Tipo</th>
                        <th>Avatar</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($users as $id => $user): ?>
                        <tr>
                            <td><?= $id ?></td>
                            <td><?= $user['code'] ?></td>
                            <td><?= $user['firstname'] ?></td>
                            <td><?= $user['lastname'] ?></td>
                            <td><?= $user['email1'] ?></td>
                            <?php foreach ($users_type as $key => $value): ?>
                                <?php if ($value['id'] == $user['user_type']): ?>
                                    <td><?= $value['type'] ?></td>
                                <?php endif ?>
                            <?php endforeach ?>
                            <td>
                                <img src="<?= $user['avatar'] ?>" style="border-radius: 50%; width: 25px;">
                            </td>
                            <td>
                                <form action="" method="post">
                                    <button class="btn btn-sm btn-success" name="detalle" value="<?= $user['id'] ?>">Detalles</button>
                                    <button class="btn btn-sm btn-primary" name="editar" value="<?= $user['id'] ?>">Editar</button>
                                    <button class="btn btn-sm btn-danger" name="eliminar" value="<?= $user['id'] ?>">Borrar</button>
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