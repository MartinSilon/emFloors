<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $incomingField = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'phonenumber' => 'required|regex:/^\+421\s?\d{3}\s?\d{3}\s?\d{3}$/',
            'area' => 'nullable',
            'town' => 'nullable',
            'addinfo' => 'nullable',
        ], [
            'firstname.required' => "Prosím, vyplňte Krsné meno",
            'firstname.string' => "Meno musí obsahovať iba písmená",

            'lastname.required' => "Prosím, vyplňte Priezvisko",
            'lastname.string' => "Priezvisko musí obsahovať iba písmená",

            'email.required' => "Prosím, vyplňte Email",
            'email.email' => "Email je v zlom formáte",

            'phonenumber.required' => "Prosím, vyplňte Telefónny kontakt",
            'phonenumber.regex' => "Telefónny kontakt upravte do formátu +421 111 222 333",

            'area.numeric' => "Prosím, zapíšte rozlohu číslom v metroch štvorcových ( ㎡ )",
        ]);
        Orders::create($incomingField);
        session()->flash('confirmMessUser', "Objednávka sa úspešne vytvorila");
        return redirect('/vsetky-projekty');

    }

    public function orderPage(){
        $user = Auth::user();
        if ($user['status'] != 'admin') {
            return redirect()->route('home');
        } else {
            $orders = Orders::orderBy('updated_at', 'desc')->get();
            $valueExists = array();
            foreach( $orders as $key => $order){
                $valueExists[] = $order->id;
            }
            return view('admin/orders', compact('orders', 'valueExists'));
        }
    }
}
