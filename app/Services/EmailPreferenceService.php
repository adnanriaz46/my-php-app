<?php

namespace App\Services;

use App\Models\User;

class EmailPreferenceService
{
    public function isUnsubscribed(User $user, string $category): bool
    {
        if ($user->email_unsubscribed_global) {
            return true;
        }

        $prefs = $user->email_unsubscribed_list_preference ?? [];
        return in_array($category, $prefs);
    }

    public function unsubscribe(User $user, string $category): void
    {
        $prefs = $user->email_unsubscribed_list_preference ?? [];
        if (!in_array($category, $prefs)) {
            $prefs[] = $category;
            $user->email_unsubscribed_list_preference = $prefs;
            $user->save();
        }
    }

    public function subscribe(User $user, string $category): void
    {
        $prefs = $user->email_unsubscribed_list_preference ?? [];
        $prefs = array_filter($prefs, fn($item) => $item !== $category);
        $user->email_unsubscribed_list_preference = array_values($prefs);
        $user->save();
    }

    public function unsubscribeAll(User $user): void
    {
        $user->email_unsubscribed_global = true;
        $user->save();
    }

    public function subscribeAll(User $user): void
    {
        $user->email_unsubscribed_global = false;
        $user->email_unsubscribed_list_preference = [];
        $user->save();
    }



}
