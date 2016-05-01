<html>
<head>
    <title>CS143 - Project 1B - Add review</title>
    <link href="./bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    $mid = $_POST["mid"];
    $name = $_POST["name"];
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];

    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($name))
    {
    	$db = mysql_connect("localhost", "cs143", "");
        if (!$db)
            die("Unable to connect to database: " . mysql_error());

        $db_selected = mysql_select_db("CS143", $db);
        if (!$db_selected)
            die("Unable to select database: " . mysql_error()); 

    	$mid = (int) $mid;
    	$name = "'" . mysql_real_escape_string($name) . "'";
    	$rating = (int) $rating;
    	if(empty($comment))
    		$comment = "NULL";
    	else
    		$comment = "'" . mysql_real_escape_string($comment) . "'";

    	$query = "INSERT INTO Review (name, time, mid, rating, comment) VALUES (";
    	$query .= "$name, NOW(), $mid, $rating, $comment)";
    	if (!$result = mysql_query($query))
    		die("Error executing query: ". mysql_error()); 
    	mysql_close($db);

        echo "Comment added by $name.\n";
        echo "<hr />";
    }
    else if ($_SERVER["REQUEST_METHOD"] === "POST")
    {
        echo "Must enter a name.\n";
        echo "<hr />";
    }
    ?>

    <p>Add new comment:</p>
    <form action="./add_review.php" method="POST">
        <?php
        $db = mysql_connect("localhost", "cs143", "");
        if (!$db)
            die("Unable to connect to database: " . mysql_error());

        $db_selected = mysql_select_db("CS143", $db);
        if (!$db_selected)
            die("Unable to select database: " . mysql_error());

        // All movies
        $query = "SELECT * FROM Movie ORDER BY title ASC";
        if (!$result = mysql_query($query))
            die("Error executing query: " . mysql_error());

        echo "Movie: <select name=\"mid\">\n";
        while ($row = mysql_fetch_assoc($result)) {
            $title = $row["title"];
            $year = $row["year"];
            $mid = $row["id"];
            if (!empty($_GET["mid"]) && $mid === $_GET["mid"])
                echo "<option value=\"$mid\" selected>$title ($year)</option>\n";
            else
                echo "<option value=\"$mid\">$title ($year)</option>\n";
        }    
        echo "</select><br />\n";
        mysql_free_result($result);
        mysql_close($db);
        ?>

        Your Name:	<input type="text" name="name" value="Anonymous" maxlength="20"><br/>
        Rating:	<select name="rating">
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Okay</option>
                    <option value="2">2 - Bad</option>
                    <option value="1">1 - Terrible</option>
                </select>
        <br/>
        Comments: <br/>
        <textarea name="comment" cols="80" rows="10"></textarea>
        <br/>
        <input type="submit" value="Add review"/>
    </form>
</body>
</html>
