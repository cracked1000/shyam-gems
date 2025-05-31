<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->enum('message_type', ['text', 'image', 'file', 'system'])->default('text');
            $table->json('metadata')->nullable(); // For storing additional message data (e.g., reactions, formatting)
            $table->boolean('is_deleted_by_sender')->default(false);
            $table->boolean('is_deleted_by_receiver')->default(false);
            $table->timestamp('deleted_at')->nullable(); // For soft deletes
            $table->timestamps();

            // Indexes for better performance
            $table->index(['sender_id', 'receiver_id', 'created_at']);
            $table->index(['receiver_id', 'is_read']);
            $table->index(['created_at']);
        });

        // Create conversations table for better conversation management
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('last_message_id')->nullable()->constrained('messages')->onDelete('set null');
            $table->timestamp('last_activity_at')->useCurrent();
            $table->boolean('is_archived_by_user1')->default(false);
            $table->boolean('is_archived_by_user2')->default(false);
            $table->timestamps();

            // Ensure unique conversations between users (user1_id should always be less than user2_id)
            $table->unique(['user1_id', 'user2_id']);
            $table->index(['user1_id', 'last_activity_at']);
            $table->index(['user2_id', 'last_activity_at']);
        });

        // Add online status to users table if it doesn't exist
        if (!Schema::hasColumn('users', 'last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable();
                $table->boolean('is_online')->default(false);
                $table->index('last_seen_at'); // For faster online status queries
            });
        }

        // Create message_attachments table for future file sharing
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->onDelete('cascade');
            $table->string('filename'); // Unique filename on the server
            $table->string('original_filename'); // Original filename uploaded by the user
            $table->string('file_path'); // Path to the file in storage
            $table->string('file_type'); // e.g., image, document
            $table->unsignedBigInteger('file_size'); // File size in bytes
            $table->string('mime_type'); // MIME type of the file
            $table->timestamps();

            $table->index('message_id');
        });

        // Create user_message_preferences table for message settings
        Schema::create('user_message_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('sound_notifications')->default(true);
            $table->enum('online_status', ['online', 'away', 'busy', 'invisible'])->default('online');
            $table->unsignedInteger('auto_away_after')->nullable(); // Time in minutes before marking user as away
            $table->timestamps();

            $table->unique('user_id');
            $table->index('online_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_message_preferences');
        Schema::dropIfExists('message_attachments');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('messages');

        // Remove columns from users table
        if (Schema::hasColumn('users', 'last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['last_seen_at', 'is_online']);
            });
        }
    }
};