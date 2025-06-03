<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Requirement;
use App\Models\Reply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
    public $lastUpdated;
    public $newRequirementsCount = 0;
    public $newRepliesCount = 0;

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
        $this->lastUpdated = now();
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

        // Update cache to notify other users
        $this->updateLastActivityCache();

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

        // Update cache to notify other users
        $this->updateLastActivityCache();

        // Update current user's feed immediately
        $this->updateRequirementWithNewReply($reply);

        $this->reset(['replyContent', 'replyImage', 'selectedRequirementId']);
        $this->showProposalForm = false;
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Reply posted successfully!']);
        Log::info('Posted Reply for Requirement ID: ' . $this->selectedRequirementId);
    }

    public function checkForUpdates()
    {
        Log::info('Checking for updates via polling');
        
        // Get the latest activity timestamp
        $latestActivity = Cache::get('feed_last_activity', $this->lastUpdated);
        
        if ($latestActivity > $this->lastUpdated) {
            Log::info('New activity detected, refreshing feed');
            
            // Store current requirement IDs and reply counts
            $currentRequirementIds = $this->requirements->pluck('id')->toArray();
            $currentReplyCounts = $this->requirements->mapWithKeys(function ($req) {
                return [$req->id => $req->replies->count()];
            })->toArray();
            
            // Reload requirements
            $this->loadRequirements();
            
            // Check for new requirements
            $newRequirementIds = $this->requirements->pluck('id')->diff($currentRequirementIds);
            if ($newRequirementIds->count() > 0) {
                $this->newRequirementsCount += $newRequirementIds->count();
                $newRequirement = $this->requirements->firstWhere('id', $newRequirementIds->first());
                if ($newRequirement) {
                    $this->dispatch('alert', [
                        'type' => 'info', 
                        'message' => 'New requirement posted by ' . $newRequirement->user->name . '!'
                    ]);
                }
            }
            
            // Check for new replies
            foreach ($this->requirements as $requirement) {
                $oldCount = $currentReplyCounts[$requirement->id] ?? 0;
                $newCount = $requirement->replies->count();
                if ($newCount > $oldCount) {
                    $this->newRepliesCount += ($newCount - $oldCount);
                    $latestReply = $requirement->replies->sortByDesc('created_at')->first();
                    if ($latestReply) {
                        $this->dispatch('alert', [
                            'type' => 'info', 
                            'message' => 'New reply from ' . $latestReply->user->name . '!'
                        ]);
                    }
                }
            }
            
            $this->lastUpdated = now();
            Log::info('Feed updated via polling', [
                'new_requirements' => $newRequirementIds->count(),
                'new_replies' => $this->newRepliesCount
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

    private function updateLastActivityCache()
    {
        // Update the last activity timestamp in cache
        Cache::put('feed_last_activity', now(), now()->addMinutes(60));
        Log::info('Updated last activity cache timestamp');
    }

    public function refreshFeed()
    {
        $this->loadRequirements();
        $this->lastUpdated = now();
        $this->newRequirementsCount = 0;
        $this->newRepliesCount = 0;
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