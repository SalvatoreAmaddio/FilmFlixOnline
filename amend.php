<?php
    include("php/controller/FilmFormController.php");
    $controller = new FilmFormController();
    $controller->fetchData();
    $controller->readSessions();
    $controller->sessions->selectedID($controller->model()->filmID);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Amend Record</title>
        <?php include 'head.html' ?>
        <link rel="stylesheet" href="css/formData.css">
    </head>
    
    <body>
        <div id="wrapper">
        <?php include 'header.html'?>

            <main>
                <section id="dataSection" style="background-image: url('img/projector.jpg')">
                    <div id="data">
                        <table>
                            <caption>Record</caption>
                            <tr>
                                <td>
                                    <label>Title</label>
                                </td>
                                <td>
                                    <input class="recordField" type="text" value='<?php echo $controller->model()->title?>'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Year</label>
                                </td>
                                <td>
                                    <input class="recordField" type="number" value='<?php echo $controller->model()->yearReleased?>'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Rating</label>
                                </td>
                                <td>
                                    <select class="recordField" value='<?php echo $controller->model()->rating->ratingID?>'>
                                            <?php $controller->ratingController->ratingList($controller->model()->rating)?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Duration</label>
                                </td>
                                <td>
                                    <input class="recordField" type="number" value='<?php echo $controller->model()->duration?>'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Genre</label>
                                </td>
                                <td>
                                    <select class="recordField" value='<?php echo $controller->model()->genre->genreID?>'>
                                        <?php $controller->genreController->genreList($controller->model()->genre)?>
                                    </select>
                                </td>
                            </tr>
                        </table>

                        <div class="commands">
                            <button class="saveButton" value='<?php echo $controller->model()->filmID?>'>💾</button>
                            <button class="deleteButton" value='<?php echo $controller->model()->filmID?>'>X</button>
                        </div>
                    </div>
                </section>
            </main>

            <footer style="background-image: url('img/projector.jpg')">
                    <?php $controller->recordTracker->addRecordTracker()?>
            </footer>
        </div>

        <script>
            new Form("php/controller/FilmFormController.php");
        </script>
    </body>
</html>
