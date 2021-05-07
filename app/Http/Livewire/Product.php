<?php

namespace App\Http\Livewire;

use App\Models\Product as ModelsProduct;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Product extends Component
{
    use WithFileUploads;

    public $name, $image, $description, $qty, $price;

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'image' => 'required|max:1024',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required',
        ]);

        $imageName = md5($this->image . microtime() . '.' . $this->image->extension());

        Storage::putFileAs('public/images', $this->image, $imageName);

        ModelsProduct::create([
            'name' => $this->name,
            'image' => $imageName,
            'description' => $this->description,
            'qty' => $this->qty,
            'price' => $this->price
        ]);

        request()->session()->flash('info', 'Product created successfully');

        $this->clearForm();
    }

    public function clearForm()
    {
        $this->name = '';
        $this->image = '';
        $this->description = '';
        $this->qty = '';
        $this->price = '';
    }

    public function render()
    {
        $products = ModelsProduct::orderBy('created_at', 'desc')->get();

        return view('livewire.product', compact('products'));
    }
}
