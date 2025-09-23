<?php

// Test script untuk login dan access dashboard
$baseUrl = 'http://localhost:8000';

// Initialize curl session
$ch = curl_init();

// Set curl options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');

echo "🔐 Testing RISDA Login System...\n";
echo "================================\n\n";

// Step 1: Get login page and CSRF token
echo "1. Getting login page...\n";
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
$loginPage = curl_exec($ch);

if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
    echo "❌ Failed to get login page\n";
    exit(1);
}

// Extract CSRF token
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $loginPage, $matches);
$csrfToken = $matches[1] ?? '';

if (empty($csrfToken)) {
    echo "❌ Could not extract CSRF token\n";
    exit(1);
}

echo "✅ Login page loaded, CSRF token extracted\n";

// Step 2: Attempt login
echo "2. Attempting login...\n";
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $csrfToken,
    'email' => 'test@risda.gov.my',
    'password' => 'RisdaSecure123!',
]));

$loginResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 302) {
    echo "✅ Login successful (redirected)\n";
} else {
    echo "❌ Login failed (HTTP $httpCode)\n";
    echo "Response: " . substr($loginResponse, 0, 500) . "\n";
    exit(1);
}

// Step 3: Access dashboard
echo "3. Accessing dashboard...\n";
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');

$dashboardResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    echo "✅ Dashboard accessed successfully\n";
    
    // Check if dashboard contains expected content
    if (strpos($dashboardResponse, 'Dashboard') !== false) {
        echo "✅ Dashboard content loaded correctly\n";
    } else {
        echo "⚠️  Dashboard loaded but content may be incomplete\n";
    }
    
    // Check for any PHP errors
    if (strpos($dashboardResponse, 'Fatal error') !== false || 
        strpos($dashboardResponse, 'TypeError') !== false ||
        strpos($dashboardResponse, 'htmlspecialchars()') !== false) {
        echo "❌ PHP errors detected in dashboard\n";
        echo "Error snippet: " . substr($dashboardResponse, strpos($dashboardResponse, 'error'), 200) . "\n";
    } else {
        echo "✅ No PHP errors detected\n";
    }
    
} else {
    echo "❌ Dashboard access failed (HTTP $httpCode)\n";
    echo "Response: " . substr($dashboardResponse, 0, 500) . "\n";
}

// Cleanup
curl_close($ch);
unlink('/tmp/cookies.txt');

echo "\n🎯 Test completed!\n";
