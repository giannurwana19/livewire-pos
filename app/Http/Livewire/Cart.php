<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Carbon\Carbon;
use Darryldecode\Cart\CartCondition;
use Darryldecode\Cart\Facades\CartFacade;
use Livewire\Component;
use Livewire\WithPagination;

class Cart extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $tax = '0%';
    public $search = '';

    public function updatingSearch() // .. 1
    {
        $this->resetPage();
    }

    public function addItem($id)
    {
        $rowId = "Cart$id";
        $cart = CartFacade::session(auth()->id())->getContent();
        $checkItem = $cart->whereIn('id', $rowId);

        $product = Product::findOrFail($id);

        if ($checkItem->isNotEmpty()) {
            if ($product->qty == $checkItem[$rowId]->quantity) {
                request()->session()->flash('error', 'Jumlah item Habis!');
            } else {
                CartFacade::session(auth()->id())->update($rowId, [
                    'quantity' => [
                        'relative' => true,
                        'value' => 1
                    ]
                ]);
            }
        } else {

            CartFacade::session(auth()->id())->add([
                'id' => "Cart" . $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'attributes' => [
                    'added_at' => Carbon::now()
                ]
            ]);
        }
    }

    public function enableTax()
    {
        $this->tax = '2%';
    }

    public function disableTax()
    {
        $this->tax = '0%';
    }

    public function decreaseItem($id)
    {
        $cart = CartFacade::session(auth()->id())->getContent();
        $checkItem = $cart->whereIn('id', $id);

        if ($checkItem[$id]->quantity == 1) {
            $this->removeItem($id);
        } else {
            CartFacade::session(auth()->id())->update($id, [
                'quantity' => [
                    'relative' => true,
                    'value' => -1
                ]
            ]);
        }
    }

    public function increaseItem($id)
    {
        $productId = substr($id, 4, 5);
        $product = Product::findOrFail($productId);
        $cart = CartFacade::session(auth()->id())->getContent();
        $checkItem = $cart->whereIn('id', $id);

        if ($product->qty == $checkItem[$id]->quantity) {
            request()->session()->flash('error', 'Jumlah item Habis!');
        } else {
            CartFacade::session(auth()->id())->update($id, [
                'quantity' => [
                    'relative' => true,
                    'value' => 1
                ]
            ]);
        }
    }

    public function removeItem($id)
    {
        CartFacade::session(auth()->id())->remove($id);
    }

    public function render()
    {
        $products = $this->search == '' ? Product::orderBy('created_at', 'desc')->paginate(3) : Product::where('name', 'like', "%$this->search%")->orderBy('created_at', 'desc')->paginate(3);

        $condition = new CartCondition([
            'name' => 'pajak',
            'type' => 'tax',
            'target' => 'total',
            'value' => $this->tax,
            'order' => 1
        ]);

        CartFacade::session(auth()->id())->condition($condition);

        $items = CartFacade::session(auth()->id())->getContent()->sortBy(function ($cart) {
            return $cart->attributes->get('added_at');
        });


        if (CartFacade::isEmpty()) {
            $carts = [];
        } else {
            foreach ($items as $item) {
                $carts[] = [
                    'rowId' => $item->id,
                    'name' => $item->name,
                    'qty' => $item->quantity,
                    'priceSingle' => $item->price,
                    'price' => $item->getPriceSum(),
                ];
            }

            $carts = collect($carts);
        }

        $subTotal = CartFacade::session(auth()->id())->getSubTotal();
        $total = CartFacade::session(auth()->id())->getTotal();

        $condition = CartFacade::session(auth()->id())->getCondition('pajak');
        $pajak = $condition->getCalculatedValue($subTotal);

        $summary = [
            'subTotal' => $subTotal,
            'pajak' => $pajak,
            'total' => $total,
        ];

        return view('livewire.cart', compact('summary', 'products', 'carts'));
    }
}

// h: DOKUMENTASI

// p: 1
// lifecycle hooks update
// kita gunakan lifecycle update
// search disini artinya lifecycle update hanya berlaku pada variable search
//     public function updatingSearch()
// {
    // $this->resetPage();
// }
