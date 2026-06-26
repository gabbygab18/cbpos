<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\ReportType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with the team, facilities, and
     * report types Arlene shared, so the app is demo-ready immediately.
     * Default password for every seeded account is: password123
     * (everyone should change it after first login).
     */
    public function run(): void
    {
        $this->seedAdmins();
        $this->seedEmployees();
        $this->seedFacilities();
        $this->seedReportTypes();
    }

    private function seedAdmins(): void
    {
        $admins = [
            ['name' => 'Jaybee Beley', 'email' => 'mjcbeley@jacbservices.com'],
            ['name' => 'Arlene Fabay', 'email' => 'afabay@cbpos.net'],
            ['name' => 'Jo Beley', 'email' => 'jbeley.jacbservices@gmail.com'],
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_ADMIN,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedEmployees(): void
    {
        $employees = [
            ['name' => 'Dharell Sales', 'email' => 'dsales@cbpos.net'],
            ['name' => 'Mailyn Ann Magtalas', 'email' => 'mmagtalas@cbpos.net'],
            ['name' => 'Lilet Navarroza', 'email' => 'lnavarroza@cbpos.net'],
            ['name' => 'Glady Joy Cepida-Bolante', 'email' => 'gjbolante@cbpos.net'],
            ['name' => 'Alessandra Tabula', 'email' => 'atabula@cbpos.net'],
            ['name' => 'Ma. Angelica Garcia', 'email' => 'agarcia@cbpos.net'],
            ['name' => 'Jerilee Melba U. Agatep', 'email' => 'jagatep@cbpos.net'],
            ['name' => 'John Michael Vincent Jomalesa', 'email' => 'jmjomalesa@cbpos.net'],
            ['name' => 'Glaena Cariaga', 'email' => 'gcariaga@cbpos.net'],
            ['name' => 'Laron, Micar Paula', 'email' => 'mpsl@cbpos.net'],
            ['name' => 'Felrose Barez', 'email' => 'mfbarez@cbpos.net'],
            ['name' => 'Jemelle Lara Cardenas', 'email' => 'jcardenas@cbpos.net'],
            ['name' => 'Leona Kristle Deiparine', 'email' => 'ldeiparine@cbpos.net'],
            ['name' => 'Anna Lou Sales', 'email' => 'asales@cbpos.net'],
            ['name' => 'Ahlen Villena', 'email' => 'apvillena@cbpos.net'],
            ['name' => 'Arlene Aizon Sape', 'email' => 'arleneasape@gmail.com'],
            ['name' => 'Karisse Alas', 'email' => 'karissealas.omega@gmail.com'],
            ['name' => 'Rojen Amigable', 'email' => 'rojenamigable@gmail.com'],
            ['name' => 'Liezel Purganan', 'email' => 'liezel0630@gmail.com'],
            ['name' => 'Clarence Mangio', 'email' => 'clarencemangio@gmail.com'],
            ['name' => 'Franz Rigor', 'email' => 'frnzknnthrgr@gmail.com'],
            ['name' => 'Zyra Ebdalin', 'email' => 'zbebdalin@gmail.com'],
            ['name' => 'Madel Guadalupe', 'email' => 'madelguadalupe18@gmail.com'],
        ];

        foreach ($employees as $employee) {
            User::firstOrCreate(
                ['email' => $employee['email']],
                [
                    'name' => $employee['name'],
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_EMPLOYEE,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedFacilities(): void
    {
        $facilities = [
            'Great Neck', 'Fulton', 'San Simeon', 'Regal', 'Fairport', 'Schervier',
            'Beacon', 'Mohawk Meadows', 'Wilkinson', 'Pawling', 'Batavia', 'Chittenago',
            'Rome', 'Barnwell', 'Wesley', 'Waterview', 'Utica', 'South Point',
            'Morris Park', 'Maple', 'Mohawk', 'Guidlerland', 'Colonial', 'Bridge View',
        ];

        foreach ($facilities as $index => $name) {
            Facility::firstOrCreate(
                ['name' => $name],
                ['is_active' => true, 'sort_order' => $index]
            );
        }
    }

    private function seedReportTypes(): void
    {
        $reportTypes = [
            'Daily Skilled Notes', 'IPA', 'New Medication Report', '24 hour Re-audit',
            'Diagnosis Audit', 'Pre-assessment', 'PDPM', 'CMI', 'ISP Monitoring',
            'IV Monitoring', 'Admission Follow Up', 'Admission Report', 'ADR', 'ADT',
            '24 hour Report', 'On Standby', 'Combined Assessment',
            'End of PPS Stay (Medicare)', '5 Day', 'Care Plans', 'Quality Measures',
            'Admission', 'Entry', 'Significant Change', 'Discharge', 'Quarterly',
            'Annual', 'IPA - QA', 'New Medication Report - QA', 'Entry - QA',
            'Combined Assessment - QA', 'End of PPS Stay - QA', '5 Day - QA',
            'Care Plans - QA', 'Admission - QA', 'Significant Change - QA',
            'Discharge - QA', 'Quarterly - QA', 'Annual - QA', 'Others',
        ];

        foreach ($reportTypes as $index => $name) {
            ReportType::firstOrCreate(
                ['name' => $name],
                ['is_active' => true, 'sort_order' => $index]
            );
        }
    }
}
