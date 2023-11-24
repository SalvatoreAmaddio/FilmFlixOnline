<?php
    include("php/controller/FilmFormListController.php");
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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Rowdies:wght@300;400;700&display=swap" rel="stylesheet">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/file.css">
        <link rel="stylesheet" href="css/data.css">
        <link rel="stylesheet" href="css/recordTracker.css">
        <link rel="stylesheet" href="css/index.css">
        <script src="php/js/listForm.js"></script>
    </head>

    <body>
        <div id="wrapper">
            <header style="background-image: url('img/projector.jpg')">
                <img src="img/theater.png">
                <div id="headerTitle">
                    <h2>Welcome to the</h2>
                    <h1><span>Film</span>Flix</h1>
                    <h1>Management System</h1>
                </div>
            </header>

            <main>
                <section id="searchSection" style="background-image: url('img/projector.jpg')">
                    <div id="searchPanel">
                        <input id="searchBar" type="text" placeholder="Search...">
                        <button>+</button>
                    </div>
                </section>

                <section id="dataSection">
                    <div id="data">
                        <table>
                            <?php $controller->displayData();?>
                        </table>
                    </div>
                </section>

                <section class="rt">
                    <?php $controller->recordTracker->addRecordTracker()?>
                </section>
            </main>
        </div>

        <script>
            new ListForm("php/controller/FilmFormListController.php");
        </script>
    </body>
</html>
