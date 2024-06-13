<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            [
                'name' => 'connect_facebook',
                'guard_name' => 'web'
            ],
            [
                'name' => 'connect_linkedin',
                'guard_name' => 'web'
            ],
            [
                'name' => 'connect_google',
                'guard_name' => 'web'
            ],
            [
                'name' => 'meta_facebook_text_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'meta_facebook_image_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'meta_facebook_video_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'meta_instagram_image_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'meta_instagram_video_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'linkedin_text_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'linkedin_image_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'linkedin_video_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'immediate_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'scheduled_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 'draft_post',
                'guard_name' => 'web'
            ],
            [
                'name' => 're_post',
                'guard_name' => 'web'
            ],
        ];
        Permission::insert($permissions);
    }
}
