<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCQ Questions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .correct {
            border-color: green;
            background-color: green;
        }

        .incorrect {
            border-color: red;
            background-color: red;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-200 flex w-full h-screen justify-center items-center flex-col">

    <div class="w-3/4 h-auto bg-gray-400 rounded-lg shadow-lg p-8 flex flex-col justify-center items-center">

        <h2 class="w-full text-center font-bold text-blue-600">{{ $question[0]->name }}</h2>

        <h2 class="w-full text-center font-bold text-[30px]">{{ $question[0]->question }}</h2>

        <h3 id="count" class="font-bold text-lg">Question Count</h3>

        <!-- Div that contains options -->
        <div class="flex flex-col w-full h-full items-center justify-center space-y-0">
            <!-- options 1 and 2 -->
            <div id="option-1-2" class="flex flex-col w-full items-center">
                <!-- Option 1 -->
                <button id="1"
                    class=" w-3/4 h-auto rounded-lg justify-center items-center p-4 m-3 border-[2px] border-black"
                    onclick="checkAnswer(1)">
                    <h2 id="1-text" class="font-bold text-center"></h2>
                </button>

                <!-- Option 2 -->
                <button id="2"
                    class=" w-3/4  h-auto rounded-lg justify-center items-center p-4 m-3 border-[2px] border-black"
                    onclick="checkAnswer(2)">
                    <h2 id="2-text" class="font-bold text-center"></h2>
                </button>
            </div>

            <!-- options 3 and 4 -->
            <div id="option-3-4" class="flex flex-col w-full items-center">
                <!-- Option 3 -->
                <button id="3"
                    class=" w-3/4  h-auto rounded-lg justify-center items-center p-4 m-3 border-[2px] border-black"
                    onclick="checkAnswer(3)">
                    <h2 id="3-text" class="font-bold text-center"></h2>
                </button>

                <!-- Option 4 -->
                <button id="4"
                    class=" w-3/4  h-auto rounded-lg justify-center items-center p-4 m-3 border-[2px] border-black"
                    onclick="checkAnswer(4)">
                    <h2 id="4-text" class="font-bold text-center"></h2>
                </button>
            </div>
        </div>

        <div id="next" class="flex justify-around w-full">
            <h1 class="text-justify font-bold">{{$question->links()}}</h1>
            <a href="/" id="end" class="rounded px-5 py-2 font-bold bg-white text-center items-center">End Quiz</a>
        </div>

    </div>

    <script>
        var question = {!! json_encode($question) !!}; // Pass the questions array from Laravel
        var correct_answer = question['data'][0].correct.trim();
        var end_button = document.getElementById('end');

        end_button.addEventListener('click', function() {
           //get the current score and Count
           var score = Number(sessionStorage.getItem('score'));
           var count = question.current_page;

           alert('Your score is: ' + score + ' out of ' + count);

        });

        // Set the question count
        document.getElementById('count').innerText = 'Question: ' + question.current_page + '/' + {{$count}};

        // Function to shuffle options
        function shuffle(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        // Assign options to buttons
        function loadOptions() {
            var options = [
                { text: question['data'][0].option_1.trim(), id: 1 },
                { text: (question['data'][0].option_2 ? question['data'][0].option_2.trim() : 'null'), id: 2 },
                { text: (question['data'][0].option_3 ? question['data'][0].option_3.trim() : 'null'), id: 3},
                { text: (question['data'][0].option_4 ? question['data'][0].option_4.trim() : 'null'), id: 4 }
            ];

            // Shuffle options
            //var shuffledOptions = shuffle(options);

            // Assign shuffled options to buttons
            for (let i = 0; i < options.length; i++) {
                document.getElementById((i + 1) + '-text').innerText = options[i].text;
                document.getElementById(i + 1).setAttribute('data-original-id', options[i].id);
            }
        }

        // Hide options if they are empty
        function hideEmptyOptions() {
            for (let i = 1; i <= 4; i++) {

                let optionText = document.getElementById(i + '-text').innerText.trim();
                if (optionText == 'null') {
                    document.getElementById(i).style.display = 'none';
                }
            }
        }

        // Call the loadOptions function on page load
        loadOptions();
        hideEmptyOptions();

        // Function to check the answer
        function checkAnswer(buttonIndex) {
            var originalIndex = document.getElementById(buttonIndex).getAttribute('data-original-id');
            var selected_answer = document.getElementById(buttonIndex + '-text').innerText.trim();
            var correct = selected_answer === correct_answer;

            if (correct) {
                document.getElementById(buttonIndex).classList.add('correct');
                storeAnswer(question['data'][0].id, true);
            } else {
                document.getElementById(buttonIndex).classList.add('incorrect');
                storeAnswer(question['data'][0].id, false);
            }

            // Mark the correct answer
            for (let i = 1; i <= 4; i++) {
                var option_text = document.getElementById(i + '-text').innerText.trim();
                if (option_text === correct_answer) {
                    document.getElementById(i).classList.add('correct');
                } else {
                    document.getElementById(i).classList.add('incorrect');
                }
            }

            var timer = setInterval(() => {
                //test if the current page is the last page
                if(question.current_page == {{$count}}) {
                    alert("Quiz Completed, you scored: " + sessionStorage.getItem('score') + '/' + {{$count}});
                    clearInterval(timer);
                }
            }, 2000);
            

            // Disable all buttons after selection
            $('button').attr('disabled', true);
        }

        // Store answer correctness in sessionStorage
        function storeAnswer(questionId, isCorrect) {
            let results = JSON.parse(sessionStorage.getItem('results')) || {};
            results[questionId] = isCorrect;
            sessionStorage.setItem('results', JSON.stringify(results));

            var score = Number(sessionStorage.getItem('score'));

            if(isCorrect) {
                if(score !== undefined)
                    sessionStorage.setItem('score', score + 1);
                else {
                    sessionStorage.setItem('score', 1);
                }
            }

        }

        // Load previously answered questions and highlight
        function loadPreviousAnswer() {
            let results = JSON.parse(sessionStorage.getItem('results')) || {};
            if (results[question['data'][0].id] !== undefined) {
                $('button').attr('disabled', true);
                let correct_answer = question['data'][0].correct.trim();

                for (let i = 1; i <= 4; i++) {
                    var option_text = document.getElementById(i + '-text').innerText.trim();
                    if (option_text === correct_answer) {
                        document.getElementById(i).classList.add('correct');
                    }
                }
            }
        }

        // Call this function when the page loads
        loadPreviousAnswer();
    </script>

</body>

</html>
