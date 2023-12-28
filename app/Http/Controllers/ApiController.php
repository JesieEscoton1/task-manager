<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Yajra\DataTables\Facades\DataTables;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Task::all();

            return DataTables::of($query)
                ->addColumn('actions', function ($query) {
                    return
                        '<button class="btn btn-sm btn-icon hide-arrow" data-bs-toggle="dropdown">' .
                        '<i class="bx bx-dots-vertical-rounded"></i>' .
                        '</button>' .
                        '<div class="dropdown-menu dropdown-menu-end">' .
                        '<a href=' . route('preview', ['id' => $query->id]) . ' class="dropdown-item edit"><i class="bx bx-show"></i> View</a>' .
                        '<a href=' . route('edit', ['id' => $query->id]) . ' class="dropdown-item"><i class="bx bx-edit"></i> Edit</a>' .
                        '<div class="dropdown-divider">' .
                        '</div>' .
                        '<a href=' . route('delete', ['id' => $query->id]) . ' class="dropdown-item delete text-danger"><i class="bx bx-trash"></i> Delete</a>' .
                        '</div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('task-manager.index');
    }

    public function add()
    {
        return view('task-manager.create');
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $data = Task::where('id', $id)->get();

        return view('task-manager.edit', compact('data'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'added successfully']);
    }

    public function preview(Request $request)
    {
        $id = $request->id;
        $data = Task::where('id', $id)->get();

        return view('task-manager.preview', compact('data'));
    }

    public function update(Request $request)
    {
        $id = $request->taskId;
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Task::where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'updated successfully']);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        Task::find($id)->delete($id);

        return redirect()->back()->with('success', 'deleted successfully');
    }

    public function weather(Request $request)
    {
        return view('weather.index');
    }
}

