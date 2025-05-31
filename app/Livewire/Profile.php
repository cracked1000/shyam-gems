<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Gem; // Add the Gem model
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $user;
    public $gems;

    public function mount($username)
    {
        try {
            $this->user = User::where('username', $username)->firstOrFail();
            $this->gems = Gem::where('seller_id', $this->user->id)->get();
            
            Log::info('Profile loaded for user: ' . $this->user->username, [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'telephone' => $this->user->telephone,
                'role' => $this->user->role,
                'profile_photo_path' => $this->user->profile_photo_path,
            ]);

            if (Auth::check()) {
                Log::info('Authenticated user: ' . Auth::user()->username);
            }
        } catch (\Exception $e) {
            Log::error('Error loading profile: ' . $e->getMessage());
            $this->user = null;
            session()->flash('error', 'User not found or an error occurred.');
        }
    }

    public function render()
    {
        Log::info('Rendering Profile component', [
            'username' => $this->user ? $this->user->username : 'Not Found',
        ]);
        return view('livewire.profile', ['gems' => $this->gems])
            ->layout('components.layouts.app')
            ->title('Profile - ' . ($this->user ? $this->user->username : 'Not Found'));
    }
}