<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Roles
        Role::create([
            'name' => 'Basic',
            'guard_name' => 'web',
            'is_visible' => 1,
        ])->givePermissionTo([
            'connect_facebook',
            'facebook_text_post',
            'linkedin_text_post',
        ]);


        Role::create([
            'name' => 'Premium',
            'guard_name' => 'web',
            'is_visible' => 1,
        ])->givePermissionTo([
            'connect_facebook',
            'connect_linkedin',
            'facebook_text_post',
            'facebook_image_post',
            'facebook_video_post',
            'instagram_image_post',
            'instagram_video_post',
            'linkedin_text_post',
            'linkedin_image_post',
            'linkedin_video_post',
        ]);
    }
}
