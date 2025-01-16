<?php

namespace Database\Seeders;

use App\Models\AbsenceType;
use App\Models\Country;
use App\Models\ExpenseKind;
use App\Models\PaymentPeriod;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RolePermissionSeeder::class,
        ]);

        /**
         * Name,
         * ISO 3166 Alpha-3 Code,
         * ISO 3166 Numeric Code,
         * ISO 4217 Currency Code,
         */
        $countries = [
            ['El Salvador', 'SLV', 222, 'USD', '🇸🇻 El Salvador', 'https://crmsv.glasswing.org/'],
            ['Guatemala', 'GTM', 320, 'GTQ', '🇬🇹 Guatemala', 'https://crmgt.glasswing.org/'],
            ['Honduras', 'HND', 340, 'HNL', '🇭🇳 Honduras', 'https://crmhn.glasswing.org/'],
            ['Costa Rica', 'CRI', 188, 'CRC', '🇨🇷 Costa Rica', 'http://crmcr.glasswing.org/'],
            ['Panama', 'PAN', 591, 'PAB', '🇵🇦 Panama', 'http://crmpa.glasswing.org/'],
            ['República Dominicana', 'DOM', 214, 'DOP', '🇩🇴 Republica Dominicana', null],
            ['México', 'MEX', 484, 'MXN', '🇲🇽 Mexico', 'http://crmmx.glasswing.org/'],
            ['Colombia', 'COL', 170, 'COP', '🇨🇴 Colombia', null],
            ['Estados Unidos', 'USA', 840, 'USD', '🇺🇲 Estados Unidos', null],
        ];

        /**
         * Internal name,
         * Name,
         */
        $teams = [
            ['staff', 'Staff'],
            ['project_manager', 'Gerente de Proyecto'],
            ['regional_project_manager', 'Gerente Regional de Proyecto'],
            ['administrator', 'Administrador'],
            ['superadmin', 'Super Admin'],
            ['human_resources', 'Recursos Humanos'],
            ['accounting', 'Contabilidad'],
            ['editor', 'Redactor de Contenido'],
            ['country_director', 'Director de País'],
            ['regional_project_director', 'Director Regional de Proyecto'],
            ['board', 'Junta Directiva'],
        ];

        $usersProd = [
            'superadmin',
        ];

        $usersDev = [
            ['lclaros', 'Leslie Claros', true],
            ['jaortega', 'Jesús Ortega', true],
            ['cpalacios', 'Claudia Palacios', false],
            ['rosegueda', 'Romeo Osegueda', false],
            ['lventura', 'Luis Enrique Ventura', false],
            ['jtrejo', 'Juan Trejo', false],
            ['nloucel', 'Nataly Elizabeth Loucel de Torres', false],
            ['msanchez', 'Miguel Angel Ruiz', false],
            ['cescamilla', 'Celia Liseth Escamilla Melendez', false],
            ['njmartinez', 'Norberto Martinez', false],
            ['jmoctezuma', 'Jocelyn Narayama Moctezuma Genis', false],
            ['mtrejo', 'Maritza Raquel Trejo', false],
            ['wpaniagua', 'Wilmar Paniagua', false],
            ['bblanco', 'Brian Blanco', false],
            ['amendoza', 'Sergio Alejandro Hernández Mendoza', false],
            ['sibarra', 'Salvador Enrique Ibarra Fernandez', false],
        ];

        $expenseKinds = [
            'Combustible',
            'Alimentación',
            'Transporte',
            'Alojamiento',
            'Viáticos',
            'Otros',
        ];

        $absenceTypes = [
            'Enfermedad',
            'Vacación',
            'Asueto / Feriado',
            'Incapacidad',
            'Paternidad / Maternidad',
            'Otros permisos',
        ];

        foreach ($countries as $country) {
            $country = Country::create([
                'name' => $country[0],
                'alpha_code' => $country[1],
                'numeric_code' => $country[2],
                'currency_code' => $country[3],
                'label' => $country[4],
                'brilo_url' => $country[5],
            ]);
        }

        $countries = Country::all();

        foreach ($usersDev as $userFromArray) {
            $user = User::factory()->create([
                'email' => $userFromArray[0].'@glasswing.org',
                'name' => $userFromArray[1],
            ]);
        }

        foreach ($expenseKinds as $expenseKind) {
            ExpenseKind::factory()->create([
                'name' => $expenseKind,
                'description' => '',
            ]);
        }

        foreach ($absenceTypes as $absenceType) {
            AbsenceType::factory()->create([
                'name' => $absenceType,
                'active' => true,
            ]);
        }

        foreach (Country::all() as $country) {
            $paymentPeriod = new PaymentPeriod;
            $paymentPeriod->country_id = $country->id;
            $paymentPeriod->save();
        }

        $jesus = User::where('email', 'jaortega@glasswing.org')->first();
        $romeo = User::where('email', 'rosegueda@glasswing.org')->first();
        $leslie = User::where('email', 'lclaros@glasswing.org')->first();
        $luis = User::where('email', 'lventura@glasswing.org')->first();

        $jesus->assignRole('contabilidad');
        $romeo->assignRole('colaborador');
        $leslie->assignRole('colaborador');
        $luis->assignRole('creador-contenido');
    }
}
