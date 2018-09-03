<?php
include './ParseIdcard.php';

$fd = fopen('./batch.txt', 'rb');
while ($row = fgetcsv($fd)) {
    if (!ParseIdcard::getIns($row[0])->isValidate()) {
        echo $row[0] . PHP_EOL;
    }
}