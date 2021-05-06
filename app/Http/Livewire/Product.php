<?php

namespace App\Http\Livewire;

use App\Models\Product as ModelsProduct;
use Livewire\Component;
use Livewire\WithFileUploads;

class Product extends Component
{
    use WithFileUploads;

    public $name, $image, $description, $qty, $price;

    public function store()
    {
        dd('ok');
    }

    public function render()
    {
        $products = ModelsProduct::orderBy('created_at', 'desc')->get();

        return view('livewire.product', compact('products'));
    }
}
