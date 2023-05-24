<?php

require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Classification\NaiveBayes;
use Phpml\Metric\Accuracy;
use Phpml\Dataset\CsvDataset;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Metric\ClassificationReport;

// load csv file start

$filename = __DIR__ . '/data.csv';
$loadDataset = new CsvDataset($filename, 30, true);

// load csv file end

// preprocessing start

// train test split start

// get 70% data train 30% data test and 5 seed
$train_test_split = 0.3;
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

echo 'Akurasi = ' . Accuracy::score($actualLabels, $predictedLabels) * 100 . '%';

// $report = new ClassificationReport($actualLabels, $predictedLabels);

// $hasilEvaluasi = $report->getAverage();
// foreach ($hasilEvaluasi as $key => $value) {
//     # code...
//     echo "<br><hr>" . $key . " = " . $value * 100 . "%";
// }
// echo "<br><hr>";

// evaluasi akurasi end

?>
<!-- html tag title -->
<!DOCTYPE html>
<html>
  <head>
      <title>Prediksi Gaya Belajar dengan Algoritma C4.5</title>
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
      <hr>

    <!-- table view end -->

    </body>
</html>