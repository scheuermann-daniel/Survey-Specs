<?php
// Include a file named 'top.php'
include 'top.php';

// Function to get and sanitize data from POST request
function getData($field) {
    if (!isset($_POST[$field])) {
        $data = "";
    } else {
        $data = trim($_POST[$field]);
        $data = htmlspecialchars($data);
    }
    return $data;
}

// Function to verify alphanumeric characters and specific symbols
function verifyAlphaNum($testString) {
    // Checks for letters, numbers, dash, period, space, single quote, ampersand, semicolon, and hash only.
    // Single quote sanitized with html entities will have this in it: bob's will become bob&#039;s
    return (preg_match("/^([[:alnum:]]|-|\.| |\'|&|;|#)+$/", $testString));
}

// SQL query to select main surveys
$sql = 'SELECT pmkSurveyID, fldSurveyName, fldSurveyDescription ';
$sql .= 'FROM tblSurveyList ';
$sql .= 'WHERE fldMain = "1"';

$dataIsGood = false;
$data = array();  
$surveys = $thisDataBaseReader->select($sql, $data);

// SQL query to select questions based on the first main survey
$sql = 'SELECT pmkQuestionID, fldQuestionText, fldQuestionType ';
$sql .= 'FROM tblQuestions ';
$sql .= 'WHERE fpkSurveyID = ' . $surveys[0]['pmkSurveyID'];

$data = array();  
$questions = $thisDataBaseReader->select($sql, $data);

$responses = array();
$questionIDs = array();

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dataIsGood = true;

    // Loop through each question and collect responses
    foreach ($questions as $question) {
        $sql = 'SELECT pmkOptionID, fldOptionText, fpkQuestionID ';
        $sql .= 'FROM tblQuestionOptions ';
        $sql .= 'WHERE fpkQuestionID = ' . $question['pmkQuestionID'];

        $data = array();
        $options = $thisDataBaseReader->select($sql, $data);

        // Based on the question type, collect responses and question IDs
        if ($question['fldQuestionType'] == 'text') {
            array_push($responses, getData('txt' . $question['pmkQuestionID']));
            array_push($questionIDs, $question['pmkQuestionID']);
        }

        if ($question['fldQuestionType'] == 'select') {
            array_push($responses, getData('sel' . $question['pmkQuestionID']));
            array_push($questionIDs, $question['pmkQuestionID']);
        }

        if ($question['fldQuestionType'] == 'radio') {
            array_push($responses, getData('rad' . $question['pmkQuestionID']));
            array_push($questionIDs, $question['pmkQuestionID']);
        }

        if ($question['fldQuestionType'] == 'checkbox') {
            foreach ($options as $option) {
                if (getData('chk' . $option['pmkOptionID']) == 1) {
                    array_push($responses, $option['fldOptionText']);
                    array_push($questionIDs, $question['pmkQuestionID']);
                }  
            }
        }
    }

    // Output a section tag
    print '<section>';

    if ($dataIsGood) {
        try {
            // Loop through responses and insert/update into the database
            for ($i = 0; $i < count($responses); $i++) {
                $sql = 'INSERT INTO tblResponses SET pmkResponseID = ?,
                                                     fldResponse = ?,
                                                     fpkQuestionID = ? 
                        ON DUPLICATE KEY UPDATE fldResponse = ?,
                                                fpkQuestionID = ?';
            
                $params = array(null, $responses[$i], $questionIDs[$i], $responses[$i], $questionIDs[$i]);

                // Check if the database update is successful
                if ($thisDataBaseWriter->update($sql, $params)) {
                    print '<p>added</p>';
                } else {
                    print "Response didn't go through :(";
                }
            }
        } catch (PDOExecutionException $e) {
            print '<p>Couldn\'t insert the question, please contact someone</p>';
        }
    }
}

// Output a message if the form is successfully submitted
if ($dataIsGood) {
    print '<h1>Form Submitted.</h1>';
}
?>

<main>
    <!-- HTML form for survey responses -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

        <fieldset>

            <div class="box black">
                <?php
                // Output survey name and description
                foreach ($surveys as $survey) {
                    print '<h2>' . $survey['fldSurveyName'] . '</h2>';
                    print '<p>' . $survey['fldSurveyDescription'] . '</p>';
                }
                ?>
            </div>

            <?php
            // Output questions and corresponding input fields
            foreach ($questions as $question) {

                $sql = 'SELECT pmkOptionID, fldOptionText ';
                $sql .= 'FROM tblQuestionOptions ';
                $sql .= 'WHERE fpkQuestionID = ' . $question['pmkQuestionID'];
            
                $data = array();
                $options = $thisDataBaseReader->select($sql, $data);

                print '<div class="box black">';

                if ($question['fldQuestionType'] == 'text') {
                    // Output text input field
                    print '<fieldset>';
                    print '<h3>' . $question['fldQuestionText'] . '</h3>';
                    print '<p>';
                    print '<input type="text" name="txt' . $question['pmkQuestionID'] .'" id="txt' . $question['pmkQuestionID'] . '" required>';
                    print '</p>';
                    print '</fieldset>';
                }

                if ($question['fldQuestionType'] == 'select') {
                    // Output select dropdown
                    print '<fieldset>';
                    print '<h3>' . $question['fldQuestionText'] . '</h3>';
                    print '<p>';
                    print '<select name="sel' .  $question['pmkQuestionID'] . '" id="sel' .  $question['pmkQuestionID'] . '" >';
                    foreach ($options as $option) {
                        print '<option value="' . $option['fldOptionText'] . '">' . $option['fldOptionText'] . '</option>';
                    }
                    print '</select>';
                    print '</p>';
                    print '</fieldset>';
                }

                if ($question['fldQuestionType'] == 'radio') {
                    // Output radio buttons
                    print '<fieldset>';
                    print '<h3>' . $question['fldQuestionText'] . '</h3>';
                    foreach ($options as $option) {
                        print '<p>';
                        print '<label for="rad' . $option['pmkOptionID'] . '">' . $option['fldOptionText'] . '</label>';
                        print '<input type="radio" name="rad' . $question['pmkQuestionID'] . '" id="' . $option['pmkOptionID'] . '" value="' . $option['fldOptionText'] . '"required />';                        
                        print '</p>';
                    }
                    print '</fieldset>';
                }

                if ($question['fldQuestionType'] == 'checkbox') {
                    // Output checkboxes
                    print '<fieldset>';
                    print '<h3>' . $question['fldQuestionText'] . '</h3>';
                    foreach ($options as $option) {
                        print '<p>';
                        print '<label for="chk' . $option['pmkOptionID'] . '">' . $option['fldOptionText'] . '</label>';
                        print '<input type="checkbox" name="chk' . $option['pmkOptionID'] . '" id="chk' . $option['pmkOptionID'] . '" value="1"/>';
                        print '</p>';
                    }
                    print '</fieldset>';
                }

                print '</div>';
            }
            ?>
            
        </fieldset>
        

        <fieldset>
            <!-- Submit button -->
            <input class="box orange" type="submit" name="btnSubmit" value="Submit">
        </fieldset>

    </form>

</main>

<?php
// Include a file named 'footer.php'
include 'footer.php';
?>
