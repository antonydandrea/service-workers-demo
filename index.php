<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Simple ToDo App</title>
        <link rel="stylesheet" type="text/css" href="css/main.css"/>
    </head>
    <body>
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }).catch(function(err){
                   console.log('ServiceWorker registration failed: ', err); 
                });
            }
        </script>
        <h1>Simple ToDo App</h1>
        <div id="main-content">
            <form method="POST">
                <input name="todo_item" id="todo_item_input" type="text" width="400"/>
                <input name="submit_todo" type="submit" value="Add to List" />
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
                            <p class="todo_item <?php echo $item['done_status'] == 1?'done':'';?>"><?php echo $i + 1;?>.</p>
                            &nbsp;<p class="todo_item <?php echo $item['done_status'] == 1?'done':'';?>"><?php echo $item['item'];?></p>
                            <?php if ($item['done_status'] == 1) {?>
                                <button name="mark_undone" value="<?php echo $item['pk'];?>" type="submit">Mark as not done</button>
                            <?php } elseif ($item['done_status'] == 0) {?>
                                <button name="mark_done" value="<?php echo $item['pk'];?>" type="submit">Mark as done</button>
                            <?php }?>
                        </div>
                    <?php } ?>
                </div>
            </form>
        </div>
    </body>
</html>
<?php
    if (isset($_POST['mark_done']) || isset($_POST['mark_undone'])) {
        if (isset($_POST['mark_done'])) {
            $status = 1;
            $pk = $_POST['mark_done'];
        } elseif (isset($_POST['mark_undone'])) {
            $status = 0;
            $pk = $_POST['mark_undone'];
        }
        $connection = getConnection();
        if ($connection) {
            $query = "UPDATE items SET done_status = ? WHERE pk = ?;";
            $stmt = $connection->prepare($query);
            $stmt->bind_param('ii', $status, $pk);
            $stmt->execute();
        }
        echo '<meta HTTP-EQUIV="Refresh" Content="0; URL=/ServiceWorkersDemo">';
    } elseif (isset($_POST['submit_todo']) && !empty($_POST['todo_item'])) {
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
