<?php

shell_exec('rm data/*.txt');

$buses = array();

$dir = scandir('data/');
foreach ($dir as $file)
{
    $file = 'data/' . $file;

    if (!is_file($file) || strrchr($file, ".") != '.csv')
    {
        continue;
    }

    $lines = file($file);
    array_shift($lines);

    $fileNumber = 0;
    foreach ($lines as $line)
    {
        $fileNumber++;
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
            $buses[$busNumbers[$key]][$name][] = $value;
        }

        //$allData[] = $data;
    }
}

foreach ($buses as $busNumber => $busData)
{
    $newLines = array();
    $filename = "data/$busNumber.txt";
    $lastStopName = end(array_keys($busData));
    $busName = $busNumber . ';' . $lastStopName;

    $newLines[] = $busName;

    foreach ($busData as $stopName => $times)
    {
        foreach ($times as &$time)
        {
            $time = (float) $time;
            if ($time == 0.0)
            {
                $time = '-';
                continue;
            }

            $time = str_replace('.', ':', sprintf('%01.2f', $time));
        }
        $newLines[] = $stopName . ';' . implode(';', $times);
    }

    file_put_contents($filename, implode("\n", $newLines), FILE_APPEND);
}

echo 'done';
