<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Audio') }}
            
        </h2>
        <div class ="py-8 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <button class = "font-semibold float-right rounded-md border-solid border-2 border-emerald-300 h-8 w-32 text-slate-50" type = "button" value = ""><p class= "text-gray-800 dark:text-gray-200">Import</p></button>
                <button class = "mx-8 font-semibold float-right rounded-md border-solid border-2 border-indigo-600 h-8 w-32 text-slate-50" type = "button" value = ""><p class = "text-gray-800 dark:text-gray-200">Auto Split</p></button>
            </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="border-collapse border border-slate-400 w-full">
                        <thead>
                            <tr>
                                <th class="border border-slate-300">FileName</th>
                                <th class="border border-slate-300">Duration</th>
                                <th class="border border-slate-300">Size</th>
                                <th class="border border-slate-300">action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                            <tr class = "h-12">
                                <td class="border border-slate-300 "><p class = "text-center">{{$file}}</p></td>
                                <td class="border border-slate-300"><p class = "text-center">20240201PM0240</p></td>
                                <td class="border border-slate-300"><p class = "text-center">32.MB</p></td>
                                <td class="border border-slate-300 w-44">
                                    <button class = "font-semibold float-left text-sm rounded-md border-solid border-2 border-indigo-600 h-8 w-14 mx-4" type = "button" value = "split">Split</button>
                                    <button class = "font-semibold float-right text-sm rounded-md border-solid border-2 border-amber-400 h-8 w-14 me-4" type = "button" value = "split">Export</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
