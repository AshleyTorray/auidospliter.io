<?php


namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AudioFile;
// use Illuminate\Support\Facades\File;
use FFMpeg;
use FFMpeg\Coordinate\TimeCode;
class AudioFileController extends Controller
{

    //Import the aduio files info from the local db
    public function index () {
        $allFiles = AudioFile::all();
        return view('audioManagement.index', ['files' => $allFiles]);
    }

    //analyze the aduio files and read all files per day, split the file and merge the file per client by voice recognization

    public function audiospliter() {
        // $inputFilePath = storage_path('audioflutter\resources\assets\audio\20231011165202_000652_INP1_TT.wav');
        $inputFilePath = 'E:/task/project/laravel/audioflutter\resources\assets\audio\20231011165202_000652_INP1_TT.wav';
        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => 'E:/FFmpeg/bin/ffmpeg.exe',
            'ffprobe.binaries' => 'E:/FFmpeg/bin/ffprobe.exe' 
        ]);
        $audio = $ffmpeg->open($inputFilePath);
        // Log::info('Current input file path: ' . $audio);
        
        // Define the duration of each split/chunk in seconds
        $splitDuration = 30; // Duration of each chunk in seconds
        $format = new FFMpeg\Format\Audio\Wav();

        // Get the duration of the audio and calculate the number of chunks
        $ffprobe = FFMpeg\FFProbe::create();
        $duration = $ffprobe
            ->format($inputFilePath) 
            ->get('duration');

        $numberOfChunks = ceil($duration / $splitDuration);

        // Split the audio into multiple chunks
        for ($i = 0; $i < $numberOfChunks; ++$i) {
            
            $start = $i * $splitDuration;

            $chunkDuration = min($splitDuration, $duration - $start);
            

            $audio->addFilter(new \FFMpeg\Filters\Audio\SimpleFilter(['-af', 'anlmdn']));
            
            $audio->filters()->clip(TimeCode::fromSeconds($start), TimeCode::fromSeconds($chunkDuration));

            
            $outputFilePath = "E:/task/project/laravel/audioflutter/resources/assets/audio/" .'chunk_' . $i . '.wav';
            
            
            $audio->save($format, $outputFilePath);
        }

        // return response()->json(['message' => 'Audio file split successfully']);

        // return view('audio.index', ['files' => $allFiles]);
        $allFiles = AudioFile::all();
        return view('audioManagement.index', ['files' => $allFiles]);
    }


    //Import the audio files info from the FTP server and save the data(audio files)
    public function getFromFTP()
    {
        $disk = Storage::disk('ftp');

        $files = $disk->allFiles();

        \DB::transaction(function() use ($files) {
            foreach ($files as $file) {
                AudioFile::updateOrCreate([
                    'file_path' => $file,
                    'file_name' => basename($file),
                ],
            );
            }
        });
        unset($disk);

        $allFiles = AudioFile::all();
        return view('audioManagement.index', ['files' => $allFiles]);
    }
  
}
