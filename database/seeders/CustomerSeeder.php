<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Rafiul Islam', 'email' => 'rafiul.islam@example.com', 'phone' => '01711-223344', 'city' => 'Dhaka', 'gender' => 'male', 'address' => 'House 12, Road 5, Dhanmondi'],
            ['name' => 'Sadia Afrin', 'email' => 'sadia.afrin@example.com', 'phone' => '01812-334455', 'city' => 'Dhaka', 'gender' => 'female', 'address' => 'Flat 4B, Block C, Bashundhara R/A'],
            ['name' => 'Tanvir Ahmed', 'email' => 'tanvir.ahmed@example.com', 'phone' => '01913-445566', 'city' => 'Chattogram', 'gender' => 'male', 'address' => 'House 7, GEC Circle'],
            ['name' => 'Nusrat Jahan', 'email' => 'nusrat.jahan@example.com', 'phone' => '01614-556677', 'city' => 'Sylhet', 'gender' => 'female', 'address' => 'Road 3, Shahjalal Upashahar'],
            ['name' => 'Mehedi Hasan', 'email' => 'mehedi.hasan@example.com', 'phone' => '01515-667788', 'city' => 'Khulna', 'gender' => 'male', 'address' => 'KDA Avenue, Sonadanga'],
            ['name' => 'Farhana Yasmin', 'email' => 'farhana.yasmin@example.com', 'phone' => '01716-778899', 'city' => 'Dhaka', 'gender' => 'female', 'address' => 'House 21, Road 11, Uttara Sector 7'],
            ['name' => 'Shakib Al Noman', 'email' => 'shakib.noman@example.com', 'phone' => '01817-889900', 'city' => 'Rajshahi', 'gender' => 'male', 'address' => 'New Market Road'],
            ['name' => 'Taslima Begum', 'email' => 'taslima.begum@example.com', 'phone' => '01918-990011', 'city' => 'Dhaka', 'gender' => 'female', 'address' => 'House 3, Road 27, Gulshan 1'],
        ];

        foreach ($customers as $data) {
            $customer = Customer::updateOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => 'password',
                'location_city' => $data['city'],
                'gender' => $data['gender'],
                'customer_group' => 'regular',
                'status' => 'active',
            ]);

            $customer->addresses()->firstOrCreate(
                ['address_line' => $data['address']],
                ['label' => 'Home', 'city' => $data['city'], 'is_default' => true]
            );
        }
    }
}
