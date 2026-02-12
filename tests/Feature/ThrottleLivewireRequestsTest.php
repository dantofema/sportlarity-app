<?php

use Illuminate\Support\Facades\Log;

uses()->group('security');

beforeEach(function () {
    // Disable CSRF protection for testing
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
});

describe('bot detection', function () {
    test('blocks requests with python-requests user agent', function () {
        Log::spy();

        $response = $this->post('/livewire/update', [], [
            'User-Agent' => 'python-requests/2.32.5',
            'X-Livewire' => 'true',
        ]);

        $response->assertStatus(403);
        Log::shouldHaveReceived('warning')
            ->with('Livewire request blocked', \Mockery::on(function ($context) {
                return $context['reason'] === 'bot_detected';
            }));
    });

    test('blocks requests with curl user agent', function () {
        Log::spy();

        $response = $this->post('/livewire/update', [], [
            'User-Agent' => 'curl/7.68.0',
        ]);

        $response->assertStatus(403);
    });

    test('blocks requests with wget user agent', function () {
        Log::spy();

        $response = $this->post('/livewire/update', [], [
            'User-Agent' => 'Wget/1.20.3',
        ]);

        $response->assertStatus(403);
    });

    test('blocks requests with empty user agent', function () {
        Log::spy();

        $response = $this->post('/livewire/update', [], [
            'User-Agent' => '',
        ]);

        $response->assertStatus(403);
    });

    test('allows requests with legitimate browser user agent', function () {
        $response = $this->postJson('/livewire/update', [
            'fingerprint' => ['id' => 'test123'],
            'serverMemo' => [],
            'updates' => [],
        ], [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        // Should not be blocked by bot detection (may fail later due to other validation)
        $this->assertNotEquals(403, $response->getStatusCode());
    });
});

describe('payload validation', function () {
    test('rejects invalid json payload', function () {
        Log::spy();

        $response = $this->call('POST', '/livewire/update', [], [], [], [
            'HTTP_User-Agent' => 'Mozilla/5.0',
            'CONTENT_TYPE' => 'application/json',
        ], 'not valid json');

        $response->assertStatus(400);
        Log::shouldHaveReceived('warning')
            ->with('Livewire request blocked', \Mockery::on(function ($context) {
                return $context['reason'] === 'invalid_payload' && $context['error'] === 'invalid_json';
            }));
    });

    test('rejects payload missing required fields', function () {
        Log::spy();

        $response = $this->postJson('/livewire/update', [
            '_nightwatch_error' => 'NOT_ENABLED',
        ], [
            'User-Agent' => 'Mozilla/5.0',
        ]);

        $response->assertStatus(400);
        Log::shouldHaveReceived('warning')
            ->with('Livewire request blocked', \Mockery::on(function ($context) {
                return $context['reason'] === 'invalid_payload' && $context['error'] === 'missing_required_fields';
            }));
    });

    test('rejects legacy format without fingerprint id', function () {
        Log::spy();

        $response = $this->postJson('/livewire/update', [
            'fingerprint' => [],
            'serverMemo' => [],
            'updates' => [],
        ], [
            'User-Agent' => 'Mozilla/5.0',
        ]);

        $response->assertStatus(400);
        Log::shouldHaveReceived('warning')
            ->with('Livewire request blocked', \Mockery::on(function ($context) {
                return $context['reason'] === 'invalid_payload' && $context['error'] === 'missing_fingerprint_id';
            }));
    });

    test('accepts valid legacy format payload', function () {
        $response = $this->postJson('/livewire/update', [
            'fingerprint' => ['id' => 'test-component'],
            'serverMemo' => [],
            'updates' => [],
        ], [
            'User-Agent' => 'Mozilla/5.0',
        ]);

        // Should not be blocked (may fail later due to other reasons like CSRF, but that's ok)
        $this->assertNotEquals(400, $response->getStatusCode());
    });

    test('accepts valid new format payload', function () {
        $response = $this->postJson('/livewire/update', [
            'components' => [
                [
                    'snapshot' => 'test',
                    'updates' => [],
                ],
            ],
        ], [
            'User-Agent' => 'Mozilla/5.0',
        ]);

        // Should not be blocked (may fail later due to other reasons like CSRF, but that's ok)
        $this->assertNotEquals(400, $response->getStatusCode());
    });
});

describe('rate limiting', function () {
    test('rate limits invalid requests', function () {
        // Make 10 requests that will be marked as invalid
        for ($i = 0; $i < 11; $i++) {
            $response = $this->postJson('/livewire/update', [
                '_nightwatch_error' => 'NOT_ENABLED',
            ], [
                'User-Agent' => 'Mozilla/5.0',
            ]);

            if ($i < 10) {
                $response->assertStatus(400);
            } else {
                // After 10 invalid requests, should be rate limited
                $response->assertStatus(429);
            }
        }
    });
});

describe('logging', function () {
    test('logs bot detection with full context', function () {
        Log::spy();

        $this->post('/livewire/update', [], [
            'User-Agent' => 'python-requests/2.32.5',
        ]);

        Log::shouldHaveReceived('warning')
            ->with('Livewire request blocked', \Mockery::on(function ($context) {
                return isset($context['ip']) &&
                       isset($context['user_agent']) &&
                       isset($context['method']) &&
                       isset($context['url']) &&
                       $context['reason'] === 'bot_detected';
            }));
    });

    test('logs invalid payload with payload preview', function () {
        Log::spy();

        $this->postJson('/livewire/update', [
            '_nightwatch_error' => 'NOT_ENABLED',
        ], [
            'User-Agent' => 'Mozilla/5.0',
        ]);

        Log::shouldHaveReceived('warning')
            ->with('Livewire request blocked', \Mockery::on(function ($context) {
                return isset($context['payload_preview']) &&
                       $context['reason'] === 'invalid_payload';
            }));
    });
});
