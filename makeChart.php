<?php

// Iterate through each question
foreach ($questions as $question) {

    // Initialize variables to store values and labels for the pie chart
    $values = "";
    $labels = "";

    // Check if the question type is 'radio'
    if ($question['fldQuestionType'] == 'radio') {

        // SQL query to retrieve responses for the current question
        $sql = 'SELECT * FROM tblResponses ';
        $sql .= 'WHERE fpkQuestionID = ' . $question['pmkQuestionID'];
        $responses = $thisDataBaseReader->select($sql);

        // SQL query to retrieve options for the current question
        $sql = 'SELECT * FROM tblOptions ';
        $sql .= 'WHERE fpkQuestionID = ' . $question['pmkQuestionID'];
        $options = $thisDataBaseReader->select($sql);

        // Loop through options to build labels and values for the pie chart
        foreach ($options as $option) {
            $labels .= $option['fldOptionText'] . ",";
            $count = 0;
            foreach ($responses as $response) {
                if ($option['fldOptionText'] == $response['fldResponse']) {
                    $count++;
                }
            }
            $values .= $count . ",";
        }
    }

    // Execute R script to generate pie chart
    exec("Rscript rScripts/piChart.R $values $labels");

    // Generate image tag with a random number to prevent caching
    $nocache = rand();
    print("<img src='charts/temp.png?$nocache' />");

}

?>
