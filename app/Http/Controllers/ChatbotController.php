<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\Chatbot\HelpdeskService;
use App\Services\Chatbot\KnowledgeBaseService;
use App\Services\Chatbot\NlpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function index(Request $request): View
    {
        return view('chatbot.index');
    }

    public function start(Request $request): JsonResponse
    {
        $conversation = Conversation::create([
            'channel' => 'web',
            'language' => 'en',
            'location' => $request->string('location')->toString() ?: null,
            'status' => 'open',
            'metadata' => [
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ],
        ]);
        return response()->json(['conversation_id' => $conversation->id]);
    }

    public function message(Request $request, NlpService $nlp, KnowledgeBaseService $kb, HelpdeskService $helpdesk): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'text' => 'required|string',
        ]);

        $conversation = Conversation::findOrFail($request->integer('conversation_id'));
        $userText = $request->string('text')->toString();

        $detected = $nlp->detectLanguage($userText);
        if ($detected !== 'auto') {
            $conversation->language = $detected;
            $conversation->save();
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'content' => $userText,
            'language' => $conversation->language,
        ]);

        // Try knowledge base first
        $article = $kb->search($userText, $conversation->language, $conversation->location);
        if ($article) {
            $botReply = $nlp->translate($article->content, $conversation->language);
            Message::create([
                'conversation_id' => $conversation->id,
                'sender' => 'bot',
                'content' => $botReply,
                'language' => $conversation->language,
            ]);
            return response()->json(['answer' => $botReply, 'source' => 'kb', 'article' => [
                'title' => $article->title,
                'slug' => $article->slug,
            ]]);
        }

        // Fallback: escalate if not found
        $summary = 'Guest asked: ' . $userText;
        $externalId = $helpdesk->createTicketForConversation($conversation, $summary);
        $handoffText = $nlp->translate('I am connecting you with a live agent for further assistance. Ticket: ' . $externalId, $conversation->language);
        Message::create([
            'conversation_id' => $conversation->id,
            'sender' => 'bot',
            'content' => $handoffText,
            'language' => $conversation->language,
        ]);

        return response()->json([
            'answer' => $nlp->translate('I am connecting you with a live agent for further assistance.', $conversation->language),
            'handoff' => true,
            'ticket' => $externalId,
        ]);
    }
}

