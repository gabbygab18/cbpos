<?php

namespace Database\Seeders;

use App\Models\OrgChartNode;
use Illuminate\Database\Seeder;

class OrgChartSeeder extends Seeder
{
    public function run(): void
    {
        OrgChartNode::truncate();

        // ── Tier 1: CEO ───────────────────────────────────
        $ceo = OrgChartNode::create([
            'parent_id'  => null,
            'name'       => 'Jo Beley',
            'title'      => 'CEO',
            'color'      => 'purple',
            'sort_order' => 1,
        ]);

        // ── Tier 2: Country Manager ───────────────────────
        $cm = OrgChartNode::create([
            'parent_id'  => $ceo->id,
            'name'       => 'Jaybee Beley',
            'title'      => 'Country Manager',
            'color'      => 'teal',
            'sort_order' => 1,
        ]);

        // ── Tier 3: MDS Account Manager ───────────────────
        $am = OrgChartNode::create([
            'parent_id'  => $cm->id,
            'name'       => 'Arlene Fabay',
            'title'      => 'MDS Account Manager',
            'color'      => 'amber',
            'sort_order' => 1,
        ]);

        // ── Tier 4: MDS Quality Analyst ───────────────────
        $qa = OrgChartNode::create([
            'parent_id'  => $am->id,
            'name'       => 'Jemelle Cardenas',
            'title'      => 'MDS Quality Analyst',
            'color'      => 'pink',
            'sort_order' => 1,
        ]);

        // ── Tier 5: MDS Nurses ────────────────────────────
        $nurses = [
            ['name' => 'Glady Joy Cepida-Bolante',                          'title' => 'MDS Nurse',                  'facilities' => ['Waterview'],                                          'sort_order' => 1],
            ['name' => 'Glaena Cariaga / Kenneth Franz Rigor',              'title' => 'MDS Nurse',                  'facilities' => ['Utica'],                                              'sort_order' => 2],
            ['name' => 'Leona Kristle Deiparine',                           'title' => 'MDS Nurse',                  'facilities' => ['South Point', 'Colonial'],                            'sort_order' => 3],
            ['name' => 'Mailyn Ann Magtalas / Rojen Amigable / Zyra Ebdalin','title' => 'MDS Nurse',                 'facilities' => ['Morris Park'],                                        'sort_order' => 4],
            ['name' => 'Micar Paula Laron',                                  'title' => 'MDS Nurse',                 'facilities' => ['Maple'],                                              'sort_order' => 5],
            ['name' => 'Liezel Purganan',                                    'title' => 'MDS Nurse',                 'facilities' => ['Mohawk'],                                             'sort_order' => 6],
            ['name' => 'Karisse Alas',                                       'title' => 'MDS Nurse',                 'facilities' => ['Guilderland'],                                        'sort_order' => 7],
            ['name' => 'Lilet Navarroza / Clarence Mangio',                  'title' => 'MDS Nurse',                 'facilities' => ['Bridge View'],                                        'sort_order' => 8],
        ];

        foreach ($nurses as $n) {
            OrgChartNode::create(array_merge($n, ['parent_id' => $qa->id, 'color' => 'blue']));
        }

        // ── Tier 5: PDPM / Admission Reviewers ───────────
        OrgChartNode::create([
            'parent_id'  => $qa->id,
            'name'       => 'John Michael Vincent Jomalesa',
            'title'      => 'PDPM / Admission Reviewer',
            'facilities' => ['Fulton', 'Great Neck', 'Mayfair', 'South Point'],
            'color'      => 'red',
            'sort_order' => 9,
        ]);

        OrgChartNode::create([
            'parent_id'  => $qa->id,
            'name'       => 'Dharell Sales',
            'title'      => 'PDPM / Admission Reviewer',
            'facilities' => ['Atrium', 'Throgs Neck'],
            'color'      => 'red',
            'sort_order' => 10,
        ]);

        OrgChartNode::create([
            'parent_id'  => $qa->id,
            'name'       => 'Alessandra Tabula',
            'title'      => 'PDPM / MDS Nurse',
            'facilities' => ['San Simeon', 'Regal', 'Fairport', 'Schervier', 'Beacon', 'Mohawk Meadows', 'Wilkinson', 'Pawling', 'Batavia', 'Wesley', 'Waterview'],
            'color'      => 'coral',
            'sort_order' => 11,
        ]);

        OrgChartNode::create([
            'parent_id'  => $qa->id,
            'name'       => 'Ma. Angelica Garcia',
            'title'      => 'Admission Reviewer / SME',
            'facilities' => ['Chittenago', 'Rome', 'Barnwell', 'Utica', 'Maple', 'Mohawk', 'Guildlerland'],
            'color'      => 'green',
            'sort_order' => 12,
        ]);

        OrgChartNode::create([
            'parent_id'  => $qa->id,
            'name'       => 'Alhen Patrick Villena',
            'title'      => 'Targeted Probe and Educate (TPE)',
            'color'      => 'gray',
            'sort_order' => 13,
        ]);

        // ── Tier 5: Clinical Auditors (children of QA) ───
        $auditors = [
            'Arlene Aizon Sape',
            'Jerilee Melba U. Agatep',
            'Anna Lou Sales',
            'Madel Guadalupe',
        ];

        foreach ($auditors as $i => $auditor) {
            OrgChartNode::create([
                'parent_id'  => $qa->id,
                'name'       => $auditor,
                'title'      => 'Clinical Auditor',
                'color'      => 'gray',
                'sort_order' => 14 + $i,
            ]);
        }
    }
}
