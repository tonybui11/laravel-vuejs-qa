<?php

namespace App\Http\Controllers;

use App\Question;
use App\Http\Requests\AskQuestionRequest;
use Illuminate\Http\Request;

class QuestionsController extends Controller {

  public function __construct() {
    $this->middleware('auth', ['except' => ['index', 'show']]);
  }

  /**
   * Display a list of questions.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {
    $questions = Question::with('user')->latest()->paginate(5);

    return view('questions.index', compact('questions'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create() {
    $question = new Question();

    return view('questions.create', compact('question'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(AskQuestionRequest $request) {
    $request->user()->questions()->create($request->only('title', 'body'));

    // TODO: Need to define locale here
    return redirect()->route('questions.index')->with('success', "Your question has been submitted.");
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Question  $question
   * @return \Illuminate\Http\Response
   */
  public function show(Question $question) {

    // TODO: Need to fix only others member can increase views counter
    $question->increment('views');

    return view('questions.show', compact('question'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Question  $question
   * @return \Illuminate\Http\Response
   */
  // Laravel automatically gets the $question instance for us by id passed in URI,
  // if no question found, then it returns 404
  public function edit(Question $question) {
    $this->authorize('update', $question);

    return view('questions.edit', compact('question'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Question  $question
   * @return \Illuminate\Http\Response
   */
  public function update(AskQuestionRequest $request, Question $question) {
    $this->authorize('update', $question);

    $question->update($request->only('title', 'body'));
    
    // TODO: Need to define locale here
    return redirect()->route('questions.index')->with('success', "Your question has been updated.");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Question  $question
   * @return \Illuminate\Http\Response
   */
  public function destroy(Question $question) {
    $this->authorize('delete', $question);

    $question->delete();

    // TODO: Need to define locale here
    return redirect()->route('questions.index')->with('success', "Your question has been deleted.");
  }
}
