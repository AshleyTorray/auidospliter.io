<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AudioFileController;
use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelAudioLogImport;
use App\Models\ExcelAudioLog;
use App\Models\AudioFile;
class ExcelImportController extends Controller
{


    public function index()
    {
        $directory ="E:\\task\\requirement\\excellog";
        $files = scandir($directory);

        foreach ($files as $file) {
            // Check if the file is an Excel file based on its extension
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['xlsx', 'xls', 'csv'])) {
                $excelfilepath = $directory . '/' . $file;

                if (file_exists($excelfilepath)) {
                    echo "Importing file: " . $file . PHP_EOL;
                    Excel::import(new ExcelAudioLogImport, $excelfilepath);
                } else {
                    echo "File does not exist: " . $file . PHP_EOL;
                }
            }
        }
    }
    public function import($filename, $filepath)
    {
        // $this->index();
        $audioFileInfo = $this->getAudioFileInfo($filename);
        // $audioFileInfo['date'];
        $time = $audioFileInfo['time'];
        $tillTime = new DateTime($time);
        $toTime = $tillTime->modify('+1 hour');
        // $fromTime = $tillTime->modify('-1 hour');
        $toTime = $toTime->format('H:i');
        // $fromTime = $fromTime->format('H:i');
        $matchedAudio = ExcelAudioLog::select('order_no', 'precek', 'waiter')->where('accounting_day', '=', $audioFileInfo['date'])->where('precek', '>', $audioFileInfo['time'])->where('precek', '<', $toTime)->orderBy('precek', 'asc')->get();
        if($matchedAudio->isEmpty()){
            echo "failed";
        }
        $precek = array();
        foreach ($matchedAudio as $audio)
        {
            // echo "Order_no: {$audio->order_no}, Precek: {$audio->precek}, Name:{$audio->waiter}".PHP_EOL;
            
            if($audio->precek != 0)
            {
                array_push($precek, $audio->precek);
            }
        }
        
        $audioSplit = new AudioFileController();
        print_r($precek);
        // $audioSplit->spiltOneFile($precek, $filepath);
       
        // $allFiles = AudioFile::all();
        // return view('audioManagement.index', ['files' => $allFiles]);
        
        
       
        // elsexit

        // {

        // }
    }

    public function getAudioFileInfo($filename)
    {
        // Regular expression to match the expected format
        $pattern = '/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})\d*_.+\.wav$/';

        if (preg_match($pattern, $filename, $matches)) {
            // Construct the date string
            $date = "{$matches[1]}-{$matches[2]}-{$matches[3]}";
            
            // Construct the time string
            $time = "{$matches[4]}:{$matches[5]}";

            return [
                'date' => $date,
                'time' => $time,
            ];
        }

        // Return false or any other indicator if the pattern does not match
        return false;
    }
}
