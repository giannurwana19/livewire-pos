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
                        <div class="card mb-2" style="cursor: pointer" title="add to cart"
                            wire:click="addItem({{ $product->id }})">
                            <div class="card-body">
                                <img src="{{ asset('storage/images') }}/{{ $product->image }}" class="img-fluid mt-4"
                                    alt="{{ $product->name }}">
                                <h6 class="text-center mt-2 font-weight-bold">{{ $product->name }}</h6>
                                <p class="text-center mb-n1">Rp {{ number_format($product->price, 0, '.', ',') }}</p>
                                <p class="text-center mb-1">stock : {{ $product->qty }}</p>
                                <button wire:click="addItem({{ $product->id }})"
                                    class="btn btn-primary btn-sm position-absolute"
                                    style="top: 0; right: 0; padding: 5px 10px">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <h4>No Products Found!</h4>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="card-footer">
                {{ $products->links() }}
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carts as $index => $cart)
                        <tr>
                            <td class="text-center">
                                {{ $index + 1 }}
                                <button wire:click="removeItem('{{ $cart['rowId'] }}')" class="btn btn-danger btn-sm"
                                    style="padding: 0 6px">
                                    <i class="fas fa-trash"></i>
                                </button></td>
                            <td>
                                <strong>{{ $cart['name'] }} </strong>
                                <br> Rp {{number_format($cart['priceSingle'], 0, '.', ',') }}

                            </td>
                            <td class="text-center">
                                <button wire:click="decreaseItem('{{ $cart['rowId'] }}')" class="btn btn-success btn-sm"
                                    style="padding: 0 6px">
                                    <i class="fas fa-minus"></i>
                                </button>
                                {{ $cart['qty'] }}
                                <button wire:click="increaseItem('{{ $cart['rowId'] }}')" class="btn btn-success btn-sm"
                                    style="padding: 0 6px">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </td>
                            <td>Rp{{ number_format($cart['price'], 0, '.', ',') }}</td>
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

                <h5>Tax (2%): {{ number_format($summary['pajak'], 0, '.', ',') }}</h5>
                <h5>Total: {{ number_format($summary['total'], 0, '.', ',') }}</h5>



                <div>
                    @if($summary['pajak'] > 0)
                    <button wire:click="disableTax" class="btn btn-danger btn-block mb-3">
                        <i class="fas fa-tag"></i> Remove Tax
                    </button>
                    @else
                    <button wire:click="enableTax" class="btn btn-success btn-block mb-3">
                        <i class="fas fa-tag"></i> Add Tax
                    </button>
                    @endif
                    <div class="form-group">
                        <input type="number" class="form-control" id="payment" placeholder="Input Customer Payment">
                        <input type="hidden" id="total" value="{{ $summary['total'] }}">
                    </div>

                    <form wire:submit.prevent="saveTransaction">
                        <div>
                            <label for="">Payment</label>
                            <h4 id="paymentText">Rp. 0</h4>
                        </div>

                        <div class="mb-2">
                            <label for="">Kembalian</label>
                            <h4 id="kembalianText">Rp. 0</h4>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" wire:ignore id="saveButton" disabled>
                            <i class="fas fa-save"></i> Save Transaction
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    payment.oninput = () => {
        const paymentAmount = document.getElementById('payment').value;
        const totalAmount = document.getElementById('total').value;
        const kembalian = paymentAmount - totalAmount;

        document.getElementById('kembalianText').innerHTML = `Rp. ${formatRupiah(kembalian)}`;
        document.getElementById('paymentText').innerHTML = `Rp. ${formatRupiah(paymentAmount)}`;

        const saveButton = document.getElementById('saveButton');

        if(kembalian < 0){
            saveButton.disabled = true;
        }else{
            saveButton.disabled = false;
        }
    }

    const formatRupiah = angka => {
        const numberString = angka.toString();
        const split = numberString.split('.');

        const sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        const ribuan = split[0].substr(sisa).match(/\d{1,3}/gi);

        if(ribuan){
            const separator = sisa ? ',' : '';
            rupiah += separator + ribuan.join(',');
        }

        return split[1] != undefined ? rupiah + '.' + split[1] : rupiah;
    }
</script>
@endpush
