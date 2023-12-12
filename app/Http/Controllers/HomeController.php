<?php

namespace App\Http\Controllers;

use App\Models\Images;
use App\Models\Posts;
use App\Models\Projects;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\Utils\Image;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $posts = Posts::orderBy('updated_at', 'desc')->take(3)->get();
        $projects = Projects::orderBy('updated_at', 'desc')->take(4)->get();
        $images = Images::all();
        $postExists = array();
        foreach( $posts as $key => $post){
            $postExists[] = $post->id;
        }

        $projectExists = array();
        foreach( $projects as $key => $project){
            $projectExists[] = $project->id;
        }
        $user = Auth::user();


//        $tourImages = Images::pluck('path','tour_id')->toArray();
        return view('home', compact('posts', 'postExists', 'user', 'projects', 'projectExists', 'images'));
    }

    public function allProjects()
    {
        $user = Auth::user();
//        $projects = Projects::orderBy('updated_at', 'desc')->get();
        $projects = Projects::all();
        $projectExists = array();
        return view('allProjects', compact('projects', 'projectExists'));
    }
}
