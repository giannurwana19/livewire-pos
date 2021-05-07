<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Product List</h4>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $index => $product)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}.</td>
                            <td>{{ $product->name }}</td>
                            <td><img src="{{ asset('storage/images') }}/{{ $product->image }}"
                                    alt="{{ $product->name }}" width="100">
                            </td>
                            <td>{{ $product->description }}</td>
                            <td>{{ $product->qty }}</td>
                            <td>{{ $product->price }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Create Product</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ session('success') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <form wire:submit.prevent="store">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" wire:model="name" class="form-control" id="name">
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Image</label>
                        <div class="custom-file">
                            <input type="file" wire:model="image" class="custom-file-input" id="image">
                            <label for="image" class="custom-file-label">Choose Image</label>
                            @error('image')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        @if($image)
                        <div class="my-3">
                            <img src="{{ $image->temporaryUrl() }}" class="img-fluid" alt="preview image">
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea wire:model="description" class="form-control" id="description"></textarea>
                        @error('description')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="qty">Quantity</label>
                        <input type="number" wire:model="qty" class="form-control" id="qty">
                        @error('qty')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" wire:model="price" class="form-control" id="price">
                        @error('price')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Create Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
