<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <link rel="stylesheet" type="text/css" href="css/css.css">
    <link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
    <meta charset="ISO-8859-1">
    <title>search engine</title>
    <link
        href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
        rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script>
    //JQUERY function for autocomplet based of "name_ngram" field
        $(function () {

            var URL_PREFIX = "http://localhost:8983/solr/searchengine/select?q=title:";
            var URL_MIDDLE = "OR name_ngram:";
            var URL_SUFFIX = "&wt=json";
            $("#ngramBox").autocomplete(
                {
                    source: function (request, response) {
                        var searchString = "\"" + $("#ngramBox").val() + "\"";
                        var URL = URL_PREFIX + searchString + URL_MIDDLE
                            + searchString + URL_SUFFIX;
                        $.ajax({
                            url: URL,
                            success: function (data) {
                                var docs = JSON.stringify(data.response.docs);
                                var jsonData = JSON.parse(docs);
                                response($.map(jsonData, function (value, key) {
                                    return {
                                        label: value.title
                                    }
                                }));
                            },
                            dataType: 'jsonp',
                            jsonp: 'json.wrf'
                        });
                    },
                    minLength: 1
                })
        });
    </script>
</head>
<body>
<h2>This is a web application created by Emad Al Rifai & Bilal Grine as a M2-DNR2I project at
    UNICAEN with a goal of building a semantic Wayback machine.</h2><br><br>

<div id="wrapper">
    <h5 style="color: chocolate" >ASK YOUR QUESTION</h5>
    <!-- search form -->
    <form action='search.php' method='GET'>


        <label for="ngramBox"></label>
        <input id="ngramBox" type='text' size='90' name='search' value="">


        <input type='submit' name='submit' value='Search'>
        <br><br>
        <div class="select-style">

            <select id="date" name="date">
                <option value="*">ALL TIME</option>
                <option value="[2015-01-15T10:49:00Z+TO+2016-01-15T16:33:00Z]">First Crawl</option>
                <option value="[2016-01-16T10:49:00Z+TO+2016-01-16T16:33:00Z]">Second Crawl</option>
                <option value="[2016-01-18T10:49:00Z+TO+2016-01-18T16:33:00Z]">third Crawl</option>

            </select>
        </div>
        <div class="select-style">

            <select id="tags" name="tags">
                <option value="*">ALL TAGS</option>
                <option value="javascript">javascript</option>
                <option value="java">java</option>
                <option value="solr">solr</option>
                <option value="node.js">node.js</option>
                <option value="swift">swift</option>
                <option value="html">html</option>
                <option value="sql">sql</option>

            </select>

</div>

    </form>


    <?php


    //get the data from the search form
    $search = $_GET ['search'];

    $date = $_GET ['date'];
    $tags = $_GET ['tags'];
    //replace the spacese with "%2B" it mean "+" in solr query
    $string = preg_replace("/ /", "%2B", $search);
    //replace the "?" with a "."
    $name_ngram = str_replace("?", ".", $string);
    if($name_ngram==""){
        $name_ngram="*";
    }


    //query URL
    $url = "http://localhost:8983/solr/searchengine/select?q=date%3A$date+AND+title%3A$name_ngram+AND+tags%3A$tags&rows=20&wt=json&indent=true";


    //Executes the URL and saves the content (json) in the variable.
    $content = file_get_contents($url);
    $result = json_decode($content, true);
    
    //Return number of result
    $rr = $result['response']['numFound'];
    if (!empty($_GET)) {
        if ($rr == 0)
            echo "<br><br><br><div id=\"wrapper\">No Result Found</div>";
        else
            echo "<br><br><br><div id=\"wrapper\">  number of result found :" . $rr . "</div>";
    }
    
    //Return result as (url,title,tags)
    $data = $result['response']['docs'];

    foreach ($data as $key => $value) {
        foreach ($value as $key1 => $value1) {
            if ($key1 == "url")
                echo "<br><a href='$value1' >$value1</a><br>";
            if ($key1 == "title")
                echo $value1 . "<br>";
            if ($key1 == "tags")
                echo "<p style=color:red >tags :" . $value1 . "</p><br>";

        }
    }


    ?>

</div><!-- #wrapper -->

</body>
</html>

