<?php

namespace App\Http\Controllers;

use App\Models\questions;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class QuestionsController extends Controller
{
    /**
     * Return the questions to the home view
     */
    public function index()
    {

        $names = questions::select('name')->distinct()->get();

        return view('welcome', [
            'files' => $names
        ]);
    }

    /**
     * Get the form data and validate it
     */
    public function unisa_submit(Request $request)
    {
        // Validate that an option is selected
        $request->validate([
            'options' => 'required'
        ]);

        //set the name in the session
        Session::put('name', $request->options);

        return redirect()->route('unisa-questions');
    }

    /**
     * REturn the questions view with the data
     */
    public function unisa_questions(Request $request) {

        // Fetch the selected question record from the database
        $questions = questions::where('name', Session::get('name'))->simplePaginate(4);

        foreach ($questions as $question) {
            $options = [
                'a' => $question->option_1,
                'b' => $question->option_2,
                'c' => $question->option_3,
                'd' => $question->option_4,
            ];

            //test if option b-d are not null, if null remove them
            foreach ($options as $key => $value) {
                if ($value == null) {
                    unset($options[$key]);
                }
            }

    
            // Shuffle the options
            if (trim($question->correct) == 'True' || trim($question->correct) == 'False') {
                // Do not shuffle, but reset keys to numeric
                $shuffledOptions = array_values($options);
            } else {
                // Shuffle and reset keys to numeric
                $shuffledOptions = collect($options)->shuffle()->values()->toArray();
            }
    
            // Store shuffled options back into the question object
            $question->shuffledOptions = $shuffledOptions;
        }

        $count = questions::where('name', Session::get('name'))->count();

        return view('unisa-questions', [
            'questions' => $questions,
            'count' => $count,
            'name' => Session::get('name')
        ]);
    }

    /**
     * Get the form data and validate it
     */
    public function submit(Request $request)
    {
        // Validate that an option is selected
        $request->validate([
            'options' => 'required'
        ]);

        //set the name in the session
        Session::put('name', $request->options);

        return redirect()->route('questions');
    }

    /**
     * REturn the questions view with the data
     */
    public function questions(Request $request) {

        // Fetch the selected question record from the database
        $questions = questions::where('name', Session::get('name'))->simplePaginate(1);    //randomizes the questions

        foreach ($questions as $question) {
            $options = [
                'a' => $question->option_1,
                'b' => $question->option_2,
                'c' => $question->option_3,
                'd' => $question->option_4,
            ];

            //test if option b-d are not null, if null remove them
            foreach ($options as $key => $value) {
                if ($value == null) {
                    unset($options[$key]);
                }
            }
    
            //test if the options are just true and false - else shuffle
            if(trim($question->correct) ==  'True' || trim($question->correct) ==  'False') {
                //do not shuffle
                $shuffledOptions = $options;
            } else {
                //shuffle
                $shuffledOptions = collect($options)->shuffle()->toArray();
            }
    
            // Store shuffled options back into the question object
            $question->shuffledOptions = $shuffledOptions;
        }

        $count = questions::where('name', Session::get('name'))->count();

        return view('questions', [
            'question' => $questions,
            'count' => $count,
            'name' => Session::get('name')
        ]);
    }

    /**
     * Returns all the questions inside each txt file
     */
    public function all(Request $request)
    { 
        // Fetch 50 random questions from the database
        $questions = questions::whereIn('name', ['01 Past Exam Questions', '2024 Ass 03 MCQ', '2024 Ass 01 MCQ'])->inRandomOrder()->take(50)->get();

        // Get the current page from the request, defaulting to 1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Define how many items we want to show per page (1 question per page)
        $perPage = 1;

        // Slice the questions collection to get the items for the current page
        $currentPageItems = $questions->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Create a new LengthAwarePaginator instance for the sliced items
        $paginatedQuestions = new LengthAwarePaginator(
            $currentPageItems, // Items for the current page
            $questions->count(), // Total items (50)
            $perPage, // Items per page (1)
            $currentPage, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Path and query parameters for pagination links
        );

        // Process each question (in this case, only 1 per page)
        foreach ($paginatedQuestions as $question) {
            $options = [
                'a' => $question->option_1,
                'b' => $question->option_2,
                'c' => $question->option_3,
                'd' => $question->option_4,
            ];

            // Remove null options
            foreach ($options as $key => $value) {
                if ($value === null) {
                    unset($options[$key]);
                }
            }

            // Test if the options are just "True" and "False" - else shuffle
            if (trim(strtolower($question->correct)) == 'true' || trim(strtolower($question->correct)) == 'false') {
                // Do not shuffle
                $shuffledOptions = $options;
                logger("Not Shuffled: The correct answer: " . $question->correct . ' matched the if condition.');
            } else {
                // Shuffle
                $shuffledOptions = collect($options)->shuffle()->toArray();
                logger("Shuffled: The correct answer: " . $question->correct . ' did not match the if condition.');
            }

            // Store shuffled options back into the question object
            $question->shuffledOptions = $shuffledOptions;
        }

        // Get the total count of questions
        $count = 50;

        return view('questions', [
            'question' => $paginatedQuestions, // Pass the paginated questions
            'count' => $count,
            'name' => '50 random questions'
        ]);
    }
}
