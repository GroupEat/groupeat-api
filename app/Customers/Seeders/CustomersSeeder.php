<?php
namespace Groupeat\Customers\Seeders;

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Abstracts\Seeder;
use Groupeat\Support\Database\Traits\GeneratePhoneNumber;

class CustomersSeeder extends Seeder
{
    use GeneratePhoneNumber;

    protected function makeEntry($id, $max)
    {
        $customer = Customer::create([
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'phoneNumber' => $this->generatePhoneNumber(),
        ]);

        $credentials = new UserCredentials([
            'email' => $this->faker->email,
            'password' => $customer->lastName,
            'locale' => 'fr',
        ]);
        $credentials->user()->associate($customer);
        $credentials->save();
    }

    protected function insertAdditionalEntries($id)
    {
        $customer = Customer::create([
            'firstName' => 'Groupeat',
            'lastName' => 'User',
            'phoneNumber' => $this->generatePhoneNumber(),
        ]);

        $credentials = new UserCredentials([
            'email' => 'groupeat@ensta.fr',
            'password' => 'groupeat',
            'activatedAt' => Carbon::now(),
            'locale' => 'fr',
            'token' => 'eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI2IiwiaXNzIjoiaHR0cDpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwiaWF0IjoiMTQzNTk2MTE1NSIsImV4cCI6IjIwNjY2ODExNTUiLCJuYmYiOiIxNDM1OTYxMTU1IiwianRpIjoiNWE4Y2Y5OThmNmFiNzI1NzAwOWNjYTBmMmVkOTI2NDYifQ.KlVyE_7LRc164GaQo8anxzwtrkIiBl06J_w-IadaABg',
        ]);
        $credentials->user()->associate($customer);
        $credentials->save();
    }
}
