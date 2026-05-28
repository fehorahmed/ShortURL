<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@shorturl.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        // Plans
        $plans = [
            [
                'name'             => 'Free',
                'description'      => 'Perfect for getting started',
                'price'            => 0,
                'currency'         => 'BDT',
                'duration_days'    => 36500,
                'max_links'        => 5,
                'link_expiry_days' => 30,
                'show_ads'         => true,
                'custom_alias'     => false,
                'analytics'        => false,
                'sort_order'       => 0,
            ],
            [
                'name'             => 'Starter',
                'description'      => 'For individuals and bloggers',
                'price'            => 199,
                'currency'         => 'BDT',
                'duration_days'    => 30,
                'max_links'        => 50,
                'link_expiry_days' => 90,
                'show_ads'         => false,
                'custom_alias'     => false,
                'analytics'        => true,
                'sort_order'       => 1,
            ],
            [
                'name'             => 'Pro',
                'description'      => 'For marketers and businesses',
                'price'            => 499,
                'currency'         => 'BDT',
                'duration_days'    => 30,
                'max_links'        => 200,
                'link_expiry_days' => 365,
                'show_ads'         => false,
                'custom_alias'     => true,
                'analytics'        => true,
                'sort_order'       => 2,
            ],
            [
                'name'             => 'Business',
                'description'      => 'Unlimited power for teams',
                'price'            => 999,
                'currency'         => 'BDT',
                'duration_days'    => 30,
                'max_links'        => -1,
                'link_expiry_days' => 3650,
                'show_ads'         => false,
                'custom_alias'     => true,
                'analytics'        => true,
                'sort_order'       => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }

        // Sample ad slot (disabled by default)
        Advertisement::create([
            'name'       => 'Adsterra Redirect Page',
            'type'       => 'adsterra',
            'placement'  => 'redirect_page',
            'ad_code'    => '<!-- Paste your Adsterra script tag here -->',
            'is_active'  => false,
            'sort_order' => 0,
        ]);

        // Settings
        $settings = [
            ['key' => 'site_name',             'value' => 'ShortURL',               'group' => 'general'],
            ['key' => 'site_description',      'value' => 'Powerful URL Shortener',  'group' => 'general'],
            ['key' => 'default_link_expiry',   'value' => '30',                      'group' => 'links'],
            ['key' => 'allow_guest_links',     'value' => '1',                       'group' => 'links'],
            ['key' => 'redirect_wait_seconds', 'value' => '5',                       'group' => 'ads'],
            ['key' => 'maintenance_mode',      'value' => '0',                       'group' => 'general'],
        ];

        foreach ($settings as $s) {
            Setting::create($s);
        }

        $this->command->info('✅ Seeded: admin@shorturl.com / password');
    }
}