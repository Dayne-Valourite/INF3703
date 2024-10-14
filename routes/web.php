<?php

use App\Http\Controllers\QuestionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QuestionsController::class, 'index'])->name('welcome');

//get the unisa form data and process it
Route::post('/unisa-submit', [QuestionsController::class, 'unisa_submit'])->name('unisa-submit');

Route::get('/unisa-questions', [QuestionsController::class,'unisa_questions'])->name('unisa-questions');

//get the form data and process it
Route::post('/submit', [QuestionsController::class, 'submit'])->name('submit');

Route::get('/questions', [QuestionsController::class,'questions'])->name('questions');

//Route::view('/questions', 'questions');

//Get all the questions and display them
Route::get('/all', [QuestionsController::class, 'all'])->name('all');
