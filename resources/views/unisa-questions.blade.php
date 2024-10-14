<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unisa Formatted</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .correct {
            color: green;
        }

        .incorrect {
            color: red;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body class="h-full w-full items-center justify-center flex flex-col bg-white overflow-y-scroll space-y-2">
    
    <h2 class="font-bold text-[30px]">{{$name}}</h2>

    <!-- Div that contains the questions -->
    <div class="w-3/4 h-full flex flex-col justify-start items-start">

        @foreach ($questions as $question)
            <!-- Div that will contain the question and box-->
            <div class="w-full h-full flex flex-row space-x-5">
                
                <!-- box -->
                <div class="h-full w-[7vw] flex flex-col">
                    <h3 class="text-sm font-bold">
                        Question <strong id="question_number_{{ ($questions->currentPage() - 1) * $questions->perPage() + $loop->index + 1 }}" class="text-lg">
                            {{ $question_num = ($questions->currentPage() - 1) * $questions->perPage() + $loop->index + 1 }}
                        </strong>
                    </h3>
                </div>

                <!-- Question box -->
                <div class="w-full h-full bg-blue-100 rounded-lg px-5 py-2 space-y-3 mb-10">
                    <h2>{{$question->question}}</h2>

                    <!-- inputs -->
                    <div class="flex flex-col items-start justify-start">
                        
                        @foreach ($question->shuffledOptions as $key => $option)
                            <div class="flex items-center justify-center flex-row space-x-5">
                                <input id="option_{{ $question_num }}_{{ $key }}" name="option_{{ $question_num }}" type="radio" value="{{$option}}" class="flex flex-col" onclick="checkAnswer({{ $key }}, {{$question_num}}, '{{$question->correct}}')">
                                <h4>{{ chr(65 + $key) }}.</h4> <!-- A, B, C, D -->
                                <h4 id="option_{{ $question_num }}_{{ $key }}_text">{{ $option }}</h4>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>             
        @endforeach

        <div class="flex justify-center items-center flex-col">
            {{$questions->links()}}
        </div>

        <!-- Score display after all questions are complete -->
        <div id="score-display" class="font-bold text-lg hidden">
            You scored: <span id="score"></span>/<span id="total"></span>
        </div>

        <div id="home" class="font-bold text-lg hidden my-5 border border-gray-500 rounded-lg px-5 py-1">
            <a href="/">Home</a>
        </div>

    </div>
</body>
</html>

<script>
    var questions = {!! json_encode($questions) !!};

    //Check if on last page
    if(Object.is(questions.next_page_url, null)) {
        document.getElementById('home').classList.remove('hidden');
    }

    // Function to check the answer and store the result in sessionStorage
    function checkAnswer(optionIndex, questionNum, correctOption) {
        var selectedOption = document.getElementById('option_' + questionNum + '_' + optionIndex + '_text').innerText.trim();

        // Check if selected option is correct
        var isCorrect = selectedOption === correctOption.trim();
        
        if (isCorrect) {
            document.getElementById('option_' + questionNum + '_' + optionIndex + '_text').classList.add('correct');
            updateScore(true);
        } else {
            document.getElementById('option_' + questionNum + '_' + optionIndex + '_text').classList.add('incorrect');
            updateScore(false);
        }

        // Disable all radio buttons for this question
        var optionButtons = document.querySelectorAll(`input[name="option_${questionNum}"]`);
        optionButtons.forEach(button => button.disabled = true);

        // Highlight the correct answer and mark incorrect ones
        for (var i = 0; i < optionButtons.length; i++) {
            var optionText = document.getElementById('option_' + questionNum + '_' + i + '_text').innerText.trim();
            if (optionText === correctOption.trim()) {
                document.getElementById('option_' + questionNum + '_' + i + '_text').classList.add('correct');
            } else {
                document.getElementById('option_' + questionNum + '_' + i + '_text').classList.add('incorrect');
            }
        }

        // Store the answer in sessionStorage
        storeAnswer(questionNum, optionIndex, isCorrect);
    }

    // Function to store answers in sessionStorage
    function storeAnswer(questionNum, selectedOption, isCorrect) {
        let answers = JSON.parse(sessionStorage.getItem('answers')) || {};
        answers[questionNum] = { selectedOption, isCorrect };
        sessionStorage.setItem('answers', JSON.stringify(answers));
    }

    // Function to update score in sessionStorage
    function updateScore(isCorrect) {
        let score = Number(sessionStorage.getItem('score')) || 0;
        let total = Number(sessionStorage.getItem('total')) || 0;

        total += 1;
        if (isCorrect) {
            score += 1;
        }

        sessionStorage.setItem('score', score);
        sessionStorage.setItem('total', total);

        // Check if all questions have been answered
        if (total === {{$count}}) {
            displayScore();
        }
    }

    // Function to display the final score
    function displayScore() {
        let score = sessionStorage.getItem('score');
        let total = sessionStorage.getItem('total');
        
        document.getElementById('score').innerText = score;
        document.getElementById('total').innerText = total;
        
        document.getElementById('score-display').classList.remove('hidden');
        document.getElementById('home').classList.remove('hidden');
    }

    // Function to load previously answered questions
    function loadPreviousAnswers() {
        let answers = JSON.parse(sessionStorage.getItem('answers')) || {};
        
        for (const [questionNum, answer] of Object.entries(answers)) {
            let selectedOption = answer.selectedOption;
            let isCorrect = answer.isCorrect;

            // Disable all radio buttons for this question
            var optionButtons = document.querySelectorAll(`input[name="option_${questionNum}"]`);
            optionButtons.forEach(button => button.disabled = true);

            // Restore selected option and highlight correct/incorrect answers
            for (var i = 0; i < optionButtons.length; i++) {
                var optionText = document.getElementById('option_' + questionNum + '_' + i + '_text').innerText.trim();
                if (i == selectedOption) {
                    document.getElementById('option_' + questionNum + '_' + i + '_text').classList.add(isCorrect ? 'correct' : 'incorrect');
                }
                if (optionText === document.getElementById("option_" + questionNum + "_0").getAttribute("value").trim()) {
                    document.getElementById('option_' + questionNum + '_' + i + '_text').classList.add('correct');
                }
            }
        }
    }

    // Call this function when the page loads
    loadPreviousAnswers();
</script>