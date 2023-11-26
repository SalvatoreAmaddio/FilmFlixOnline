<?php
 $x = explode("php", __DIR__);
    include($x[0]."/php/controller/FilmFormListController.php");
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
            new ListForm("php/controller/FilmFormListController.php");

            const s = document.getElementById("searchSection");
            const k = document.getElementById("infoButton");
            window.addEventListener("scroll",(e)=>
            {
                let x=s.getBoundingClientRect().top;
                if (x==0) 
                {
                    k.style.top="4.5rem";
                    k.style.left="88%";
                }
                else 
                {
                    k.style.top="1rem";
                    k.style.left="1rem";
                }
            });
        </script>
    </body>
</html>
