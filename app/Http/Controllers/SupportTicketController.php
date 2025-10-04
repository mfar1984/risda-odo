<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Display support tickets list (UI Mockup with dummy data)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Pagination: 5 tickets per page
        $perPage = 5;
        
        // Dummy data for UI showcase with pagination
        $driverTickets = $this->getDummyDriverTicketsPaginated($request, $perPage);
        $myTickets = $this->getDummyMyTicketsPaginated($request, $perPage);
        $allTickets = $this->getDummyAllTicketsPaginated($request, $perPage);
        
        $stats = [
            'baru' => 3,
            'dalam_proses' => 5,
            'urgent' => 2,
            'selesai' => 45,
        ];
        
        $adminStats = [
            'escalated' => 8,
            'staff' => 3,
            'driver' => 156,
            'today_resolved' => 23,
        ];
        
        return view('help.hubungi-sokongan', compact(
            'user',
            'driverTickets',
            'myTickets',
            'allTickets',
            'stats',
            'adminStats'
        ));
    }
    
    /**
     * Get dummy driver tickets (for staff view)
     */
    private function getDummyDriverTickets()
    {
        return [
            (object)[
                'ticket_number' => 'TICKET-0001',
                'subject' => 'Tak boleh login di aplikasi mobile',
                'status' => 'baru',
                'priority' => 'tinggi',
                'category' => 'Teknikal',
                'created_by' => 'fairiz@jara.my',
                'created_by_name' => 'Fairiz Ahmad',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '2 jam lalu',
                'message_count' => 3,
                'is_escalated' => false,
            ],
            (object)[
                'ticket_number' => 'TICKET-0002',
                'subject' => 'Tuntutan tidak keluar dalam senarai',
                'status' => 'dalam_proses',
                'priority' => 'sederhana',
                'category' => 'Tuntutan',
                'created_by' => 'abu@jara.my',
                'created_by_name' => 'Abu Bakar',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '30 min lalu',
                'message_count' => 5,
                'is_escalated' => false,
            ],
            (object)[
                'ticket_number' => 'TICKET-0003',
                'subject' => 'Apps crash bila buka program',
                'status' => 'baru',
                'priority' => 'tinggi',
                'category' => 'Teknikal',
                'created_by' => 'siti@jara.my',
                'created_by_name' => 'Siti Aminah',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '1 jam lalu',
                'message_count' => 1,
                'is_escalated' => false,
            ],
        ];
    }
    
    /**
     * Get dummy my tickets (staff to admin)
     */
    private function getDummyMyTickets()
    {
        return [
            (object)[
                'ticket_number' => 'TICKET-0010',
                'subject' => 'Perlukan akses ke sistem billing',
                'status' => 'escalated',
                'priority' => 'rendah',
                'category' => 'Pentadbiran',
                'created_by' => 'faizan@jara.my',
                'created_by_name' => 'Faizan Abdullah',
                'created_by_role' => 'Staff',
                'organization' => 'Stesen A',
                'created_ago' => '1 hari lalu',
                'message_count' => 2,
                'is_escalated' => true,
            ],
        ];
    }
    
    /**
     * Get dummy all tickets (for admin view)
     */
    private function getDummyAllTickets()
    {
        return array_merge($this->getDummyDriverTickets(), $this->getDummyMyTickets());
    }
    
    /**
     * Get paginated dummy driver tickets
     */
    private function getDummyDriverTicketsPaginated($request, $perPage)
    {
        $allData = collect([
            (object)[
                'ticket_number' => 'TICKET-0001',
                'subject' => 'Tak boleh login di aplikasi mobile',
                'status' => 'baru',
                'priority' => 'tinggi',
                'category' => 'Teknikal',
                'created_by' => 'fairiz@jara.my',
                'created_by_name' => 'Fairiz Ahmad',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '2 jam lalu',
                'message_count' => 3,
                'is_escalated' => false,
            ],
            (object)[
                'ticket_number' => 'TICKET-0002',
                'subject' => 'Tuntutan tidak keluar dalam senarai',
                'status' => 'dalam_proses',
                'priority' => 'sederhana',
                'category' => 'Tuntutan',
                'created_by' => 'abu@jara.my',
                'created_by_name' => 'Abu Bakar',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '30 min lalu',
                'message_count' => 5,
                'is_escalated' => false,
            ],
            (object)[
                'ticket_number' => 'TICKET-0003',
                'subject' => 'Apps crash bila buka program',
                'status' => 'baru',
                'priority' => 'tinggi',
                'category' => 'Teknikal',
                'created_by' => 'siti@jara.my',
                'created_by_name' => 'Siti Aminah',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '1 jam lalu',
                'message_count' => 1,
                'is_escalated' => false,
            ],
            (object)[
                'ticket_number' => 'TICKET-0004',
                'subject' => 'Lupa password sistem',
                'status' => 'selesai',
                'priority' => 'rendah',
                'category' => 'Akaun',
                'created_by' => 'ali@jara.my',
                'created_by_name' => 'Ali Hassan',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '3 jam lalu',
                'message_count' => 2,
                'is_escalated' => false,
            ],
            (object)[
                'ticket_number' => 'TICKET-0005',
                'subject' => 'Data perjalanan tak sync',
                'status' => 'dalam_proses',
                'priority' => 'sederhana',
                'category' => 'Teknikal',
                'created_by' => 'ahmad@jara.my',
                'created_by_name' => 'Ahmad Yusof',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '4 jam lalu',
                'message_count' => 7,
                'is_escalated' => false,
            ],
            (object)[
                'ticket_number' => 'TICKET-0006',
                'subject' => 'Error masa submit tuntutan',
                'status' => 'baru',
                'priority' => 'tinggi',
                'category' => 'Tuntutan',
                'created_by' => 'rahim@jara.my',
                'created_by_name' => 'Abdul Rahim',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '5 jam lalu',
                'message_count' => 4,
                'is_escalated' => false,
            ],
            (object)[
                'ticket_number' => 'TICKET-0007',
                'subject' => 'GPS tidak accurate',
                'status' => 'dalam_proses',
                'priority' => 'sederhana',
                'category' => 'Teknikal',
                'created_by' => 'nurul@jara.my',
                'created_by_name' => 'Nurul Huda',
                'created_by_role' => 'Pemandu',
                'organization' => 'Stesen A',
                'created_ago' => '1 hari lalu',
                'message_count' => 9,
                'is_escalated' => false,
            ],
        ]);
        
        $currentPage = $request->get('driver_page', 1);
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $allData->forPage($currentPage, $perPage),
            $allData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'driver_page']
        );
    }
    
    /**
     * Get paginated dummy my tickets
     */
    private function getDummyMyTicketsPaginated($request, $perPage)
    {
        $allData = collect([
            (object)[
                'ticket_number' => 'TICKET-0010',
                'subject' => 'Perlukan akses ke sistem billing',
                'status' => 'escalated',
                'priority' => 'rendah',
                'category' => 'Pentadbiran',
                'created_by' => 'faizan@jara.my',
                'created_by_name' => 'Faizan Abdullah',
                'created_by_role' => 'Staff',
                'organization' => 'Stesen A',
                'created_ago' => '1 hari lalu',
                'message_count' => 2,
                'is_escalated' => true,
            ],
        ]);
        
        $currentPage = $request->get('my_page', 1);
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $allData->forPage($currentPage, $perPage),
            $allData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'my_page']
        );
    }
    
    /**
     * Get paginated dummy all tickets (admin)
     */
    private function getDummyAllTicketsPaginated($request, $perPage)
    {
        // Combine driver and my tickets for admin
        $driverData = $this->getDummyDriverTicketsPaginated($request, 999)->items();
        $myData = $this->getDummyMyTicketsPaginated($request, 999)->items();
        $allData = collect(array_merge($driverData, $myData));
        
        $currentPage = $request->get('all_page', 1);
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $allData->forPage($currentPage, $perPage),
            $allData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'all_page']
        );
    }
}

