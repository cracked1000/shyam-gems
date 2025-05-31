<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Gem;

class ClientDashboard extends Component
{
    public $user;
    public $gems;

    public function mount()
    {
        $this->user = Auth::user();
        $this->gems = Gem::all();
    }

    public function render()
    {
        return view('livewire.client-dashboard')
            ->layout('components.layouts.app');
    }
}