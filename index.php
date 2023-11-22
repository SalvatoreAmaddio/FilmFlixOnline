<?php
    include("php/controller/filmController.php");
    $controller = new FilmController();
    $controller->readTable();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FilmFlix</title>
        <link rel="stylesheet" href="css/file.css">
        <link rel="stylesheet" href="css/data.css">
        <link rel="stylesheet" href="css/recordTracker.css">
        <link rel="stylesheet" href="css/index.css">
        <script src="php/js/ajax.js"></script>
        <script src="php/js/listForm.js"></script>
    </head>

    <body>
        <div id="wrapper">
            <header>
                <h2>Welcome to the</h2>
                <h1>FilmFlix</h1>
                <h1>Management System</h1>
            </header>

            <main>
                <section id="searchSection">
                    <div id="searchPanel">
                        <input type="text" placeholder="Search...">
                        <button>+</button>
                    </div>
                </section>

                <section id="dataSection">
                    <div id="data">
                        <table>
                            <tr>
                                <th colspan="2">Title</th>
                                <th>Year</th>
                                <th>Rating</th>
                                <th>Duration</th>
                                <th>Genre</th>
                                <th colspan="2">COMMANDS</th>
                            </tr>
                            <?php $controller->displayTableData();?>
                        </table>
                    </div>
                </section>

                <?php $controller->addRecordTracker()?>
            </main>
        </div>

        <script>
            let form = new ListForm();
            form.ajax = new Ajax("index.php");
            
        </script>
    </body>
</html>