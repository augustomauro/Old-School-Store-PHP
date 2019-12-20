<?php

if ($_POST) {

    $users_type = get_json('json/users_type.json');

    $count = count($users_type);

    $tmp = [
        'id' => $count++,
        'type' => $_POST['user_type']
        ];

    $users_type[] = $tmp;

    save_json($users_type,'json/users_type.json');

}

?>


<div class="container">
    <div class="row">
        <div class="col">

            <h1>Users Type Create</h1>

                <form action="" method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="User Type" name="user_type" style="width:25%">
                    </div>
                    
                    <div class="form-group">
                        <button class="btn btn-primary">Guardar</button>
                    </div>
                </form>

                <br>
                <a href="?view=home">Ir a Home</a>

        </div>
    </div>
</div>


