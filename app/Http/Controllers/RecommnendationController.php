<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Posts;
use App\Models\Recommendations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RecommnendationController extends Controller
{
    //      ---  UPDATE PRÍSPEVKU    ---
    public function update(Request $request, Recommendations $recommendations) {
        $incomingFields = $request->validate([
            'pinned',
            'id' => 'required',
        ]);

        $id = $incomingFields['id'];


        if($request->has('pinned')) {
            $incomingFields['pinned'] = 1;
            session()->flash('confirmMess', 'Recenzia je PRIPNUTÁ.'); // Flash zpráva pro potvrzení
        } else {
            $incomingFields['pinned'] = 0;
            session()->flash('confirmMess', 'Recenzia je ODOPNUTÁ.'); // Flash zpráva pro potvrzení
        }

        $recommendation = Recommendations::find($id);
        $recommendation->update($incomingFields);


        return redirect('/recenzie');
    }




    //      ---  ZOBRAZENIE HODNOTENÍ   ---
    public function recommendationPage()
    {
        $user = Auth::user();
        $recommendations = Recommendations::orderBy('updated_at', 'desc')->take(50)->get();

        if ($user['status'] != 'admin') {
            return redirect()->route('home');
        } else {
            $valueExists = array();
            foreach( $recommendations as $key => $post){
                $valueExists[] = $post->id;
            }
            return view('admin/recommendations', compact('recommendations', 'valueExists'));
        }
    }
}
