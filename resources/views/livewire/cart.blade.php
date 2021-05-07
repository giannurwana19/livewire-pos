<div class="row">
    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-header">
                <h4>Product List</h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <input type="search" wire:model="search" class="form-control" placeholder="Search product here..."
                        autofocus>
                </div>

                <div class="row">
                    @forelse($products as $product)
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="card mb-2">
                            <div class="card-body">
                                <img src="{{ asset('storage/images') }}/{{ $product->image }}" class="img-fluid"
                                    alt="{{ $product->name }}">
                                <h6 class="text-center mt-2 font-weight-bold">{{ $product->name }}</h6>
                                <p class="text-center mb-n1">Rp {{ number_format($product->price, 0, '.', ',') }}</p>
                                <p class="text-center mb-1">stock : {{ $product->qty }}</p>
                                <button wire:click="addItem({{ $product->id }})"
                                    class="btn btn-success btn-sm btn-block">Add to
                                    cart</button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <h4>No Products Found!</h4>
                    @endforelse
                </div>
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h4>Carts</h4>
            </div>
            <div class="card-body">
                @if(session('error'))
                <small class="text-center text-danger">{{ session('error') }}</small>
                @endif

                <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carts as $index => $cart)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $cart['name'] }}
                            </td>
                            <td width="50">{{ $cart['qty'] }}</td>
                            <td>Rp {{ number_format($cart['price'], 0, '.', ',') }}</td>
                            <td class="text-center">
                                <button class="btn btn-outline-success btn-sm"
                                    wire:click="decreaseItem('{{ $cart['rowId'] }}')">
                                    <strong>-</strong>
                                </button>
                                <button class="btn btn-outline-success btn-sm"
                                    wire:click="increaseItem('{{ $cart['rowId'] }}')">
                                    <strong>+</strong>
                                </button>
                                <button wire:click="removeItem('{{ $cart['rowId'] }}')"
                                    class="btn btn-outline-danger btn-sm">
                                    <strong>x</strong>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <h6 class="text-center">Empty Cart</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4>Cart Summary</h4>
                <h5>Sub Total: {{ number_format($summary['subTotal'], 0, '.', ',') }}</h5>

                <h5>Tax: {{ number_format($summary['pajak'], 0, '.', ',') }}</h5>
                <h5>Total: {{ number_format($summary['total'], 0, '.', ',') }}</h5>

                <div>
                    @if($summary['pajak'] > 0)
                    <button wire:click="disableTax" class="btn btn-danger btn-block">Remove Tax</button>
                    @else
                    <button wire:click="enableTax" class="btn btn-success btn-block">Add Tax</button>
                    @endif
                    <button class="btn btn-primary btn-block">Save Transaction</button>
                </div>
            </div>
        </div>
    </div>
</div>
