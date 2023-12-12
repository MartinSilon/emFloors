<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Images;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function delete(Images $image){
        $image->delete();
        session()->flash('confirmMess', "Obrázok sa úspešne vymazal.");
        return redirect('/edit-project/'.$image->project_id);
    }
}
