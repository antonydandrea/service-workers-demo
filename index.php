<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Simple ToDo App</title>
        <link rel="stylesheet" type="text/css" href="css/main.css"/>
    </head>
    <body>
        <h1>Simple ToDo App</h1>
        <div id="main-content">
            <form method="POST">
                <input name="todo_item" id="todo_item_input" type="text" width="400"/>
                <input name="submit_todo" type="submit" value="Add to List" />
            </form>
        </div>
    </body>
</html>
<?php
    if (isset($_POST['submit_todo']) && !empty($_POST['todo_item'])) {
        $settingsFilePath = 'config.json';
        if (file_exists($settingsFilePath)) {
            $jsonSettings = file_get_contents($settingsFilePath);
            $settings = json_decode($jsonSettings, 1);
            $dbSettings = $settings['dbsettings'][0];
            if (isset($dbSettings['user']) && isset($dbSettings['password']) && isset($dbSettings['host']) && isset($dbSettings['database'])) {
                $connection = mysqli_connect($dbSettings['host'],
                    $dbSettings['user'],
                    $dbSettings['password'],
                    $dbSettings['database']
                );
                $query = "INSERT INTO items (item) VALUES ('{$_POST['todo_item']}');";
                $result = $connection->query($query);
            }
        }
    }
?>
