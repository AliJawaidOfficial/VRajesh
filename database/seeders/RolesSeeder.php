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
            'meta_facebook_text_post',
            'linkedin_text_post',
        ]);


        Role::create([
            'name' => 'Premium',
            'guard_name' => 'web',
            'is_visible' => 1,
        ])->givePermissionTo([
            'connect_facebook',
            'connect_linkedin',
            'meta_facebook_text_post',
            'meta_facebook_image_post',
            'meta_facebook_video_post',
            'meta_instagram_image_post',
            'meta_instagram_video_post',
            'linkedin_text_post',
            'linkedin_image_post',
            'linkedin_video_post',
            'immediate_post',
            'scheduled_post',
            'draft_post',
            're_post',
        ]);
    }
}
