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
            <div>
                <?php
                    $connection = getConnection();
                    $resultArray = array();
                    if ($connection) {
                        $query = "SELECT * FROM items;";
                        $results = $connection->query($query);
                        if ($results) {
                            while($row = $results->fetch_array(MYSQLI_ASSOC)) {
                                $resultArray[] = $row;
                            }
                            $results->close();
                        }
                    }
                ?>
                <?php foreach ($resultArray as $i => $item) { ?>
                    <div>
                        <p class="todo_item"><?php echo $i + 1;?>.</p>&nbsp;<p class="todo_item"><?php echo $item['item'];?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </body>
</html>
<?php
    if (isset($_POST['submit_todo']) && !empty($_POST['todo_item'])) {
        $connection = getConnection();
        if ($connection) {
            $query = "INSERT INTO items (item) VALUES (?);";
            $stmt = $connection->prepare($query);
            $stmt->bind_param('s', $_POST['todo_item']);
            $stmt->execute();
        }
         echo '<meta HTTP-EQUIV="Refresh" Content="0; URL=/ServiceWorkersDemo">';
    }
    
    function getConnection()
    {
        $dbSettings = array();
        $connection = false;
        $settingsFilePath = 'config.json';
        if (file_exists($settingsFilePath)) {
            $jsonSettings = file_get_contents($settingsFilePath);
            $settings = json_decode($jsonSettings, 1);
            $dbSettings = $settings['dbsettings'][0];
        }
        if (isset($dbSettings['user']) && isset($dbSettings['password']) && isset($dbSettings['host']) && isset($dbSettings['database'])) {
            $connection = mysqli_connect($dbSettings['host'],
                $dbSettings['user'],
                $dbSettings['password'],
                $dbSettings['database']
            );
        }
        return $connection;
    }
?>
