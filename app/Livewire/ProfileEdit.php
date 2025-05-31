<?php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileEdit extends Component
{
    public $user;
    public $first_name;
    public $last_name;
    public $email;
    public $username;
    public $telephone;
    public $bio;
    public $experience;
    public $profile_photo;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . 'id',
        'username' => 'required|string|max:255|unique:users,username,' . 'id',
        'telephone' => 'nullable|string|max:20',
        'bio' => 'nullable|string|max:1000',
        'experience' => 'nullable|string|max:1000',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        if (!$this->user) {
            abort(403, 'Unauthorized access');
        }
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->username = $this->user->username;
        $this->telephone = $this->user->telephone ?? '';
        $this->bio = $this->user->bio ?? '';
        $this->experience = $this->user->experience ?? '';
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updateProfile()
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'telephone' => $this->telephone,
            'bio' => $this->bio,
            'experience' => $this->experience,
        ];

        if ($this->profile_photo) {
            // Delete old photo if exists
            if ($this->user->profile_photo_path) {
                Storage::disk('public')->delete($this->user->profile_photo_path);
            }

            $data['profile_photo_path'] = $this->profile_photo->store('profile_photos', 'public');
        }

        $this->user->update($data);

        session()->flash('message', 'Profile updated successfully!');
        $this->reset('profile_photo');
    }

    public function render()
    {
        return view('livewire.profile-edit')
            ->layout('components.layouts.app');
    }
}