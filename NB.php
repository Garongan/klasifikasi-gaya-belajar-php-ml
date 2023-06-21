<?php

// import class
use Phpml\Classification\NaiveBayes;

// set label untuk hasil klasifiaksi algortime 
$labelAlgoritme = "Hasil Klasifikasi Algoritme Naïve Bayes dari file yang telah diupload:";

// train start

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

// train end

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