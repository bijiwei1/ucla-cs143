<html>
<head>
    <title>CS143 - Project 1B - Show movie info</title>
    <link href="./bootstrap.min.css" rel="stylesheet">
    <style>
    table, td, th {
        border: 1px solid black;
    }
    </style>
</head>
<body style="background-color:lightblue">
<div class="container">
    <!-- Search for actors/movies -->
    <form action="./search.php" method="GET">
        Search for actors/movies:
        <input type="text" name="keyword" />
        <input class="btn btn-default" type="submit" value="Search" />
    </form>
    <hr />

    <?php
    if (!isset($_GET["mid"]) || $_GET["mid"] === "")
        die("No movie entered.");

    $db = mysql_connect("localhost", "cs143", "");
    if (!$db)
        die("Unable to connect to database: " . mysql_error());

    $db_selected = mysql_select_db("CS143", $db);
    if (!$db_selected)
        die("Unable to select database: " . mysql_error());

    $mid = (int) $_GET["mid"];

    // Movie info
    $query = "SELECT * FROM Movie WHERE id=" . $mid;
    if (!$result = mysql_query($query))
        die("Error executing query: " . mysql_error());
    if (mysql_num_rows($result) != 1)
        die("No movie with mid=$mid found.");

    $row = mysql_fetch_assoc($result);
    $title = $row["title"];
    $year = $row["year"];
    echo "<h3>$title ($year)</h3>\n";
    if (is_null($row["rating"]))
        echo "MPAA Rating: N/A<br />\n";
    else
        echo "MPAA Rating: $row[rating]<br />\n";
    if (is_null($row["company"]))
        echo "Company: N/A<br />\n";
    else
        echo "Company: $row[company]<br />\n";
    mysql_free_result($result);

    // Movie director
    $query = "SELECT * FROM MovieDirector
        INNER JOIN Director ON Director.id = MovieDirector.did
        WHERE mid=" . $mid;
    if (!$result = mysql_query($query))
        die("Error executing query: " . mysql_error());
    $directors = "Director: ";
    for ($i = 0; $i < mysql_num_rows($result) - 1; $i++)
    {
        $row = mysql_fetch_assoc($result);
        $name = "$row[first] $row[last]";
        $directors .= "$name, ";
    }
    $row = mysql_fetch_assoc($result);
    $name = "$row[first] $row[last]";
    $directors .= $name;
    echo "$directors<br />\n";
    mysql_free_result($result);

    // MovieGenre info
    $query = "SELECT genre FROM MovieGenre WHERE mid=" . $mid;
    $query .= " ORDER BY genre ASC";
    if (!$result = mysql_query($query))
        die("Error executing query: " . mysql_error());
    $genres = "Genre: ";
     for ($i = 0; $i < mysql_num_rows($result) - 1; $i++)
    {
        $row = mysql_fetch_assoc($result);
        $genres .= "$row[genre], ";
    }
    $row = mysql_fetch_assoc($result);
    $genres .= $row["genre"];
    echo "$genres<br /> \n";
    mysql_free_result($result);

    echo "<br />";
    echo "<h4>More info:</h4>\n";

    // MovieRating info
    $query = "SELECT imdb,rot FROM MovieRating WHERE mid=" . $mid;
    if(!$result = mysql_query($query))
        die("Error executing query: " . mysql_error());
    $row = mysql_fetch_assoc($result);
     if (is_null($row["imdb"]))
        echo "IMDB Rating: N/A<br />\n";
    else
        echo "IMDB Rating: $row[imdb]/100<br />\n";
    if (is_null($row["rot"]))
        echo "Rotten Tomatoes Rating: N/A<br />\n";
    else
        echo "Rotten Tomatoes Rating: $row[rot]/100<br />\n";
    mysql_free_result($result);

    // Sales info
    $query = "SELECT * FROM Sales WHERE mid=" . $mid;
    if(!$result = mysql_query($query))
        die("Error executing query: " . mysql_error());
    $row = mysql_fetch_assoc($result);
    $tickets = $row["ticketsSold"];
    $income = $row["totalIncome"];

     if (is_null($tickets))
        echo "Tickets Sold: N/A<br />\n";
    else
        echo "Tickets Sold: $tickets<br />\n";
    if (is_null($income))
        echo "Total Income: N/A<br />\n";
    else
        echo "Total Income: $income<br />\n";
    mysql_free_result($result);
    //
    // MovieActor info
    $queryMA = "SELECT aid, role, first, last FROM MovieActor
              INNER JOIN Actor ON MovieActor.aid = Actor.id
              WHERE mid=" . $mid;
    if (!$resultMA = mysql_query($queryMA))
        die("Error executing query: " . mysql_error());
    echo "<br />";
    echo "<h4>Cast:</h4>\n";
    echo "<div class=\"row\">\n";
    echo "<div class=\"col-md-3\"></div>\n";
    echo "<div class=\"col-md-6\">\n";
    echo "<table class=\"table\">\n";
    echo "<tr align=center>";
    echo "<td><b>Actor </b></td>";
    echo "<td><b>Role</b></td>";
    echo "</tr>\n";
    while ($row = mysql_fetch_assoc($resultMA)) {
        echo "<tr align=center>";
        $aid = $row["aid"];
        $name = "$row[first] $row[last]";
        $role = $row["role"];
        echo "<td><a href=\"./show_actor_info.php?aid=$aid\">$name</a></td>";
        echo "<td>$role</td>";
        echo "</tr>\n";
    }    
    echo "</table>\n";
    echo "</div>\n";
    echo "<div class=\"col-md-3\"></div>\n";
    echo "</div>\n";
    mysql_free_result($resultMA);
    echo "<hr>\n";

    // All reviews
    $query = "SELECT avg(rating), count(rating) FROM Review
           WHERE mid=" . $mid;
    if(!$result = mysql_query($query))
        die("Error executing query: " . mysql_error());
     $row = mysql_fetch_row($result);
    $avgrat = $row[0];
    $countrat = $row[1];
     if (is_null($avgrat))
        echo "Average Score: N/A. \n";
    else
        echo "Average Score: $avgrat/5 by $countrat review(s). \n";
    mysql_free_result($result);

    $query = "SELECT name, time, rating, comment FROM Review
           WHERE mid=" . $mid . " ORDER BY time DESC";
    if(!$result = mysql_query($query))
        die("Error executing query: " . mysql_error());

    echo "<a href=\"./add_review.php?mid=$mid\">Add Review Here!</a><br/>\n";
    echo "All Comments Displayed With Details:";
    while ($row = mysql_fetch_assoc($result)) {
        $name = $row["name"];
        $time = $row["time"];
        $rating = $row["rating"];
        $comment = $row["comment"];
        if (empty($rating))
            $rating = "n/a";
        if (empty($comment))
            $comment = "";

        echo "<br/><br/>\n";
        echo "On $time, <b>$name</b> rated this movie a score of $rating star(s). The rater said: <br/>\n";
        echo "$comment\n";
    }
    mysql_free_result($result);
    mysql_close($db);
    ?>
</div>
</body>
</html>
