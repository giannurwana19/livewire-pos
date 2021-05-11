<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Darryldecode\Cart\CartCondition;
use Darryldecode\Cart\Facades\CartFacade;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class Cart extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $tax = '0%';
    public $search = '';
    public $payment = 0;

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
            if ($product->qty == 0) {
                request()->session()->flash('error', 'Jumlah item habis!');
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
            if ($product->qty == 0) {
                request()->session()->flash('error', 'Jumlah item habis!');
            } else {
                CartFacade::session(auth()->id())->update($id, [
                    'quantity' => [
                        'relative' => true,
                        'value' => 1
                    ]
                ]);
            }
        }
    }

    public function removeItem($id)
    {
        return CartFacade::session(auth()->id())->remove($id);
    }

    public function saveTransaction()
    {
        $cartTotal = CartFacade::session(auth()->id())->getTotal();
        $bayar = $this->payment;
        $kembalian = (int) $bayar - (int) $cartTotal;

        if ($kembalian >= 0) {
            DB::transaction(function () use ($cartTotal, $bayar) {
                DB::beginTransaction();
                try {
                    $allCart = CartFacade::session(auth()->id())->getContent();
                    $filterCart = $allCart->map(function ($item) {
                        return [
                            'id' => substr($item->id, 4, 5),
                            'quantity' => $item->quantity
                        ];
                    });

                    foreach ($filterCart as $cart) {
                        $product = Product::find($cart['id']);

                        if ($product->qty === 0) {
                            return request()->session()->flash('error', 'Jumlah item kurang');
                        }

                        $product->decrement('qty', $cart['quantity']);
                    }

                    $invoiceNumber = IdGenerator::generate([
                        'table' => 'transactions',
                        'length' => 10,
                        'prefix' => 'INV',
                        'field' => 'invoice_number'
                    ]);

                    Transaction::create([
                        'invoice_number' => $invoiceNumber,
                        'user_id' => auth()->id(),
                        'pay' => $bayar,
                        'total' => $cartTotal
                    ]);

                    foreach ($filterCart as $cart) {
                        ProductTransaction::create([
                            'product_id' => $cart['id'],
                            'invoice_number' => $invoiceNumber,
                            'qty' => $cart['quantity']
                        ]);
                    };

                    CartFacade::session(auth()->id())->clear();

                    $this->payment = 0;

                    DB::commit();
                } catch (Throwable $e) {
                    DB::rollBack();
                    return request()->session()->flash('error', $e);
                }
            });
        }
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
