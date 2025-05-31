<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Requirement;
use App\Models\Reply;
use App\Events\NewRequirementPosted;
use App\Events\NewReplyPosted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Feed extends Component
{
    use WithFileUploads;

    // Properties for requirements
    public $title;
    public $description;
    public $requirementImage;

    // Properties for replies
    public $replyContent = '';
    public $replyImage = null;
    public $selectedRequirementId = null;
    public $showProposalForm = false;

    public $requirements;

    protected $listeners = [
        'refreshFeed' => 'refreshFeed',
        'echo:requirements,NewRequirementPosted' => 'onNewRequirement',
        'echo:requirements,NewReplyPosted' => 'onNewReply',
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'requirementImage' => 'nullable|image|max:2048',
        'replyContent' => 'required|string',
        'replyImage' => 'nullable|image|max:2048',
        'selectedRequirementId' => 'required|exists:requirements,id',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }
        $this->loadRequirements();
        Log::info('Mounted Feed: Requirements count - ' . $this->requirements->count());
    }

    private function loadRequirements()
    {
        $this->requirements = Requirement::with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function postRequirement()
    {
        Log::info('Starting postRequirement method');
        
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirementImage' => 'nullable|image|max:2048',
        ]);

        Log::info('Validation passed for requirement');

        $imagePath = null;
        if ($this->requirementImage) {
            Log::info('Storing requirement image');
            $imagePath = $this->requirementImage->store('requirements', 'public');
        }

        Log::info('Creating requirement in database');
        $requirement = Requirement::create([
            'title' => $this->title,
            'description' => $this->description,
            'image' => $imagePath,
            'user_id' => Auth::id(),
        ]);

        Log::info('Requirement created with ID: ' . $requirement->id);

        // Load relationships for the new requirement
        $requirement->load(['user', 'replies.user']);
        Log::info('Relationships loaded for requirement');

        // Broadcast to others (not current user)
        try {
            broadcast(new NewRequirementPosted($requirement))->toOthers();
            Log::info('Broadcasting completed successfully for requirement: ' . $requirement->id);
        } catch (\Exception $e) {
            Log::error('Broadcasting failed: ' . $e->getMessage());
        }

        // Add to current user's feed immediately
        $this->requirements->prepend($requirement);
        Log::info('Requirement added to current user feed');

        $this->reset(['title', 'description', 'requirementImage']);
        session()->flash('message', 'Requirement posted successfully!');
        Log::info('Posted Requirement: ' . $requirement->title);
    }

    public function toggleProposalForm($requirementId)
    {
        $this->selectedRequirementId = $requirementId;
        $this->showProposalForm = !$this->showProposalForm;
        if ($this->showProposalForm) {
            $this->reset(['replyContent', 'replyImage']);
        }
    }

    public function postReply()
    {
        if (Auth::user()->role !== 'seller') {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Only sellers can reply.']);
            return;
        }

        $this->validate([
            'replyContent' => 'required|string',
            'replyImage' => 'nullable|image|max:2048',
            'selectedRequirementId' => 'required|exists:requirements,id',
        ]);

        $imagePath = null;
        if ($this->replyImage) {
            $imagePath = $this->replyImage->store('replies', 'public');
        }

        $reply = Reply::create([
            'content' => $this->replyContent,
            'image' => $imagePath,
            'user_id' => Auth::id(),
            'requirement_id' => $this->selectedRequirementId,
        ]);

        // Load user relationship
        $reply->load('user');

        // Broadcast to others (not current user)
        try {
            broadcast(new NewReplyPosted($reply))->toOthers();
            Log::info('Reply broadcasting completed successfully for reply: ' . $reply->id);
        } catch (\Exception $e) {
            Log::error('Reply broadcasting failed: ' . $e->getMessage());
        }

        // Update current user's feed immediately
        $this->updateRequirementWithNewReply($reply);

        $this->reset(['replyContent', 'replyImage', 'selectedRequirementId']);
        $this->showProposalForm = false;
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Reply posted successfully!']);
        Log::info('Posted Reply for Requirement ID: ' . $this->selectedRequirementId);
    }

    public function onNewRequirement($data)
    {
        Log::info('Received new requirement via Reverb', ['data' => $data]);

        try {
            // For Laravel Reverb, log the exact structure to debug
            Log::info('Event data structure:', [
                'data' => $data,
                'keys' => is_array($data) ? array_keys($data) : 'not_array',
                'type' => gettype($data)
            ]);

            $requirementData = null;
            
            // Try different possible data structures
            if (isset($data['requirement'])) {
                $requirementData = $data['requirement'];
            } elseif (is_array($data) && isset($data['id'])) {
                // Data might be at root level
                $requirementData = $data;
            } else {
                Log::warning('Using raw data as requirement data');
                $requirementData = $data;
            }

            // Validate we have the required fields
            if (!isset($requirementData['id'])) {
                Log::error('No ID found in requirement data', ['data' => $requirementData]);
                return;
            }

            // Fetch the fresh requirement from database to ensure we have all data
            $requirement = Requirement::with(['user', 'replies.user'])->find($requirementData['id']);
            
            if (!$requirement) {
                Log::error('Requirement not found in database', ['id' => $requirementData['id']]);
                return;
            }

            // Add to the top of the feed if not already present
            if (!$this->requirements->contains('id', $requirement->id)) {
                $this->requirements->prepend($requirement);
                
                // Show notification
                $this->dispatch('alert', ['type' => 'info', 'message' => 'New requirement posted by ' . $requirement->user->name . '!']);
                Log::info('New requirement added to feed via broadcast');
            }
            
        } catch (\Exception $e) {
            Log::error('Error processing new requirement broadcast', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $data
            ]);
        }
    }

    public function onNewReply($data)
    {
        Log::info('Received new reply via Reverb', ['data' => $data]);

        try {
            // Log the exact structure to debug
            Log::info('Reply event data structure:', [
                'data' => $data,
                'keys' => is_array($data) ? array_keys($data) : 'not_array',
                'type' => gettype($data)
            ]);

            $replyData = null;
            $requirementId = null;
            
            // Try different possible data structures
            if (isset($data['reply']) && isset($data['requirement_id'])) {
                $replyData = $data['reply'];
                $requirementId = $data['requirement_id'];
            } elseif (isset($data['requirement_id'])) {
                $replyData = $data;
                $requirementId = $data['requirement_id'];
            } else {
                Log::error('Cannot determine reply structure', ['data' => $data]);
                return;
            }

            // Validate we have the required fields
            if (!isset($replyData['id']) || !$requirementId) {
                Log::error('Missing required reply data', [
                    'reply_data' => $replyData,
                    'requirement_id' => $requirementId
                ]);
                return;
            }

            // Fetch the fresh reply from database to ensure we have all data
            $reply = Reply::with('user')->find($replyData['id']);
            
            if (!$reply) {
                Log::error('Reply not found in database', ['id' => $replyData['id']]);
                return;
            }

            // Update the requirement with the new reply
            $this->updateRequirementWithNewReply($reply);

            // Show notification
            $this->dispatch('alert', ['type' => 'info', 'message' => 'New reply from ' . $reply->user->name . '!']);
            Log::info('New reply added to feed via broadcast');
            
        } catch (\Exception $e) {
            Log::error('Error processing new reply broadcast', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $data
            ]);
        }
    }

    private function updateRequirementWithNewReply($reply)
    {
        $requirement = $this->requirements->firstWhere('id', $reply->requirement_id);
        if ($requirement) {
            // Check if reply already exists to avoid duplicates
            $existingReply = $requirement->replies->firstWhere('id', $reply->id);
            if (!$existingReply) {
                // Add reply to the requirement's replies collection
                $replies = $requirement->replies ?? collect([]);
                $replies->push($reply);
                $requirement->setRelation('replies', $replies);
                Log::info('Reply added to requirement in feed', [
                    'reply_id' => $reply->id,
                    'requirement_id' => $reply->requirement_id
                ]);
            } else {
                Log::info('Reply already exists, skipping duplicate', ['reply_id' => $reply->id]);
            }
        } else {
            Log::warning('Requirement not found in current feed', ['requirement_id' => $reply->requirement_id]);
        }
    }

    public function refreshFeed()
    {
        $this->loadRequirements();
        Log::info('Feed refreshed manually');
    }

    public function render()
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }

        return view('livewire.feed', [
            'requirements' => $this->requirements,
        ])->layout('components.layouts.app')->title('Title');
    }
}