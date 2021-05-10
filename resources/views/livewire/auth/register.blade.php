<div class="container">
    <div class="row justify-content-center mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center">Register</h2>
                    <hr>
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong class="text-center">{{ session('error') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <form wire:submit.prevent="register">
                        <div class="form-group">
                            <label for="email">Name</label>
                            <input type="text" wire:model="form.name" class="form-control" id="email"
                                placeholder="Your Name" autofocus>
                            @error('form.name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" wire:model="form.email" class="form-control" id="email"
                                placeholder="Your email" autofocus>
                            @error('form.email')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" wire:model="form.password" class="form-control" id="password"
                                placeholder="Your password">
                            @error('form.password')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Confirm password</label>
                            <input type="password" wire:model="form.password_confirmation" class="form-control"
                                id="password" placeholder="Confirm password">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </form>

                    <div class="mt-3">
                        Already have acount? <a href="{{ route('login') }}">Login Now!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
