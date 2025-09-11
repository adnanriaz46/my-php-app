<?php
return [
    'categories' => [
        'saved_list_updates',
        'buy_box_alerts',
        'saved_search_alerts',
        'event_invitations',
        'user_feedback_surveys',
        'training_webinar_invites',
        'promotional_emails',
//        'account_notifications',
        'newsletter',
        'product_updates',
        'assessment_drip',
    ],
    'categories_description' => [
        'saved_list_updates' => [
            'root' => false,
            'name' => 'Saved List Updates',
            'description' => 'Updates or changes to properties in one of your "Saved Lists". Update in "my profile"',
            'template_id' => ''
        ],
        'buy_box_alerts' => [
            'root' => false,
            'name' => 'Buy Box Alerts',
            'description' => 'Properties that fall within the buy box you set on your profile.',
            'template_id' => ''
        ],
        'saved_search_alerts' => [
            'root' => false,
            'name' => 'Saved Search Alerts',
            'description' => 'Alerts for properties that match a saved search alert. (Customize this from your profile section)',
            'template_id' => 'd-ddd9e20ffa7448d9a245fd373cb1c202'
        ],
        'event_invitations' => [
            'root' => false,
            'name' => 'Event Invitations',
            'description' => 'Invitations to conferences, meetups, and other events.',
            'template_id' => ''
        ],
        'user_feedback_surveys' => [
            'root' => false,
            'name' => 'User Feedback and Surveys',
            'description' => 'Requests for feedback, surveys, and participation in user research.',
            'template_id' => ''
        ],
        'training_webinar_invites' => [
            'root' => false,
            'name' => 'Training and Webinar Invites',
            'description' => 'Invitations to webinars, training sessions, and other educational content.',
            'template_id' => ''
        ],
        'promotional_emails' => [
            'root' => false,
            'name' => 'Promotional Emails',
            'description' => 'Special offers, discounts, and promotions related to our services.',
            'template_id' => 'd-54db12e9983443718ef67a221981cd66'
        ],
        'newsletter' => [
            'root' => false,
            'name' => 'Newsletter',
            'description' => 'Regular updates, articles, and general news about the company and industry.',
            'template_id' => ''
        ],
        'product_updates' => [
            'root' => false,
            'name' => 'Product Updates',
            'description' => 'Emails about new features, improvements, and changes to the platform.',
            'template_id' => ''
        ],
        'assessment_drip' => [
            'root' => false,
            'name' => 'Assessment - Mail Drip',
            'description' => 'Receive 20 step-by-step emails each day after completing the assessment task.',
            'template_id' => 'd-174de7dd1c87417e9bf9ec77c61bff5c'
        ],

    ],
    'account' => [
        'email_verify' => [
            'root' => true,
            'name' => 'Email verification',
            'description' => 'Email verification',
            'template_id' => 'd-d61a333dfe4446629bce17fc5dc9bedb',
        ],
        'password_change' => [
            'root' => true,
            'name' => 'Password Reset',
            'description' => 'Password Reset',
            'template_id' => 'd-f19a89407d674df7a7f015438febe5f1',
        ],
        'subscription_expired' => [
            'root' => true,
            'name' => 'Subscription Expired',
            'description' => 'Notifies the user that their subscription has expired',
            'template_id' => 'd-174de7dd1c87417e9bf9ec77c61bff5c'
        ],
    ],
    'wholesale' => [
        'address_request_approval' => [
            'root' => true,
            'name' => 'Address Request Approval',
            'template_id' => 'd-174de7dd1c87417e9bf9ec77c61bff5c'
        ],
        'wholesale_moved_to_offmarket' => [
            'root' => true,
            'name' => 'Moved to off-market - Wholesale',
            'description' => 'During your wholesale property moved to off-market',
            'template_id' => 'd-132a41a929f14c49a4db4512461871b6'
        ],
        'wholesale_static_alert' => [
            'root' => true,
            'name' => 'Static Alert - Wholesale',
            'description' => '',
            'template_id' => 'd-5b0bcdc43f11472185108c59652d433b'
        ],
        'wholesale_instant_offer' => [
            'root' => true,
            'name' => 'Instant Offer - Wholesale',
            'description' => '',
            'template_id' => 'd-8b73481f8d3c40aeb96576ffe4e679a2'
        ],
        'wholesale_ask_question' => [
            'root' => true,
            'name' => 'Ask Question - Wholesale',
            'description' => '',
            'template_id' => 'd-c75b0fe8cff94f169c985a9337d5b36f'
        ],
        'wholesale_schedule_showing' => [
            'root' => true,
            'name' => 'Schedule Showing - Wholesale',
            'description' => '',
            'template_id' => 'd-e5cce14e110e4fc98b15ee7944d299e3'
        ],
        'wholesale_on_live' => [
            'root' => true,
            'name' => 'On Live Alert - Wholesale',
            'description' => '',
            'template_id' => 'd-c655b12b38de4e4f8b403da02f47531a'
        ],
    ],
    'revamp_team' => [
        'common' => [
            'root' => true,
            'name' => 'Uncategories emails',
            'template_id' => 'd-174de7dd1c87417e9bf9ec77c61bff5c'
        ],
    ],
    'email_marketing' => [
        'email_marketing_offer' => [
            'root' => true,
            'name' => 'Bulk Property Offer - Email Marketing',
            'description' => '',
            'template_id' => 'd-54db12e9983443718ef67a221981cd66'
        ],
    ]
];
