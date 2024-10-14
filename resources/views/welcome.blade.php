<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCQ Viewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex w-full h-screen justify-center items-center flex-col">

    <div class=" flex w-auto h-auto justify-center items-center flex-row space-x-5">

        <div class="w-1/2 h-auto bg-white rounded-lg shadow-lg p-8 flex flex-col justify-center items-center ml-5">
            <form class="w-full flex flex-col items-center p-3" action="{{ route('unisa-submit') }}" method="POST">
                @csrf
                <label for="options" class="mb-2 text-lg font-medium">Choose an option:</label>
                <select id="options" name="options" class="mb-4 w-3/4 p-2 border border-gray-300 rounded-lg">
                    <!-- Create for each loop for files -->
                    @foreach ($files as $file)
                        <option name="'options" value="{{ $file->name }}">{{$file->name}}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-1/2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Get unisa web questions
                </button>
            </form>
        </div>

        <div class="w-1/2 h-auto bg-white rounded-lg shadow-lg p-8 flex flex-col justify-center items-center mr-5">
            <form class="w-full flex flex-col items-center p-3" action="{{ route('submit') }}" method="POST">
                @csrf
                <label for="options" class="mb-2 text-lg font-medium">Choose an option:</label>
                <select id="options" name="options" class="mb-4 w-3/4 p-2 border border-gray-300 rounded-lg">
                    <!-- Create for each loop for files -->
                    @foreach ($files as $file)
                        <option name="'options" value="{{ $file->name }}">{{$file->name}}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-1/2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Get web questions
                </button>
            </form>
        </div>
    </div>
    
    <div class="flex flex-col justify-center items-center h-auto w-1/2">
        <form  class="w-full flex flex-col items-center p-3" action="{{ route('all') }}" method="GEt">
            @csrf
            <button type="submit" class="w-1/2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Get All questions
            </button>
        </form>
    </div>

    @error('file_not_found')
        <h2>{{ $message }}</h2>
    @enderror

</body>
</html>

<script>
    sessionStorage.clear();
</script>