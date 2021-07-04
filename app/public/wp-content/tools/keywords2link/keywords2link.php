<?php
echo "<html><body>\n\n";
// Open a file
$file = fopen("keywords2link.csv", "r");
  
// Fetching data from csv file row by row
while (($data = fgetcsv($file)) !== false) {
    echo $data[0].','.$data[1];
    // HTML tag for placing in row format
    // foreach ($data as $i) {
    //     echo "<td>" . htmlspecialchars($i) 
    //         . "</td>";
    // }
    echo "<br/> \n";
}

// Closing the file
fclose($file);

echo "\n</body></html>";