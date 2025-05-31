<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Gem;
use Illuminate\Support\Facades\Log;

class SellerDashboard extends Component
{
    use WithFileUploads;

    public $user;
    public $gems;
    public $name;
    public $description;
    public $image;
    public $feeds = [];
    public $messages = [];
    public $selectedImage = null; // For image preview

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'required|image|max:2048',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }

        $this->user = Auth::user();
        $this->loadGems();
        $this->loadSampleData();
    }

    public function loadGems()
    {
        $this->gems = Gem::where('seller_id', $this->user->id)->get();
    }

    public function loadSampleData()
    {
        $this->feeds = [
            ['id' => 1, 'user_id' => 2, 'content' => 'Check out my new gem!', 'username' => 'janesmith'],
            ['id' => 2, 'user_id' => 1, 'content' => 'Selling a sapphire.', 'username' => 'johndoe'],
        ];

        $this->messages = [
            ['id' => 1, 'sender_id' => 2, 'receiver_id' => 1, 'content' => 'Interested in your gem?', 'username' => 'janesmith'],
            ['id' => 2, 'sender_id' => 1, 'receiver_id' => 2, 'content' => 'Sure, let’s talk!', 'username' => 'johndoe'],
        ];
    }

    public function storeGem()
    {
        $this->validate();

        $imagePath = $this->image->store('gems', 'public');

        Gem::create([
            'name' => $this->name,
            'description' => $this->description,
            'image' => $imagePath,
            'seller_id' => $this->user->id,
        ]);

        $this->reset(['name', 'description', 'image']);
        $this->loadGems();
        session()->flash('message', 'Gem added successfully!');
    }

    public function deleteGem($gemId)
    {
        $gem = Gem::where('seller_id', $this->user->id)->where('id', $gemId)->first(); // Use 'id' for relational DB
        if ($gem) {
            if ($gem->image && Storage::disk('public')->exists($gem->image)) {
                Storage::disk('public')->delete($gem->image);
            }
            $gem->delete();
            $this->loadGems();
            session()->flash('message', 'Gem deleted successfully!');
        } else {
            session()->flash('message', 'Gem not found or unauthorized to delete!');
        }
    }

    public function showImage($imagePath)
    {
        $this->selectedImage = $imagePath; // Set the selected image for preview
    }

    public function closeImage()
    {
        $this->selectedImage = null; // Close the preview
    }

    public function render()
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }

        $this->loadGems();

        return view('livewire.seller-dashboard', [
            'user' => $this->user,
            'gems' => $this->gems,
            'feeds' => $this->feeds,
            'messages' => $this->messages,
            'selectedImage' => $this->selectedImage,
        ])->layout('components.layouts.app')->title('Seller Dashboard');
    }
}