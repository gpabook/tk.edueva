<?php

namespace App\Http\Controllers;

use App\Models\ClassLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassLevelController extends Controller
{
    public function index()
    {
        // load pagination + rooms count
        $classLevels = ClassLevel::withCount('rooms')
            ->orderBy('id')
            ->paginate(20);

        return Inertia::render('ClassLevels/Index', [
            'classLevels' => $classLevels,
        ]);
    }

    public function create()
    {
        return Inertia::render('ClassLevels/Form', [
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'     => 'required|string|max:5|unique:class_levels,code',
            'name_en'  => 'required|string|max:50',
            'name_th'  => 'required|string|max:50',
        ]);

        ClassLevel::create($data);

        return redirect()
            ->route('class-levels.index')
            ->with('success', 'Class level created.');
    }

    public function edit(ClassLevel $class)
    {
        return Inertia::render('ClassLevels/Form', [
            'mode'       => 'edit',
            'classLevel' => $class,
        ]);
    }

    public function update(Request $request, ClassLevel $class)
    {
        $data = $request->validate([
            'code'     => "required|string|max:5|unique:class_levels,code,{$class->id}",
            'name_en'  => 'required|string|max:50',
            'name_th'  => 'required|string|max:50',
        ]);

        $class->update($data);

        return redirect()
            ->route('class-levels.index')
            ->with('success', 'Class level updated.');
    }

    public function destroy(ClassLevel $class)
    {
        $class->delete();

        return redirect()
            ->route('class-levels.index')
            ->with('success', 'Class level deleted.');
    }
}
