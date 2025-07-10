# Prism PHP - Quick Reference Guide

## Overview
Prism is a Laravel package that provides a unified interface for interacting with multiple AI providers (OpenAI, Anthropic, Gemini, etc.). When the user mentions "Prism", they are referring to this specific package.

## Installation
```bash
sail composer require prism-php/prism
```

## Configuration
1. Publish config: `sail artisan vendor:publish --tag=prism-config`
2. Add API keys to `.env`:
```env
ANTHROPIC_API_KEY=your-key-here
OPENAI_API_KEY=your-key-here
```

## Quick Usage Examples

### Text Generation
```php
use Prism\Prism;
use Prism\Enums\Provider;

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withPrompt('Hello!')
    ->asText();

echo $response->text;
```

### With System Prompt
```php
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4')
    ->withSystemPrompt('You are a helpful assistant')
    ->withPrompt('Explain quantum computing')
    ->asText();
```

### Message Chains
```php
use Prism\ValueObjects\Messages\UserMessage;
use Prism\ValueObjects\Messages\AssistantMessage;

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withMessages([
        new UserMessage('What is JSON?'),
        new AssistantMessage('JSON is a data format...'),
        new UserMessage('Show me an example')
    ])
    ->asText();
```

### Streaming
```php
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4')
    ->withPrompt('Tell me a story')
    ->asStream();

foreach ($response as $chunk) {
    echo $chunk->text;
    ob_flush();
    flush();
}
```

### Tools & Function Calling
```php
use Prism\Facades\Tool;

$weatherTool = Tool::as('weather')
    ->for('Get weather information')
    ->withStringParameter('city', 'City name')
    ->using(function (string $city): string {
        return "Weather in {$city}: Sunny, 72°F";
    });

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withMaxSteps(2)  // Important for tools!
    ->withTools([$weatherTool])
    ->withPrompt('What\'s the weather in Paris?')
    ->asText();
```

### Structured Output
```php
use Prism\Schema\ObjectSchema;
use Prism\Schema\StringSchema;

$schema = new ObjectSchema(
    name: 'review',
    description: 'Movie review',
    properties: [
        new StringSchema('title', 'Movie title'),
        new StringSchema('rating', 'Rating'),
        new StringSchema('summary', 'Review summary')
    ],
    requiredFields: ['title', 'rating', 'summary']
);

$response = Prism::structured()
    ->using(Provider::OpenAI, 'gpt-4o')
    ->withSchema($schema)
    ->withPrompt('Review the movie Inception')
    ->asStructured();

$review = $response->structured;
```

### Embeddings
```php
$response = Prism::embeddings()
    ->using(Provider::OpenAI, 'text-embedding-3-large')
    ->fromInput('Your text here')
    ->asEmbeddings();

$vector = $response->embeddings[0]->embedding;
```

### Image Generation
```php
$response = Prism::image()
    ->using('openai', 'dall-e-3')
    ->withPrompt('A cute baby sea otter')
    ->generate();

$image = $response->firstImage();
echo $image->url;
```

### Images in Messages
```php
use Prism\ValueObjects\Messages\Support\Image;

$message = new UserMessage(
    "What's in this image?",
    [Image::fromLocalPath('/path/to/image.jpg')]
);

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withMessages([$message])
    ->asText();
```

### Documents
```php
use Prism\ValueObjects\Messages\Support\Document;

$message = new UserMessage('Summarize this document', [
    Document::fromLocalPath('path/to/document.pdf', 'My Document')
]);
```

## Provider-Specific Features

### Anthropic
- **Prompt Caching**: `->withProviderOptions(['cacheType' => 'ephemeral'])`
- **Extended Thinking**: `->withProviderOptions(['thinking' => ['enabled' => true]])`
- **Citations**: `->withProviderOptions(['citations' => true])`
- **Tool Calling for Structured**: `->withProviderOptions(['use_tool_calling' => true])`

### OpenAI
- **Strict Mode**: `->withProviderOptions(['schema' => ['strict' => true]])`

## Common Parameters
- `withMaxTokens(1000)` - Limit response length
- `usingTemperature(0.7)` - Control randomness
- `usingTopP(0.9)` - Alternative to temperature
- `withClientOptions(['timeout' => 30])` - HTTP options
- `withClientRetry(3, 100)` - Retry configuration

## Testing
```php
use Prism\Testing\PrismFake;

$fake = PrismFake::create()->text();
Prism::fake($fake);

// Your test code here
```

## Important Notes
- Always use `sail` commands instead of direct `php` commands
- Use `pnpm` instead of `npm`
- For tools, always set `withMaxSteps(2)` or higher
- Different providers support different features - check compatibility
- Environment variables are loaded from `.env` file

## Current Configuration
- Anthropic API key is configured
- OpenAI API key needs to be added
- Configuration file: `config/prism.php`