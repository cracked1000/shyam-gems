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
    public $selectedImage = null;
    public $confirmingGemDeletion = false;
    public $gemIdToDelete = null;

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
        $this->gems = Gem::forSeller($this->user->id)->get(); // Use scope with string casting
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
        Log::info('Gem image stored at: ' . $imagePath);

        Gem::create([
            'name' => $this->name,
            'description' => $this->description,
            'image' => $imagePath,
            'seller_id' => (string) $this->user->id, // Explicitly cast to string
        ]);

        $this->reset(['name', 'description', 'image']);
        $this->loadGems();
        session()->flash('message', 'Gem added successfully!');
    }

    public function confirmDelete($gemId)
    {
        $this->confirmingGemDeletion = true;
        $this->gemIdToDelete = $gemId;
    }

    public function cancelDelete()
    {
        $this->confirmingGemDeletion = false;
        $this->gemIdToDelete = null;
    }

    public function deleteGem($gemId)
    {
        $gem = Gem::where('_id', $gemId)->where('seller_id', (string) $this->user->id)->first();
        if ($gem) {
            if ($gem->image && Storage::disk('public')->exists($gem->image)) {
                Storage::disk('public')->delete($gem->image);
                Log::info('Deleted gem image: ' . $gem->image);
            }
            $gem->delete();
            $this->loadGems();
            $this->cancelDelete();
            session()->flash('message', 'Gem deleted successfully!');
        } else {
            $this->cancelDelete();
            session()->flash('message', 'Gem not found or unauthorized to delete!');
        }
    }

    public function showImage($imagePath)
    {
        $this->selectedImage = $imagePath;
    }

    public function closeImage()
    {
        $this->selectedImage = null;
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
            'confirmingGemDeletion' => $this->confirmingGemDeletion,
            'gemIdToDelete' => $this->gemIdToDelete,
        ])->layout('components.layouts.app')->title('Seller Dashboard');
    }
}