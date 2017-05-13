<?php

// Just for development purpose
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Try to connect to mysql-server with pdo instated of using mysqli
    $dbCon = new PDO('mysql:dbname=db655937740;host=db655937740.db.1and1.com', 'dbo655937740', 'Massadas');
	
} catch (PDOException $e) {
    // Catching error && stoping script
    die('<strong>Error:</strong> Connection failed with error "' . $e->getMessage() . '"');
}

if(isset($_POST['upload'])) {
    $filename = $_FILES['sel_file']['name'];

    // Show filename of uploaded file
    echo 'Uploaded file: ' . $filename . '<br>';

    // Check file-extension for csv
    // @ToDo: Determinate which mime-type your csv-files generally have and check for mime-type additionally or instated
    if (pathinfo($filename, PATHINFO_EXTENSION) == 'csv') {
        // Get file-content
        $fileContent = file_get_contents($_FILES['sel_file']['tmp_name']);

        // Split file into lines
        $rows = explode("\n", $fileContent);

        // Check for data
        if (empty($rows) || count($rows) < 2) {
            // Shwo error-messages && stopping script
            die('<strong>Error:</strong> CSV-File does not contain data!');
        }

        // Get columns of header-row
        $header = explode(',', $rows[0]);

        // Set query variables
        $query = 'INSERT INTO test_prods (';
        $values = '(';

        // Build query based on header-columns
        for($a = 0; $a < count($header); ++$a) {
            // Add column
            $query .= $header[$a] . ', ';
            $values .= ':column' . $a . ', ';
        }

        // Finish query
        $query = substr($query, 0, -2) . ') VALUES ' . substr($values, 0, -2) . ')';

        // Loop through rows, starting after header-row
        for ($b = 1; $b < count($rows); ++$b) {
            // Split row
            $row = explode(',', $rows[$b]);

            // Check that row has the same amount of columns like header
            if (count($header) != count($row)) {
                // Show message
                echo '<strong>Warning:</strong> Skipped line #' . $b . ' because of missing columns!';

                // Skip line
                continue;
            }

            // Create statement
            $statement = $dbCon->prepare($query);

            // Loop through columns
            for ($c = 0; $c < count($row); ++$c) {
                // Bind values from column to statement
                $statement->bindValue(':column' . $c, $row[$c]);
            }

            // Execute statement
            $result = $statement->execute();

            // Check for error
            if ($result === false) {
                // Show info
                echo '<strong>Warning:</strong>: DB-Driver returned error on inserting line #' . $b;
            }
        }

        // @ToDo: Get numbers of row before import, check number of rows after import against number of rows in file
        // Show success message
        echo '<span style="color: green; font-weight: bold;">Import successfully!</span>';
    } else {
        die('<strong>Error:</strong> The file-extension of uploaded file is wrong! Needs to be ".csv".');
    }
}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
    Import File: <input class="btn btn-primary" type="file" name='sel_file' size='20'> <br>
    <input class="btn btn-primary" type="submit" name="upload" value='upload'>
</form>