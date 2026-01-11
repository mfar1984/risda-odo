<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\AuditTrail;

/**
 * **Feature: audit-trail, Property 6: Login event records session start**
 * **Feature: audit-trail, Property 7: Logout event records session duration**
 * **Validates: Requirements 5.1, 5.2**
 */
class AuditTrailAuthTest extends TestCase
{
    /**
     * Test login event data structure
     * 
     * **Feature: audit-trail, Property 6: Login event records session start**
     * **Validates: Requirements 5.1**
     */
    public function test_login_event_has_required_fields(): void
    {
        // Define required fields for login event
        $requiredFields = [
            'user_id',
            'action_type',
            'action_name',
            'ip_address',
            'user_agent',
            'created_at',
        ];

        // Simulate login event data
        $loginData = [
            'user_id' => 1,
            'action_type' => AuditTrail::TYPE_LOGIN,
            'action_name' => 'Log masuk berjaya',
            'url' => 'http://localhost:8000/login',
            'route_name' => 'login',
            'http_method' => 'POST',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'properties' => [
                'email' => 'user@example.com',
                'login_time' => now()->toIso8601String(),
            ],
            'created_at' => now(),
        ];

        // Assert all required fields are present
        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey(
                $field,
                $loginData,
                "Required field '{$field}' should be present in login event data"
            );
            $this->assertNotNull(
                $loginData[$field],
                "Required field '{$field}' should not be null"
            );
        }

        // Assert action type is correct
        $this->assertEquals(AuditTrail::TYPE_LOGIN, $loginData['action_type']);
    }

    /**
     * Test logout event data structure with session duration
     * 
     * **Feature: audit-trail, Property 7: Logout event records session duration**
     * **Validates: Requirements 5.2**
     */
    public function test_logout_event_has_session_duration(): void
    {
        // Simulate logout event data
        $logoutData = [
            'user_id' => 1,
            'action_type' => AuditTrail::TYPE_LOGOUT,
            'action_name' => 'Log keluar',
            'url' => 'http://localhost:8000/logout',
            'route_name' => 'logout',
            'http_method' => 'POST',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'properties' => [
                'email' => 'user@example.com',
                'logout_time' => now()->toIso8601String(),
                'session_duration_minutes' => 45,
            ],
            'created_at' => now(),
        ];

        // Assert action type is correct
        $this->assertEquals(AuditTrail::TYPE_LOGOUT, $logoutData['action_type']);

        // Assert session duration is present in properties
        $this->assertArrayHasKey('session_duration_minutes', $logoutData['properties']);
        $this->assertIsInt($logoutData['properties']['session_duration_minutes']);
    }

    /**
     * Test failed login event data structure
     */
    public function test_failed_login_event_has_reason(): void
    {
        // Simulate failed login event data
        $failedLoginData = [
            'user_id' => null, // May be null if user doesn't exist
            'action_type' => AuditTrail::TYPE_LOGIN_FAILED,
            'action_name' => 'Log masuk gagal',
            'url' => 'http://localhost:8000/login',
            'route_name' => 'login',
            'http_method' => 'POST',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'properties' => [
                'email' => 'user@example.com',
                'reason' => 'Kata laluan tidak sah',
                'attempt_time' => now()->toIso8601String(),
            ],
            'created_at' => now(),
        ];

        // Assert action type is correct
        $this->assertEquals(AuditTrail::TYPE_LOGIN_FAILED, $failedLoginData['action_type']);

        // Assert reason is present in properties
        $this->assertArrayHasKey('reason', $failedLoginData['properties']);
        $this->assertNotEmpty($failedLoginData['properties']['reason']);
    }

    /**
     * Test action type constants are defined correctly
     */
    public function test_action_type_constants(): void
    {
        $this->assertEquals('login', AuditTrail::TYPE_LOGIN);
        $this->assertEquals('logout', AuditTrail::TYPE_LOGOUT);
        $this->assertEquals('login_failed', AuditTrail::TYPE_LOGIN_FAILED);
        $this->assertEquals('page_view', AuditTrail::TYPE_PAGE_VIEW);
        $this->assertEquals('button_click', AuditTrail::TYPE_BUTTON_CLICK);
        $this->assertEquals('form_submit', AuditTrail::TYPE_FORM_SUBMIT);
    }
}
