<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['code' => 'WTH', 'name' => 'Housing, Education, Medical, Hajj, Age-based withdrawals and application assistance', 'description' => 'Housing withdrawal, Education withdrawal, Medical withdrawal, Hajj withdrawal, Age-based withdrawals (50, 55, 60), identity verification and application assistance.', 'common_issues' => 'Not sure about eligibility; Incomplete documents; Check status of application; Need officer assistance to submit or verify.', 'estimated_time' => '15-25 mins'],
            ['code' => 'NOM', 'name' => 'Register, update or cancel EPF beneficiary nominations with identity verification', 'description' => 'Register or update beneficiaries for your EPF savings with e-KYC verification.', 'common_issues' => 'Missing IC copies, outdated nomination, identity verification.', 'estimated_time' => '10-15 mins'],
            ['code' => 'CON', 'name' => 'Contribution & Statements', 'description' => 'Check contributions, verify records, print statements and resolve contribution issues.', 'common_issues' => 'Employer contribution delay, statement discrepancy.', 'estimated_time' => '10-20 mins'],
            ['code' => 'CMP', 'name' => 'Employer Issues & Complaints', 'description' => 'Lodge complaints against employers, report unpaid contributions and seek advice.', 'common_issues' => 'Late payment, incorrect contribution amount.', 'estimated_time' => '15-30 mins'],
        ];
        foreach ($services as $s) {
            Service::updateOrCreate(['code' => $s['code']], $s);
        }
    }
}
