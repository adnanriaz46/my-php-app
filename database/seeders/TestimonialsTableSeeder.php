<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestimonialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'description' => 'I just posted my latest deal on your revamp 365 app and got a cash buyer the next day closing in the next week or so!! Your hard work really shows on this project man I love it. Im about to post a new deal stay tuned. Oh yeah It\'s also my go to now for comping properties and on market fixer uppers all day!! I\'m telling everyone haha. Thanks for walking me through all my questions with it as well!🫡💪🏻🐐appreciate you bro!!',
                'email' => '',
                'name' => 'Mike Jack',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782940550x595757115190688100/Mike%20Jack.PNG',
                'published_date' => Carbon::parse('Mar 25, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'The Revamp365 app is awesome! As a smaller part time flipper/investor it takes a lot of leg work out of running numbers and data for areas I may be newer to or unfamiliar with. Using this app landed me my most recent flip in Delco.\n\nIv been here from the beginning of this app and watched it evolve over the years and will always have this in my arsenal of tools for investing.',
                'email' => '',
                'name' => 'Tom Nicholls',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782930630x876928022106108400/Tom%20Nicholls.PNG',
                'published_date' => Carbon::parse('Jan 22, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'As a flipper, I love the ease of "shopping" for a deal whether on the MLS or off market in a one-stop-shop.\n\nAs a wholesaler, its a great way to introduce our deals to the vast buyer pool that just want a good deal, no matter where is comes from, and to have the system start analyzing on your behalf, takes all that grunt work out when you start every new property underwrite. Slam dunk from Revamp and team on this product, I am excited to see where it can go from here!',
                'email' => '',
                'name' => 'Chris McGeady',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782888819x101328759240175040/Chris%20McGeady.PNG',
                'published_date' => Carbon::parse('Jan 16, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'Revamp365 is an absolute must-have for real estate investors! One feature I love is the ability to automate and send live offers directly to listed properties. It\'s a huge time-saver and keeps me ahead of the game. This software truly understands what investors need to scale and succeed. Highly recommend!\n',
                'email' => '',
                'name' => 'Joe Evangelisti',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782900755x574774685722860800/Joe%20Evangelisti.PNG',
                'published_date' => Carbon::parse('Jan 6, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'This platform has completely transformed the way I search for and evaluate properties, streamlining what used to be a complex and time-consuming process. From the moment I started using it, I was impressed by its intuitive design and user-friendly interface, which made it easy to learn and navigate. The powerful filters and search functionalities allow me to quickly identify ideal properties that match my investment criteria, saving me countless hours of manual research.',
                'email' => '',
                'name' => 'Bryan Batista',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782828823x946509167435366500/Bryan%20Batista.PNG',
                'published_date' => Carbon::parse('Jan 8, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'Revamp 365 app and website are incredibly helpful in finding your next investment property! It breaks everything down so you can easily see where the money\'s at! And Drew will sit and walk you through everything so there\'s no guess work finding your next project!',
                'email' => '',
                'name' => 'Greg Veturys',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782864370x836112705345350800/Greg%20Veturys.PNG',
                'published_date' => Carbon::parse('Jan 9, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'The website and app are so use friendly which makes my searches for good deals so much easier!! I use it daily to find the good deals that are listed and am starting to have great success with it. Anyone looking to get started and want the best software to find good investment deals should sign up.',
                'email' => '',
                'name' => 'Alex Martyn',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782876241x497767288762005950/Alex%20Martyn.PNG',
                'published_date' => Carbon::parse('Jan 3, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'Truthfully I would have missed these 2 deals on my mls searches/feeds due to the price point but your software and the filters picked them up. The back end spread is 6 figures on the first one and similar on the second, but the second one needs a lot more work.',
                'email' => '',
                'name' => 'Stan Wilder',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782784042x637189264116564400/Stan%20Wilder.PNG',
                'published_date' => Carbon::parse('Jun 4, 2024 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'This platform and the filters used made seeking out ideal properties so much simpler. I was able to quickly underwrite multiple deals at once and send offers on all of those deals. This platform has cut out a lot of tedious work I used to do and now I can send out many offers in a fraction of my time. I am able to focus more on building my team and scaling my business\n',
                'email' => '',
                'name' => 'Steve Murphy',
                'profile_image' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743782802507x385801530820367500/Steve%20Murphy.PNG',
                'published_date' => Carbon::parse('Jan 6, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => 'I use Revamp to contract MLS deals and wholesaled them to my buyers. It makes it easier than anything else to find flips and rentals for my buyers\n',
                'email' => '',
                'name' => 'Josh Papa',
                'profile_image' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHBvaW50ZXItZXZlbnRzPSJub25lIiB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgc3R5bGU9ImJhY2tncm91bmQ6ICAwJSAwJSAvIDQwMCUgNDAwJSAjYmRjM2M3OyB3aWR0aDogMTAwcHg7IGhlaWdodDogMTAwcHg7IGJvcmRlci1yYWRpdXM6IDBweDsiPjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHk9IjUwJSIgeD0iNTAlIiBkeT0iMC4zNWVtIiBwb2ludGVyLWV2ZW50cz0iYXV0byIgZmlsbD0iI2ZmZmZmZiIgZm9udC1mYW1pbHk9IkhlbHZldGljYU5ldWUtTGlnaHQsSGVsdmV0aWNhIE5ldWUgTGlnaHQsSGVsdmV0aWNhIE5ldWUsSGVsdmV0aWNhLCBBcmlhbCxMdWNpZGEgR3JhbmRlLCBzYW5zLXNlcmlmIiBzdHlsZT0iZm9udC13ZWlnaHQ6IDQwMDsgZm9udC1zaXplOiA0M3B4OyI+SlA8L3RleHQ+PC9zdmc+',
                'published_date' => Carbon::parse('Jan 3, 2025 12:00 am'),
                'rate' => 5,
                'title' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('testimonials')->insert($testimonials);
    }
}
