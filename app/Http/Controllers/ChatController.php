<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\SystemMessage;

use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
	public function index()
	{
		return inertia('Chat/Index');
	}

	public function getConversations()
	{
		return Auth::user()->conversations()
			->with(['messages', 'profile'])
			->orderBy('updated_at', 'desc')
			->get();
	}

	public function createConversation(Request $request)
	{
		$validated = $request->validate([
			'title' => 'nullable|string|max:255',
			'model' => 'required|string',
			'provider' => 'required|string',
			'profile_id' => 'nullable|exists:profiles,id',
		]);

		$conversation = Auth::user()->conversations()->create([
			'title' => $validated['title'] ?? 'New Conversation',
			'model' => $validated['model'],
			'provider' => $validated['provider'],
			'profile_id' => $validated['profile_id'] ?? null,
		]);

		return response()->json($conversation->load(['messages', 'profile']));
	}

	public function getConversation($id)
	{
		$conversation = Auth::user()->conversations()
			->with(['messages', 'profile'])
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
		$systemPrompt = null;
		
		// If this is the first message and we have a profile, add the personality context
		if ($conversation->messages()->count() === 1 && $conversation->profile_id) {
			$profile = Profile::find($conversation->profile_id);
			if ($profile && $profile->status === 'completed') {
				$personalityContext = $profile->generateChatProfile();
				// For Anthropic, we'll use withSystemPrompt instead of SystemMessage
				$systemPrompt = $personalityContext;
			}
		}
		
		foreach ($conversation->messages()->orderBy('created_at')->get() as $msg) {
			if ($msg->role === 'user') {
				$messages[] = new UserMessage($msg->content);
			} elseif ($msg->role === 'assistant') {
				$messages[] = new AssistantMessage($msg->content);
			}
		}

		// Generate AI response using streaming
		return response()->eventStream(function () use ($conversation, $messages, $systemPrompt) {
			$fullResponse = '';

			try {
				$provider = $this->getProvider($conversation->provider);

				$prism = Prism::text()
					->using($provider, $conversation->model)
					->withMessages($messages);

				// Add system prompt if available
				if ($systemPrompt) {
					$prism = $prism->withSystemPrompt($systemPrompt);
				}

				$stream = $prism->asStream();

				// Send start event
				yield "event: start\n";
				yield "data: " . json_encode(['type' => 'start']) . "\n\n";

				foreach ($stream as $chunk) {
					ray($chunk);
					
					// Skip Meta chunks as they contain the full accumulated text
					if ($chunk->chunkType && $chunk->chunkType->value === 'meta') {
						continue;
					}
					
					// Only process Text chunks which contain incremental content
					if ($chunk->chunkType && $chunk->chunkType->value === 'text') {
						$chunkText = $chunk->text;
						$fullResponse .= $chunkText;

						// Send chunk event with only the new text
						yield "event: chunk\n";
						yield "data: " . json_encode([
								'type' => 'chunk',
								'content' => $chunkText
							]) . "\n\n";
					}
				}

				// Save assistant response
				$assistantMessage = $conversation->messages()->create([
					'role' => 'assistant',
					'content' => $fullResponse,
				]);

				// Generate conversation title after first exchange if it's still "New Conversation"
				$conversationTitle = null;
				if ($conversation->title === 'New Conversation' && $conversation->messages()->count() === 2) {
					$conversationTitle = $this->generateConversationTitle($conversation, $fullResponse);
					$conversation->update(['title' => $conversationTitle]);
				}

				// Send end event con el mensaje completo guardado
				yield "event: end\n";
				yield "data: " . json_encode([
						'type' => 'end',
						'message_id' => $assistantMessage->id,
						'conversation_title' => $conversationTitle
					]) . "\n\n";

			} catch (\Exception $e) {
				Log::error('Error in chat streaming', [
					'error' => $e->getMessage(),
					'conversation_id' => $conversation->id
				]);

				// Send error event
				yield "event: error\n";
				yield "data: " . json_encode([
						'type' => 'error',
						'error' => $e->getMessage()
					]) . "\n\n";
			}
		}, endStreamWith: null); // Disable the default end stream message
	}

	private function getProvider(string $provider): Provider
	{
		return match($provider) {
			'openai' => Provider::OpenAI,
			'anthropic' => Provider::Anthropic,
			default => Provider::Anthropic,
		};
	}

	public function getAvailableProfiles()
	{
		$profiles = Auth::user()->profiles()
			->where('status', 'completed')
			->select('id', 'name', 'full_name')
			->get()
			->map(function ($profile) {
				return [
					'id' => $profile->id,
					'name' => $profile->full_name ?: $profile->name,
				];
			});

		return response()->json($profiles);
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

	private function generateConversationTitle(Conversation $conversation, string $assistantResponse): string
	{
		try {
			// Get the first user message
			$firstUserMessage = $conversation->messages()
				->where('role', 'user')
				->orderBy('created_at')
				->first();

			if (!$firstUserMessage) {
				return 'New Conversation';
			}

			$provider = $this->getProvider($conversation->provider);

			// Create a prompt to generate a concise title in the same language as the conversation
			$titlePrompt = new UserMessage(
				"Based on this conversation, generate a concise, descriptive title in the SAME LANGUAGE as the conversation (maximum 5 words). Do not translate, use the conversation's language:\n\n" .
				"User: {$firstUserMessage->content}\n" .
				"Assistant: {$assistantResponse}\n\n" .
				"Generate title in the same language:"
			);

			$titleResponse = Prism::text()
				->using($provider, $conversation->model)
				->withMessages([$titlePrompt])
				->generate();

			// Clean up the response and limit length
			$title = trim(str_replace(['"', "'", 'Title:', 'title:', '\n'], '', $titleResponse->text));
			$title = substr($title, 0, 50); // Limit to 50 characters

			return $title ?: 'New Conversation';

		} catch (\Exception $e) {
			Log::error('Failed to generate conversation title', [
				'error' => $e->getMessage(),
				'conversation_id' => $conversation->id
			]);
			
			return 'New Conversation';
		}
	}
}