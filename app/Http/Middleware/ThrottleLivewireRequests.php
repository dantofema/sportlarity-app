<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLivewireRequests
{
    private const BLOCKED_USER_AGENTS = [
        'python-requests',
        'curl',
        'wget',
        'postman',
        'httpie',
        'axios',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('livewire/update')) {
            return $next($request);
        }

        // 1. Check User-Agent for bots
        if ($this->isBotUserAgent($request)) {
            $this->logBlockedRequest($request, 'bot_detected');

            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 2. Validate payload structure
        $validationResult = $this->validateLivewirePayload($request);
        if (! $validationResult['valid']) {
            $this->logBlockedRequest($request, 'invalid_payload', $validationResult['error']);

            // Apply stricter rate limiting for invalid requests
            if ($this->isRateLimitedForInvalidRequests($request)) {
                return response()->json(['message' => 'Too Many Requests'], 429);
            }

            return response()->json(['message' => 'Bad Request'], 400);
        }

        // 3. Apply normal rate limiting for valid requests
        if ($this->isRateLimited($request)) {
            return response()->json(['message' => 'Too Many Requests'], 429);
        }

        return $next($request);
    }

    private function isBotUserAgent(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        if (empty($userAgent)) {
            return true;
        }

        foreach (self::BLOCKED_USER_AGENTS as $blockedAgent) {
            if (str_contains($userAgent, $blockedAgent)) {
                return true;
            }
        }

        return false;
    }

    private function logBlockedRequest(Request $request, string $reason, ?string $error = null): void
    {
        $context = [
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->url(),
        ];

        if ($error !== null) {
            $context['error'] = $error;
        }

        // Log payload preview (limited to avoid huge logs)
        $content = $request->getContent();
        if (! empty($content)) {
            $context['payload_preview'] = substr($content, 0, 500);
        }

        Log::warning('Livewire request blocked', $context);
    }

    private function validateLivewirePayload(Request $request): array
    {
        $content = $request->getContent();

        // Check if valid JSON
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['valid' => false, 'error' => 'invalid_json'];
        }

        // Check for legacy Livewire format (fingerprint/serverMemo/updates)
        $hasLegacyFormat = isset($data['fingerprint']) && isset($data['serverMemo']) && isset($data['updates']);

        // Check for new Livewire format (components[])
        $hasNewFormat = isset($data['components']) && is_array($data['components']) && count($data['components']) > 0;

        if (! $hasLegacyFormat && ! $hasNewFormat) {
            return ['valid' => false, 'error' => 'missing_required_fields'];
        }

        // For legacy format, verify fingerprint has id
        if ($hasLegacyFormat && ! isset($data['fingerprint']['id'])) {
            return ['valid' => false, 'error' => 'missing_fingerprint_id'];
        }

        return ['valid' => true];
    }

    private function isRateLimitedForInvalidRequests(Request $request): bool
    {
        $key = 'livewire-invalid:'.($request->user()?->id ?: $request->ip());

        return ! RateLimiter::attempt($key, 10, function () {}, 60);
    }

    private function isRateLimited(Request $request): bool
    {
        $key = 'livewire:'.($request->user()?->id ?: $request->ip());

        if (RateLimiter::tooManyAttempts($key, 60)) {
            return true;
        }

        RateLimiter::hit($key);

        return false;
    }
}
