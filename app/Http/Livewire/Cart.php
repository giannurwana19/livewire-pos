<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Carbon\Carbon;
use Darryldecode\Cart\CartCondition;
use Darryldecode\Cart\Facades\CartFacade;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Cart extends Component
{
    public $tax = '0%';

    public function addItem($id)
    {
        $rowId = "Cart $id";
        $cart = CartFacade::session(auth()->id())->getContent();
        $cekItemId = $cart->whereIn('id', $rowId);

        if ($cekItemId->isNotEmpty()) {
            CartFacade::session(auth()->id())->update($rowId, [
                'quantity' => [
                    'relative' => true,
                    'value' => 1
                ]
            ]);
        } else {
            $product = Product::findOrFail($id);

            CartFacade::session(auth()->id())->add([
                'id' => "Cart $product->id",
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
        $this->tax = '10%';
    }

    public function disableTax()
    {
        $this->tax = '0%';
    }


    public function render()
    {
        $products = Product::orderBy('created_at', 'desc')->get();

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
            'total' => $total
        ];


        return view('livewire.cart', compact('summary', 'products', 'carts'));
    }
}
