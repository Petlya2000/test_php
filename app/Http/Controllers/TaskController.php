<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     *Poluchenie vseh zadach.
     */
    public function index()
    {
	    $tasks = Task::all();
	    return response()->json($tasks);
    }

    /**
     *Cozdanie zadachi.
     */	
    public function store(Request $request)
    {
      	    $validated = $request->validate([
		    'title' => 'required|string|max:255',
		    'description' => 'nullable|string',
		    'status' => 'nullable|in:pending,completed',
	    ]);

	    $task = Task::create($validated);

	    return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
	    $task = Task::find($id);

	    if(!$task) {
		    return response()->json(['error' => 'Task not found'], 404);
	    }

	    return response()->json($task);
	    
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
	    $task = Task::find($id);

	    if(!$task){
		    return response()->json(['error' => 'Task not found'],404);
	    }

	    $validated = $request->validate([
		    'title'=>'required|string|max:255',
		    'descripton' => 'nullable|string',
		    'status' => 'nullable|in:pending,completed',
	    ]);

	    $task->update($validated);

	    return response()->json(['message' => 'Task updated', 'task' => $task]);
    }

    /**
     * Udalenie zadachi
     */
    public function destroy($id)
    {
	    $task = Task::find($id);

	    if(!$task){
		    return response()->json(['error' => 'Task not found'],404);
	    }

	    $task->delete();

	    return response()->json(['message' =>'Task deleted']);

    }
}
