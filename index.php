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
    <link rel="stylesheet" href="css/index.css">
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
                    <button>NEW</button>
                </div>
            </section>

            <section id="dataSection">
                <div id="data">
                    <table>
                        <tr>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Rating</th>
                            <th>Duration</th>
                            <th>Genre</th>
                            <th colspan="2">COMMANDS</th>
                        </tr>
                        <?php
                            $controller->displayTableData();
                        ?>
<!--                        <tr>
                            <td><p>The Muppets</p></td>
                            <td><p>2022</p></td>
                            <td><p>PG</p></td>
                            <td><p>116</p></td>
                            <td><p>Comedy</p></td>
                            <td><button>Edit</button></td>
                            <td><button>Delete</button></td>
                        </tr>-->
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>