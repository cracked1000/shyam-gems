<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class Messages extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedUser = null;
    public $selectedUserId = null;
    public $messageContent = '';
    public $conversationMessages = [];
    public $newMessageMode = false;
    public $newMessageSearch = '';
    public $unreadCount = 0;

    protected $rules = [
        'messageContent' => 'required|string|max:1000',
    ];

    protected function messages()
    {
        return [
            'messageContent.required' => 'Please enter a message.',
            'messageContent.max' => 'Message cannot exceed 1000 characters.',
        ];
    }

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login.show');
        }
        $this->resetPage();
        $this->updateUnreadCount();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedNewMessageSearch()
    {
        // No pagination reset needed for new message search
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function selectUser($userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                $this->dispatch('show-error', message: 'User not found.');
                return;
            }
            $this->selectedUser = $user;
            $this->selectedUserId = $userId;
            $this->newMessageMode = false;
            $this->loadMessages();
            $this->reset(['messageContent', 'newMessageSearch']);
            $this->markMessagesAsRead();
            $this->updateUnreadCount();
            $this->dispatch('scrollToBottom');
        } catch (\Exception $e) {
            \Log::error('Error selecting user: ' . $e->getMessage());
            $this->dispatch('show-error', message: 'Error selecting user.');
        }
    }

    public function startNewMessage()
    {
        $this->newMessageMode = true;
        $this->selectedUser = null;
        $this->selectedUserId = null;
        $this->reset(['messageContent', 'newMessageSearch']);
        $this->conversationMessages = collect([]);
    }

    public function loadMessages()
    {
        try {
            if ($this->selectedUser) {
                $this->conversationMessages = Message::betweenUsers(Auth::id(), $this->selectedUser->id)
                    ->notDeletedByUser(Auth::id())
                    ->with(['sender', 'receiver'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                $this->conversationMessages = collect([]);
            }
        } catch (\Exception $e) {
            \Log::error('Error loading messages: ' . $e->getMessage());
            $this->conversationMessages = collect([]);
            $this->dispatch('show-error', message: 'Error loading messages.');
        }
    }

    public function sendMessage()
    {
        try {
            $this->validate();

            if (!$this->selectedUser) {
                $this->dispatch('show-error', message: 'Please select a user to send message to.');
                return;
            }

            $this->messageContent = trim($this->messageContent);

            if (empty($this->messageContent)) {
                $this->addError('messageContent', 'Message cannot be empty.');
                return;
            }

            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $this->selectedUser->id,
                'content' => $this->messageContent,
            ]);

            if (!$message || !$message->exists) {
                \Log::error('Message creation failed', [
                    'sender_id' => Auth::id(),
                    'receiver_id' => $this->selectedUser->id,
                    'content' => $this->messageContent,
                ]);
                $this->dispatch('show-error', message: 'Failed to send message.');
                return;
            }

            // Load relationships for the message
            $message->load(['sender', 'receiver']);
            
            // Update the sender's UI immediately
            $this->conversationMessages->push($message);
            $this->reset('messageContent');

            // Broadcast the new message
            event(new \App\Events\MessageSent($message));

            $this->dispatch('show-success', message: 'Message sent successfully!');
            $this->dispatch('scrollToBottom');
            
        } catch (ValidationException $e) {
            \Log::warning('Validation failed in sendMessage', ['errors' => $e->errors()]);
            // Let Livewire handle validation exceptions
        } catch (\Exception $e) {
            \Log::error('Error sending message', ['exception' => $e->getMessage()]);
            $this->dispatch('show-error', message: 'Error sending message. Please try again.');
        }
    }

    public function deleteMessage($messageId)
    {
        try {
            $message = Message::find($messageId);
            if (!$message) {
                $this->dispatch('show-error', message: 'Message not found.');
                return;
            }
            if ($message->sender_id !== Auth::id()) {
                $this->dispatch('show-error', message: 'You can only delete your own messages.');
                return;
            }
            $message->deleteForUser(Auth::id());
            $this->loadMessages();
            $this->dispatch('show-success', message: 'Message deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting message: ' . $e->getMessage());
            $this->dispatch('show-error', message: 'Error deleting message.');
        }
    }

    public function markMessagesAsRead()
    {
        try {
            if ($this->selectedUser) {
                Message::betweenUsers(Auth::id(), $this->selectedUser->id)
                    ->unreadForUser(Auth::id())
                    ->get()
                    ->each(function ($message) {
                        $message->markAsRead();
                    });
            }
        } catch (\Exception $e) {
            \Log::error('Error marking messages as read: ' . $e->getMessage());
        }
    }

    public function updateUnreadCount()
    {
        try {
            $this->unreadCount = Message::unreadForUser(Auth::id())->count();
        } catch (\Exception $e) {
            \Log::error('Error updating unread count: ' . $e->getMessage());
            $this->unreadCount = 0;
        }
    }

    public function render()
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login.show');
            }

            $conversations = \App\Models\Conversation::getForUser(Auth::id(), 10);
            $conversations = $conversations instanceof Collection ? $conversations : collect($conversations);

            if ($this->search) {
                $searchTerm = strtolower(trim($this->search));
                $conversations = $conversations->filter(function ($conversation) use ($searchTerm) {
                    if (!isset($conversation->other_user)) {
                        return false;
                    }
                    $otherUser = $conversation->other_user;
                    return str_contains(strtolower($otherUser->first_name ?? ''), $searchTerm) ||
                           str_contains(strtolower($otherUser->last_name ?? ''), $searchTerm) ||
                           str_contains(strtolower($otherUser->username ?? ''), $searchTerm);
                });
            }

            $newMessageUsers = collect([]);
            if ($this->newMessageMode) {
                $query = User::where('id', '!=', Auth::id());
                if ($this->newMessageSearch) {
                    $searchTerm = trim($this->newMessageSearch);
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('first_name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('username', 'like', '%' . $searchTerm . '%');
                    });
                }
                $newMessageUsers = $query->limit(20)->get();
            }

            return view('livewire.messages', [
                'users' => $conversations,
                'newMessageUsers' => $newMessageUsers,
            ])->layout('components.layouts.app')->title('Messages');
        } catch (\Exception $e) {
            \Log::error('Render error: ' . $e->getMessage(), ['exception' => $e]);
            return view('livewire.messages', [
                'users' => collect([]),
                'newMessageUsers' => collect([]),
            ])->layout('components.layouts.app')->title('Messages');
        }
    }

    public function getListeners()
    {
        return [
            'echo:messages.' . Auth::id() . ',MessageSent' => 'messageReceived',
            'refreshMessages' => 'refreshMessages',
            'messageReceived' => 'messageReceived',
        ];
    }

    public function messageReceived($event)
    {
        \Log::debug('MessageReceived event triggered', ['event' => $event]);
        
        try {
            // Handle both direct event format and wrapped format
            $messageData = isset($event['message']) ? $event['message'] : $event;
            
            if (!isset($messageData['id'])) {
                \Log::error('Invalid message data received', ['data' => $messageData]);
                return;
            }

            // Create a Message model instance from the data
            $message = new Message($messageData);
            $message->exists = true;
            
            // Load relationships manually
            $message->sender = User::find($messageData['sender_id']);
            $message->receiver = User::find($messageData['receiver_id']);
            
            // If we have a selected user and the message is from/to them, update the conversation
            if ($this->selectedUser) {
                $senderId = $messageData['sender_id'] ?? null;
                $receiverId = $messageData['receiver_id'] ?? null;
                
                // Check if the message involves the currently selected user
                if (($senderId == $this->selectedUser->id && $receiverId == Auth::id()) ||
                    ($senderId == Auth::id() && $receiverId == $this->selectedUser->id)) {
                    
                    // Check if message already exists in our collection
                    $existingMessage = $this->conversationMessages->firstWhere('id', $message->id);
                    if (!$existingMessage) {
                        $this->conversationMessages->push($message);
                        $this->dispatch('scrollToBottom');
                    }
                }
            }

            // Always update unread count
            $this->updateUnreadCount();
            
        } catch (\Exception $e) {
            \Log::error('Error handling received message', [
                'error' => $e->getMessage(),
                'event' => $event
            ]);
        }
    }

    public function refreshMessages()
    {
        $this->loadMessages();
        $this->updateUnreadCount();
    }

    public function getFormattedContent($content)
    {
        return nl2br(e($content));
    }
}