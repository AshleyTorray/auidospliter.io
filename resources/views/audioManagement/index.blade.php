<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Audio Management') }}
            <a href = "{{route('audio.display', ['filetype' => 'convert'])}}"><input type = "radio" id = "convert" class= "font-semibold float-right h-4 w-4 text-slate-50 mx-8 my-1" value="convert" checked/><label class= "font-semibold float-right h-4 w-4 text-slate-50 mx-8">Convert</label></a>
            <a href = "{{route('audio.display', ['filetype' => 'original'])}}"><input type = "radio" id = "original" class= "font-semibold float-right h-4 w-4 text-slate-50 mx-8 my-1" value="original" checked><label class= "font-semibold float-right h-4 w-4 text-slate-50 mx-8">Original</label></a>
            
        </h2>
        <div class ="py-8 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <button class = "font-semibold float-left h-8 w-32 text-slate-50" type = "button" title = "Find new added audio files & Convert to mp3"><p class= "text-gray-800 dark:text-gray-200"><a href = "{{ route('import.audio')}}"><image src = "/images/recycle.png" width = "40%"/>
                </p></a></button>
                <button class = "mx-8 font-semibold float-right rounded-md border-solid border-2 border-indigo-600 h-8 w-32 text-slate-50" type = "button" value = ""><p class = "text-gray-800 dark:text-gray-200"><a href = "{{ route('audiospliter')}}">Auto Split</a></p></button>
            </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="border-collapse border border-slate-400 w-full">
                        <thead>
                            <tr>
                                <th class="border border-slate-300">No</th>
                                <th class="border border-slate-300">FileName</th>
                                <th class="border border-slate-300">Duration</th>
                                <th class="border border-slate-300">Size</th>
                                <th class="border border-slate-300">action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                            <tr class = "h-12">
                                <td class="border border-slate-300 "><p class = "text-center">{{$file->id}}</p></td>
                                <td class="border border-slate-300 "><a href="{{ route('download.file', ['filePath' => $file->file_path, 'fileName' => $file->file_name]) }}"><p class = "text-center">{{$file->file_name}}</p></a></td>
                                <td class="border border-slate-300"><p class = "text-center">{{$file->duration}}</p></td>
                                <td class="border border-slate-300"><p class = "text-center">{{$file->file_size}}</p></td>
                                <td class="border border-slate-300 w-44">
                                    <button class = "font-semibold float-left text-sm rounded-md border-solid border-2 border-indigo-600 h-8 w-14 mx-4" type = "button" value = "split" id="playBtn{{$file->id}}"  data-audio-src="{{asset($file->file_path.'\\'.$file->file_name)}}">Play</button>
                                    <button class = "font-semibold float-right text-sm rounded-md border-solid border-2 border-amber-400 h-8 w-14 me-4" type = "button" value = "split"><a href = "{{ route('excel.import', ['filename' => $file->file_name, 'filepath' => $file->file_path."\\".$file->file_name])}}">Split</a></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="audioPlayerModal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h5>Audio Player</h5>
                        <!-- Your Audio Player Here -->
                        <audio id="audioPlayer" controls>
                            <source id="audioSource" src="" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                </div>
                <script>
                    var modal = document.getElementById("myModal");
                    var audioSource = document.getElementById("audioSource");
                    var audioPlayer = document.getElementById("modalAudio");
                    var span = document.getElementsByClassName("close")[0];

                    // Function to play audio based on the button clicked
                    function playAudio(audioFile) {
                        // Set the audio file as the source
                        audioSource.src = audioFile;
                        // Load and play the audio
                        audioPlayer.load();
                        audioPlayer.play();
                        // Show the modal
                        modal.style.display = "block";
                    }

                    // Event listener for all buttons with the class 'audio-button'
                    document.addEventListener('click', function(event) {
                        // Check if the clicked element is an audio play button
                        if (event.target && event.target.tagName === 'BUTTON' && event.target.textContent === 'Play') {
                            var audioFile = event.target.getAttribute('data-audio-src');
                            playAudio(audioFile);
                        }
                    });

                    // When the user clicks on <span> (x), close the modal and pause the audio
                    span.onclick = function() {
                        modal.style.display = "none";
                        audioPlayer.pause();
                    };

                    // Click outside the modal to close it and pause audio
                    window.onclick = function(event) {
                        if (event.target === modal) {
                            modal.style.display = "none";
                            audioPlayer.pause();
                        }
                    };
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
