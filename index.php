<?php
    if (!defined('controller')) define('controller', __DIR__."/mc/controller");    
    require_once controller."/filmFormListController.php";
    $controller = new FilmFormListController();
    $controller->fetchData();
    $controller->readSessions();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FilmFlix</title>
        <?php include 'head.html' ?>
        <link rel="stylesheet" href="css/data.css">
    </head>

    <body>
        <div id="wrapper">
            <?php include 'header.html'?>

            <main>
                <section id="searchSection" style="background-image: url('img/projector.jpg')">
                    <div id="searchPanel">
                        <input id="searchBar" type="text" placeholder="Search movie...">
                        <button>+</button>
                    </div>

                    <div id="filtersPanel">
                        <label>Filter by</label>
                        <select id="dropdownOptions">
                            <option id='0'></option>
                            <option id='1'>Rating</option>
                            <option id='2'>Genre</option>
                            <option id='3'>Year</option>
                        </select>
                        <div id="filterOptions">
                        </div>
                    </div>
                </section>

                <section id="dataSection" style="background-image: url('img/projector.jpg')">
                    <div id="data">
                        <?php $controller->displayData();?>
                    </div>
                </section>
            </main>


            <footer style="background-image: url('img/projector.jpg')">
                    <?php $controller->recordTracker->addRecordTracker()?>
            </footer>
        </div>

        <script>
            new FilmFormList();
        </script>
    </body>
</html>
