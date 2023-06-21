<?php

require_once __DIR__ . '/vendor/autoload.php';
use Phpml\CrossValidation\RandomSplit;
use Phpml\Dataset\CsvDataset;
use Phpml\Dataset\Demo\IrisDataset;

// load csv file start
$filename = __DIR__ . '/data.csv';
$loadDataset = new CsvDataset($filename, 30, true);
// load csv file end

// preprocessing start

// train test split start

// get 90% data train 10% data test and 5 seed
$train_test_split = 0.1;
$dataset = new RandomSplit($loadDataset, $train_test_split, 5);

// train test split end

// get train data start

$trainSamples = $dataset->getTrainSamples();
$trainLabels =  $dataset->getTrainLabels();

// get train data end

// get test data start

$testSamples = $dataset->getTestSamples();
$testLabels = $dataset->getTestLabels();

// get test data end

// preprocessing end

// Decision Tree
include('./DC.php');

// Naive Bayes
// include('./NB.php');

?>

<!-- html tag title -->
<!DOCTYPE html>
<html>
  <head>
      <title>Prediksi Gaya Belajar</title>
  </head>
  <body>

    <!-- upload form start -->
    
    <form method="POST" enctype="multipart/form-data">
      <label for="inputData">upload csv file input data: </label> 
      <input type="file" name="inputData" accept=".csv">
      <hr>
      <input type="submit" value="Submit">
    </form>
    <hr>
    
    <!-- upload form end -->

    </body>
</html>

<!-- klasifikasi upload data start -->
<?php

use Phpml\FileUploader\FileUploader;

if (!empty($_FILES['inputData'])) {
    # code...

    $uploadedFile = $_FILES['inputData'];
    // upload file
    $upload = new FileUploader(__DIR__ . '/');
    $upload->uploadFile($uploadedFile);
    $uploadedFileName = $uploadedFile['name'];

    // load csv uploaded file
    $loadUploadFile = array_map('str_getcsv', file($uploadedFileName));

    // Create an array of labels
    $labels = ['visual', 'auditori', 'kinestetik'];

    // get header label
    $file = fopen('data.csv', 'r');
    $header = fgetcsv($file);
    fclose($file);
    
    $samples = '';
    $samples .= implode(',', $header) . "\n";
    foreach ($loadUploadFile as $row) {
        $row[] = $labels[array_rand($labels)];
        $samples .= implode(',', $row) . "\n";
    }

    $samples = mb_convert_encoding($samples, 'ISO-8859-1', 'UTF-8');

    $samples = explode('?', $samples);
  
    // Write the csv file
    file_put_contents($uploadedFileName, $samples);
    
    $loadUploadedDataset = new CsvDataset($uploadedFileName, 30, true);
    $uploadedSamples = $loadUploadedDataset->getSamples();
    
    // delete uploaded file

    $upload->deleteFile($uploadedFileName);
    echo '<hr>';
?>

<!-- view table hasil klasifikasi start -->
<html>
<head>
    <style>
        table 
        {
            border-collapse: collapse;
            overflow-x: auto; /* Menerapkan horizontal scrollbar jika melebihi lebar tabel */
        }
        
        th, td 
        {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        
        .scrollable-table 
        {
            height: 400px;
            overflow-y: scroll; /* Menerapkan vertical scrollbar jika melebihi tinggi tabel */
        }
        .scrollable-table td:last-child,
        .scrollable-table th:last-child
        {
            position: sticky;
            right: 0;
            background-color: #f9f9f9;
            z-index: 1;
        }
        .scrollable-table td:first-child, 
        .scrollable-table th:first-child 
        {
            position: sticky;
            left: 0;
            background-color: #f9f9f9;
            z-index: 1;
        }
    </style>
</head>
<body>
    <?php echo $labelAlgoritme; ?>
    <div class="scrollable-table">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <?php
                    // header
                    foreach ($header as $value) {
                        # code...
                        echo "<th>$value</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Menampilkan data dalam tabel
                foreach ($uploadedSamples as $key => $row) {
                    echo "<tr>";
                    echo "<td>". $key + 1 ."</tf>";
                    foreach ($row as $key) {
                        # code...
                        echo "<td>$key</td>";   
                    }
                    $hasilPrediksi = $classifier->predict($row);
                    echo "<td>" . $hasilPrediksi . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<!-- view table hasil klasifikasi end -->

<!-- klasifikasi upload data end -->

<?php
}
?>