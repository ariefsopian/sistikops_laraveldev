<?php // Pastikan tidak ada spasi atau baris kosong di atas baris ini

namespace Database\Seeders; // Ini harus menjadi pernyataan pertama setelah <?php

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon; // Untuk mengelola tanggal

class TicketSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     *
     * @return void
     */
    public function run(): void
    {
        // Pastikan user dan role sudah di-seed terlebih dahulu
        // Anda bisa memanggil UserSeeder di sini jika belum dipanggil di DatabaseSeeder
        // $this->call(UserSeeder::class);

        $ticketsData = [
            [
                'ticket_no' => 'REQ-00006',
                'requester_username' => 'Tony Budiman',
                'project' => 'Access Management', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Integrasi Tokenisasi - Access Management Apps',
                'description' => 'Implementasi Tokenisasi ' . "\r\n" . '1. Create User Cred ' . "\r\n" . '2. Install BDT',
                'due_date' => '2025-09-25',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-19 00:59:00',
            ],
            [
                'ticket_no' => 'REQ-00007',
                'requester_username' => 'Bakti Setyo Prajanto',
                'project' => 'PPL Apps Integrasi', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Integrasi Tokenisasi - PPL apps',
                'description' => '1. Create User Cred' . "\r\n" . '2. whitelist 103.42.117.113 - Tanggal 02 July 2025' . "\r\n" . '3. ',
                'due_date' => '2025-08-31',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-18 01:11:00',
            ],
            [
                'ticket_no' => 'REQ-00008',
                'requester_username' => 'Budi Arie Wibowo',
                'project' => 'iCustomer Integration', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Integrasi Tokenisasi - iCustomer',
                'description' => '1. Create User Cred',
                'due_date' => '2025-08-31',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-17 19:00:00',
            ],
            [
                'ticket_no' => 'REQ-00009',
                'requester_username' => 'Muhammad Ilya Asha Soegondo',
                'project' => 'PMSOL SuperApps', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'SuperApps PMSOL',
                'description' => 'PIC Developer : zainal.arifin@mitrais.com' . "\r\n" . '1. Create User Cred' . "\r\n" . '2.  ',
                'due_date' => '2025-08-31',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-18 01:23:00',
            ],
            [
                'ticket_no' => 'REQ-00010',
                'requester_username' => 'Tsabit Alifudin',
                'project' => 'DIOS Apps Deployment', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'DIOS Apps',
                'description' => '1. Create User Cred',
                'due_date' => '2025-08-31',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-17 19:00:00',
            ],
            [
                'ticket_no' => 'REQ-00011',
                'requester_username' => 'Tsabit Alifudin',
                'project' => 'SITA Apps Integration', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'SITA Apps',
                'description' => '1. Create User Cred' . "\r\n" . '2. ',
                'due_date' => '2025-08-31',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-17 19:00:00',
            ],
            [
                'ticket_no' => 'REQ-00012',
                'requester_username' => 'luckita.jackaria@pelita-air.com',
                'project' => 'Pelita Air Apps Support', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Pelita Air Apps',
                'description' => '1. Create User Cred' . "\r\n" . '2. ',
                'due_date' => '2025-08-31',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-18 02:06:00',
            ],
            [
                'ticket_no' => 'REQ-00013',
                'requester_username' => 'Diana Wenny Pawestri',
                'project' => 'Talend Datawarehouse Dev', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Talend Datawarehouse',
                'description' => '1. Create User Cred (16 Mei 2025)' . "\r\n" . '2. BDT Simulation',
                'due_date' => '2025-07-11',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-05-16 00:30:00',
            ],
            [
                'ticket_no' => 'CR-00014',
                'requester_username' => 'Fahmi Kurniawan',
                'project' => 'CTS Software Upgrade', // Kolom 'project' ditambahkan
                'type' => 'Change Request',
                'subject' => 'Upgrade Software version CTS',
                'description' => '1. Lab' . "\r\n" . '2. Create MOP' . "\r\n" . '3. Presentation MOP' . "\r\n",
                'due_date' => '2025-07-11',
                'priority' => 'High',
                'status' => 'Pending Approval',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-30 21:23:00',
            ],
            [
                'ticket_no' => 'CR-00015',
                'requester_username' => 'Fahmi Kurniawan',
                'project' => 'CTM Migration', // Kolom 'project' ditambahkan
                'type' => 'Change Request',
                'subject' => 'Upgrade Software version CTM dan Migrasi Jatiluhur Ke Andalas',
                'description' => '1. Lab' . "\r\n" . '2. Create MOP' . "\r\n" . '3. Presentation MOP' . "\r\n" . '4. Waiting Change Request Approval' . "\r\n" . 'belum ada approval',
                'due_date' => '2025-07-11',
                'priority' => 'High',
                'status' => 'Pending Approval',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-30 21:25:00',
            ],
            [
                'ticket_no' => 'REQ-00016',
                'requester_username' => 'M Agung Prasetyo',
                'project' => 'INMAR Deployment', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'INMAR (Industry and Marine)',
                'description' => 'Sudah implementasi Production',
                'due_date' => '2025-02-28',
                'priority' => 'Medium',
                'status' => 'Closed',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-04-01 09:50:00',
            ],
            [
                'ticket_no' => 'REQ-00017',
                'requester_username' => 'M Agung Prasetyo',
                'project' => 'Web Kemitraan Final', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Web Kemitraan',
                'description' => 'Sudah implementasi Production',
                'due_date' => '2025-02-02',
                'priority' => 'Medium',
                'status' => 'Closed',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-01-01 01:53:00',
            ],
            [
                'ticket_no' => 'REQ-00018',
                'requester_username' => 'M Agung Prasetyo',
                'project' => 'Stakeholder Management System', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Stakeholder Management (Stakeview)',
                'description' => 'Sudah implementasi Production',
                'due_date' => '2025-03-01',
                'priority' => 'Medium',
                'status' => 'Closed',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-03-01 01:54:00',
            ],
            [
                'ticket_no' => 'REQ-00019',
                'requester_username' => 'Oktavianus Aji',
                'project' => 'Vault Certification', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Belajar Vault',
                'description' => 'Plan Certified Vault',
                'due_date' => '2025-07-11',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-07-01 03:27:00',
            ],
            [
                'ticket_no' => 'REQ-00020',
                'requester_username' => 'Rudi Nurhidayanto',
                'project' => 'PGE MEvent App', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'PGE - Aplikasi MEvent',
                'description' => 'Sosialisasi 18 Juni' . "\r\n" . 'Fandi (Programmer)' . "\r\n" . 'testtest' . "\r\n" . 'tstest',
                'due_date' => '2025-07-06',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-06-16 03:30:00',
            ],
            [
                'ticket_no' => 'REQ-00021',
                'requester_username' => 'Ammyra Fatma Rizky',
                'project' => 'eGatePass System', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Aplikasi Gate Pass (eGatePass)',
                'description' => 'Integrasi Tokenisasi' . "\r\n" . '1. Pembuatan Credential (10 July 2025) Sent by email' . "\r\n" . '2. Vault Integration',
                'due_date' => '2025-07-18',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-07-09 23:22:00',
            ],
            [
                'ticket_no' => 'REQ-00022',
                'requester_username' => 'Fahmi Kurniawan',
                'project' => 'Server Monitoring', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'List Aplikasi dan capture RAM and CPU',
                'description' => '',
                'due_date' => '2025-07-11',
                'priority' => 'Medium',
                'status' => 'Closed',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-07-10 01:00:00',
            ],
            [
                'ticket_no' => 'INC-00023',
                'requester_username' => 'Oktavianus Aji',
                'project' => 'CTM Monitoring', // Kolom 'project' ditambahkan
                'type' => 'Incident',
                'subject' => 'CTM Jatiluhur Terpantau Down',
                'description' => 'Up Down',
                'due_date' => '2025-07-11',
                'priority' => 'Medium',
                'status' => 'Closed',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-07-11 08:51:57',
            ],
            [
                'ticket_no' => 'REQ-00024',
                'requester_username' => 'Fahmi Kurniawan',
                'project' => 'Internal Test App', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'test aplikasi',
                'description' => 'test12324',
                'due_date' => '2025-07-31',
                'priority' => 'Medium',
                'status' => 'Open',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-07-11 19:56:03',
            ],
            [
                'ticket_no' => 'REQ-00025',
                'requester_username' => 'Ghifari Kautsar',
                'project' => 'ERP System Upgrade', // Kolom 'project' ditambahkan
                'type' => 'Request',
                'subject' => 'Activity Maintenance System ERP (MySAP) dan Nor ERP – Switch Over - Pertamina CITR',
                'description' => 'Activity Maintenance System ERP (MySAP) dan Nor ERP – Switch Over - Pertamina CITR' . "\r\n" . 'Vault Hashicorp',
                'due_date' => '2025-07-13',
                'priority' => 'Medium',
                'status' => 'Open',
                'assignee_username' => 'Arief Sopian',
                'created_at' => '2025-07-12 04:08:35',
            ],
        ];

        foreach ($ticketsData as $ticketData) {
            $requester = User::where('username', $ticketData['requester_username'])->first();
            $assignee = User::where('username', $ticketData['assignee_username'])->first();

            if ($requester && $assignee) {
                Ticket::firstOrCreate(
                    ['ticket_no' => $ticketData['ticket_no']],
                    [
                        'requester_id' => $requester->id,
                        'project' => $ticketData['project'],
                        'type' => $ticketData['type'],
                        'subject' => $ticketData['subject'],
                        'description' => $ticketData['description'],
                        'due_date' => Carbon::parse($ticketData['due_date']),
                        'priority' => $ticketData['priority'],
                        'status' => $ticketData['status'],
                        'assignee_id' => $assignee->id,
                        'created_at' => Carbon::parse($ticketData['created_at']),
                        'updated_at' => Carbon::parse($ticketData['created_at']),
                    ]
                );
            } else {
                \Log::warning("Requester atau Assignee tidak ditemukan untuk tiket: " . $ticketData['ticket_no']);
            }
        }
    }
}
