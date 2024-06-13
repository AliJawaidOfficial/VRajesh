<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Ali',
            'last_name' => 'Jawaid',
            'email' => 'alijawaidofficial.pk@gmail.com',
            
            'meta_email' => 'alijawaidofficial.pk@gmail.com',
            'meta_access_token' => 'EAAGkZB6ubflMBOxt8A2HXk5T7hccauAtrrZCA18XEJ4kSHARR1rS649w4yBC9Fj4K2ru7i0f9ZAfgLw75z4EqGjXEnOBA5ubYTRRK1iggd174r0ibtj9yb2s5wqCvZBbX4QtDqJ1YfKgy9icNw1EiD3U4e6NLKC7Pb75XBoX1k1QffCmKZAPCetRbQeqf4gQu',
            'meta_avatar' => 'https://graph.facebook.com/v19.0/122107401440330450/picture',
            'meta_name' => 'Suraj Kumar',
            
            'linkedin_email' => 'alijawaidofficial.pk@gmail.com',
            'linkedin_access_token' => 'AQU91_peSjL2AsCX0eB3pMcxX1irYV7foMDJ3A5xKfnylyiDXn2O8_PiVd3LLRjUkvf49LtpkntKpA_7QfbnjHzX1pPJ0Fig3iS9xHCB_Txd1LPcQjrozyoNxdEH3BFQfvNfSRJauFaHh2V-z4GMaQaXm3zCqmx5aaVEyT7Y8GvwBhNZDbulwZYUZltUB_a1L76JDfOB4CcArnropXp7RHHv8MttcJIU0SDc1L8gAnysJiWZoBGPJpVRYc4A5H_AxOKd3LKTFAPeJkxjYLvoSBAAtKfJ_IV3q5MIeVvOU-REtWa4FTESfEX9PkeYCUn7ylbq6-CPMGiq0egLOTy8Ws96lJCoRg',
            'linkedin_community_access_token' => 'AQVvuVEZvEfgl2mZTL2Gd_zKBJYKXHBGTs3Nx4K_-RvCKYNuXOQnrx2YKcZht4uDgt0hbO394RgXM9lrkzqK_ZezkAjyYxrsJ3ditQ6wk6ncryaAvBu_FmB8yPqZuYHiRBh90ULCxg1DmXDXUEdmcXMz5wHHc7bXyE_xmAvOxolEKyEllg2NPgpI7FUivYiXvdmpfrS_Xfapn5eAC3CAIzWgwAtYSZX2StUU-cYHsEis-mwJUyNzc9SsRDQiO7SQRBImluB34xizDCsnHtThrw6bZ_gpJb6A6SyyVbckOHucPcJebEDWrpRvC78Y00b8H3DkmLz9KX2mbeneX245tAxCkjzNFQ',
            'linkedin_urn' => 'gKaqSwQora',
            'linkedin_avatar' => 'https://media.licdn.com/dms/image/D4D03AQE3JrxRoZFFSA/profile-displayphoto-shrink_100_100/0/1686325867388?e=1723680000&v=beta&t=1li2R3AVIQ5tSxHOk_YhUNXBCJ7vzQNqnZuwYQfnniY',
            'linkedin_name' => 'Ali Jawaid',

            'google_email' => 'alijawaidofficial.pk@gmail.com',
            'google_access_token' => 'ya29.a0AXooCgsr1UwOTrMDzElVsczewW_cQs0BR1vXkj9qgScwORD8Qt_OSmdwz6Fowvk0ljFXxuqqmuiujg9m4pEF1p9zMQNwMBYNb4FOdkGgBG791RPaRrP8YZSXyWqzv9MKTiyP63E5H1Wre5Vv0AMgqWgspirGYi44jQaCgYKAaESARESFQHGX2MicdgWrbnbsq43ekvhEw2EAQ0169',
            'google_avatar' => 'https://lh3.googleusercontent.com/a/ACg8ocJOVwCWCjxF2qOgXUwVeIh-NArWBNg6jNABt0g7UNByJZtroTc=s96-c',
            'google_name' => 'Ali Jawaid',

            'email_verified_at' => now(),
            'password' => Hash::make('admin'),
        ]);
    }
}
