<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "post_id",

        "title",
        "description",
        "media",
        "media_type",

        "business_profile_call_to_action_button",
        "business_profile_call_to_action_url",

        "on_facebook",
        "facebook_page_id",
        "facebook_page_access_token",
        "facebook_page_name",

        "on_instagram",
        "instagram_account_id",
        "instagram_account_name",

        "on_linkedin",
        "linkedin_company_id",
        "linkedin_company_name",

        "on_business_profile",
        "business_profile_id",
        "business_profile_name",
        "business_profile_account_id",

        "scheduled_at",
        "draft",
        "posted",
    ];
}
