<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class PrivacyPolicyController extends Controller
{
    /**
     * Get privacy policy for mobile app.
     */
    public function index()
    {
        try {
            $policy = [
                'title' => 'Privacy Policy',
                'effective_date' => '01 October 2025',
                'last_updated' => now()->format('d F Y'),
                
                'sections' => [
                    [
                        'heading' => 'Introduction',
                        'content' => 'RISDA ("we," "us," or "our") is committed to protecting the privacy and security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use the JARA (Jejak Aset & Rekod Automotif) Mobile Application ("App"). Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the application.',
                    ],
                    [
                        'heading' => 'Information We Collect',
                        'content' => 'We collect information that you provide directly to us, including:',
                        'list' => [
                            'Personal identification information (name, employee number, IC number, email address, phone number)',
                            'Vehicle information (vehicle plate number, make, model, odometer readings)',
                            'Trip and journey data (GPS location, start/end times, distance traveled, fuel consumption)',
                            'Claim and expense information (receipts, claim amounts, categories)',
                            'Maintenance records (service dates, costs, service providers)',
                            'Photos and images (odometer photos, fuel receipts, claim receipts)',
                            'Device information (device type, operating system, unique device identifiers)',
                        ],
                    ],
                    [
                        'heading' => 'How We Use Your Information',
                        'content' => 'We use the information we collect to:',
                        'list' => [
                            'Provide, maintain, and improve our services',
                            'Process and manage trip records, claims, and maintenance schedules',
                            'Calculate mileage, fuel consumption, and overtime compensation',
                            'Generate reports and analytics for fleet management',
                            'Communicate with you about your account, trips, and claims',
                            'Ensure compliance with organizational policies and regulations',
                            'Detect, prevent, and address technical issues or fraudulent activities',
                            'Monitor and improve app performance and user experience',
                        ],
                    ],
                    [
                        'heading' => 'Data Storage and Security',
                        'content' => 'We implement appropriate technical and organizational security measures to protect your personal information, including:',
                        'list' => [
                            'Encrypted data transmission using industry-standard SSL/TLS protocols',
                            'Secure authentication with password hashing (Argon2id with email salt)',
                            'Role-based access control and multi-tenant architecture',
                            'Regular security audits and vulnerability assessments',
                            'Secure cloud hosting with backup and disaster recovery',
                            'Limited access to personal data on a need-to-know basis',
                        ],
                    ],
                    [
                        'heading' => 'Location Data',
                        'content' => 'The App collects precise location data to enable trip tracking, mileage calculation, and GPS-based features. Location data is collected only when you start a journey and when you end a journey. You can control location permissions through your device settings, but disabling location services may limit app functionality.',
                    ],
                    [
                        'heading' => 'Data Sharing and Disclosure',
                        'content' => 'We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:',
                        'list' => [
                            'With authorized RISDA personnel for fleet management and administrative purposes',
                            'With your supervisors or department heads for trip approval and oversight',
                            'When required by law, court order, or government regulations',
                            'To protect the rights, property, or safety of RISDA, our employees, or others',
                            'With your explicit consent for specific purposes',
                        ],
                    ],
                    [
                        'heading' => 'Data Retention',
                        'content' => 'We retain your personal information for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law. Trip records, claims, and maintenance data are retained for a minimum of 7 years for audit and compliance purposes.',
                    ],
                    [
                        'heading' => 'Your Rights and Choices',
                        'content' => 'You have the right to:',
                        'list' => [
                            'Access and review your personal information',
                            'Request correction of inaccurate or incomplete data',
                            'Request deletion of your data (subject to legal and operational requirements)',
                            'Withdraw consent for specific data processing activities',
                            'Lodge a complaint with the relevant data protection authority',
                        ],
                        'footer' => 'To exercise these rights, please contact us at prbsibu@risda.gov.my',
                    ],
                    [
                        'heading' => 'Children\'s Privacy',
                        'content' => 'The App is not intended for use by individuals under the age of 18. We do not knowingly collect personal information from children. If we become aware that we have collected personal information from a child, we will take steps to delete such information.',
                    ],
                    [
                        'heading' => 'Changes to This Privacy Policy',
                        'content' => 'We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy in the App and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted in the App.',
                    ],
                    [
                        'heading' => 'Contact Us',
                        'content' => 'If you have any questions or concerns about this Privacy Policy or our data practices, please contact us at:',
                        'contact' => [
                            'organization' => 'RISDA Bahagian Sibu',
                            'address' => [
                                'Pejabat RISDA Bahagian Sibu',
                                '49, Lorong 51, Jalan Lau King Howe',
                                '96000 Sibu, Sarawak, Malaysia',
                            ],
                            'phone' => '084-344712 / 084-344713',
                            'fax' => '084-322531',
                            'email' => 'prbsibu@risda.gov.my',
                            'website' => 'https://www.jara.com.my',
                        ],
                    ],
                ],
                
                'acknowledgment' => 'By using the JARA Mobile App, you acknowledge that you have read, understood, and agree to be bound by this Privacy Policy.',
            ];

            return response()->json([
                'success' => true,
                'data' => $policy,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch privacy policy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

