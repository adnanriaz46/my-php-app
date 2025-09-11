<?php

namespace App\Helper;

use Illuminate\Support\Facades\Request;

class AssessmentHelper
{
    public static function getAiOverviewInitMessage($request)
    {


        $investment_strategy = self::arrayToString($request->investment_strategy);
        $counties_invest = self::arrayToString($request->counties_invest);
        $acq_strategy = self::arrayToString($request->acq_strategy);
        $other_investments_yn = $request->other_investments_yn ? 'Yes' : 'No';

        $content1 = <<<EOT
                I am going to give you text in the second prompt. The text will be answers a user filled out on an "Investor assessment" quiz on our revamp365.ai software platform. The point of our software is to be the single best place for real estate investors to find flips, rentals, and wholesale deals to invest in. Use of the platform is free with limited features, the first tiered paid plan is only $50/month and advanced tier is $97/month (billed annually). Here is a list of features of the plans:
Essentials
$50/month
Billed annually

All features of Free plus:

View Wholesale Deals
Revamp University
Unlock Data Fields
Pending/Closed Data
MLS COMP Data
Cash Flow Filter
Flip Profit Filter
Delta PSF Filter
Rental Calculator & Reports
Flip Calculator & Reports
Deal Alerts
Affiliate Reselling (Tier 2) - Earn 22% on affiliate signups

Accelerator
$97/month
Billed annually

All features of Essentials plus:

Property AI
List Off-Market Deals
Data Exporting
Email Tools
Beta Features Access
Automated Offers (Coming soon)
Affiliate Reselling (Tier 3) - Earn 33% on affiliate signups

Here's are the titles of some of the training videos included in "Revamp University" which is included with paid memberships:
Data Definitions Series:
-Intro to the DATA 
-Delta PSF 
-AVM
-ARV
-Flip Profit Estimates
-Cash Flow Estimates
-Average PPSF (Low, High)
-Highest 3 Avg (& Lowest)
-Accuracy Scores

Advanced Filtering & Sorting Series:
-Intro To Filters and Sorting
-Delta PSF
-Cash Flow
-Flip Profit
-DOM
-MORE
-Sorting
-Saved Searches & Real World Use

Advanced Bonus Features:
-1 Platform Mastery 
-Lists & Saved Properties
-Exporting Data
-Views & Display
-Suppression
-Rental Calculator
-Flip Calculator
-Property AI

Sell Your Deals - How to list properties on the platform

*This following 101 series is unreleased - you can mention it it coming soon, but refer to outside sources or training for now. You can recommend specific books or youtube videos to help a user based on their needs.*

Marketing 101 - Source your own off market deals

Sales 101 - Intro to sales frameworks and psychology

Finance 101 - Intro to real estate finance

Operations 101 - Intro to building real estate systems, processes, KPIs, and tracking

Leadership 101 - Intro to becoming the leader you need to be for your organization and the world.

Above all that and the most EXPENSIVE tier plan is called "Scale". We do not advertise a price on the website as it is a custom package with white glove service, usually costing between $1000-30,000 per month depending on the users needs. Some of the advertised features of the Scale plan are:
Dedicated account rep
Weekly calls
Advanced trainings and infrastructure
Focus group

__________________________________

 The point of this assessment they filled out is to help craft a customized strategy plan for them. 

Based on the answers given in the following prompt I want you to create a short yet detailed action plan to help the user reach their goals. 

Based on their answer of what their investment strategies are, we should tailor to that but highlight any areas of concern. For example, if the user selects Wholesale, Fix and flip AND buy & hold, and it looks like they have only done a few deals, we should mention that they probably need to focus on ONE strategy until they have more experience. On the other hand if they mention multiple strategies but indicate they have done dozens of deals, they will probably be ok.

Your answer should first congratulate the user on any notable fields they put in. Then emphasize that the user should be leveraging the revamp365.ai platform, as it is the best, most efficient, and cheapest way to be informed on the best REI deals. It should encourage them to sign up for a paid plan if they have not done so yet.  

Then, your answer should give a step by step plan of what the user should do on a week by week basis to hit their goals. This should include 3 relevant REI books, courses that could be helpful, groups or live training event recommendations, etc. You can also recommend our trainings in the Revamp University if applicable. Our goal is to give as much value as humanly possible to our users here.

Then, related to the question about how much time per week the user thinks they need to spend per week - make sure to focus on the fact that if they simply leverage the revamp365.ai platform, they can likely cut their time spent deal hunting into a fraction while actually being more productive and never missing the best deals in their neighborhoods that match their buy box.

Then, related to the question "What would you consider paying to add 1 deal per month to your pipeline?" if the user answer is below $1000 you should encourage and convince them that investing in themselves is the best investment and they should break the scarcity mindset if they want to hit their goals. You can really drive the point home if their answer to the question "How much is your average profit per deal?" is a very high number. *For example if the user said their average profit per deal is $30,000 and they mentioned they want to buy 5 deals this year, we could spin that and say "it would totally make sense to pay a service like us that can add deals to your pipeline at least $15,000 a year, since we can add 10x that to your bottom line every year. But guess what, you don't even need to spend a FRACTION of that, our Accelerator plan is only $97/month. The "Scale" Plan should be recommended to anyone who wants to do more than 10 deals a year or anyone that says they are willing to invest more than $5000. * NOTE that was an example - you will customize this little sales pitch based on the user data. 

It should also give detailed feedback based on how the user scored themselves on each of the 5 pillars and what they should do to improve on ones with low scores and why they are important. Again, this can recommend sections to focus on in Revamp University. 

You also need to include at least three hyperlinks in the email. Here is an index to choose from based on the context of the email and what url is most fitting:
http://revamp365.ai -   (main SEARCH page)
http://revamp365.ai/revamp-university   (revamp university training page with video series)
http://revamp365.ai/user_profile -   (users profile where they can edit their info and customize their "buy box"
http://revamp365.ai/user_profile/?referrals=yes  -  (the users affiliate portal where they can share their affiliate link and track earnings)
http://revamp365.ai/user_profile/?properties=yes  -  (the users section where they can upload their OWN wholesale deals to sell for huge assignment fees and earn big with Revamp)


Your response should NOT be formatted as "Markdown", it should be formatted with HTML tags for any bold, italic, underline, or special formatting to make the text as readable and attractive as possible. This is critical. Ensure each email body falls within HTML formatting for rich text. You may also use emojis in the email as long as they are tasteful and not overdone. Don't include email subject line in your response.
EOT;

        $messages = [
            [
                'role' => 'user',
                'content' => $content1,
            ],
            [
                'role' => 'user',
                'content' => "
First Name: {$request->first_name}
Last Name: {$request->last_name}
Email: {$request->email}
Phone: {$request->phone}
Investment Strategies Include: {$investment_strategy}  
Counties the user invests in: {$counties_invest}
How long have you been investing in real estate?  {$request->how_long_in_rei}

Number of deals:
Fix & flips: {$request->how_many_flips}
Buy & Holds: {$request->how_many_rentals}
Wholesale Deals: {$request->how_many_wholesale}
Agent / Broker deals: {$request->how_many_agent_deals}
New construction deals: {$request->how_many_new_construction}
Land deals: {$request->how_many_land}
Private lending deals: {$request->how_many_private_lending}
Do you have any other investment types?  {$other_investments_yn}
What is your primary goal for real estate investing? {$request->primary_goal}
Summary of investment strategy: {$request->investment_biz_plan}
Summary of investment strategy enhanced with Ai assistance: {$request->smart_goals}
What's holding you back? {$request->main_obstacle}
What is your current acquisition strategy? {$acq_strategy}
How much time per week do you spend deal hunting? {$request->time_per_week_actual}
How much time per week do you THINK you need to spend to hit your goals? {$request->time_per_week_goal}

We break down the skills required to excel into 5 pillars - Marketing, sales, operations, finance, and leadership. How do you rate yourself on all 5?
Marketing: {$request->pillar_ranking_marketing}
Sales: {$request->pillar_ranking_sales}
Operations: {$request->pillar_ranking_operations}
Finance: {$request->pillar_ranking_finance}
Leadership: {$request->pillar_ranking_leadership}
How much is your average profit per deal? {$request->average_deal_profit}
What would you consider paying to add 1 deal per month to your pipeline?  {$request->what_would_you_pay}

*Remember - Your response should NOT be formatted as \"Markdown\", it should be formatted with HTML tags for any bold, italic, underline, or special formatting to make the text as readable and attractive as possible. *
                ",
            ]
        ];
        return $messages;
    }



    public static function getAiEmailDripMessages($assessment)
    {
        $messages = [
            [
                'role' => 'user',
                'content' => "
                Now you are going to serve as an EXPERT copywriter and write highly compelling,  20 step customized email drip sequence to this user to try to get them to sign up for our paid memberships which include the training program, Revamp University,  that helps cut the learning curve of using the platform and getting results. The program also covers all areas of REI, teaching topics like the 5 pillars, deal structures, creative deals, and everything in between. Of course, signing up also grants all the other benefits of the software as mentioned in previous messages in this thread. You need to refer back to all the user inputs to make this email drip sequence as customized and as perfect for the user as possible.

All the emails should be written as custom as possible, using the users answers to convince the user to subscribe to our software. Use persuasive language and write in the style of Russel Brunson.  


Laws you must follow:
1. Each email body text must be above 250 - 400 words (excluding HTML tags). This is very important. Ensure that each email falls within this word count range.


2. Follow the Hook, Story, Offer framework. Your emails should all relate to the user as much as possible based on the information we have about them. You can tell stories about made-up users and their journey of deal-finding and success, following the Hero's Journey concept. Note that not every email needs a story with a made-up user; only 15% of the emails should have such a story. Make sure to relate stories and analogies to the user as much as possible based on any useful information we have about them. (You should never use the words Hook, story, offer in your output though)


3. Include at least one hyperlink in each email. Here is an index to choose from based on the context of the email and what URL is most fitting:
http://revamp365.ai -   (main SEARCH page)
http://revamp365.ai/revamp_university   (revamp university training page with video series)
http://revamp365.ai/user_profile -   (users profile where they can edit their info and customize their \"buy box\")
https://revamp365.ai/pricing (page for user to upgrade or review pricing)
http://revamp365.ai/user_profile/?referrals=yes  -  (the users affiliate portal where they can share their affiliate link and track earnings)
http://revamp365.ai/user_profile/?properties=yes  -  (the users section where they can upload their OWN wholesale deals to sell for huge assignment fees and earn big with Revamp)

4. The copywriting for the subject lines can vary, but always make the subject as compelling as possible to encourage the user to click.

5. The first email should directly reinforce the goal the user made, and challenge them to make it happen. For example, if the user said they want to buy \"x #\" of deals in the next \"y #\" months,  the email subjects could say \"You said you were going to close \"x\" deals in \"y timeline\"...the clock is ticking...\" * Remember that was just an example - you need to use the information provided from the user in the previous message *

6. The second email should directly reference how much the user said one deal can profit for them in the assessment (as long as it is over $10,000), and we should remind them how much they said one deal is worth, and paint the picture perfect scenario of accomplishing their goals and showing them what that life can look like and re-assuring that it is not a far fetched goal.

8. *Important: Please provide these separators for splitting content: for the steps, use \"|-----|\" and between subject and body, use \"====\". don't mention subject word as prefix for subject and the step count. The body of each email should be formatted as HTML(without the root HTML tags)

9. Email recipient name is {$assessment->first_name} {$assessment->last_name}

Remember: Each email body should be formatted as HTML and should all have line breaks between every sentence or two and should take advantage of H1, H2, H3, H4, bold, italic, underline, where fitting to emphasize specific points and make the text visually appealing in the email. Any compelling sentences or words should be bold or headings.

Remember: Each emails word count should be more than 250 words

*Important: Response first 10 emails is current response
                ",
            ]
        ];
        return $messages;
    }

    public static function getAiEmailDripWithPreviousMessages($previousMessages)
    {
        $messages = $previousMessages;
        $messages[] = [
            'role' => 'user',
            'content' => "Generate the remaining 11 to 20 steps now(don't mention step count, and avoid \"Subject:\" prefix on subject), like previous generation also each step's emails should be more than 250 words",
        ];
        return $messages;
    }

    public static function getAiDEResponseEmailAndSubject(string $response)
    {
        if ($response) {
            $explodedEmails = explode("|-----|", $response);
            $explodedEmails = array_filter($explodedEmails);
            $Emails = [];
            foreach ($explodedEmails as $email) {
                $explodedEmailAndSubject = explode("====", $email);
                $Emails[] = [
                    'email' => $explodedEmailAndSubject[1] ?? '', // HTML content
                    'subject' => trim($explodedEmailAndSubject[0] ?? ''), // Subject
                ];
            }
            return $Emails;
        }

        return [];
    }

    public static function arrayToString($array, $separator = ', ')
    {
        if (is_array($array)) {
            return implode($separator, $array);
        }
        return $array;
    }
}