<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to insert all restaurant data
     */
    public function up(): void
    {
        $restaurants = [
            ['name' => 'Abha', 'code' => 'R001'],
            ['name' => 'Abha-TNDR', 'code' => 'R002'],
            ['name' => 'Al-Qassim', 'code' => 'R003'],
            ['name' => 'Al-Sultan-T', 'code' => 'R004'],
            ['name' => 'Albadia', 'code' => 'R005'],
            ['name' => 'Ameer Fawaz', 'code' => 'R006'],
            ['name' => 'Awali', 'code' => 'R007'],
            ['name' => 'Aziziyah-Madina', 'code' => 'R008'],
            ['name' => 'Aziziyah-Riyad', 'code' => 'R009'],
            ['name' => 'Basateen', 'code' => 'R010'],
            ['name' => 'Batha Quresh', 'code' => 'R011'],
            ['name' => 'DAMMAM', 'code' => 'R012'],
            ['name' => 'Dahiat Laban', 'code' => 'R013'],
            ['name' => 'Faisaliya', 'code' => 'R014'],
            ['name' => 'Hail', 'code' => 'R015'],
            ['name' => 'Hamdaniya', 'code' => 'R016'],
            ['name' => 'Hamdaniya-TNDR', 'code' => 'R017'],
            ['name' => 'Hassan Bin Thabit', 'code' => 'R018'],
            ['name' => 'Hijra', 'code' => 'R019'],
            ['name' => 'Hira', 'code' => 'R020'],
            ['name' => 'Iceland', 'code' => 'R021'],
            ['name' => 'Imam Saud -TNDR', 'code' => 'R022'],
            ['name' => 'KHARG', 'code' => 'R023'],
            ['name' => 'KING FAHAD MED', 'code' => 'R024'],
            ['name' => 'Khaleej', 'code' => 'R025'],
            ['name' => 'Khamis Mushet', 'code' => 'R026'],
            ['name' => 'Khobar', 'code' => 'R027'],
            ['name' => 'Khulias', 'code' => 'R028'],
            ['name' => 'King Salman', 'code' => 'R029'],
            ['name' => 'Marwa', 'code' => 'R030'],
            ['name' => 'Marwa-TNDR', 'code' => 'R031'],
            ['name' => 'Mohamadiah', 'code' => 'R032'],
            ['name' => 'Nahdah', 'code' => 'R033'],
            ['name' => 'Nakheel', 'code' => 'R034'],
            ['name' => 'Nassem', 'code' => 'R035'],
            ['name' => 'Nassem-TNDR', 'code' => 'R036'],
            ['name' => 'Nozha', 'code' => 'R037'],
            ['name' => 'Obhur', 'code' => 'R038'],
            ['name' => 'Qurtuba', 'code' => 'R039'],
            ['name' => 'Rabigh', 'code' => 'R040'],
            ['name' => 'Raqi', 'code' => 'R041'],
            ['name' => 'Rawabi Plaza', 'code' => 'R042'],
            ['name' => 'Rehab', 'code' => 'R043'],
            ['name' => 'Riy-Warehouse Dep', 'code' => 'R044'],
            ['name' => 'Riyadh- Factory', 'code' => 'R045'],
            ['name' => 'Rowdah', 'code' => 'R046'],
            ['name' => 'Rowdah-TNDR', 'code' => 'R047'],
            ['name' => 'Samer', 'code' => 'R048'],
            ['name' => 'King Salman 2', 'code' => 'R049'], // Made unique since King Salman appeared twice
            ['name' => 'Sari', 'code' => 'R050'],
            ['name' => 'Shoukiya', 'code' => 'R051'],
            ['name' => 'Shraia', 'code' => 'R052'],
            ['name' => 'Sultana -TNDR', 'code' => 'R053'],
            ['name' => 'Tahlia', 'code' => 'R054'],
            ['name' => 'Taif-Hawiyah', 'code' => 'R055'],
            ['name' => 'Taif-Shehar', 'code' => 'R056'],
            ['name' => 'Thumama', 'code' => 'R057'],
            ['name' => 'Umluj', 'code' => 'R058'],
            ['name' => 'Waziriyah', 'code' => 'R059'],
            ['name' => 'Yanbu 1', 'code' => 'R060'],
            ['name' => 'Yanbu 2', 'code' => 'R061'],
            ['name' => 'Yasmen', 'code' => 'R062'],
            ['name' => 'Zaidi (Sasco)', 'code' => 'R063'],
            ['name' => 'Jed-Warehouse Dep', 'code' => 'R064'],
            ['name' => 'Jeddah Factory', 'code' => 'R065']
        ];

        foreach ($restaurants as $restaurant) {
            DB::table('restaurants')->insertOrIgnore([
                'name' => $restaurant['name'],
                'code' => $restaurant['code'],
                'address' => 'Saudi Arabia',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all restaurants with codes R001-R065
        DB::table('restaurants')->whereIn('code', [
            'R001', 'R002', 'R003', 'R004', 'R005', 'R006', 'R007', 'R008', 'R009', 'R010',
            'R011', 'R012', 'R013', 'R014', 'R015', 'R016', 'R017', 'R018', 'R019', 'R020',
            'R021', 'R022', 'R023', 'R024', 'R025', 'R026', 'R027', 'R028', 'R029', 'R030',
            'R031', 'R032', 'R033', 'R034', 'R035', 'R036', 'R037', 'R038', 'R039', 'R040',
            'R041', 'R042', 'R043', 'R044', 'R045', 'R046', 'R047', 'R048', 'R049', 'R050',
            'R051', 'R052', 'R053', 'R054', 'R055', 'R056', 'R057', 'R058', 'R059', 'R060',
            'R061', 'R062', 'R063', 'R064', 'R065'
        ])->delete();
    }
};