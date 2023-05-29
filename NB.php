<?php

use Phpml\Classification\NaiveBayes;
use Phpml\Metric\Accuracy;
use Phpml\Dataset\CsvDataset;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Metric\ConfusionMatrix;
use Phpml\FileUploader\FileUploader;

// load csv file start

$filename = __DIR__ . '/data.csv';
$loadDataset = new CsvDataset($filename, 30, true);

// load csv file end

// preprocessing start

// train test split start

// get 70% data train 30% data test and 5 seed
$train_test_split = 0.2;
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
$classifier = new NaiveBayes();
$classifier->train($trainSamples, $trainLabels);

// loop all classified object start

$hasilPrediksi = [];
foreach ($testSamples as $value) {
    # code...
    $hasilPrediksi[] = $classifier->predict($value);
}

// loop all classified object end

// classified end

// evaluasi akurasi start

$actualLabels = $testLabels;
$predictedLabels = $hasilPrediksi;

$confusionMatrix = ConfusionMatrix::compute($actualLabels, $predictedLabels);

// Print the confusion matrix
echo "Hasil Confusion Matrix Algoritma Naive Bayes";
echo "<pre>";
$labels = ['visual', 'auditori', 'kinestetik'];

$header = array_merge([''], $labels);
$rows = [];
for ($i = 0; $i < count($labels); $i++) {
    $rows[] = array_merge([$labels[$i]], $confusionMatrix[$i]);
}
$table = array_merge([$header], $rows);
$rowLengths = array_map('max', array_map('array_map', array_fill(0, count($table[0]), 'strlen'), $table));
$formatString = implode('  ', array_map(function($len) { return "%-{$len}s"; }, $rowLengths));
foreach ($table as $row) {
    echo vsprintf($formatString, $row) . PHP_EOL;
}
echo "</pre>";

echo 'Akurasi = ' . Accuracy::score($actualLabels, $predictedLabels) * 100 . '%';

?>
<!-- html tag title -->
<!DOCTYPE html>
<html>
  <head>
      <title>Prediksi Gaya Belajar dengan Naive Bayes</title>
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
  <button onclick="toggleContent()">Show/Hide Hasil</button>

    <script>
    function toggleContent() {
    var content = document.getElementById("content");
    if (content.style.display === "none") {
        content.style.display = "block";
    } else {
        content.style.display = "none";
    }
    }
    </script>
    <div class="content" id="content" style="display: none;">
    <!-- table view -->
    <i>klasifikasi dengan <?php echo (1-$train_test_split) * 100; ?>% data training dan <?php echo $train_test_split * 100; ?>% data testing </i>
      <table>
          <thead>
              <tr>
                  <th>Nomor</th>
                  <th>Kelas Prediksi</th>
                  <th>Kelas Aktual</th>
              </tr>
          </thead>
          <tbody>
              <?php
              $counter = 0;
              foreach ($predictedLabels as $row): 
              ?>
              <tr>
                  <td><?php echo $counter + 1; ?></td>
                  <td><?php echo $row; ?></td>
                  <td><?php echo $actualLabels[$counter]; ?></td>
              </tr>
              <?php 
              $counter++; 
              endforeach; 
              ?>
          </tbody>
      </table>
      
    <!-- table view end -->
    </div>
    <hr>

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

    echo "<hr><br>Hasil Klasifikasi Algoritma Naive Bayes dari file yang telah diupload:";
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