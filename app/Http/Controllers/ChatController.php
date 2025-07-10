<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;

class ChatController extends Controller
{
    public function index()
    {
        return inertia('Chat/Index');
    }

    public function getConversations()
    {
        return Auth::user()->conversations()
            ->with('messages')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function createConversation(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'model' => 'required|string',
            'provider' => 'required|string',
        ]);

        $conversation = Auth::user()->conversations()->create([
            'title' => $validated['title'] ?? 'New Conversation',
            'model' => $validated['model'],
            'provider' => $validated['provider'],
        ]);

        return response()->json($conversation->load('messages'));
    }

    public function getConversation($id)
    {
        $conversation = Auth::user()->conversations()
            ->with('messages')
            ->findOrFail($id);

        return response()->json($conversation);
    }

    public function updateConversation(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'model' => 'nullable|string',
            'provider' => 'nullable|string',
        ]);

        $conversation = Auth::user()->conversations()->findOrFail($id);
        $conversation->update($validated);

        return response()->json($conversation);
    }

    public function deleteConversation($id)
    {
        $conversation = Auth::user()->conversations()->findOrFail($id);
        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }

    public function sendMessage(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $conversation = Auth::user()->conversations()->findOrFail($id);

        // Save user message
        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $validated['content'],
        ]);

        // Build message history for context
        $messages = [];
        foreach ($conversation->messages()->orderBy('created_at')->get() as $msg) {
            if ($msg->role === 'user') {
                $messages[] = new UserMessage($msg->content);
            } elseif ($msg->role === 'assistant') {
                $messages[] = new AssistantMessage($msg->content);
            }
        }

        // Generate AI response using streaming
        return response()->stream(function () use ($conversation, $messages) {
            $fullResponse = '';
            
            try {
                $provider = $this->getProvider($conversation->provider);
                
                $stream = Prism::text()
                    ->using($provider, $conversation->model)
                    ->withMessages($messages)
                    ->asStream();

                echo "data: " . json_encode(['type' => 'start']) . "\n\n";
                ob_flush();
                flush();

                foreach ($stream as $chunk) {
                    $fullResponse .= $chunk->text;
                    
                    $data = [
                        'type' => 'chunk',
                        'content' => $chunk->text,
                    ];
                    
                    echo "data: " . json_encode($data) . "\n\n";
                    ob_flush();
                    flush();
                }

                // Save assistant response
                $conversation->messages()->create([
                    'role' => 'assistant',
                    'content' => $fullResponse,
                ]);

                echo "data: " . json_encode(['type' => 'end']) . "\n\n";
                ob_flush();
                flush();
            } catch (\Exception $e) {
                echo "data: " . json_encode([
                    'type' => 'error',
                    'error' => $e->getMessage()
                ]) . "\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function getProvider(string $provider): Provider
    {
        return match($provider) {
            'openai' => Provider::OpenAI,
            'anthropic' => Provider::Anthropic,
            default => Provider::Anthropic,
        };
    }

    public function getAvailableModels()
    {
        return response()->json([
            'models' => [
                [
                    'provider' => 'anthropic',
                    'models' => [
                        ['id' => 'claude-3-5-sonnet-20241022', 'name' => 'Claude 3.5 Sonnet'],
                        ['id' => 'claude-3-5-haiku-20241022', 'name' => 'Claude 3.5 Haiku'],
                        ['id' => 'claude-3-opus-20240229', 'name' => 'Claude 3 Opus'],
                        ['id' => 'claude-3-sonnet-20240229', 'name' => 'Claude 3 Sonnet'],
                        ['id' => 'claude-3-haiku-20240307', 'name' => 'Claude 3 Haiku'],
                    ]
                ],
                [
                    'provider' => 'openai',
                    'models' => [
                        ['id' => 'gpt-4o', 'name' => 'GPT-4o'],
                        ['id' => 'gpt-4o-mini', 'name' => 'GPT-4o Mini'],
                        ['id' => 'gpt-4-turbo', 'name' => 'GPT-4 Turbo'],
                        ['id' => 'gpt-4', 'name' => 'GPT-4'],
                        ['id' => 'gpt-3.5-turbo', 'name' => 'GPT-3.5 Turbo'],
                    ]
                ]
            ]
        ]);
    }
}