<?php

namespace App\Services\Chatbot;

use App\Models\Conversation;

class HelpdeskService
{
    public function createTicketForConversation(Conversation $conversation, string $summary): string
    {
        // Placeholder: call helpdesk API, return external ticket ID
        $externalId = 'TCK-' . now()->timestamp . '-' . $conversation->id;
        $conversation->update(['external_id' => $externalId, 'status' => 'pending_agent']);
        return $externalId;
    }

    public function notifyAgent(string $externalId, string $message): void
    {
        // Placeholder for webhook/notification
    }
}


