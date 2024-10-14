<?php

namespace Database\Seeders;

use App\Models\questions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Seeder;

class QuestionsSeeder extends Seeder
{
    public function run()
    {
        // Get all .txt files from the 'storage/app/public/Questions' directory
        $files = Storage::disk('public')->allFiles('Questions');

        foreach ($files as $file) {
            // Loop through the content of the file
            $name = pathinfo($file, PATHINFO_FILENAME);
            $content = Storage::disk('public')->get($file);

            // Split the file content by double line breaks to separate questions
            $rawQuestions = preg_split('/\n\s*\n/', $content);

            // Loop through each raw question and process it
            foreach ($rawQuestions as $rawQuestion) {
                // Split each question by lines (first line is the question, next lines are the options)
                $lines = explode("\n", trim($rawQuestion));

                if (count($lines) > 0) {
                    // First line is the question text
                    $questionText = trim($lines[0]);

                    // Remaining lines are the options
                    $options = array_slice($lines, 1);

                    // Parse and clean the options, identify the correct option (marked with an asterisk)
                    $cleanOptions = [];
                    $correctOption = null;
                    foreach ($options as $option) {
                        // Remove the letter/number (A., 1., [A], etc.) and check for the correct answer (*)
                        $isCorrect = strpos($option, '*') !== false;
                        $optionText = preg_replace('/^\[?[a-dA-D0-9]+\]?\s*\.?\s*/', '', str_replace('*', '', trim($option)));

                        // Add the option to the cleanOptions array
                        $cleanOptions[] = $optionText;

                        if ($isCorrect) {
                            $correctOption = $optionText;
                        }
                    }

                    // Create the record in the database
                    questions::create([
                        'name' => $name,
                        'question' => $questionText,
                        'option_1' => $cleanOptions[0] ?? 'Corrupt option',
                        'option_2' => $cleanOptions[1] ?? 'Corrupt option or only 1 option',
                        'option_3' => $cleanOptions[2] ?? null,
                        'option_4' => $cleanOptions[3] ?? null,
                        'correct' => $correctOption ?? 'Corrupt option'
                    ]);

                    logger('A question has been added');
                }
            }
        }
    }

}
