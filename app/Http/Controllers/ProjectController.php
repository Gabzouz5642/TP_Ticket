<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function create(Request $request)
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'collaborators' => 'required|array|min:1',
            'collaborators.*' => 'string|max:255',
            'hours_included' => 'required|numeric|min:0',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        Project::create([
            'name' => $data['project_name'],
            'client' => $data['client_name'],
            'collaborators' => implode(', ', $data['collaborators']),
            'hours_included' => $data['hours_included'],
            'hourly_rate' => $data['hourly_rate'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Projet cree !');
    }
}
