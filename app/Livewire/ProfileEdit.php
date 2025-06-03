<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileEdit extends Component
{
    use WithFileUploads;

    public $user;
    public $first_name;
    public $last_name;
    public $email;
    public $username;
    public $telephone;
    public $bio;
    public $experience;
    public $profile_photo;
    public $isLoading = false;

    public function mount()
    {
        $this->user = Auth::user();
        
        if (!$this->user) {
            abort(403, 'Unauthorized access');
        }

        $this->initializeFormData();
    }

    private function initializeFormData()
    {
        $this->first_name = $this->user->first_name ?? '';
        $this->last_name = $this->user->last_name ?? '';
        $this->email = $this->user->email ?? '';
        $this->username = $this->user->username ?? '';
        $this->telephone = $this->user->telephone ?? '';
        $this->bio = $this->user->bio ?? '';
        $this->experience = $this->user->experience ?? '';
    }

    protected function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required', 
                'email', 
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id)
            ],
            'username' => [
                'required', 
                'string', 
                'min:3',
                'max:255', 
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($this->user->id)
            ],
            'telephone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'experience' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    protected function messages()
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name should only contain letters and spaces.',
            'last_name.required' => 'Last name is required.',
            'last_name.regex' => 'Last name should only contain letters and spaces.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.regex' => 'Username can only contain letters, numbers, dots, underscores, and hyphens.',
            'username.unique' => 'This username is already taken.',
            'telephone.regex' => 'Please enter a valid phone number.',
            'bio.max' => 'Bio cannot exceed 1000 characters.',
            'experience.max' => 'Experience description cannot exceed 1000 characters.',
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.mimes' => 'Profile photo must be a JPEG, PNG, JPG, or WebP file.',
            'profile_photo.max' => 'Profile photo cannot be larger than 2MB.',
        ];
    }

    public function updated($propertyName)
    {
        // Real-time validation for better UX
        $this->validateOnly($propertyName);
    }

    public function updateProfile()
    {
        $this->isLoading = true;

        try {
            $validatedData = $this->validate();

            $updateData = [
                'first_name' => trim($validatedData['first_name']),
                'last_name' => trim($validatedData['last_name']),
                'email' => strtolower(trim($validatedData['email'])),
                'username' => strtolower(trim($validatedData['username'])),
                'telephone' => $validatedData['telephone'] ? trim($validatedData['telephone']) : null,
                'bio' => $validatedData['bio'] ? trim($validatedData['bio']) : null,
                'experience' => $validatedData['experience'] ? trim($validatedData['experience']) : null,
            ];

            // Handle profile photo upload
            if ($this->profile_photo) {
                $updateData['profile_photo_path'] = $this->handlePhotoUpload();
            }

            // Update user data
            $this->user->update($updateData);

            // Flash success message
            session()->flash('message', 'Profile updated successfully!');
            session()->flash('message_type', 'success');

            // Reset file input
            $this->reset('profile_photo');

            // Refresh user data
            $this->user->refresh();

        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('message', 'Please fix the validation errors below.');
            session()->flash('message_type', 'error');
            throw $e;
        } catch (\Exception $e) {
            session()->flash('message', 'An error occurred while updating your profile. Please try again.');
            session()->flash('message_type', 'error');
            
            // Log the error for debugging
            \Log::error('Profile update error: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    private function handlePhotoUpload()
    {
        try {
            // Delete old photo if exists
            if ($this->user->profile_photo_path && Storage::disk('public')->exists($this->user->profile_photo_path)) {
                Storage::disk('public')->delete($this->user->profile_photo_path);
            }

            // Store new photo with a unique name
            $filename = 'profile_' . $this->user->id . '_' . time() . '.' . $this->profile_photo->getClientOriginalExtension();
            return $this->profile_photo->storeAs('profile_photos', $filename, 'public');

        } catch (\Exception $e) {
            \Log::error('Photo upload error: ' . $e->getMessage());
            throw new \Exception('Failed to upload profile photo. Please try again.');
        }
    }

    public function removePhoto()
    {
        try {
            if ($this->user->profile_photo_path && Storage::disk('public')->exists($this->user->profile_photo_path)) {
                Storage::disk('public')->delete($this->user->profile_photo_path);
                
                $this->user->update(['profile_photo_path' => null]);
                $this->user->refresh();

                session()->flash('message', 'Profile photo removed successfully!');
                session()->flash('message_type', 'success');
            }
        } catch (\Exception $e) {
            session()->flash('message', 'Failed to remove profile photo. Please try again.');
            session()->flash('message_type', 'error');
        }
    }

    public function render()
    {
        return view('livewire.profile-edit')
            ->layout('components.layouts.app');
    }
}