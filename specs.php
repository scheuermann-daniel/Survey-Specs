<?php
include 'top.php';

// Get the 'survey' parameter from the URL, sanitize it, and set a default value of 0 if not present
$survey = isset($_GET['survey']) ? (int)htmlspecialchars($_GET['survey']) : 0;

// SQL query to select survey details based on the given survey ID
$sql = 'SELECT * FROM tblSurveyList WHERE pmkSurveyID = ' . $survey;
$surveys = $thisDataBaseReader->select($sql);

// SQL query to select questions based on the given survey ID
$sql = 'SELECT * FROM tblQuestions WHERE fpkSurveyID = ' . $survey;
$questions = $thisDataBaseReader->select($sql);

$count = 0;

// Loop through each question to get the count of responses
foreach ($questions as $question) {
    $sql = 'SELECT * FROM tblResponses ';
    $sql .= 'WHERE fpkQuestionID = ' . $question['pmkQuestionID'];

    $responses = $thisDataBaseReader->select($sql);
    $count = count($responses);
}

?>

<main class="grid-container">

    <section class="specsinfo">
        <div class="fillBox">
            <!-- Display survey name and description -->
            <h2><?php print $surveys[0]['fldSurveyName']; ?></h2>
            <p><?php print $surveys[0]['fldSurveyDescription']; ?></p>
        </div>
    </section>

    <section class="charts fillBox">
        <div>
            <?php
            // Loop through each question to generate pie charts for radio-type questions
            foreach ($questions as $question) {
                $values = "";
                $labels = "";

                if ($question['fldQuestionType'] == 'radio') {
                    $labels = "";
                    $count = "";
                    $values = "";

                    // SQL query to get responses for the current question
                    $sql = 'SELECT * FROM tblResponses ';
                    $sql .= 'WHERE fpkQuestionID = ' . $question['pmkQuestionID'];
                    $responses = $thisDataBaseReader->select($sql);

                    // SQL query to get options for the current question
                    $sql = 'SELECT * FROM tblQuestionOptions ';
                    $sql .= 'WHERE fpkQuestionID = ' . $question['pmkQuestionID'];
                    $options = $thisDataBaseReader->select($sql);

                    // Loop through options to build labels and values for the chart
                    foreach ($options as $option) {
                        $labels .= $option['fldOptionText'] . ",";
                        $count = 0;
                        foreach ($responses as $response) {
                            if ($option['fldOptionText'] == $response['fldResponse']) {
                                $count++;
                            }
                        }
                        $values .= $count . ",";
                        $count = 0;
                    }

                    // Execute R script to generate pie chart image
                    exec("Rscript my_rscript.R $values $labels");

                    // Add a random number to the image URL to prevent caching
                    $nocache = rand();
                    print '<h2>Pie Chart for Q' . $question['pmkQuestionID'] . '</h2>';
                    echo("<img class='chart' src='charts/temp.png?$nocache' />");
                }
            }
            ?>
        </div>
    </section>

    <section class="report">
        <div class="fillBox">
            <?php
            // Display a link to the survey report if it exists
            if (file_exists("reports/" . $surveys[0]['fldReportName'] . ".pdf")) {
                print '<h2><a href="reports/' . $surveys[0]['fldReportName'] . '.pdf">Report</a></h2>';
            } else {
                print '<h2>Report Not Uploaded</h2>';
            }
            ?>
        </div>
    </section>

    <section class="recordsTable fillBox">
        <?php
        // Display a table of responses for each question
        print '<div class="records">';
        print '<table id="entries">';
        print '<tr>';
        print '<th><a class="head" href="specs.php?survey=' . $survey . '">Question ID</a></th>';
        print '<th>Question</th>';
        print '<th>Question Type</th>';
        print '<th><a class="head" href="specs.php?survey=' . $survey . '">Response</a></th>';
        print '</tr>';

        foreach ($questions as $question) {
            $sql = 'SELECT * FROM tblResponses ';
            $sql .= 'WHERE fpkQuestionID = ' . $question['pmkQuestionID'];
            $responses = $thisDataBaseReader->select($sql);

            foreach ($responses as $response) {
                print '<tr>' . PHP_EOL;
                print '<td>' . $response['fpkQuestionID'] . '</td>' . PHP_EOL;

                $sql = 'SELECT * FROM tblQuestions WHERE pmkQuestionID = ' . $response['fpkQuestionID'];
                $questionTexts = $thisDataBaseReader->select($sql);
                print '<td>' . $questionTexts[0]['fldQuestionText'] . '</td>' . PHP_EOL;
                print '<td>' . $questionTexts[0]['fldQuestionType'] . '</td>' . PHP_EOL;

                print '<td>' . $response['fldResponse'] . '</td>' . PHP_EOL;
                print '</tr>' . PHP_EOL;
            }
        }

        print '<tr>';
        print '</tr>';
        print '</table>';
        print '</div>';
        ?>
    </section>

</main>

<?php
include 'footer.php';
?>
