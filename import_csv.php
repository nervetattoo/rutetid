<?php
require_once("Config.php");
require_once("View.php");

/*
$db = Config::getDb();

// Flush routes
$db->routes->drop();
$db->routes->ensureIndex(array('id'=>1), array('unique'=>true));
$db->routes->ensureIndex(array('name'=>1));

$db->stops->drop();
$db->stops->ensureIndex(array('name'=>1),array('unique'=>true, 'dropDups'=>true));
*/


$dir = scandir('data/');

foreach ($dir as $file)
{
    $tmpFile = $file;
    $file = 'data/' . $file;

    if (is_file($file) && strrchr($file, ".") == '.csv')
    {
        $lines = file($file);
        array_shift($lines);

        $busses = array();
        $newLines = array();
        foreach ($lines as $line)
        {
            $data = str_getcsv($line);
            if (empty($data[0]))
            {
                $busNumbers = $data;
                array_shift($busNumbers);
                array_shift($data);
                continue;
            }

            if (preg_match('/(\X*)\.*\D+([\d]+\.[\d]+)/u', $data[0], $matches))
            {
                $data[0] = $matches[1];
                $data[1] = $matches[2];
            }

            $data[0] = trim($data[0], '. ');

            $name = array_shift($data);
            foreach ($data as $key => $value)
            {
                $busses[$busNumbers[$key]][$name][] = $value;
            }

            $allData[] = $data;
        }

        foreach ($busses as $bussNumber => $bussData)
        {
            $newLines[] = $bussNumber . ';n/a';
            foreach ($bussData as $stopName => $times)
            {
                foreach ($times as &$time)
                {
                    $time = str_replace('0', '-', $time);
                }
                $newLines[] = $stopName . ';' . implode(';', $times);
            }
            file_put_contents("data/$bussNumber.txt", implode("\n", $newLines));
        }
    }

}
echo 'done';
