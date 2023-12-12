<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Images;
use App\Models\Projects;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{

    //      ---  VYMAZANIE PRISPEVKOV   ---
    public function delete(Projects $project, Images $images)
    {
        $user = Auth::user();
        if ($user['status'] != 'admin') {
            return redirect()->route('home');
        } else {
            $projectImages = Images::where('project_id', $project->id)->get();
            foreach ($projectImages as $image){
                $image->delete();
            }

            $project->delete();
            session()->flash('confirmMess', "Príspevok sa úspešne vymazal.");
            return redirect('/projekty');
        }
    }

    //      ---  UPDATE PRÍSPEVKU    ---
    public function update(Request $request, Projects $project){

        $projectImages = Images::where('project_id', $project->id)->get();
        $valueExists = array();
        foreach( $projectImages as $key => $image){
            $valueExists[] = $image->id;
        }

        if($valueExists){
            $incomingFields = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'images.*' => 'mimes:jpg,jpeg,png|max:2048',
            ], [
                'title.required' => 'Prosím, zadajte názov.',
                'description.required' => 'Prosím, zadajte popis.',
                'images.*.mimes' => 'Obrázok musí byť vo formáte jpg, jpeg alebo png.',
            ]);
        }else{
            $incomingFields = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'images' => 'required',
                'images.*' => 'mimes:jpg,jpeg,png|max:2048',
            ], [
                'title.required' => 'Prosím, zadajte názov.',
                'description.required' => 'Prosím, zadajte popis.',
                'images.required' => 'Prosím, pridajte minimálne jeden obrázok.',
                'images.*.mimes' => 'Obrázok musí byť vo formáte jpg, jpeg alebo png.',
            ]);
        }


        $project->update($incomingFields);


        if ($request->hasFile('images')){
            foreach ($request->file('images') as $key => $image) {
                $imageName = 'project_' . $project->id . '_image_' . rand(0, 99999) . '.' . $image->extension();
                $image->move(public_path('database_img'), $imageName);
                Images::create([
                    'project_id' => $project->id,
                    'path' => $imageName,
                ]);
            }
        }
        // Kontrola Flash správy
        session()->flash('confirmMess', 'Projekt bol úspešne upravený.'); //flash sprava na uistenie

        return redirect('/edit-project/'.$project->id);
    }

    //      ---  VYTVORENIE PRÍSPEVKU   ---
    public function create(Request $request, Projects $project)
    {
        $incomingField = $request->validate([
            'title' => 'required',
            'description'=> 'string',
            'images' => 'required',
            'images.*' => 'mimes:jpg,jpeg,png|max:2048',
        ], [
            'title.required' => "Prosím, vyplnte Titulok.",
            'description.required' => 'Prosím, pridajte popis projektu.',
            'images.required' => 'Prosím, pridajte minimálne obrázok.',
            'images.*.mimes' => 'Obrázok musí byť vo formáte jpg, jpeg alebo png.',
        ]);

        $project = Projects::create($incomingField);

        if ($request->hasFile('images')){
            foreach ($request->file('images') as $key => $image) {
                $imageName = 'project_' . $project->id . '_image_' . rand(0, 99999) . '.' . $image->extension();
                $image->move(public_path('database_img'), $imageName);
                Images::create([
                    'project_id' => $project->id,
                    'path' => $imageName,
                ]);
            }
        }
        session()->flash('confirmMess', "Projekt sa úspešne vytvoril.");
        return redirect('/edit-project/'.$project->id);
    }

    //      ---  ZOBRAZENIE EDIT SCREENU NA ÚPRAVU PROJEKTOV   ---
    public function showEditScreen(Projects $project)
    {
        $user = Auth::user();
        if ($user['status'] != 'admin') {
            return redirect()->route('home');
        } else {
            $images = Images::where('project_id', $project->id)->get();
            return view('admin/edit-project', compact('project', 'images'));
        }
    }


    //      ---  ZOBRAZENIE PROJEKTOV   ---
    public function projectPage(){
        $user = Auth::user();

        if ($user['status'] != 'admin') {
            return redirect()->route('home');
        } else {
            $projects = Projects::orderBy('updated_at', 'desc')->get();
            $images = Images::all();
            $valueExists = array();
            foreach( $projects as $key => $project){
                $valueExists[] = $project->id;
            }
            return view('admin/project', compact('projects', 'valueExists', 'images'));
        }
    }
}
