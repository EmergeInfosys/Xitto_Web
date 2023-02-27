<?php

namespace App\Http\Livewire;

use App\Models\Problem;
use App\Models\shoppingCart;
use Livewire\Component;

class Productlist extends Component
{
    public $problem;
    public function render()
    {
        $this->problem = Problem::get();
        return view('livewire.productlist');
    }

    public function addTocart($id)
    {
        if(auth()->user()){
            //add to cart
            $data=[
                'user_id' => auth()->user()->id,
                'product_id' => $id,
            ];
            shoppingCart::updateOrCreate($data);
            return response()->json([
                'message' => "Added Successfully On Cart",
            ]);
        }else{
        //    redirect to login 
        return redirect(route('login'));
        }
        
    }
}
