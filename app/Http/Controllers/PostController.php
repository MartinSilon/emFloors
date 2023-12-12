<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integration\Database\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Posts;

class PostController extends Controller
{

    //      ---  VYMAZANIE PRISPEVKOV   ---
    public function delete(Posts $post)
    {
        $user = Auth::user();
        if ($user['status'] != 'admin') {
            return redirect()->route('home');
        } else {
            $post->delete();
            session()->flash('confirmMess', "Príspevok sa úspešne vymazal.");
            return redirect('/prispevky');
        }
    }

    //      ---  UPDATE PRÍSPEVKU    ---
    public function update(Request $request, Posts $post){
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ], [
            'title.required' => 'Prosím, zadajte názov.',
            'body.required' => 'Prosím, zadajte popis.',
        ]);

        $post->update($incomingFields);

        // Kontrola Flash správy
        session()->flash('confirmMess', 'Príspevok bol úspešne upravený.'); //flash sprava na uistenie

        return redirect('/prispevky');
    }


    //      ---  VYTVORENIE PRÍSPEVKU   ---
    public function create(Request $request)
    {
        $incomingField = $request->validate([
            'title' => 'required',
            'body'=>'required',
            'image' => 'required|mimes:jpg,jpeg,png',
        ], [
            'title.required' => "Prosím, vyplnte Titulok.",
            'body.required' => "Prosím, vyplnte Obsah príspevku.",
            'image.required' => 'Pridajte nahľadový obrázok príspevku.',
            'image.mimes' => 'Obrázok musí byť vo formáte jpg, jpeg alebo png.',
        ]);


        $newPost = Posts::create($incomingField);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'post_' . $newPost->id . '_image_' . '.' . $image->extension();
            $image->move(public_path('database_img'), $imageName);
            $newPost->update(['image_path' => $imageName]);
        }

        session()->flash('confirmMess', "Príspevok sa úspešne vytvoril.");
        return redirect('/prispevky');
    }


    //      ---  ZOBRAZENIE EDIT SCREENU NA ÚPRAVU PRÍSPEVKOV   ---
    public function showEditScreen(Posts $post)
    {
        $user = Auth::user();

        if ($user['status'] != 'admin') {
            return redirect()->route('home');
        } else {
            return view('admin/edit-post', compact('post',));
        }
    }


    //      ---  ZOBRAZENIE PRÍSPEVKOV   ---
    public function postPage()
    {
        $user = Auth::user();
        if ($user['status'] != 'admin') {
            return redirect()->route('home');
        } else {
            $posts = Posts::orderBy('updated_at', 'desc')->get();
            $valueExists = array();
            foreach( $posts as $key => $post){
                $valueExists[] = $post->id;
            }
            return view('admin/posts', compact('posts', 'valueExists'));
        }
    }


}
