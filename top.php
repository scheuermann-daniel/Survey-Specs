<!-- The DOCTYPE declaration defines the document type and version of HTML -->
<!DOCTYPE HTML>

<!-- Opening HTML tag with language attribute set to "en" (English) -->
<html lang="en">

    <head>
        <!-- Meta tags provide metadata about the HTML document -->
        <meta charset="utf-8"> <!-- Character encoding -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport settings -->
        <meta name="author" content="Daniel Scheuermann"> <!-- Author information -->
        <meta name="decription" content="Lab 2"> <!-- Description of the page -->

        <!-- Title of the HTML document -->
        <title>Dan's Site</title>

        <!-- Link to an external CSS file (custom.css) with a version parameter to force reloading on changes -->
        <link rel="stylesheet" href="css/custom.css?version=<?php print time(); ?>" type="text/css">

        <!-- Link to another external CSS file (tablet.css) for devices with a max-width of 800px -->
        <link rel="stylsheet" media="(max-width:800px)" href="css/tablet.css?version=<?php print time(); ?>" type="text/css">

        <!-- Link to another external CSS file (phone.css) for devices with a max-width of 600px -->
        <link rel="stylsheet" media="(max-width: 600px)" href="css/phone.css?version=<?php print time(); ?>" type="text/css">
    </head>

    <?php
    // Opening PHP tag

    // Printing HTML body tag with a class attribute set to "display"
    print '<body class="display">';
    
    // Comment indicating the start of the body section
    print '<!-- ***** START OF BODY ***** -->';

    // Including a PHP file (constants.php) with constants
    include 'lib/constants.php';
    print PHP_EOL;

    // Requiring a PHP file (DataBase.php) and creating instances of the DataBase class
    require_once(LIB_PATH . 'DataBase.php');
    $thisDataBaseReader = new DataBase('dscheuer_reader', DATABASE_NAME);
    $thisDataBaseWriter = new DataBase('dscheuer_writer', DATABASE_NAME);
    print PHP_EOL;

    // Including a PHP file (nav.php) for navigation
    include 'nav.php';
    print PHP_EOL;

    // Including a PHP file (header.php) for the page header
    include 'header.php';
    print PHP_EOL;

    // Initializing a variable $managerAccess with a value of false
    $managerAccess = false;

    ?>
