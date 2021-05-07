<?php

namespace App\Http\Livewire;

use App\Models\Product as ModelsProduct;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Product extends Component
{
    use WithFileUploads, WithPagination;

    protected $paginationTheme = 'bootstrap';

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

        request()->session()->flash('success', 'Product created successfully');

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
        $products = ModelsProduct::orderBy('created_at', 'desc')->paginate(3);

        return view('livewire.product', compact('products'));
    }
}
