<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Product List</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($products as $product)
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <img src="{{ asset('storage/images') }}/{{ $product->image }}" class="img-fluid"
                                    alt="{{ $product->name }}">
                            </div>
                            <div class="card-footer">
                                <h6 class="text-center">{{ $product->name }}</h6>
                                <button wire:click="addItem({{ $product->id }})"
                                    class="btn btn-primary btn-sm btn-block">Add to
                                    cart</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Carts</h4>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carts as $index => $cart)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $cart['name'] }}</td>
                            <td>{{ $cart['qty'] }}</td>
                            <td>{{ $cart['price'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
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
                <h5>Sub Total: {{ $summary['subTotal'] }}</h5>

                <h5>Tax: {{ $summary['pajak'] }}</h5>
                <h5>Total: {{ $summary['total'] }}</h5>

                <div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <button wire:click="enableTax" class="btn btn-success btn-block">Add Tax</button>
                        </div>
                        <div class="col-md-6">
                            @if($summary['pajak'] > 0)
                            <button wire:click="disableTax" class="btn btn-danger btn-block">Remove Tax</button>
                            @endif
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block">Save Transaction</button>
                </div>
            </div>
        </div>
    </div>
</div>
