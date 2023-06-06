<?php

use Phpml\Classification\DecisionTree;
use Phpml\Dataset\CsvDataset;
use Phpml\CrossValidation\RandomSplit;
use Phpml\FileUploader\FileUploader;

// load csv file start

$filename = __DIR__ . '/data.csv';

// load csv file end

// preprocessing start

// load and delete the row that has unknown gaya belajar
$loadDataset = new CsvDataset($filename, 30, true);

// train test split start

// get 70% data train 30% data test and 5 seed
$train_test_split = 0.1;
$dataset = new RandomSplit($loadDataset, $train_test_split, 5);

// train group
$trainSamples = $dataset->getTrainSamples();
$trainLabels =  $dataset->getTrainLabels();

// test group
$testSamples = $dataset->getTestSamples();
$testLabels = $dataset->getTestLabels();
// train test split end

// preprocessing end

// classified start

// assign class
$classifier = new DecisionTree();
$classifier->train($trainSamples, $trainLabels);

// loop all classified object start

$hasilPrediksi = [];
foreach ($testSamples as $value) {
    # code...
    $hasilPrediksi[] = $classifier->predict($value);
}

// loop all classified object end

// classified end

$actualLabels = $testLabels;
$predictedLabels = $hasilPrediksi;

// Visualize the results
// Export the predicted labels and the original labels to a CSV file
$exportData = array_map(null, $predictedLabels, $actualLabels);
$fp = fopen(__DIR__ . '/results.csv', 'w');
fputcsv($fp, ['predicted', 'actual']);
foreach ($exportData as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);

?>
<!-- html tag title -->
<!DOCTYPE html>
<html>
  <head>
      <title>Prediksi Gaya Belajar dengan Decision Tree</title>
      <style>
          table {
              border-collapse: collapse;
              width: 100%;
          }

          th, td {
              border: 1px solid black;
              padding: 8px;
              text-align: left;
          }
      </style>
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

    // Loop through the csv file and add a random label to each row
    $header = array(' ');
    
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
    
    // loop all classified object start

    echo "<hr><br>Hasil Klasifikasi Algoritma Decision Tree dari file yang telah diupload:";
    echo "<hr>";
    foreach ($uploadedSamples as $value) {
        # code...
        echo '<b>' . $classifier->predict($value) . '</b><hr>';
    }
    
    // delete uploaded file

    $upload->deleteFile($uploadedFileName);
    echo '<hr>';

    // loop all classified object end
    
}

?>
<!-- klasifikasi upload data end -->