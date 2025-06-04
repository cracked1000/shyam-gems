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
    public $gemToDelete = null; // Add this property

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
        
    }

    public function loadGems()
    {
        $this->gems = Gem::forSeller($this->user->id)->get();
    }

    

    public function storeGem()
    {
        $this->validate();

        try {
            $imagePath = $this->image->store('gems', 'public');
            Log::info('Gem image stored at: ' . $imagePath);

            Gem::create([
                'name' => $this->name,
                'description' => $this->description,
                'image' => $imagePath,
                'seller_id' => (string) $this->user->id,
            ]);

            $this->reset(['name', 'description', 'image']);
            $this->loadGems();
            session()->flash('message', 'Gem added successfully!');
        } catch (\Exception $e) {
            Log::error('Error storing gem: ' . $e->getMessage());
            session()->flash('error', 'Failed to add gem. Please try again.');
        }
    }

    public function confirmDelete($gemId)
    {
        try {
            // Find the gem to get its details for the confirmation modal
            $this->gemToDelete = Gem::where('_id', $gemId)
                ->where('seller_id', (string) $this->user->id)
                ->first();
            
            if ($this->gemToDelete) {
                $this->confirmingGemDeletion = true;
                $this->gemIdToDelete = $gemId;
            } else {
                session()->flash('error', 'Gem not found or you are not authorized to delete it.');
            }
        } catch (\Exception $e) {
            Log::error('Error finding gem for deletion: ' . $e->getMessage());
            session()->flash('error', 'Error occurred while preparing to delete gem.');
        }
    }

    public function cancelDelete()
    {
        $this->confirmingGemDeletion = false;
        $this->gemIdToDelete = null;
        $this->gemToDelete = null;
    }

    public function deleteGem($gemId = null)
    {
        try {
            // Use the stored gem ID if no ID is passed
            $idToDelete = $gemId ?: $this->gemIdToDelete;
            
            if (!$idToDelete) {
                session()->flash('error', 'No gem selected for deletion.');
                return;
            }

            $gem = Gem::where('_id', $idToDelete)
                ->where('seller_id', (string) $this->user->id)
                ->first();
            
            if ($gem) {
                // Delete the image file if it exists
                if ($gem->image && Storage::disk('public')->exists($gem->image)) {
                    Storage::disk('public')->delete($gem->image);
                    Log::info('Deleted gem image: ' . $gem->image);
                }
                
                // Delete the gem record
                $gem->delete();
                
                // Reload gems and close modal
                $this->loadGems();
                $this->cancelDelete();
                
                session()->flash('message', 'Gem deleted successfully!');
                Log::info('Gem deleted successfully: ' . $gem->name);
            } else {
                $this->cancelDelete();
                session()->flash('error', 'Gem not found or you are not authorized to delete it.');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting gem: ' . $e->getMessage());
            $this->cancelDelete();
            session()->flash('error', 'An error occurred while deleting the gem. Please try again.');
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
            'gemToDelete' => $this->gemToDelete, // Pass this to the view
        ])->layout('components.layouts.app')->title('Seller Dashboard');
    }
}