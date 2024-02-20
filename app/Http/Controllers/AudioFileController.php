<?php


namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AudioFile;
use App\Models\ExcelAudioLog;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use FFMpeg;
use Carbon\Carbon;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Coordinate\TimeCode;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;
use Exception;
use DateTime;
set_time_limit(300);
class AudioFileController extends Controller
{
    
    //Import the aduio files info from the local db
    public function index () {
        $allFiles = AudioFile::all();
        return view('audioManagement.index', ['files' => $allFiles]);
    }

    // when ciick the radio button, indexs change
    public function displayAudio($filetype){
        if($filetype == "original")
        {
            $allFiles = AudioFile::select('*')->where('format', 'wav')->get();
            return view('audioManagement.index', ['files' => $allFiles]);
        }
        else if($filetype == "convert") 
        {
            $allFiles = AudioFile::select('*')->where('format', 'mp3')->get();
            return view('audioManagement.index', ['files' => $allFiles]);
        }
    }
    //analyze the aduio files and read all files per day, split the file and merge the file per client by voice recognization
    public function audiospliter() {
        

        $audioFiles= AudioFile::select("*")->where('format', 'wav')->get();
        foreach($audioFiles as $audioFile)
        {
            $res = $this->getFileInfo($audioFile['file_name'], $audioFile['file_path'].DIRECTORY_SEPARATOR.$audioFile['file_name']);
            // print_r($res);
            if($res != "failed")
            {
                
                $this->spiltOneFile($res['percek'], $res['filepath']);
            }
        }

    }
    public function spiltOneFile($precekArray, $filepath)
    {
        $tempinterval = 0;

        $inputFilePath = $filepath;
        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => 'E:/FFmpeg/bin/ffmpeg.exe',
            'ffprobe.binaries' => 'E:/FFmpeg/bin/ffprobe.exe' 
        ]);
        $audio = $ffmpeg->open($inputFilePath);
        $format = new FFMpeg\Format\Audio\Wav();

        
        $ffprobe = FFMpeg\FFProbe::create();
        // $duration = $ffprobe
        //     ->format($inputFilePath) 
        //     ->get('duration');        
        $filePathInfo = pathinfo($filepath);
        $converted_path = $filePathInfo['dirname'] . DIRECTORY_SEPARATOR .$filePathInfo['filename'];
        foreach($precekArray as $precek)
        {
            $tempTime = Carbon::createFromFormat('H:i:s', $precek);
            $audio->addFilter(new \FFMpeg\Filters\Audio\SimpleFilter(['-af', 'anlmdn']));
            $precek_duration = $tempTime->minute * 60 + $tempTime->second - $tempinterval;
            $audio->filters()->clip(TimeCode::fromSeconds($tempinterval), TimeCode::fromSeconds($precek_duration));
            $outputFilePath = $converted_path.'to'.$tempTime->format('H-i-s').'.wav';
            $audio->save($format, $outputFilePath);
            $this->zipToMp3file($outputFilePath);
            unlink($outputFilePath);
            $tempinterval = $tempTime->minute * 60 + $tempTime->second;
        }
        unlink($filepath);
        AudioFile::where('file_path', $filePathInfo['dirname'])->where('file_name', basename($filepath))->delete();
    }

    // from local, get the audio fils and their info
    public function getAudioFromLocal()
    {
        set_time_limit(300);
        $localConvertPath = env('LOCAL_CONVERT_PATH');
        $wavFiles = $this->getNewWavFiles($localConvertPath);
        \DB::transaction(function() use ($wavFiles, $localConvertPath) {
            
            foreach($wavFiles as $file)
            {
                if(strtolower(pathinfo($file, PATHINFO_EXTENSION)) != 'wav')
                {
                    continue;
                }
                $localFileDirPath = dirname($file);
                $localFilePath = $file;
                
                $ffmpeg = FFMpeg\FFMpeg::create([
                    'ffmpeg.binaries'  => env('FFMPEG_BINARIES'),
                    'ffprobe.binaries' => env('FFPROBE_BINARIES') 
                ]);
                $duration = $this->getAudioDuration($localFilePath);
                if ($duration === "00:00:00") {
                    continue;
                }
        
                $fileSize = $this->getAudioSize($localFilePath);
                AudioFile::updateOrCreate([
                    'file_path' => $localFileDirPath,
                    'file_name' => basename($file),
                    'duration' => $duration,
                    'file_size' => $fileSize,
                    'format' => 'wav'
                ],);
            }
        });
        $allFiles = AudioFile::all();
        return view('audioManagement.index', ['files' => $allFiles]);
        
    }
    public function getNewWavFiles($dir)
    {
        $wavFiles = [];

        // Check if directory exists and is a directory
        if (!file_exists($dir) || !is_dir($dir)) {
            return $wavFiles;
        }

        // Create a Finder instance to look for .wav files recursively
        $finder = new Finder();
        $finder->files()->in($dir)->name('*.wav');

        foreach ($finder as $file) {
            // SplFileInfo object contains full path to file
            $file_path = dirname($file);
            $file_name = basename($file);

            $existFlag =  AudioFile::select('*')->where('file_path', $file_path)->where('file_name', $file_name)->get();
            if($existFlag->count() == 0)
            {
                $wavFiles[] = $file->getRealPath();
            }
        }

        return $wavFiles;
    }
    public function getAudioDuration($filePath)
    {
        
        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => 'E:/FFmpeg/bin/ffmpeg.exe',
            'ffprobe.binaries' => 'E:/FFmpeg/bin/ffprobe.exe',
            'timeout'          => 3600, 
            'ffmpeg.threads'   => 12,
        ]);

        
        $audio = $ffmpeg->open($filePath);

        
        $format = $audio->getFormat();

        $durationSeconds = $format->get('duration');

        $formattedDuration = gmdate('H:i:s', (int) $durationSeconds);

        return $formattedDuration;
    }
    public function getAudioSize($filePath)
    {
        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => 'E:/FFmpeg/bin/ffmpeg.exe',
            'ffprobe.binaries' => 'E:/FFmpeg/bin/ffprobe.exe' 
        ]);
        $audio = $ffmpeg->open($filePath);
        $format = $audio->getFormat();
        $fileSize = $format->get('size');
        $fileSizeInMB = $fileSize / 1024 / 1024;
        return $fileSizeInMB;
    }
    
    public function zipToMp3file($filePath)
    {
        $lowerBitrate = 64000;
        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => 'E:/FFmpeg/bin/ffmpeg.exe',
            'ffprobe.binaries' => 'E:/FFmpeg/bin/ffprobe.exe' 
        ]);
        $audio = $ffmpeg->open($filePath);
        $audio->filters()->custom("aecho=0.8:0.9:1000:0.3");
        $format = new Mp3();
        $format->setAudioKiloBitrate((int)($lowerBitrate / 1000));
        try {
            $filePath = str_replace(".wav", ".mp3", $filePath);
            $audio->save($format, $filePath);

            $filePathInfo = pathinfo($filePath);
            $file_path = $filePathInfo['dirname'];
            $file_name = basename($filePath);
            $duration = $this->getAudioDuration($filePath);
            $file_size = $this->getAudioSize($filePath);
            $existFlag =  AudioFile::select('*')->where('file_path', $file_path)->where('file_name', $file_name)->get();

            if($existFlag->count() == 0)
            {
                AudioFile::updateOrCreate([
                    'file_path' => $file_path,
                    'file_name' => $file_name,
                    'duration' => $duration,
                    'file_size' => $file_size,
                    'format' => 'mp3'
                ]);
            }
            
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage() . "\n";
        }
        
    }
    public function getFileInfo($filename, $filepath)
    {
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
            return "failed";
        }
        else{
            $precek = array();
            foreach ($matchedAudio as $audio)
            {
                // echo "Order_no: {$audio->order_no}, Precek: {$audio->precek}, Name:{$audio->waiter}".PHP_EOL;
                array_push($precek, $audio->precek);
            }
            return $oneFileInfo = [
                'percek' => $precek,
                'filepath' => $filepath
            ];
        }
        
        
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
    //Import the audio files info from the FTP server and save the data(audio files)
    
    // public function getFromFTP()
    // {
    //     set_time_limit(300);
    //     $disk = Storage::disk('ftp');

    //     $files = $disk->allFiles();

    //     $localConvertPath = base_path(). '\resources\assets\audio';

    //     if(!file_exists($localConvertPath))
    //     {
    //         mkdir($localConvertPath, 0755, true);
    //     }
    //     \DB::transaction(function() use ($files, $disk, $localConvertPath) {
    //         foreach ($files as $file) {

    //             if(strtolower(pathinfo($file, PATHINFO_EXTENSION)) != 'wav')
    //             {
    //                 continue;
    //             }
    //             $localFilePath = $localConvertPath . '/' . basename($file);
    //             $stream = $disk->readStream($file);
    //             file_put_contents($localFilePath, stream_get_contents($stream));
    //             fclose($stream);

    //             $ffmpeg = FFMpeg\FFMpeg::create([
    //                 'ffmpeg.binaries'  => 'E:/FFmpeg/bin/ffmpeg.exe',
    //                 'ffprobe.binaries' => 'E:/FFmpeg/bin/ffprobe.exe' 
    //             ]);

    //              // Open the copy of the file from local storage
    //             $audio = $ffmpeg->open($localFilePath);
    //              // Define the output MP3 format
    //             $mp3Format = new Mp3();
    //             $mp3Format->setAudioKiloBitrate(192);

    //             // Convert the file name to .mp3
    //             $convertedName = preg_replace('/\.wav$/i', '.mp3', basename($file));
    //             $mp3FilePath = $localConvertPath . '/' . $convertedName;
    //              // Save the file in MP3 format
    //             $audio->save($mp3Format, $mp3FilePath);

    //              // If needed, upload $mp3FilePath back to FTP or another storage
    //             // For example:
    //             // $disk->put('converted_audio/'.$convertedName, fopen($mp3FilePath, 'r+'));

    //             // Add/update database record for the audio file
    //             AudioFile::updateOrCreate([
    //                 'file_path' => $file,
    //                 'file_name' => $convertedName,
    //             ],);
    //             unlink($localFilePath);
    //             unlink($mp3FilePath);
    //         };
            
    //     });
    //     unset($disk);

    //     $allFiles = AudioFile::all();
    //     return view('audioManagement.index', ['files' => $allFiles]);
    // }
}
