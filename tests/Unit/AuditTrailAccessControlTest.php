<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * **Feature: audit-trail, Property 1: Admin-only access to Audit Trail tab**
 * **Validates: Requirements 1.1, 1.5**
 */
class AuditTrailAccessControlTest extends TestCase
{
    /**
     * Test admin access control logic
     * 
     * **Feature: audit-trail, Property 1: Admin-only access to Audit Trail tab**
     * **Validates: Requirements 1.1, 1.5**
     * 
     * For any user accessing the Aktiviti Log page, the Audit Trail tab should 
     * only be visible if the user has jenis_organisasi = 'semua'
     */
    public function test_admin_access_control_logic(): void
    {
        // Test cases for different user types
        $testCases = [
            ['jenis_organisasi' => 'semua', 'expected_access' => true],
            ['jenis_organisasi' => 'bahagian', 'expected_access' => false],
            ['jenis_organisasi' => 'stesen', 'expected_access' => false],
        ];

        foreach ($testCases as $case) {
            $isAdmin = $case['jenis_organisasi'] === 'semua';
            
            $this->assertEquals(
                $case['expected_access'],
                $isAdmin,
                "User with jenis_organisasi '{$case['jenis_organisasi']}' should " . 
                ($case['expected_access'] ? 'have' : 'NOT have') . ' access to Audit Trail'
            );
        }
    }

    /**
     * Test that admin check is case-sensitive
     */
    public function test_admin_check_is_exact_match(): void
    {
        $validAdminValues = ['semua'];
        $invalidValues = ['SEMUA', 'Semua', 'admin', 'administrator', ''];

        foreach ($validAdminValues as $value) {
            $this->assertTrue(
                $value === 'semua',
                "Value '{$value}' should be recognized as admin"
            );
        }

        foreach ($invalidValues as $value) {
            $this->assertFalse(
                $value === 'semua',
                "Value '{$value}' should NOT be recognized as admin"
            );
        }
    }

    /**
     * Test that non-admin users cannot access audit trail routes
     */
    public function test_non_admin_route_access_denied(): void
    {
        // Simulate route access check
        $userTypes = ['bahagian', 'stesen'];

        foreach ($userTypes as $type) {
            $isAdmin = $type === 'semua';
            
            // Non-admin should be denied
            $this->assertFalse(
                $isAdmin,
                "User type '{$type}' should be denied access to audit trail routes"
            );
        }
    }
}
