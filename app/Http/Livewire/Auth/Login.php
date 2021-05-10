<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $form = [
        'email' => '',
        'password' => ''
    ];

    public function login()
    {
        $this->validate([
            'form.email' => 'required|email',
            'form.password' => 'required|min:6'
        ]);

        $user = User::where('email', $this->form['email'])->exists();

        if ($user && Auth::attempt($this->form)) {
            return redirect()->intended('home');
        }

        return request()->session()->flash('error', 'Invalid email or password, please try again!');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
