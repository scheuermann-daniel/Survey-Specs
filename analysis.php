<?php
include 'top.php';
?>

<main>

<?php
// SQL query to retrieve main surveys
$sql = 'SELECT * FROM tblSurveyList WHERE fldMain = 1';
$data = array();  
$mainSurvey = $thisDataBaseReader->select($sql, $data);

// Display header for current survey section
print '<h1>Current Survey</h2>';

// Loop through main surveys and display details
foreach ($mainSurvey as $survey) {
    print '<div class="longbox black">';
    print '<h2><a href="specs.php?survey=' . $survey['pmkSurveyID'] . '">' . $survey['fldSurveyName'] . '</a></h2>';
    print '<p>' . $survey['fldSurveyDescription'] . '</p>';
    print '</div>';
}

// SQL query to retrieve other surveys
$sql = 'SELECT * FROM tblSurveyList WHERE fldMain = 0';
$data = array();  
$surveys = $thisDataBaseReader->select($sql, $data);

// Display header for other surveys section
print '<h1 class="otherSurveys">Other Surveys</h2>';

// Loop through other surveys and display details
foreach ($surveys as $survey) {
    print '<div class="longbox black">';
    print '<h2><a href="specs.php?survey=' . $survey['pmkSurveyID'] . '">' . $survey['fldSurveyName'] . '</a></h2>';
    print '<p>' . $survey['fldSurveyDescription'] . '</p>';
    print '</div>';
}

?>

</main>

<?php
// Include a file named 'footer.php' for footer content
include 'footer.php';
?>
