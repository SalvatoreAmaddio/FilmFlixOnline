<?php
    session_start();
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
        <title>Amend Record</title>
        <link rel="stylesheet" href="css/file.css">
        <link rel="stylesheet" href="css/formData.css">
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="css/recordTracker.css">
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
                <section id="dataSection">
                    <div id="data">
                        <table>
                            <caption>Data</caption>
                            <tr>
                                <td>
                                    <label>Title</label>
                                </td>
                                <td>
                                    <input type="text" value='<?php echo $controller->model()->title?>'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Year</label>
                                </td>
                                <td>
                                    <input type="number" value='<?php echo $controller->model()->yearReleased?>'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Rating</label>
                                </td>
                                <td>
                                    <input type="text" value='<?php echo $controller->model()->rating?>'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Duration</label>
                                </td>
                                <td>
                                    <input type="number" value='<?php echo $controller->model()->duration?>'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Genre</label>
                                </td>
                                <td>
                                    <select>
                                        <?php $controller->genreController->genreList()?>
                                    </select>
                                </td>
                            </tr>
                        </table>

                        <div class="commands">
                            <button value='<?php echo $controller->model()->filmID?>'>💾</button>
                            <button value='<?php echo $controller->model()->filmID?>'>X</button>
                        </div>
                    </div>
                </section>

                <section class="rt">
                    <?php $controller->addRecordTracker()?>
                </section>
            </main>
        </div>

        <script>
            new ListForm("php/controller/filmController.php");
        </script>
    </body>
</html>
