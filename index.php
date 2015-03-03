<pre>
<?php

    require_once('class.db.php');
    require_once('class.model.php');

    DB::init('SERVER_LOCAL2');
    DB::connect();
    $test = Model::test();
    DB::disconnect();

    print_r($test);


?>
    </pre>