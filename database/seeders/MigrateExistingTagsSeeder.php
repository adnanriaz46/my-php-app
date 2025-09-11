<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\ContactTagOwnership;

class MigrateExistingTagsSeeder extends Seeder
{
    public function run()
    {
        return;
        $contacts = Contact::whereNotNull('tags')->get();

        foreach ($contacts as $contact) {
            $tags = $contact->tags ?? [];

            foreach ($tags as $tag) {
                if ($tag !== 'Skip-Traced') {
                    // Mark all existing non-Skip-Traced tags as owned by the contact's user
                    ContactTagOwnership::updateOrCreate(
                        [
                            'contact_id' => $contact->id,
                            'tag_name' => $tag,
                            'user_id' => $contact->user_id
                        ],
                        [
                            'is_public' => false
                        ]
                    );
                }
            }
        }
    }
}
