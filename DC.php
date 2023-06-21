<?php

// import class
use Phpml\Classification\DecisionTree;
use Phpml\Metric\Accuracy;

// set label untuk hasil klasifiaksi algortime 
$labelAlgoritme = "Hasil Klasifikasi Algoritme Decision Tree dari file yang telah diupload:";

// train start

// assign class
$classifier = new DecisionTree();
// $columnNames = ['VIS1','VIS2','VIS3','VIS4','VIS5','VIS6','VIS7','VIS8','VIS9','VIS10','AUD1','AUD2','AUD3','AUD4','AUD5','AUD6','AUD7','AUD8','AUD9','AUD10','KIN1','KIN2','KIN3','KIN4','KIN5','KIN6','KIN7','KIN8','KIN9','KIN10'];
// $classifier->setColumnNames($columnNames);
$classifier->setNumFeatures(2);
$classifier->train($trainSamples, $trainLabels);
// print_r($classifier->getHtml());
// $classifier->saveDOTToFile("decision_tree.dot", $columnNames);

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
echo Accuracy::score($actualLabels, $predictedLabels);

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