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
                    <div id='dropDownContainer'>
                        <img src="img/drop-down-arrow.png">
                    </div>
                    <div id='filterContainer'>
                        <div id='filters'>
                            <label>Filter By</label>
                            <div id='filterOptions'>
                                <label for="genre">Gener:</label>
                                <input id='genre' type="checkbox">
                                <label for='year'>Year:</label>
                                <input id='year' type="checkbox">
                                <label for="rating">Rating:</label>
                                <input id='rating' type="checkbox">
                            </div>
                            <select>
                                <option>Select an option from the above</option>
                            </select>
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
            new ListForm("php/controller/FilmFormListController.php");

            const dropDown = document.getElementById('dropDownContainer').children[0];
            const filterContainer = document.getElementById('filterContainer');
            const searchSection = document.getElementById("searchSection");
            const infoButton = document.getElementById("infoButton");

            dropDown.addEventListener("click",(e)=>
            {
                if (!filterContainer.style.display || filterContainer.style.display=='none') 
                {
                    filterContainer.style.display='block';
                    dropDown.setAttribute('src','img/drop-up-arrow.png');
                    searchSection.style.paddingBottom = '1rem';
                }
                else 
                {
                    filterContainer.style.display='none';
                    searchSection.style.paddingBottom = '2.3rem';
                    dropDown.setAttribute('src','img/drop-down-arrow.png');
                }
            });

            window.addEventListener("scroll",(e)=>
            {
                let topVal = searchSection.getBoundingClientRect().top;
                if (topVal==0) 
                {
                    infoButton.style.top="6rem";
                    infoButton.style.left="90.3%";
                }
                else 
                {
                    infoButton.style.top="1rem";
                    infoButton.style.left="1rem";
                }
            });
        </script>
    </body>
</html>
