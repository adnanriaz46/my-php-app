<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrainingContentSeeder extends Seeder
{
    public function run(): void
    {
        // Training Posts CSV as array
        $posts = [
            [
                'title' => 'Where to Get Started',
                'description' => '[START HERE] Welcome! Follow the steps in this course to get started on your journey',
                'short_description' => '[START HERE] Welcome! Follow the steps in this course to get started on your journey',
                'thumbnail' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1715872910206x572660920575375000/revamp.jpg',
                'eligible_user_types' => json_encode(['Premium', 'Free']),
                'hide' => false,
                'created_at' => Carbon::parse('May 16, 2024 8:52 pm'),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Filters & Sorting',
                'description' => 'Leverage filters and sorting to find your needle in the haystack and do more deals',
                'short_description' => 'Leverage filters and sorting to find your needle in the haystack and do more deals',
                'thumbnail' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1715627404071x821974886374178300/WhatsApp%20Image%202024-05-13%20at%2013.46.40_7e843d3b.jpg',
                'eligible_user_types' => json_encode(['Premium']),
                'hide' => false,
                'created_at' => Carbon::parse('May 16, 2024 8:59 pm'),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Advanced 101',
                'description' => 'Master the platform and become a deal magnet. You will become the source of',
                'short_description' => 'Master the platform and become a deal magnet. You will become the source of',
                'thumbnail' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1715627368290x383950207606176240/WhatsApp%20Image%202024-05-13%20at%2013.46.39_66e772e12.jpg',
                'eligible_user_types' => json_encode(['Premium']),
                'hide' => false,
                'created_at' => Carbon::parse('May 16, 2024 8:59 pm'),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Sell YOUR Deals',
                'description' => 'See how listing your wholesale deals here can increase your deal size and speed of ',
                'short_description' => 'See how listing your wholesale deals here can increase your deal size and speed of ',
                'thumbnail' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1715627500071x575665056897940200/WhatsApp%20Image%202024-05-12%20at%2014.41.13_074d55b92.jpg',
                'eligible_user_types' => json_encode(['Premium']),
                'hide' => false,
                'created_at' => Carbon::parse('May 16, 2024 8:59 pm'),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Affiliate Partnerships',
                'description' => 'Earn through our partnership program. Build recurring income for the life of membership',
                'short_description' => 'Earn through our partnership program. Build recurring income for the life of membership',
                'thumbnail' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1716918523951x134097587581531570/affiliate%20training%20thumbnail.png',
                'eligible_user_types' => json_encode(['Free', 'Premium']),
                'hide' => false,
                'created_at' => Carbon::parse('May 28, 2024 10:44 pm'),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Automated Offers',
                'description' => 'Learn how to send signed offers in seconds. This training covers every step—from building your offer list, to setting your price using AI-powered guidance, to generating and emailing a fully executed Agreement of Sale directly from the platform.',
                'short_description' => 'Send signed offers in seconds with AI-powered guidance and automated contracts.',
                'thumbnail' => '//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1743653498453x168785012950373630/automated%20offers%20thumbnail.png',
                'eligible_user_types' => json_encode([]),
                'hide' => false,
                'created_at' => Carbon::parse('Apr 3, 2025 9:41 am'),
                'updated_at' => Carbon::now(),
            ],
        ];
 
        // Insert posts and build title=>id map
        $postTitleToId = [];
        foreach ($posts as $post) {
            $id = DB::table('training_posts')->insertGetId($post);
            $postTitleToId[$post['title']] = $id;
        }

        // Training Videos CSV as array
        $videos = $videos = [
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Terminology & Data Definitions Overview',
                'summary' => '🏆 Overview: This video introduces key data definitions and explains the inner workings of the revamp365.ai platform.
        
        🔍 Data Definitions: Clarity on terminologies like \'Estimated Flip Profit\' and \'Delta per Square Foot\' is provided.
        
        🛠 Practical Applications: Insights into how understanding these definitions can enhance platform use and deal analysis.
        
        📊 Analytics: Discussion on the algorithms that drive data calculations on the platform.
        
        ⏱ Training Series: Introduction to a series of short training videos, each focusing on specific features.',
                'summary_html' => '<p>🏆 Overview: This video introduces key data definitions and explains the inner workings of the revamp365.ai platform.</p><p><br></p><p>🔍 Data Definitions: Clarity on terminologies like \'Estimated Flip Profit\' and \'Delta per Square Foot\' is provided.</p><p><br></p><p>🛠 Practical Applications: Insights into how understanding these definitions can enhance platform use and deal analysis.</p><p><br></p><p>📊 Analytics: Discussion on the algorithms that drive data calculations on the platform.</p><p><br></p><p>⏱ Training Series: Introduction to a series of short training videos, each focusing on specific features.</p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '0:01 Alright, here we go. I am super excited to get into this. This is going to be the beginning of a short mini training series on some data definitions and some of the inner workings of how the data on the platform works and the intention to get you up to speed on some of these data definitions, what they
        0:25 are, how they work and the purpose of all of that is because the better you understand some of the data stuff and how it works and how it\'s calculated and how it displays.
        0:38 is on the platform not only is it going to make your experience on the platform that much better and yield you that much better results on the platform but I promise it will also make all of your areas of deal analysis on the platform or not that much better moving forward.
        1:02 It will help you to analyze deals quicker. It will help you to um believe it that to analyze deals quicker and be able to sift through and know quickly and easily what is a deal what is not and why.
        1:18 So like I said this is the beginning of a little series. The goal is to have a different training video for each individual term or feature.
        1:32 So we can keep everything incredibly short and straight to the point and get you up to speed as quick as I\'m going to be possible, but for the beginning here, since this is an intro to some of the data and definitions and terminology, we\'ll just do a quick 60,000-foot overview And at the home page here
        1:55 , the main search area, let\'s get my mug out of the way here. Umm. So to give you an example of some of the things that we\'re going to go over here, um, terminology, right?
        2:10 So we won\'t go into- of this because that\'s pretty basic boilerplate stuff. MLS wholesale, pretty self-explanatory. Here\'s where we start getting into some of it.
        2:22 Some of the gold filters, we got estimated flip profit, estimated cash flow. Now both of them, you can probably look at them from a surface.
        2:31 It\'s level and be like, okay, well I understand what estimated flip profit means. Why do I need an explanation? It is true.
        2:39 And you\'ll understand once you watch the specific video on estimated flip profit or cash flow as to, ultimately how they are calculated and how the data algorithms of the platform work.
        2:55 And again, they\'re not supposed to be super in-depth. It\'s not gonna be a tell-all with each video being an hour long.
        3:03 The goal is for each video to be about a minute, maybe two minutes long, to tell you what you, need to know.
        3:09 And we\'ll have future trainings. If you wanna go deeper into the inner workings of everything. But if you understand all of this at a surface level, again I promise you it will make your experience in finding deals that much better.
        3:24 And last one here, Delta per square foot. That may not be familiar to you at all. Umm. And that\'s one of the most helpful beneficial umm.
        3:37 Things of this entire platform in its entirety. Right. So I\'ll leave you with that little cliffhanger. So you have to watch the Delta per square foot.
        3:47 Training video on its own. Umm. If we just look through some of the other stuff here. Uhh. Let\'s go to more.
        3:59 So again, a lot of this is kind of going to be self-explanatory. We have some keyword fill. Cultures will go into this stuff.
        4:05 Umm. ARV estimates, rent per month estimate, min max. Cool. We\'ll go into that stuff. But let\'s look at how some of the data is displayed here from the thumbnail view of it.
        4:22 So. Obviously, a lot of this info. You would be able to see on any other listing platform. But the meat and potatoes and what makes this platform so unique.
        4:34 And so incredible at finding deals is some of this stuff we can see here. So we can see. Excuse me for this.
        4:42 It\'s property. 3354 in rent. 390 thousand hour AVM. 498 thousand dollar ARV. What\'s the difference between them? You\'ll have to watch them videos.
        4:56 Um, but real quick AVM is basically the median value. So ARV is if it were renovated and comparable to the nicest few homes that have recently sold that are comparable.
        5:10 Um, that\'s what it would be worth. So again, watch the specific videos on those. Estimated cash flow, thousand bucks, delta.
        5:20 It\'s a per square foot, 82 bucks and estimated flip profit, $110,000. So that\'s some of the data that we have displayed right here.
        5:33 But let\'s just go into the record real quick to give. If a little bit more of a picture of some of the other data definitions that we\'re talking about.
        5:45 So again, a lot of this stuff is just basic boilerplate info that you\'d see on any listing platform, doesn\'t really need an explanation.
        5:53 Listening details, let\'s collapse that. Alright, so. So auto valuations, here\'s where we\'re going to get into some more of the terminology that you need to understand what it is, how it works.
        6:05 Because again, if you understand how it works, umm. It\'s going to make it that much easier to find deals. You know, and there is gold to be found in here, we\'ve done.
        6:15 A hundred of thousands of dollars of deals that we didn\'t even really intend to do just through testing this platform.
        6:22 So anyway, listing price, cool, self-explanatory, listing price per square foot. Okay, there\'s one. Average price. Per square foot. High price per square foot.
        6:35 So we\'re not going to have um training on each one of these because basically all three of these are kind of the same thing.
        6:43 Highest three average. Going to that. Um so basically all of these. These things here in the auto valuations. That\'s some of the terminology that will bring you up to speed on in future trainings.
        6:57 Get you up to speed on them. And accuracy score for the value accuracy score for rental. How that works. How that\'s calculated and how to mentally process that.
        7:08 And no when you should really rely on the value or the rental value or not. Okay. So hopefully that gives a good quick 60,000 foot overview of what we\'re trying to accomplish in this section.
        7:24 And again just. The beaded dead horse one last time. The better you do understand this information. The easier time you will have finding deals.
        7:34 You will find more deals. You will buy more deals. And that\'s ultimately the goal here. So see you on the next training video.',
                'uid_code' => 'DD1',
                'url' => 'https://youtu.be/5EKLUNEh-Ho',
                'youtube_video_id' => '5EKLUNEh-Ho',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Understanding AVM and Its Importance',
                'summary' => '📊 Understanding AVM:
        Focus on Data - Highlights the use of Average Value Metric (AVM) over Average Retail Value (ARV) due to its practical application in real estate investment. AVM uses average price per square foot for property valuation.
        
        🔑 Importance of Precision in Renovations:
        Practical Examples - Explains personal experiences of unnecessary renovations, urging reliance on accurate data to make more profitable decisions without overinvesting in renovations.
        
        💡 Maximizing Property Value:
        Strategic Investment - Discusses strategies for investing just enough in a property to reach its potential market value, emphasizing cost-effectiveness and market understanding.',
                'summary_html' => '<p><strong>📊 Understanding AVM:</strong></p><p>Focus on Data - Highlights the use of Average Value Metric (AVM) over Average Retail Value (ARV) due to its practical application in real estate investment. AVM uses average price per square foot for property valuation.</p><p><br></p><p><strong>🔑 Importance of Precision in Renovations:</strong></p><p>Practical Examples - Explains personal experiences of unnecessary renovations, urging reliance on accurate data to make more profitable decisions without overinvesting in renovations.</p><p><br></p><p><strong>💡 Maximizing Property Value:</strong></p><p>Strategic Investment - Discusses strategies for investing just enough in a property to reach its potential market value, emphasizing cost-effectiveness and market understanding.</p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '0:01 Alright, here we go. In this video we\'re going to touch on AVM. Okay, so it\'s like a sister of ARV.
        0:11 And I get questions all the time why we would look at that as opposed to ARV. And the reason is subtle, but really, really important.
        0:22 Okay, so AVM, if you watched the training on delta per square foot, we touched on it a little bit. But AVM is predicated on this average distance.
        0:36 Average price per square foot. So real simply, all the comps that are comparable, the average property sold for $125 a square foot in this section of Wilmington.
        0:47 So if we take that $125 a square foot multiplied by the $1625. square foot of the subject property, that\'s how we\'re going to get that $202,000, okay?
        1:01 So that is important because not everybody, first and foremost, likes to do full gut renovations, brand new kitchens, bedrooms. And brand new bathrooms and everything, and it\'s not always necessary.
        1:16 I am a large ah person to be accused of this, I have ripped out like decent kitchens and bathrooms to put in brand new stuff when it really wasn\'t necessary.
        1:29 I could have made more money a lot of times on a lot of properties in the past by simply keeping what was there and actually relying on the data that\'s in front of me, and I didn\'t always do that.
        1:41 So, I kick myself in the butt for it often, but now I have the insight and the wisdom to tell you to look at this as well.
        1:50 If you can put $20,000 into a property to hit the average where most properties sell for, which by the way, there\'s more buyers in that pool because that\'s where the average is, uhm, and you can make more profit by doing so, you\'re kind of crazy to rip out a good kitchen or bathroom, uhm, when it\'s not
        2:16 necessary. So all that does come down to, look at the data, know the comps, know the comps well. And uhm, AVM can be a great tool for you to just determine surface level, does this property look good, does it not?
        2:33 So that\'s a quick overview of AVM, how it works, see you on the next one.',
                'uid_code' => 'DD3',
                'url' => 'https://youtu.be/8RzZFR1ohkk',
                'youtube_video_id' => '8RzZFR1ohkk',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Training on Delta per Square Foot',
                'summary' => '🔍 Introduction to Delta per Square Foot
        - Basics Explained: The training begins with a focus on the concept of Delta per Square Foot, highlighting its importance in identifying equity in properties. This measure is presented as crucial for different investment strategies, whether for flips, rentals, or wholesale deals.
        
        📊 How Delta is Used in Property Evaluation
        - Primary Metric: Delta per Square Foot is emphasized as a key metric in filtering and finding properties. It\'s described as the difference between the listing price per square foot and the average price per square foot in the area.
        - Comparative Analysis: The method considers properties similar in type and size within the same geographical area, focusing on recent sales to calculate the average.
        
        🏠 Real-World Application and Case Studies
        - Detailed Examples: Specific examples are provided to illustrate how Delta per Square Foot works in practice, using real properties to show the calculation and its implications on investment decisions.
        - System Functionality: The platform’s functionality is discussed, showcasing how it speeds up the process of property evaluation and deal finding.
        
        📈 Strategic Insights for Investors
        - Filtering Strategies: Various filtering options are explored, explaining how investors can set up their searches to include Delta per Square Foot along with other metrics like estimated cash flow or flip profit to pinpoint potential deals.
        - Diverse Investment Approaches: The versatility of Delta per Square Foot is highlighted, showing its application across different types of real estate investments.
        
        🔧 Technical Explanation of the Platform’s Algorithms
        - Data Handling: The software\'s algorithm and its approach to handling data are detailed, providing insight into how the system prioritizes and averages property data to assist investors in making informed decisions.
        - AVM and ARV Calculations: The differences between Average Valuation Model (AVM) and After Repair Value (ARV) are explained, helping users understand the platform\'s valuation mechanisms.
        
        Insights based on numbers:
        - Delta Value Impact: Properties with a Delta per Square Foot of 82 indicate significant potential for investment, as seen in the case study where this metric highlighted properties undervalued compared to their neighborhood averages.
        - Filter Efficiency: Setting a filter for a Delta per Square Foot minimum of 50 can significantly narrow down the search results, leading to more targeted and potentially profitable investment opportunities.
            
            ',
                'summary_html' => '<p><strong>🔍 </strong><strong style="color: var(--tw-prose-bold);">Introduction to Delta per Square Foot</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Basics Explained</span>: The training begins with a focus on the concept of Delta per Square Foot, highlighting its importance in identifying equity in properties. This measure is presented as crucial for different investment strategies, whether for flips, rentals, or wholesale deals.</li></ul><p><br></p><p><strong>📊 </strong><strong style="color: var(--tw-prose-bold);">How Delta is Used in Property Evaluation</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Primary Metric</span>: Delta per Square Foot is emphasized as a key metric in filtering and finding properties. It\'s described as the difference between the listing price per square foot and the average price per square foot in the area.</li><li><span style="color: var(--tw-prose-bold);">Comparative Analysis</span>: The method considers properties similar in type and size within the same geographical area, focusing on recent sales to calculate the average.</li></ul><p><br></p><p><strong>🏠 </strong><strong style="color: var(--tw-prose-bold);">Real-World Application and Case Studies</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Detailed Examples</span>: Specific examples are provided to illustrate how Delta per Square Foot works in practice, using real properties to show the calculation and its implications on investment decisions.</li><li><span style="color: var(--tw-prose-bold);">System Functionality</span>: The platform’s functionality is discussed, showcasing how it speeds up the process of property evaluation and deal finding.</li></ul><p><br></p><p><strong>📈 </strong><strong style="color: var(--tw-prose-bold);">Strategic Insights for Investors</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Filtering Strategies</span>: Various filtering options are explored, explaining how investors can set up their searches to include Delta per Square Foot along with other metrics like estimated cash flow or flip profit to pinpoint potential deals.</li><li><span style="color: var(--tw-prose-bold);">Diverse Investment Approaches</span>: The versatility of Delta per Square Foot is highlighted, showing its application across different types of real estate investments.</li></ul><p><br></p><p><strong>🔧 </strong><strong style="color: var(--tw-prose-bold);">Technical Explanation of the Platform’s Algorithms</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Data Handling</span>: The software\'s algorithm and its approach to handling data are detailed, providing insight into how the system prioritizes and averages property data to assist investors in making informed decisions.</li><li><span style="color: var(--tw-prose-bold);">AVM and ARV Calculations</span>: The differences between Average Valuation Model (AVM) and After Repair Value (ARV) are explained, helping users understand the platform\'s valuation mechanisms.</li></ul><p><br></p><h4><strong>Insights based on numbers:</strong></h4><ul><li><span style="color: var(--tw-prose-bold);">Delta Value Impact</span>: Properties with a Delta per Square Foot of 82 indicate significant potential for investment, as seen in the case study where this metric highlighted properties undervalued compared to their neighborhood averages.</li><li><span style="color: var(--tw-prose-bold);">Filter Efficiency</span>: Setting a filter for a Delta per Square Foot minimum of 50 can significantly narrow down the search results, leading to more targeted and potentially profitable investment opportunities.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '00:01 Alright, here we go, gonna dive right into it. There is a reason that we are doing this training on delta per square foot boom right here as the first section of the series.
        00:16 If you watch the intro, I mentioned in there that, I think delta per square foot is one of the best ways to filter and find properties that have equity.
        00:27 That\'s why I like to explain it. They have equity. Maybe they make sense as a flip. Maybe it makes sense as a rental.
        00:33 Maybe it makes sense as a whole tail deal. Maybe it\'s a property that, You could put under contract. It is on the MLS.
        00:42 Put it under contract and turn around and resell that. Essentially as a whole sale deal. We did that three times by accident just in testing and launching this software.
        00:51 Umm. Delta is one of the best ways to do it. Obviously we have other ways. We can also filter and sort by estimated cash flow or estimated flip profit.
        01:06 So one of the questions that I get most often is drew why would I look at Delta when, I\'m looking for rental properties.
        01:16 Can I just filter by this estimated cash flow? Um and the answer is kind of. I like whether I\'m looking for rentals, I\'m looking for flips, or I\'m looking for deals that I\'m gonna wholesale.
        01:31 Thank you. Or deals that I know other investor friends of mine might be interested in. I\'m almost making every single filter somewhat reliant on that delta per square foot.
        01:45 And the reason is when you understand the weight. Hey that it works which we\'ll just get into here. So let\'s just look at this record.
        01:56 We\'ll give real world case study. It\'s telling us that the delta per square foot is 82 dollars. What the hell does that mean?
        02:04 Let\'s look at it. Alright so. I collapse all these other sections because just want to keep it straight to the point here.
        02:12 All this other stuff is similar to data that you would find on any other listing platform. Doesn\'t need much explanation.
        02:21 Really everything that we\'re talking about here is going to be in the auto valuations. Uhm. This is what\'s going to speed everything up, make your life easier and make it so you find deals.
        02:33 So Delta per square foot, you saw on the thumbnail it said $82. All that that is, that\'s the difference between the listing price per square foot and average price per square.
        02:44 Okay, so to give that a little bit more context, listing price per square foot is basically just how many square feet the property is divided by the listing price, right?
        02:57 Um, average price per square foot, what this is looking at, the way the data algorithm works, it\'s only looking at the same property type, so detached properties that closed in the last six months in the same zip code that are plus or 20 plus or minus 20% of the square footage of this property and it
        03:22 prioritizes properties that are closer to the subject property, right? So if in west grove, which probably is a large radius of that zip code, it\'s going to prioritizes comp.
        03:35 That are similar. Umm plus or minus 20% of the square footage that are close to it, okay? Umm. But when it\'s taking the average, it\'s taking into consideration everything, right?
        03:50 Every single sale that matches. That umm. That demographic. When we look at the high price per square foot, excuse me, that\'s only looking at the three top sales.
        04:05 So this kind of lends a little bit to the, explanation of that there. If the three highest sales averaged out, basically came to 538,000 dollars.
        04:21 So if you broke those down and looked at them on a cost per square foot base, it says the three highest comps were 271.
        04:30 So I explained that to drive the point home that high price per square foot is basically going to be the foundation of the systems way of determining ARV after repair value.
        04:45 Whereas the average price per square foot is what\'s going to be used to determine the AVM average valuation model which isn\'t a term that is very heavily used in the industry.
        05:01 We might have made it. We didn\'t. Um. But there\'s a large delineation between them too, right? So the question I get asked a lot is well if I\'m a fixed and flipped guy what did I want to just look at the delta?
        05:18 Between the listing price per square foot and the high price per square foot because if I\'m going and doing like I explained that sixty seventy eighty dollar per square foot renovation and I am predicating that on being able to sell for time.
        05:35 Top dollar shouldn\'t I be looking at the delta between the listing price per square foot and the highest? An answer is yes.
        05:42 But the reason the system pulls the delta off the difference between listing and average is because it does sometimes highlight opportunities that you would have missed if filtered in a different way like looking at the delta between list price and high price per square foot.
        06:02 Umm what I mean by that is it may be your model. Your business model that that\'s what you do. You do full gut renovations and umm you try to go through the highest possible sale price on the back end that you can get.
        06:18 That\'s all well and good but if you do that you may because I- I know I\'m guilty of it all the time.
        06:26 I have a buy box. I have a specific type of property and a specific amount of renovations that I would like to purchase.
        06:35 But if something came along that let\'s try to find something here is a good example. one. Umm. I should have more filters in here getting rid of uh high price points but I don\'t.
        06:52 Let\'s look at this. This looks like it might be pretty nice and only. I need a little bit of rehab.
        07:00 What did I do? Alright sorry about that. I have not refreshed my page in a little bit so that listing must have been cancelled.
        07:10 Umm. But show must go on so we\'ll just look at this record here. So let\'s go back real quick. It showed us that the Delta per square foot is 94 dollars a square foot.
        07:26 Umm. So yeah like if the system the way that it\'s calculating is, Umm. It\'s always accounting for 50 dollars a square foot for renovation.
        07:43 This might not need 50 dollars a square foot. I\'m sure you could spend that. I could spend that easy. Umm.
        07:49 But you might be able to go in here and do, let\'s just, look, if we come down to the calculator, by default, that\'s 50 dollars a square foot.
        08:01 We might be able to come in here and only spend 30 thousand bucks. Now if we did that, we\'re probably not going to be able to get to this foot.
        08:09 575 thousand dollar ARV, but we might be able to get to this AVM because that again is predicated on the average price per square foot.
        08:21 Umm, did the average house in that area sold for. Right? So the point is, anytime that I set filters, alerts or anything like that, if I was looking for flips, what I might do is put in that I want a minimum of.
        08:44 $40,000 in flip profit, but I\'m still leaving in a delta here. Now that\'s less relevant than what I\'ll show you here.
        08:55 Let\'s reset that. Just uh try to drive this point home. Let\'s put in. We want to have a minimum of $300 per month cash flow per door.
        09:08 And let\'s put location in. Okay, it\'s still there. Cool. Let\'s run this. Let me put in one more filter here.
        09:20 Maximum of $300,000. Okay. So if I\'m looking for rental properties, this is, even more relevant. Because this is saying that might be able to cash flow 384 hours a month on this.
        09:46 We could go into that. But since I can see right here that the Delta per square foot is negative. Subs by www.zeoranger.co You can see right there that the AVM is 113,000 bucks.
        09:59 ARV is 189,000 bucks. I probably do not want to buy that because I\'m not going to have equity. I\'m going to be upside down on it.
        10:07 So anytime that the Delta per square foot is negative it\'s something. You probably don\'t even want to consider. So if I\'m looking for rentals, same thing.
        10:19 I would put my 300 hours minimum cash flow per month, but I would also put in maybe a little bit less, maybe uh maybe 50 dollars Delta per square foot minimum.
        10:31 See that just cut my results down significantly. Um, the results are going to be much better. Okay. So hopefully that gives an intro to Delta per square foot how it works, how it makes sense and how it\'s beneficial in finding.
        10:49 In properties and how you should be using it in setting your searches and filters and alerts. And uh, the more you look at some properties and the the data in there, the more comfortable.
        11:07 You\'ll become with all of it, right? You\'ll be able to start kind of compensating for the types of renovations that you do and everything in between.
        11:19 So again, I don\'t want this to go too long. It\'s already gone longer than I hoped. But this will probably be the longest one.
        11:25 Because again, it\'s the most important and out of the dozen or so deals that me internally, we have used this software to find.
        11:36 They have all been predicated on this Delta per square foot in some way, shape or form. So, if, a great that into what you\'re doing, I promise you you\'re going to find more deals that have that equity go through them, go make offers, make deals happen.
        11:49 See you on the next one.',
                'uid_code' => 'DD2',
                'url' => 'https://youtu.be/fjTu5A5eSzA',
                'youtube_video_id' => 'fjTu5A5eSzA',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'ARV Explained: Understanding After Repair Value',
                'summary' => '📐 Understanding ARV: ARV stands for After Repair Value, a commonly used term in real estate to estimate the value of a property after necessary repairs and renovations are made.
        
        🧮 Subjectivity in Calculation: The calculation of ARV can vary significantly among different investors, making it somewhat subjective. Different investors may have different projections for the same property.
        
        💻 Platform\'s Algorithmic Advantage: Unlike manual calculations, the platform uses data algorithms for a more accurate, less subjective estimation by analyzing comparable properties based on specific criteria like location, property type, and recent sales data.
        
        🔍 Comping Process Explained: The process involves finding comps (comparable properties) sold in the last six months within a close range of the subject property\'s square footage. The highest prices per square foot among these comps are averaged to estimate the ARV.
        
        🌐 Practical Application on the Platform: Demonstrates how to utilize the platform to select comparable properties and calculate the average price per square foot to determine ARV, emphasizing a data-driven approach over emotional or instinctive decision-making.
        Insights based on numbers:
        
        ARV calculations are typically based on the three highest comps within a specific square footage and geographic criteria.
        The example used showed properties ranging significantly in price per square foot, indicating the variability in market conditions and property features.',
                'summary_html' => '<p><strong>📐 Understanding ARV:</strong> ARV stands for After Repair Value, a commonly used term in real estate to estimate the value of a property after necessary repairs and renovations are made.</p><p><br></p><p><strong>🧮 Subjectivity in Calculation:</strong> The calculation of ARV can vary significantly among different investors, making it somewhat subjective. Different investors may have different projections for the same property.</p><p><br></p><p><strong>💻 Platform\'s Algorithmic Advantage:</strong> Unlike manual calculations, the platform uses data algorithms for a more accurate, less subjective estimation by analyzing comparable properties based on specific criteria like location, property type, and recent sales data.</p><p><br></p><p><strong>🔍 Comping Process Explained: </strong>The process involves finding comps (comparable properties) sold in the last six months within a close range of the subject property\'s square footage. The highest prices per square foot among these comps are averaged to estimate the ARV.</p><p><br></p><p><strong>🌐 Practical Application on the Platform: </strong>Demonstrates how to utilize the platform to select comparable properties and calculate the average price per square foot to determine ARV, emphasizing a data-driven approach over emotional or instinctive decision-making.</p><p>Insights based on numbers:</p><p><br></p><p>ARV calculations are typically based on the three highest comps within a specific square footage and geographic criteria.</p><p>The example used showed properties ranging significantly in price per square foot, indicating the variability in market conditions and property features.</p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '0:01 Alright, here we go, quick video on ARV, this should need a little bit less of an explanation because the term is well known ARV after repair value to use a lot in the industry and that\'s AVM needs a little bit more.
        0:20 For explanation because people don\'t usually associate with the average day associated with the nicest that a property can possibly be.
        0:30 Umm. Now what I will say that I feel like I need to put on like boxing gloves for this explanation is, Everybody calculates ARV differently, right?
        0:44 Umm. You can take the same deal to a hundred different investors and you\'re going to come back with a hundred and one different ARV predictions projections whatever you want to call it in an app.
        0:59 So ultimately ARV is somewhat subjective, we\'ll say. Now the platform itself and the way that it calculates and analyzes everything and some of the data algorithms it\'s much more accurate than those hundred people.
        1:15 Well that we would take a property too because it\'s not subjective and it\'s looking at all of the data. However, what no algorithm can ever take into account is the type of renovation that you do versus I do on a property or from the next person.
        1:34 . Umm, and the material selection they have and the design level of expertise they have and everything else, right? So it makes it largely incalculable.
        1:45 However, again, the way that the data algorithm works on here. Umm, I\'ll explain it. To you as simply as I can, but let\'s go into a record here.
        1:59 Okay, so ARV, again, this is predicated based off of the high price per square foot, which again is looking at comparable properties.
        2:08 . In the same area, same zip code, same property type. It\'s not going to take twins, it\'s not going to take, umm, uh condos or anything else.
        2:20 It\'s not going to take any other property type. It\'s going to look for plus or minus twin. Any percent of the square footage of the subject property.
        2:27 Umm, so comps. It\'s going to look for comps. It\'s old in the last six months. And then it\'s taking the three highest price per square foot comps.
        2:39 And averaging them. Right, so we can kind of do it just looking at the comp section here quick and easy.
        2:47 Because it\'s already giving us same property type closed within the last six months. Plus or minus 20% of the square footage.
        2:56 It doesn\'t have distance in here. Okay, so. We\'ll talk about this on accuracy score, but it\'s a good segue into it.
        3:06 Um, if the accuracy score is 9.9, that means that it was able to find enough comps that were perfectly comparable.
        3:15 So one of the metric. Six of perfectly comparable would be all of those things I just went over as well as enough results.
        3:25 usually five plus results that are less than a mile away. So in this area, we may have to go a little bit further out, but when, in the system does go further out and starts to break some of those conditional rules of finding comparable properties.
        3:43 It starts to lower this accuracy score. Okay? So again, I don\'t want to go too deep into that here. There\'s going to be another video on, accuracy score and um deep dive into it a little bit more.
        3:57 But the purpose of what I\'m trying to show here is, let\'s just see if we can find, what did it say?
        4:06 The high price per square foot, 271 bucks. Just square foot. You might have to go five miles. But it\'s still only gonna look in that zip code.
        4:20 It\'s important to highlight. Alright so here\'s one. This is higher. So I can select that. And here\'s another one three hundred thirty four hours of square foot.
        4:35 Low, low, low. I might not have done a great job here. But. Whatever. Let\'s just select those three. So averaging those out gives us two hundred sixty four hours of square foot.
        4:52 So I might not have selected the highest one. There\'s other stuff in there. But just to demonstrate that\'s basically what it\'s doing.
        5:00 Now if I wanted to spend the time I could go. And look at each one of these properties and see, but I can tell from a surface level.
        5:09 These are probably going to be the nicest ones, right? So I\'m not going to go super deep into that. But that is what ARV is predicated on.
        5:17 The three highest price per square foot comps multiplied by the square footage of the subject property. Okay? Um. So hopefully that gives you a data driven approach to how to do your comping in the future and it gives insight past just how to- you leverage it on this platform and how the data on the 
        5:40 platform works. Make sure that any analysis that you\'re doing is data driven and not just emotion or uh gut driven.
        5:50 So that\'s ARV. Hopefully that helps. See you on the next one.',
                'uid_code' => 'DD4',
                'url' => 'https://youtu.be/8emMUqY6q3s',
                'youtube_video_id' => '8emMUqY6q3s',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Understanding Highest and Lowest Three Averages',
                'summary' => '📊 Defining Averages: Simplified explanation of how to calculate the highest and lowest three averages from comparable property sales.
        
        📍 Contextual Factors: Emphasis on using properties within the same zip code and similar square footage for reliable comparisons.
        
        💹 Impact of Outliers: The method averages out extreme sale prices to provide more stable data, avoiding skewed results.
        
        🏠 Real Estate Insights: Discusses how the lowest three averages can indicate potential deals based on price per square foot.
        
        Insights based on numbers:
        Average of highest three sale prices: $324,000
        Outlier sale price considered in example: $700,000
        Lowest three average: $161,000
        Example property listed at $159,000
        Lowest price per square foot in the example: $124',
                'summary_html' => '<p>📊 Defining Averages: Simplified explanation of how to calculate the highest and lowest three averages from comparable property sales.</p><p><br></p><p>📍 Contextual Factors: Emphasis on using properties within the same zip code and similar square footage for reliable comparisons.</p><p><br></p><p>💹 Impact of Outliers: The method averages out extreme sale prices to provide more stable data, avoiding skewed results.</p><p><br></p><p>🏠 Real Estate Insights: Discusses how the lowest three averages can indicate potential deals based on price per square foot.</p><p><br></p><p><em>Insights based on numbers:</em></p><p>Average of highest three sale prices: $324,000</p><p>Outlier sale price considered in example: $700,000</p><p>Lowest three average: $161,000</p><p>Example property listed at $159,000</p><ol><li>Lowest price per square foot in the example: $124</li></ol>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '0:00 All right, and this one we\'re gonna talk about the highest three average as well as the lowest three average, again get my face out of here, so very simply this is gonna be a simple definition again, this is looking comparable properties, which again that means something in the same zip code plus or 
        0:23 minus 20% of the square footage, um. So on and so forth and it\'s looking at the highest three properties averaged.
        0:34 Next. So that could mean that of those highest three, there was a $300,000 property, a $400,000 property and I don\'t know I\'m gonna mess this up because I\'m bad at mental math.
        0:50 Like a $280,000 property. And, Those were the three highest umm comparable sales in the last six months. But then averaging them out gives you $324,000.
        1:03 So that\'s really important to note because there are times where there\'s one totally rogue off the wall. . No price, right?
        1:14 Like to give a different example. The the two otherwise highest sale prices might have been $300,000. But then there was one that was like $700,000.
        1:26 You know like that doesn\'t make any sense at all. So this way the way the data algorithm works is it\'s kind of gonna eradicate any of those umm.
        1:39 Crazy outliers because it\'s always taking an average of the highest or lowest three. So again. And that could be applicable here because the two otherwise lowest sold prices could be 150,000.
        1:58 But there was one that was you know 30,000 or something. So rather than relying on that it\'s simple. Simply taking an average of the three lowest sale prices of comparable properties in the last six months.
        2:12 It gives you a more reliable data set. And what these are really helpful especially the lowest three average. It usually paints a picture for you.
        2:25 If the lowest umm ehm three average is a hundred sixty one thousand. And the list price here is hundred fifty nine thousand.
        2:36 Maybe that\'s a deal. What that\'s not taking into consideration though is the price per. For square foot of those. Okay so that\'s when it would be relevant to look at this as well.
        2:49 But we can see They are also list prices 132 bucks and the lowest price per square foot has 124 bucks.
        2:59 So we would basically be looking, by this property at the lowest price per square foot that any property has been purchased in the last six months.
        3:08 That leads to the idea that it might be a deal and it might make sense. So hopefully that\'s a good explanation.
        3:17 See you on the next.',
                'uid_code' => 'DD8',
                'url' => 'https://youtu.be/GqZdRYmkGqk',
                'youtube_video_id' => 'GqZdRYmkGqk',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Understanding Estimated Cash Flow',
                'summary' => '🔍 Detailed Insights:
        
        Understanding Algorithms: The video explores how the software uses algorithms to estimate cash flow on properties. It emphasizes the need to understand and adjust the default settings based on real conditions.
        
        Variables and Assumptions: Key variables such as average rent, renovation costs, and financing terms like down payments and interest rates are pivotal in these calculations.
        
        Data Sources: Information is derived from multiple sources, including Zillow and Rentometer, ensuring a broad dataset for more accurate analysis.
        
        Practical Adjustments: The instructor demonstrates how to adjust for real-world scenarios, like higher than anticipated renovation costs or changes in rental income expectations.
        
        Insights based on numbers:
        The default system settings suggest a $10 per square foot renovation cost, but practical examples show this to be inadequate, often requiring adjustments to $86,000 for an accurate estimate.
        The system also defaults to a 30-year mortgage assumption, highlighting how critical each preset value is in the cash flow calculation.',
                'summary_html' => '<p><strong>🔍 Detailed Insights:</strong></p><p><br></p><p><strong>Understanding Algorithms: </strong>The video explores how the software uses algorithms to estimate cash flow on properties. It emphasizes the need to understand and adjust the default settings based on real conditions.</p><p><br></p><p><strong>Variables and Assumptions:</strong> Key variables such as average rent, renovation costs, and financing terms like down payments and interest rates are pivotal in these calculations.</p><p><br></p><p><strong>Data Sources:</strong> Information is derived from multiple sources, including Zillow and Rentometer, ensuring a broad dataset for more accurate analysis.</p><p><br></p><p><strong>Practical Adjustments: </strong>The instructor demonstrates how to adjust for real-world scenarios, like higher than anticipated renovation costs or changes in rental income expectations.</p><p><br></p><p><em>Insights based on numbers:</em></p><p>The default system settings suggest a $10 per square foot renovation cost, but practical examples show this to be inadequate, often requiring adjustments to $86,000 for an accurate estimate.</p><ol><li>The system also defaults to a 30-year mortgage assumption, highlighting how critical each preset value is in the cash flow calculation.</li></ol>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '0:02 Here we go, a fun one estimated cash flow alright so let\'s dive right into it here and let\'s scroll around the map let\'s uh.
        0:17 Let\'s look in Chester. Okay so one of the things that I love to do is address when somebody looks at some numbers like this and they\'re like Drew the software is not working at all man it\'s telling me that I could.
        0:35 Cashflow with 1000 bucks a month on this piece of crap property in Chester for 26,000 bucks. I don\'t think I could cashflow that much.
        0:46 Pump the brakes. Let me explain to you how the how the numbers work how the data algorithm work. So we\'ll use this as our example.
        0:58 Alright. So first and foremost we gotta look at our average rent. Average rent is, what is predicating everything on. Obviously we do have some lower rent comps, higher rent comps.
        1:17 And this is basically pulling from two different sources. It\'s pulling from Zillow Reynolds. And in some cases it\'s also pulling from Renometer.
        1:27 and. It\'s our database and then it\'s kind of blending it all together. So we have a larger data set. Um.
        1:35 But surface level, the system is going to look at the average rent and take that into consideration. Okay. So we\'re going to come down to.
        1:45 The rental calculator here and we see that same number that we saw on the thumbnail that it\'s going to cast low $1,062 per month.
        1:56 That sounds great. But looking at this, I don\'t think that\'s going to cast low $1,000 bucks a month. So let\'s get.
        2:03 So you have to take into consideration all of the predetermined variables that the system is going to plug in. One of the biggest variables is obviously always going to be renovation.
        2:16 Okay. So by default the system is, is always going to plug in for a rental rehab when we\'re talking about cash flow.
        2:26 We\'re talking about, um, the rental calculator here. It\'s always going to plug in $10 a square foot for renovation. Now obviously looking at this even from these few f photos, we know that we\'re going to spend way more than $10 a square foot, okay?
        2:44 So that\'s why this number is very skewed. Ten bucks a square foot is only $6500 in rehab. We know we\'re spending way more than that here, okay?
        2:54 So that\'s first and foremost. So let\'s let\'s fix that. Let\'s say, I mean this thing is pretty bad. It\'s also very small though, 652 square feet.
        3:04 So you\'re not going to spend a fortune. But let\'s just say for the sake of it here, let\'s say you\'re going to spend $86,000 in rehab.
        3:14 We could see a bunch of our numbers just change their automatically for us. Um, also important to mention that by default, the system is calculating that you\'re getting a mortgage on 70% of the purchase plus rehab cost.
        3:34 So it\'s basically saying that you\'re putting 30% down and you\'re getting a 30 year fixed rate at 7%. If we wanted to change any of these variables, we could.
        3:47 But again, I just want you to understand surface level when you\'re looking at that thumbnail before you even go into- a property to analyze it.
        3:54 So you can understand how some of this stuff is calculated and it\'ll help again to auto correct for you. If I was just scrolling around and I saw this, I automatically know that number is not accurate, right?
        4:08 This number, the estimated flip profit is probably not accurate either, right? Um. So let\'s go a little bit deeper here.
        4:17 So we corrected this from 6,000 to 86,000. We still got our mortgage in there. We\'ll leave these other variables the same.
        4:27 Okay, if we go to the income and expenses section of the calculator, there we got that 1485 like we saw up here, the average rent.
        4:37 That\'s where it\'s pulling it from. Okay, and some of the other things that the system is automatically. Please. Taking it into consideration is the taxes.
        4:48 That sounds really cheap. $16 per year. And it\'s breaking these all down into monthly expenses. So it\'s automatically calculating that insurance is going to be- 45 hours a month.
        5:02 They can see you gotta account for 74 hours a month. These are basically all 5% of gross. Okay? So if you always have management and you want to plug that into there and- say uhh okay that\'s gonna be 148 hours a month for me.
        5:23 You could plug that in. I don\'t umm. We self-manage right now in house so I\'m gonna leave that out. Umm.
        5:32 And if there was anything else that you wanted it would just for if you looked at some rent comps and you\'re like I don\'t know I think on this crappy block I\'m not gonna be able to get 1485.
        5:42 Maybe section 8 only approves 1200 there. Can make that adjustment. Plug that in. There you go. Umm. Then come back to the summary.
        5:52 Thank you. And we can see the overview of everything from here. So with those corrected numbers it\'s looking like we\'re gonna cash flow 447 dollars a month.
        6:03 Cash on cash, cap rate, whatever. Got all this stuff here. We\'re gonna be negative on equity. Umm. Our NOI is 973.
        6:13 And I like to look at these also. A lot of rental calculators don\'t look at this stuff and it\'s actually really significant especially if you have a large portfolio.
        6:24 Is the debt pay down. Umm. Which maybe it\'s a good thing that a lot of people don\'t calculate it because it\'s, uh, side on.
        6:31 But my tenant is basically paying down $220 per month worth of debt pay down on, you know, that 30 year amortization mortgage.
        6:42 And that property is also appreciating at a rate of $282 per month depreciation. Obviously a little bit different. It\'s not a realized gain.
        6:53 But if I\'m just looking at this and I\'m saying I\'m cash low in 447 dollars a month in reality, I\'m actually making um another 500 hours a month between debt.
        7:05 Pay down an appreciation that I\'m not seeing if I just look at the cash flow number. But anyway, that\'s not really the point.
        7:13 Just had to mention that because we were there. So hopefully that\'s a quick overview of the estimated cash flow and how it\'s calculated.
        7:22 . Again, from a surface level, before even clicking into this, I could look at it and say, well, the $10 a square foot in renovations that the system is calculating for by default is definitely not going to cover this.
        7:38 And maybe I don\'t like buying rentals and chests there anyway. But if I- Do, maybe it\'s worth looking at. So hopefully that helps you find more rentals based on a estimated cash flow.
        7:51 And last thing to drive home here is again, when you\'re searching and filtering for rental properties, I would always suggest.
        8:00 test. Not just looking at a minimum cash flow per month, but also put a delta per square foot in here.
        8:08 Because again, that, that lends to the fact that the property that you\'re looking at should have equity as well. There\'s going to be some variables in there, but again, the more you know.
        8:19 About how this works, better off you\'ll be.',
                'uid_code' => 'DD6',
                'url' => 'https://youtu.be/kMDM07WJpTI',
                'youtube_video_id' => 'kMDM07WJpTI',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Accuracy Scores and the Scoring System',
                'summary' => '📊 Accuracy Scores Explained: The video delves into the accuracy scoring system used to evaluate properties, highlighting how scores are derived from comparability with nearby properties.
        
        🔍 Scoring System Insights: It starts with a perfect score (9.9) and deducts points based on the lack of comparable properties, adjusting criteria like property size and type to find sufficient data for reliable analysis.
        
        📉 Practical Implications: Lower accuracy scores (below 7 for value and below 5 for rentals) suggest the need for further due diligence to ascertain property value and rental estimates accurately.
        
        🏡 Case Study Application: Demonstrates using a specific property to explain how scoring adjusts when ideal comparables are unavailable, emphasizing the geographical and property type variations.
        
        Insights based on numbers:
        9.9: Ideal accuracy score indicating high reliability of data.
        25 to 30: Number of rental comparables needed for a top-tier rental accuracy score.
        5 Miles: Distance considered when widening the search for comparable properties.',
                'summary_html' => '<p><strong>📊 Accuracy Scores Explained: </strong>The video delves into the accuracy scoring system used to evaluate properties, highlighting how scores are derived from comparability with nearby properties.</p><p><br></p>
        <p><strong>🔍 Scoring System Insights:</strong> It starts with a perfect score (9.9) and deducts points based on the lack of comparable properties, adjusting criteria like property size and type to find sufficient data for reliable analysis.</p><p><br></p>
        <p><strong>📉 Practical Implications: </strong>Lower accuracy scores (below 7 for value and below 5 for rentals) suggest the need for further due diligence to ascertain property value and rental estimates accurately.</p><p><br></p>
        <p><strong>Our Comping Algorithm Explained</strong></p>
        <p>Our comping algorithm is designed to find the best possible comps for your property using a systematic and reliable approach. Here\'s how it works:</p><p><br></p>
        <p><strong>Main COMP Rules:</strong></p>
        <ol>
            <li><strong>Closed within the last 6 months:</strong>
                <ul>
                    <li>The property must have sold within the past six months.</li>
                    <li>The property must have a recorded finished square footage and a price per square foot.</li>
                </ul>
            </li>
            <li><strong>Same Zip Code:</strong>
                <ul>
                    <li>The comp must be in the same zip code as your property.</li>
                </ul>
            </li>
            <li><strong>Same Property Type:</strong>
                <ul>
                    <li>The comp must be the same type of property (e.g., single-family home, condo) as your property.</li>
                </ul>
            </li>
            <li><strong>+/- 20% Square Footage:</strong>
                <ul>
                    <li>The comp must have a square footage within 20% more or less of your property\'s square footage.</li>
                </ul>
            </li>
            <li><strong>Within 1 Mile:</strong>
                <ul>
                    <li>The comp must be within 1 mile of your property.</li>
                </ul>
            </li>
        </ol>
        <p><br></p>
        <p><strong>If all these conditions are met, the comp is given a high accuracy rating of 9.9%.</strong></p><p><br></p>
        <p><strong>Finding Enough Comps</strong></p>
        <p>Our goal is to find at least 10 comps that meet these criteria. If we can\'t find 10 comps, our system will relax these rules one by one to include slightly less perfect matches until we reach 10 comps. Here’s how we adjust:</p><p><br></p>
        <ol>
            <li><strong>Rule B1: Square Footage Adjustment:</strong>
                <ul>
                    <li>We expand the acceptable range to 80%-120% of your property\'s square footage.</li>
                    <li>If this rule is broken, the accuracy score drops by 2 points (from 9.9 to 7.9).</li>
                </ul>
            </li>
            <li><strong>Rule B2: Distance Adjustment:</strong>
                <ul>
                    <li>We accept comps that are within the same zip code but more than 1 mile away.</li>
                    <li>If this rule is broken, the accuracy score drops by 2 more points (from 7.9 to 5.9).</li>
                </ul>
            </li>
            <li><strong>Rule B3: Property Type Adjustment:</strong>
                <ul>
                    <li>We include comps that are not the same property type.</li>
                    <li>If this rule is broken, the accuracy score drops by 3 more points (from 5.9 to 2.9).</li>
                </ul>
            </li>
        </ol>
        <p><br></p>
        <p><strong>Example Scenario</strong></p>
        <p>Let\'s say we need 10 comps, and we find:</p>
        <ul>
            <li>5 comps with a 9.9 score.</li>
            <li>We relax Rule B1 to find 3 more comps with a 7.9 score.</li>
            <li>We relax Rule B2 to find 2 more comps with a 5.9 score.</li>
            <li>We relax Rule B3 to find 1 more comp with a 2.9 score.</li>
        </ul>
        <p><br></p>
        <p>Our final accuracy score is the average of all these comps, which would be 8.79.</p><p><br></p>
        <p><strong>Key Takeaways</strong></p>
        <ul>
            <li>Our system prioritizes finding the most accurate comps first.</li>
            <li>We ensure a minimum of 10 comps to provide a robust data sample.</li>
            <li>If necessary, we adjust our criteria to include less perfect matches, but we always aim to keep the accuracy as high as possible.</li>
        </ul><p><br></p>
        <p>This approach balances the need for a sufficient number of comps with the desire to maintain high data quality, ensuring you get the most reliable property valuation.</p>
        ',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '0:01 Alright, on this one we are gonna touch on accuracy scores. Okay, um, going to pick this record here. Okay, so this is what we\'re talking about accuracy score.
        0:17 I face out of here. Accuracy score based on value as well as accuracy score based on rental. We touched on this slightly in some of the other trainings and data definitions, but I want to drill the point home here and make it.
        0:35 It\'s specifically tailored to this. So to understand the way the system and the algorithm looks for comparable properties and everything is really where this all stems from.
        0:50 So just like an appraiser or even uhh as an investor would look at and analyze comparable properties. The system basically has an internal ranking and scoring system.
        1:04 So in a perfect world which this property is less than perfect and we\'ll talk about that in a second. Thank much.
        1:11 There would always be a comparable property that is less than a mile away from our subject property and it\'s plus or minus 20% of the square footage of the subject property.
        1:27 Same number of beds, same number of- bathrooms, um. You know, a super similar property that you could base your property off of.
        1:38 In reality, we know that\'s not always going to be the case. So essentially our scoring system, the way that it works, is if the system does- does have more than five properties at least five properties that are perfectly comparable to the subject property.
        1:58 It basically starts off with a perfect score. It\'s got a 9.9. That\'s the highest it can get. So when you see an accuracy score- of 9.9, you know you can rely heavily on the average price per square foot, the on average rent.
        2:20 Low rent, high rent. Okay, so if you can rely on those, you know that there\'s going to be a lot of comparables and umm.
        2:30 Yeah, just you can rely on the data much simpler. The system essentially starts taking, Points away when there\'s not enough properties that are directly comparable.
        2:46 So it starts breaking some of those rules in a chronological order to try to go find enough data to be able to give some sort of analysis.
        2:56 . So we have data to go off of. So basically one of the first rules that the system will break is it will look for properties that are more than 20% different on the square footage from the subject property.
        3:14 So again I might be able to demonstrate this here a little bit with this comps section because I don\'t want to go into the database I will lose 90% of you with the nerd stuff.
        3:29 So if it\'s just looking for detached plus or minus 20% of the square footage let\'s go 5 miles it\'s not going to find anything anyway so let\'s change this to 12 or 1400 square foot still didn\'t find anything let\'s change it to 400 square feet still might not find anything.
        3:52 okay so it may end up breaking the rule and going back further in timeline still didn\'t find anything then it may start adding other property types so obviously now it\'s finding some properties but they\'re not the same property type so that\'s not as comparable and I knew this was gonna be the case because
        4:20 for one I don\'t even know where skid pack is so it\'s probably remote far out farmland area in Pennsylvania so there\'s probably less comps and data in the first place but usually even in any of the try state area of Pennsylvania Jersey Delaware that we do a lot of business anytime you see a property over
        4:43 3000 square feet and definitely over 4000 square feet you know there\'s probably not gonna be a whole lot of supporting data there\'s some exceptions and caveats to that but you get my point umm so without making this a super long incredibly data driven and analytical video just know that as soon as the
        5:08 accuracy score starts dropping I would say if it\'s below a 7 on the value it\'s not super reliable and you should do a little bit more due diligence than you typically would to even determine if this is a good deal or not umm you saw just through this demonstration right here that there\'s a lot of properties
        5:28 but not detached single family homes I guess for some reason that have sold in the last six months and maybe if you went back two years you would find some more data of single families but it\'s up to you to determine is that data more reliable than things that are a different property type but they are
        5:50 really close by this is only a quarter mile away so that\'s a surface level overview of the accuracy scored for value for rentals basically I forget what the number is off the top of my head but to give you an idea to have a 9.9 score for the rental valuation it\'s something like there has to be at least
        6:16 I want to say 25 rental comps or something. And in that range 25 to 30 rental comps to give a 9.9 accuracy score so you can see right there umm it\'s got only 12 rent comps so umm that\'s still pretty darn good so.
        6:39 That number that I gave for a cutoff on my mental reliability of the estimation for value I would probably say anything less than a 7 is not that reliable however on the rental side probably anything under a 5 or a 4 is when I\'d start the question the rental valuations and everything but you can see 
        7:02 here as well the number of rental comp so hopefully that helps and uhh see you on the next.',
                'uid_code' => 'DD9',
                'url' => 'https://youtu.be/JluAifhmxQ0',
                'youtube_video_id' => 'JluAifhmxQ0',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Understanding List Price per Square Foot',
                'summary' => '📈 Key Concepts: Explains list, average, and high prices per square foot in real estate valuation.
        
        🏠 Importance of Square Foot Pricing: Demonstrates how appraisers and systems like AVM use square foot pricing for property valuation.
        
        🔍 Analysis of Prices: Discusses averaging high prices from top three comparable properties for accuracy in high price per square foot.
        
        Insights based on numbers:
        Average price per square foot is $185, derived from comparables sold in the last six months.
        Example high price per square foot calculation yields $251, averaging from three top sales: $241, $261, and $251.
        ARV calculation example: $251 per square foot x 1216 square feet = $305,216.',
                'summary_html' => '<p>📈 Key Concepts: Explains list, average, and high prices per square foot in real estate valuation.</p><p><br></p><p>🏠 Importance of Square Foot Pricing: Demonstrates how appraisers and systems like AVM use square foot pricing for property valuation.</p><p><br></p><p>🔍 Analysis of Prices: Discusses averaging high prices from top three comparable properties for accuracy in high price per square foot.</p><p><br></p><p><em>Insights based on numbers:</em></p><p>Average price per square foot is $185, derived from comparables sold in the last six months.</p><p>Example high price per square foot calculation yields $251, averaging from three top sales: $241, $261, and $251.</p><ol><li>ARV calculation example: $251 per square foot x 1216 square feet = $305,216.</li></ol>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '0:02 Alright, congratulations you made it to a quick one in this video. We are just going to talk about list price per square foot, average, and high price per square foot.
        0:12 So if you watched any of the prior training videos, we kind of drove home the point already on what these are, how they work.
        0:19 And why they are relevant. So to break it down real simply, in this specific video, listing price per square foot is very simply the square footage of the subject property divided by the list price or vice versa.
        0:35 I probably explained that backwards. But you get what I mean there. It\'s really important and applicable to break things down to a price per square foot.
        0:44 It\'s a great way to comp. It\'s a great way that appraisers look at and value properties. So we do it as well.
        0:52 Average price per square foot. This is. It\'s just taking the average of all similar comparable properties that have sold in the last six months and averaging them all out.
        1:03 So there could be some hundred hours per square foot sold listings in there. There could be some 300 hours per square foot.
        1:10 But the average across all of them is 185 hours. Okay, so that\'s basically going to be your middle of the road property.
        1:19 High price per square foot is going to be your nicest properties. And this is typically taking the three highest and averaging them.
        1:30 So there may have been a. 241, a 261, and a 251. And they averaged out to 251 dollars a square foot.
        1:41 But it\'s not taking a one-off crazy comp. It\'s always averaging the top three for the high price per square foot.
        1:51 It is an average. So hopefully that gives a quick overall summary of how that stuff works. And again, the average price per square foot is how the system determines AVM.
        2:04 And the the high price per square foot is how the system determines ARV. very simply the $251 a square foot times 1216 square feet gives us an ARV of $305,000.
        2:22 See you on the next one.',
                'uid_code' => 'DD7',
                'url' => 'https://youtu.be/RKrSLMyTfCs',
                'youtube_video_id' => 'RKrSLMyTfCs',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Flip Profit estimation - how it works',
                'summary' => '🏚️ Profit Estimations Explained: The video clarifies how profit estimations are calculated in real estate flips, particularly highlighting that the default system calculates renovation costs at $50 per square foot.
        
        💡 System Adjustments and User Input: The presenter discusses the importance of adjusting the system\'s default settings to reflect more accurate costs based on personal experience, such as adjusting the cost per square foot for renovations.
        
        📊 Real Examples and Modifications: Through a practical example, the video demonstrates how to modify estimates to match expected renovation costs, showing calculations for different renovation scenarios.
        
        🚀 Highlighting Opportunities: The system\'s role in identifying profitable opportunities is emphasized, along with the importance of user analysis to adapt the findings to their specific needs.
        Insights based on numbers:
        
        The video discusses various numerical examples, such as:
        The system\'s default renovation cost of $50 per square foot.
        Adjustments to $60 per square foot for a specific property to reach an estimated after-repair value (ARV) of $498,000, resulting in a significant profit estimate.',
                'summary_html' => '<p><strong>🏚️ Profit Estimations Explained:</strong> The video clarifies how profit estimations are calculated in real estate flips, particularly highlighting that the default system calculates renovation costs at $50 per square foot.</p><p><br></p><p><strong>💡 System Adjustments and User Input: </strong>The presenter discusses the importance of adjusting the system\'s default settings to reflect more accurate costs based on personal experience, such as adjusting the cost per square foot for renovations.</p><p><br></p><p><strong>📊 Real Examples and Modifications:</strong> Through a practical example, the video demonstrates how to modify estimates to match expected renovation costs, showing calculations for different renovation scenarios.</p><p><br></p><p><strong>🚀 Highlighting Opportunities:</strong> The system\'s role in identifying profitable opportunities is emphasized, along with the importance of user analysis to adapt the findings to their specific needs.</p><p>Insights based on numbers:</p><p><br></p><p><em><u>The video discusses various numerical examples, such as:</u></em></p><ol><li>The system\'s default renovation cost of $50 per square foot.</li><li>Adjustments to $60 per square foot for a specific property to reach an estimated after-repair value (ARV) of $498,000, resulting in a significant profit estimate.</li></ol>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => '0:02 Alright, flip, profit, estimates. We got it right here in the thumbnail, oops, didn\'t mean to actually click it, hmmm, we got it here, right, we got it everywhere.
        0:13 So what is that? How is it calculated? Surface level? Makes a lot of sense. I can make a hundred ten thousand dollars if I flip this.
        0:22 But Drew, how is that calculated? And how do I know if it\'s right? How does it know that I am going to make a hundred ten thousand dollars on that?
        0:29 Again, like a bunch of the other data definitions and terminology and valuations, it doesn\'t. However, if you understand how this works and how it\'s calculated, again it\'ll make your life that much easier to kind of self adjust for the way some of the data algorithms work.
        0:52 And very simply to get right to the point on this one. The system I mentioned it in prior trainings that the system by default when it\'s looking at a flip, it\'s automatically calculating $50 per square foot in renovation.
        1:14 So let me just, pull a calculator real quick, make sure that that math maths 13 or I\'m sorry 1836 times 50, yes.
        1:25 Equals 91,800 dollars. So that\'s what the system is always taking into consideration. Now you might say, Drew, I always, spend 75 hours of square footer.
        1:36 I always spend 30 hours of square footer. Whatever your number is. This is why a lot of it is subjective.
        1:42 Umm. You\'re gonna have your numbers and you can plug them in here. That\'s why from a surface level, just looking at this alone.
        1:54 Isn\'t incredibly helpful. What this does really well. What the system does really well is highlight opportunity. Then you have to do a little bit more work from there to analyze and correct any of the numbers that don\'t match.
        2:10 Thank you. What would make sense for you? So what myself and a lot of users that I\'ve trained on this platform do is just kind of look through some of the pictures real quick and then you can kind of adjust from there if you\'ve done a lot of renovations or flips or rental rehabs whatever.
        2:28 You probably do have an idea of a cost per square foot that you spend on renovations and if I were to just look through here real quick.
        2:39 Now if I was trying to get this to be one of the nicest houses so I could hit this. It\'s $498,000 ARV.
        2:48 For me personally I would look at this real quickly and be like I\'m probably gonna spend. Doesn\'t look that old.
        2:57 I\'m not gonna go super deep into it. I\'m just gonna say we\'re gonna spend $60 a square foot so. I\'m just gonna revise this number.
        3:05 I do 1836 times 60 which gives me let\'s just call it $110,000. Okay. So I can play with some of the other variables in the flip calculator.
        3:17 I\'m not gonna go through all of this in the video to make it longer because it\'s pretty self-explanatory. Umm, I got six months in here.
        3:26 Let\'s leave that. Let\'s just go over to the ROI summary. Alright, so this is saying that if we sold at that 498,000 bucks, um we\'d end up with an $88,000 flip-off.
        3:39 That doesn\'t look too bad. We just found it quick and easy. Just with some simple filters, sure the number is not the same as what is displaying on the thumbnail here.
        3:50 Because we modified some things. Now we\'ll mention in the future you may be watching this video. Thank you very much.
        3:56 In the future we intend to make the system so you can customize some of those variables. So instead of by default the system saying $50 a square foot for renovation you could plug in and say $75 a square foot for renovation.
        4:15 . That doesn\'t exist yet and I will preface that by saying I don\'t know when that\'s coming. Um but even without it again it\'s always going to be subjective to a degree because not even if that\'s your average number that you spent 75 hours.
        4:31 Just square foot on renovations. There\'s going to be some properties that you don\'t need to spend that and there\'s going to be some properties that you spend more.
        4:39 So again don\'t get too hung up on that stuff. The real intention behind all of this is to highlight opportunity for you so you can look at it and spend not a lot of time you\'re going spend significantly less time than you would on any other platform.
        4:55 But I can go through and analyze almost any property after looking at them here in a minute or less and decide if it\'s something that we\'re actually interested in doing or not.
        5:07 So. You\'ll get there as well. Spend the time and I\'ll see you on the next screen.',
                'uid_code' => 'DD5',
                'url' => 'https://youtu.be/1Qp-ofZwYUk',
                'youtube_video_id' => '1Qp-ofZwYUk',
            ],
            [
                'category' => 'Filters & Sorting',
                'embed_code' => null,
                'topic' => 'Filtering Properties: Finding Deals Made Easy',
                'summary' => '📊 Efficient Property Filtering:
        - Highlight: Simplification of property searches.
        - Details: Introduces basic and advanced filtering options available on the platform, focusing on reducing the overwhelming number of potential properties to a manageable, targeted list that meets specific investment criteria.
        
        🔍 Advanced Search Capabilities:
        - Highlight: Utilization of specialized filters.
        - Details: Discusses the importance of Gold filters, location-based filtering, and exclusion options, enhancing the user\'s ability to pinpoint exactly the type of properties they are interested in.
        
        📅 Days on Market and Other Filters:
        - Highlight: Strategic use of Days on Market.
        - Details: Emphasizes how using Days on Market alongside other filters can identify properties likely to accept lower offers, improving negotiation leverage.
        
        Insights based on numbers:
        - Utilizing advanced filtering narrowed down a vast database of listings, demonstrating how strategic filtering significantly increases the efficiency of the search process.
        ',
                'summary_html' => '<p>📊 Efficient Property Filtering:</p><ul><li>Highlight: Simplification of property searches.</li><li>Details: Introduces basic and advanced filtering options available on the platform, focusing on reducing the overwhelming number of potential properties to a manageable, targeted list that meets specific investment criteria.</li></ul><p><br></p><p>🔍 Advanced Search Capabilities:</p><ul><li>Highlight: Utilization of specialized filters.</li><li>Details: Discusses the importance of Gold filters, location-based filtering, and exclusion options, enhancing the user\'s ability to pinpoint exactly the type of properties they are interested in.</li></ul><p><br></p><p>📅 Days on Market and Other Filters:</p><ul><li>Highlight: Strategic use of Days on Market.</li><li>Details: Emphasizes how using Days on Market alongside other filters can identify properties likely to accept lower offers, improving negotiation leverage.</li></ul><p><br></p><p>Insights based on numbers:</p><ul><li>Utilizing advanced filtering narrowed down a vast database of listings, demonstrating how strategic filtering significantly increases the efficiency of the search process.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Filters & Sorting',
                'transcript' => '00:01 Alright, this segment is gonna be on filtering properties, because if you can\'t filter this is what you get, you get a million properties, and the core of what you\'re trying to do is find flips, find Reynolds, find deals that you can wholesale, find deals for your clients, find development deals, whatever
        00:22 it is, but all of it is predicated on finding deals. And the truth is, there\'s a lot of noise. There\'s a lot of listings.
        00:31 How do you know what are the good ones to look at? How do you know what makes a good flip?
        00:36 How do you know what makes a good rental? And prior to this software, the answer was spend a lot of time and go through absolutely everything.
        00:47 That\'s not feasible. It wasn\'t feasible for me. That\'s why this software exists today because we were spending um between 40 up to- 80 hours at one point a week with different virtual assistants and everything.
        01:05 Literally scouring every property on the MLS, analyzing all of them and seeing what fit or buy box. And I knew there had to be a better way and there was.
        01:16 So we created this. So now hopefully you already went through all of the video training series on the data definitions and how data displays on the platform.
        01:29 And now we\'re gonna work on a deep dive into the filtering. Thank you. And how some of that works from a surface level on the desktop here.
        01:42 You have some basic status filters just like you would on any other platform. Then you also have the ability to filter by MLS or wholesale.
        01:54 Or both. And as of right now may we just on boarded our first other wholesale company other than revamp ourselves.
        02:03 And we\'re going to be on boarding all of the top wholesalers first in the tri-state area and then branching out.
        02:11 So the idea is for this to be a single platform for you to find MLS deals, wholesale deals and everything in between.
        02:20 I know for me personally being subscribed to 100 plus different wholesalers email lists. It\'s a blessing in a curse because you can definitely find deals but everybody\'s emails are formatted differently or their text messages and you have to go to their site.
        02:38 Sometimes they hide the address so you can\'t really comp it properly and everything. And again ultimately all of that leads to more time spent prospecting to try to find deals.
        02:52 And our goal here is to give you one central location to find all your deals and have a lot of the preliminary analysis done for you.
        03:02 So you can sift through everything, find the needle and the haystack and simply buy deals that match your buy box.
        03:10 Thank you Okay. Umm, we got gold filters in here. This is kind of the life force of this whole software.
        03:19 This is gonna be the best way to find a lot of deals. We\'ll talk about that. You got location filters and you can stack these on top of one another or you can use them independently.
        03:32 Myself personally, I like to filter by county. Um, you can also filter by zip code or city. And this is really cool.
        03:42 You can actually suppress cities as well. So for example, if I want to search in Delaware County, but I know that I do not like Chester.
        03:54 I can filter out Chester. I can filter out whatever I want or don\'t want rather. And it\'ll tailor my results to that.
        04:05 So we\'ll talk more about that as well. We got days on market filters, which is another really good tool that most basic MLS platforms, your zillows, realtor, redfin, all the stuff like that.
        04:20 They don\'t really have this capability. If you\'re connected directly to the MLS, you can use and leverage this. Um, but having this built into the platform here with some of the other, um, gold filters that we have, there\'s a lot of ways you can leverage this really creatively.
        04:38 And actually one of the first test campaigns. What we did when we were building and testing a lot of the features with this software was we were making bulk offers on only properties that were listed for more than 90 days.
        04:53 And we ended up doing three deals in the first six or seven weeks of testing. Umm. Doing over a hundred thousand hours worth of profit through testing.
        05:06 So we\'ll talk about some of that stuff as well. And then you can go more. You got price filters. You can filter by property type.
        05:17 You can remove listings with numbers. So that would be kind of like condos, apartments, stuff like that. And then some other basic filters.
        05:24 Square footage, lot size, year built. Remove HOA\'s. Keyword filter. This is also really powerful. Again, this is something that you could leverage with the MLS.
        05:37 However, coupling. Thank you. With the gold filters that are proprietary to our software. It\'s going to yield you a lot better results.
        05:46 So a quick example there is you could put in the keyword investor. And it\'s going to find you properties where invest.
        05:56 investor is somewhere in the listing description for that property. Then if you stack that with other filters like gold filters.
        06:04 Umm. A flip profit minimum plus the keyword plus the property types that you\'re looking for. That\'s what. Where you really find deals efficiently and effectively.
        06:19 Okay so we\'re gonna go a little bit deeper into all of those here. But the last thing that I want to touch on this as a 60,000 foot overview of filters what they are, how they work and how they\'re beneficial to you is to touch on the fact.
        06:33 That once you get dialed in on a filter that you like, essentially matching your buy box, you can save that filter.
        06:45 So since we just put in here, Delaware County and suppressed some cities. Let\'s just save this. Here, filter one. Alright, would name it something more descriptive.
        06:57 I would call it Delaware County. No Chester or whatever. I could save that. And then what\'s gonna happen? Is, well, I didn\'t run the search first.
        07:14 I should have ran the search. Okay, now that it ran the search, obviously I would get a little bit more laser focused rather than just doing the county.
        07:28 But if I wanted to, I could save that and now what\'s gonna happen is any time a property matches that search criteria.
        07:39 I\'m gonna get an email alert letting me know there\'s a new property matches. Go check it out. Make it offer.
        07:47 And you can view your saved searches here. Umm so we\'ll go a little bit deeper into all of that in a specific training video.
        07:57 This is just intended to be an overview of the entire process of filters how they work and how they\'re gonna find you more deals quicker.
        08:06 back. I almost forgot to touch on it and I would regret doing that in this overview because this is also a really awesome feature and function that makes life a whole lot easier for you in prospecting is the sort function.
        08:29 So we already have that filter in there. Again, it\'s not super laser focused or anything, but having the ability to sort by not just list price, we could also sort by AVM, we could sort by rent, we could sort by cash flow if we were looking for rentals, flip profit, we can sort by delta per square foot
        08:52 , high to low, and we can sort by days on market, shortest to longest, or longest to shortest. And there\'s a lot of different strategies and ways that you can leverage almost each and every one of these.
        09:05 Based on what it is that you\'re trying to do. I can tell you one of my favorite ways to do this is when I have a saved search that I look at regularly.
        09:15 I usually uhm will set my sort to days on market, shortest to longest because if I\'m looking at this every day every other day and there\'s some properties on here that have been listed for a little bit longer, I\'ve probably already seen them so I would only really need to look at the things that were
        09:37 just listed in the last day or so. Again, I\'m again you can set this up any way you want and like I alluded to earlier if you were planning on making offers on properties that have been listed for a while, you could sort longest to shortest.
        09:55 Looks like we got some new construction or pre-construction going construction stuff in here. Whatever, you can dial in your filter criteria a little bit tighter but just to give a quick overview and show the power and benefit of that.
        10:08 Again, if I was looking for rental properties, I could sort by cash flow. Okay, some of the results are going to be skewed sometimes you know, uhm, it says 10 grand a month in rent for this.
        10:22 I don\'t know, maybe it\'s possible. It\'s hard to believe to me, uhm, from a surface level. I was actually at this one.
        10:30 That\'s, that\'s actually probably possible if you did a decent job. But there you go, you get the idea. We\'ll talk a little bit deeper about those in the specified training videos.
        10:41 See you on the next one.',
                'uid_code' => 'FS1',
                'url' => 'https://youtu.be/3bdKfZZCBIA',
                'youtube_video_id' => '3bdKfZZCBIA',
            ],
            [
                'category' => 'Filters & Sorting',
                'embed_code' => null,
                'topic' => 'Delta PSF - Filtering and Sorting Tutorial',
                'summary' => '📏 Understanding Delta PSF:
        - Highlight: Explanation of Delta PSF as a key metric.
        - Details: The tutorial emphasizes the importance of Delta PSF in identifying properties that offer potential equity, making them suitable for various investment strategies such as flipping or renting.
        
        🔍 Setting Effective Filters:
        - Highlight: How to apply Delta PSF in search filters.
        - Details: Demonstrates setting a minimum Delta PSF to filter out properties that don\'t meet specific equity criteria, and advises on avoiding setting it too high to not miss potential deals.
        
        🛠 Practical Application Tips:
        - Highlight: Integration of Delta PSF with other search parameters.
        - Details: Covers the combination of Delta PSF with other filters like location and price to refine search results further and target properties more precisely.
        
        Insights based on numbers:
        - Using a Delta PSF of 65 dollars per square foot as a starting point, the tutorial showcases how adjusting this threshold can drastically alter the search results, influencing the investment decisions.
        ',
                'summary_html' => '<p>📏 Understanding Delta PSF:</p><ul><li>Highlight: Explanation of Delta PSF as a key metric.</li><li>Details: The tutorial emphasizes the importance of Delta PSF in identifying properties that offer potential equity, making them suitable for various investment strategies such as flipping or renting.</li></ul><p><br></p><p>🔍 Setting Effective Filters:</p><ul><li>Highlight: How to apply Delta PSF in search filters.</li><li>Details: Demonstrates setting a minimum Delta PSF to filter out properties that don\'t meet specific equity criteria, and advises on avoiding setting it too high to not miss potential deals.</li></ul><p><br></p><p>🛠 Practical Application Tips:</p><ul><li>Highlight: Integration of Delta PSF with other search parameters.</li><li>Details: Covers the combination of Delta PSF with other filters like location and price to refine search results further and target properties more precisely.</li></ul><p><br></p><p>Insights based on numbers:</p><ul><li>Using a Delta PSF of 65 dollars per square foot as a starting point, the tutorial showcases how adjusting this threshold can drastically alter the search results, influencing the investment decisions.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Filters & Sorting',
                'transcript' => '0:01 Alright, here we go. So we are going to jump right into the filtering and sorting tutorial here. And we\'re going to start off with delta per square foot.
        0:13 Hopefully you already went through the data definitions training series and you understand what delta per square foot is. Somewhat of how it\'s calculated and how it works and is displayed on the platform.
        0:25 Because that\'s why we\'re talking about it first here. I believe it\'s one of the best and easiest ways to find properties that have equity.
        0:33 And if it has equity that means it could be a flip, it could be a rental, it could be a property that you put under contract and wholesale even though it\'s already on the MLS.
        0:42 Yes, we\'ve done that as well and Delta is a great way to do that. So let\'s jump right into it.
        0:49 We have no filters applied here yet. If we go into gold filters, there it is. Delta per square foot. Again, back to the training on, excuse me, the data definitions.
        1:05 I usually like to use 65 hours a square foot as a minimum for me. Test around with this, see what you like, see what they results are, get a feel for it, and you could end up at $30 or you could end up at $100, whatever.
        1:24 See what works for you. I will mention though, setting the bar too high can shoot you down. You can\'t in the foot so to speak because you\'re only gonna see, you know, things that already match what it is that you\'re looking to buy.
        1:40 And you always gotta keep in the back of your mind, I know this is like elementary stuff, but a listing price isn\'t always what somebody is selling.
        1:50 It\'s married to as a seller, sometimes they\'ll list the property for $200,000 when they\'re really hoping to get $140,000 out of it.
        1:58 So if you don\'t see that result in the first place, you\'re not gonna be able to go make an offer and do a deal.
        2:06 So with that being said, play around, see what works for you. But I\'m gonna put $65 a square foot in here as a minimum.
        2:15 I usually don\'t set a maximum for anything because um I just don\'t because I usually have a price filter in here as well.
        2:25 So if I was just looking over at these results that are here already unfiltered. I can see right at the top of the list we got a delta of $768.
        2:33 The reality is it\'s probably not accurate when you have any sort of luxury property like that or any ridiculously large square foot properties.
        2:45 Not that that\'s a luxury property, is, but it looks like a beach property, um, it skews the results. So you could, excuse me, if you were getting too many properties that it seemed like the delta was messed up or any of the automated valuations were messed up, that\'s when it could be beneficial to put
        3:06 a max in there. Okay, but we\'re going to keep this as simple as we can here. We\'re going to put in 65 hours a square foot as a minimum.
        3:19 And this training is supposed to be for specifically delta per square foot. But, you usually do want to stack that with whatever else is kind of your buy box.
        3:31 So, as I\'ve mentioned before, I usually like to filter by county. And I\'ll usually put multiple in here at the same time.
        3:42 There\'s a, There\'s benefits and drawbacks to that, which I\'ll touch on here in a second. But let\'s say I do Delaware County, Chester County, Newcastle County, and let\'s do Camden County.
        3:57 Okay. And I could suppress cities if I wanted to. I\'m not going to here. But just to touch on that while it\'s top of mind.
        4:07 When we\'re talking about running a search, that\'s different than the way that I think about a saved search or alert.
        4:15 But what I mean by that is I may have a specific bias. I box criteria for Delaware County and it might be a little bit different for Chester County.
        4:26 So for example, if in Delaware County I know that I usually, let\'s change this here, let\'s Let\'s just go back to Delaware County.
        4:38 and let\'s look at, let\'s put in structure type, let\'s do detached, I\'ll buy rows or townhomes, I\'ll buy interior rows, I will buy twins, and really manufactured or mobile isn\'t really applicable in Delaware County.
        5:01 We have bought them and made a lot of money on them in different counties, but whatever. So let\'s say I know in Delaware County I usually purchase between, let\'s see, there\'s very little chance we\'re finding anything for $75,000 these days, but whatever.
        5:18 It\'s sometimes good to put a minimum in there because sometimes people will put up a listing and you know, it\'ll say $2,500 or something.
        5:30 In reality, they probably won\'t. Probably meant to put the property up for rent and they entered it in the wrong section of the MLS.
        5:36 So we don\'t even want to waste our time seeing any of those results. And let\'s say my upper threshold is $300,000.
        5:46 I could run that. Alright, and right there we got six results by putting that in. So, now to my point about looking at multiple counties versus one, what I could do is now save this as Delaware County uhm Delta 65 plus or name it whatever you want and I could save that and then I could create a separate
        6:20 saved search or filter or alert for Chester County. If I know that my buy box is a little bit different there.
        6:32 If instead of 65 hours a square foot, I want to do whatever 95 hours a square foot for whatever reason, just for the purpose of this training here, now I could run that.
        6:54 Okay, here\'s what we got. We got 5 results there and I can save this. And the benefit of doing that is when you get the filter email alerts letting you know that there\'s a new property that matches your filter.
        7:07 Maybe you like Chester County or whatever county a little bit more and you\'re more inclined to look at that and jump on it as soon as humanly possible.
        7:17 Whereas if I just ran a search here with all of the different counties that I buy in, whatever I\'m not going to put them all in here, but we do business in 15 different counties.
        7:36 If I ran this and I say saved this as 15 counties, okay I could save that. Now that\'s good in a way if that\'s the way that you want the results and you want emails and alerts and everything set up.
        7:57 but again the downside is if there\'s something that pops up in a specific county that you like a little bit more you may not um you may not see it right away because at the end of the day if you\'re getting a lot a lot of emails a lot of alerts it\'s hard to look all the time I understand everybody\'s busy
        8:20 but the goal is to find deals as soon as they pop up so you can jump on them right so again play around with that set it up the way that suits you best there is no right or wrong way this is a tool so ultimately the way that you use and leverage it is going to be different from anyone else but the only
        8:42 way that you\'re going to figure that out is by testing and playing around with it and a great way to do that is by modeling some of the things that you we\'ve already done internally and had success with and some of our other users have had success with so that\'s it for this one see you on the next',
                'uid_code' => 'FS2',
                'url' => 'https://youtu.be/-_Y9rcvNPM0',
                'youtube_video_id' => '-_Y9rcvNPM0',
            ],
            [
                'category' => 'Filters & Sorting',
                'embed_code' => null,
                'topic' => 'Flip Profit - Filtering and Sorting Tutorial',
                'summary' => '🔄 Overview of Flip Profit Filtering:
        - Highlight: Introduction to filtering for flip profit opportunities.
        - Details: The tutorial starts by explaining how to set up filters to identify properties with a minimum potential flip profit, specifically using $330,000 as an example.
        - 🛠 Utilization of Data and Algorithms:
        - Highlight: Explanation of how data algorithms estimate flip profit.
        - Details: The platform estimates flip profits by applying a standard $50 per square foot for renovations and calculates ARV (After Repair Value) based on the highest comps (comparable sales).
        
        🏡 Practical Filtering Example:
        - Highlight: Application of filters in a real-world scenario.
        - Details: The video demonstrates filtering properties in the state of New Jersey, starting by eliminating properties that do not meet the desired flip profit threshold, thereby reducing the list from 3,100 to fewer properties.
        - 🔧 Refining Search Criteria:
        - Highlight: Further refinement to enhance search results.
        - Details: Additional filters, such as price caps and property types (excluding mobile homes), are applied to narrow down the list and focus on the most promising opportunities.
        - 📉 Interpretation of Data Points:
        - Highlight: Critical analysis of data for informed decision-making.
        - Details: The tutorial emphasizes the importance of critically analyzing list price, average and high comps, and ensuring that the property matches the investor\'s specific criteria and market realities.
        
        🎯 Strategy and Vision in Investment:
        - Highlight: Encouragement to align strategy with personal investment goals.
        - Details: The speaker advises on tailoring the approach to suit personal investment strategies, emphasizing that understanding and using the platform\'s data effectively can significantly impact investment success.
        
        Insights based on numbers:
        - The example highlighted the critical role of specific numerical thresholds (e.g., $40,000 flip profit) in filtering properties, showing how such criteria can drastically reduce the pool of potential investments and focus efforts on the most viable options.
        - Discussion of data points like estimated flip profit versus Delta per square foot provides insights into how numerical analysis can guide decisions between full renovations and lighter updates to maximize returns.
        ',
                'summary_html' => '<p>🔄 Overview of Flip Profit Filtering:</p><ul><li>Highlight: Introduction to filtering for flip profit opportunities.</li><li>Details: The tutorial starts by explaining how to set up filters to identify properties with a minimum potential flip profit, specifically using $330,000 as an example.</li><li>🛠 Utilization of Data and Algorithms:</li><li>Highlight: Explanation of how data algorithms estimate flip profit.</li><li>Details: The platform estimates flip profits by applying a standard $50 per square foot for renovations and calculates ARV (After Repair Value) based on the highest comps (comparable sales).</li></ul><p><br></p><p>🏡 Practical Filtering Example:</p><ul><li>Highlight: Application of filters in a real-world scenario.</li><li>Details: The video demonstrates filtering properties in the state of New Jersey, starting by eliminating properties that do not meet the desired flip profit threshold, thereby reducing the list from 3,100 to fewer properties.</li><li>🔧 Refining Search Criteria:</li><li>Highlight: Further refinement to enhance search results.</li><li>Details: Additional filters, such as price caps and property types (excluding mobile homes), are applied to narrow down the list and focus on the most promising opportunities.</li><li>📉 Interpretation of Data Points:</li><li>Highlight: Critical analysis of data for informed decision-making.</li><li>Details: The tutorial emphasizes the importance of critically analyzing list price, average and high comps, and ensuring that the property matches the investor\'s specific criteria and market realities.</li></ul><p><br></p><p>🎯 Strategy and Vision in Investment:</p><ul><li>Highlight: Encouragement to align strategy with personal investment goals.</li><li>Details: The speaker advises on tailoring the approach to suit personal investment strategies, emphasizing that understanding and using the platform\'s data effectively can significantly impact investment success.</li></ul><p><br></p><p>Insights based on numbers:</p><ul><li>The example highlighted the critical role of specific numerical thresholds (e.g., $40,000 flip profit) in filtering properties, showing how such criteria can drastically reduce the pool of potential investments and focus efforts on the most viable options.</li><li>Discussion of data points like estimated flip profit versus Delta per square foot provides insights into how numerical analysis can guide decisions between full renovations and lighter updates to maximize returns.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Filters & Sorting',
                'transcript' => '00:03 I needed to take a sip of water for this one, because I know that so many people are really excited for this one, cause we\'re gonna jump into flip profit and filtering by that, so if you\'re looking for flips, this is one of the best ways to do it.
        00:22 You can also find wholesale deals this way, and believe it or not, you can also find rental properties this way as well, that um.
        00:31 Have equity and could make sense as a flip or a rental, so go to die. Right into it here, so let\'s just pop in and say that we want to only look for things where we can do a minimum of thirty thousand dollar flip profit and surface level.
        00:54 Before we even go into anything, just looking at a thumbnail here. So we can see Rhett, AVM, ARV, cash flow per month, Delta per square foot and the estimated flip profit.
        01:10 So again going back to the dated, definitions and how the algorithm works, how the software is pulling everything together. Keep in mind that for the flip profit basically the way it\'s coming up with that is it\'s always applying $50 a square foot for renovation to the subject property.
        01:32 Again not every property is going to need that some properties are only going to need you know $20 a square foot.
        01:39 Some properties are going to be real crapples that need $100 a square foot. Again the idea is not to have the rehab estimates be totally accurate or anything in a perfect world that sounds wonderful but we know that everybody\'s preferences are different.
        02:00 Everybody\'s vision is different on what they want to do with the property and there\'s not really a right or wrong rehab budget to put in.
        02:09 To do a property based on someone\'s vision. So it\'s taking into consideration $50 a square foot in rehab for the property and for the valuation it\'s taking into consideration the three high.
        02:26 It\'s sold price per square foot comps in the last six months. It\'s averaging the price per square foot of those three highest sales and it\'s multiplying that price per square foot by the price per by the square footage of the subject property.
        02:45 Okay, so it\'s not an exact science and frankly it never will be. But the good news is it doesn\'t need to be.
        02:53 The purpose is just for it to highlight opportunity for us and cut down the list of properties that we\'re looking at.
        03:00 So we can only see things that might make sense, might match our- by box and might be a really good deal for us.
        03:07 So here we\'re gonna do this backwards this time. We\'re gonna start with- let\'s just do- cause I don\'t normally do this.
        03:17 Let\'s just filter by state. We\'re gonna say the whole state of New Jersey. I\'m just gonna apply that- real quick.
        03:29 Alright so while that\'s loading, obviously it\'s a large list, 3100 properties. But that\'s basically just all active listings in the state of Jersey right now.
        03:39 It\'s got one of our holes- they\'ll deals in there as well. But we don\'t have any other filters. So to drive the point home of why this is so valuable and so beneficial.
        03:50 If I was a big flipper in Jersey and I was like man, basically anywhere in South Jersey, I\'ll flip. I\'ll do it.
        03:56 If it\'s a deal- I\'ll make it work. I\'ll make sense of it. There\'s no way that I can see it through 3100 listings and see what\'s gonna actually be fitting for me or not.
        04:09 So real simply, let\'s just go right into estimated flip profit and we\'ll just start with that but we\'ll die. All it in is we go as always.
        04:19 So let\'s uh, let\'s put $40,000 in here. We only want to look things where there\'s a potential profit of 40,000 or greater.
        04:30 All right, so right off the top, it cut. Basically 90% of those listings out, which is good. Now out of the results that are here, we\'re gonna put in more filters because still 426.
        04:46 I mean, I know a lot of big time flippers in Jersey and if they could buy 400. 26 properties that all made sense and they could make 40, 50, 60,000 dollars on them.
        04:56 They would buy every single one of them. So more than likely, these aren\'t all deals. So one of the first things that I do is like this right here.
        05:06 This looks like it\'s a new construction property or something like that. So often those values and estimates are off. A lot of times they\'ll put up a listing at a low price anyway because it\'s a model or pre-construction and it\'s not going to be a flip, right?
        05:25 We know that. Let\'s just look through some of the results real quick. See if we can see anything else that we can just kind of filter out.
        05:38 Another one that I do often is put a price cap. So that\'s going to get rid of a lot of this stuff here.
        05:47 Again, this looks like new construction or pre-construction. So if I just put a price cap anyway, it\'s going to get rid of most of that stuff.
        05:56 Um, if you know the price points that you don\'t normally purchase over, that\'s a great way. So here we \'ll just do that real quick.
        06:07 So we got 426 results. I know for me, I don\'t think I\'ve ever done an acquisition on something that I flipped where my purchase was over 500.
        06:19 That may be different for you. I know a lot of guys that to purchase North the $1 million on acquisition and target significantly higher out sales.
        06:31 But for this demonstration, we\'re going to put a $500,000 cap on it. Alright, so that cut out about 150 results.
        06:41 Now same thing if I\'m looking for flips. Thanks. Again, my personal preference, I\'m just going to be dropping my two cents in my bias here.
        06:51 But I probably don\'t want to flip mobile homes. I shouldn\'t say that. I just flipped a mobile home and made like 130,000 bucks.
        06:59 Uhm. But that was my thought as I\'m looking through this right here. I\'m like, I don\'t want to f flip a mobile home.
        07:04 But maybe that\'s inaccurate. For this demonstration, we will go, we will eliminate them. We\'ll just say detached end of row, interior row and twins.
        07:18 Let\'s apply that. Alright, we cut out another 100 results. Uhh. Can we look through? Let\'s see, what have we got?
        07:32 Some of these are looking pretty darn good. And this is a good test. See, ears. A good example of, I\'m sure that\'s not actually listed for $500.
        07:45 The agent probably put it in wrong. This phrase is not reserve amount. So it\'s an auction of some sort. Whatever.
        07:53 There\'s no way that we can fix that. Don\'t blame us. It\'s not our fault. We can\'t fix the data. Looking through.
        08:04 All right. We\'re just going to pick one at random here to uhh um. Salem. There we go. Let\'s look at Salem.
        08:12 Could that really be worth 258,000 bucks in Salem? I don\'t. Oh no. Uhh it\'s going to be a weird one because it looks like it might be, we won\'t use that as an example.
        08:24 Cause that looks like it might be too separate parcels and I don\'t want this video to go too too long here.
        08:32 Let\'s find a different one. got a $46,000 potential profit in the Lambertville, 171,000. That sounds pretty good, doesn\'t it? Alright, let\'s uhh.
        08:51 Let\'s use this as an example. Let\'s see what we got. Alright, so. The first thing that I usually do, usually I\'m just going to collapse some of this stuff because I want to get right to the meat and potatoes of the auto valuations and see what we got.
        09:07 So we got 194 bucks list price per square foot and the average is 266 and the high is significantly higher than the average.
        09:18 So I would want to verify there because again keep in mind that the the ARV is predicated on that high price per square foot and when I see that much of a range between the average and the high I would want to look a little bit deeper and make sure that those three highest um price per square foot comps
        09:42 are actually comparable to our property. What happens sometimes is you get three brand new construction homes that are the same square footage and the same property type and everything and they\'re nearby.
        09:59 But we know that our house that is 50 years old probably isn\'t going to fetch as high of a sale price as new construction so sometimes the data can get skewed.
        10:08 So just bringing that up as I look here cause we\'re about what 70 bucks from the list price to the average um.
        10:19 But between average and high you know that\'s a pretty significant so. We just want to. Verify. And we could do that by just going into some of the comps here.
        10:32 We could look and see what we got going on. So two miles away. Three bed two bath. We\'re more. Bedrooms, but generally the same square footage.
        10:48 And it listed for 409, but sold for 372 a square foot. So, okay. Maybe ah, that\'s not crazy. Cause we didn\'t even look super deep into the data here.
        11:00 Let\'s just take a- quick peek at this. Kind of cool. Okay, so this could skew our data a lot, right?
        11:11 Because this looks like it\'s right on the water. So, now we would have to look a little bit more at the subject property cause we didn\'t look- at all.
        11:20 It doesn\'t up here that we\'re on the water though. Good look at the map. No, it doesn\'t look like it.
        11:31 So, we would look at a couple more comps. I\'m getting a little sidetracked here, but I\'m interested in this now.
        11:44 Whatever. I won\'t go deep into it because the point of this video isn\'t too competency if this property is good or makes sense or anything.
        11:51 It\'s really just phot- focused on running the filters, running the, umm, sorting to be able to highlight that opportunity for us.
        12:03 So I won\'t go deeper down that rabbit hole. So another thing that I will add to filtering to make it more effective for you.
        12:13 Umm, so right now I\'m just looking at things that are gonna potentially make a $40,000 flip profit. Umm, without even putting delta per square foot as a minimum in, here.
        12:31 Just when I\'m looking through my results, looking at the delta per square foot number, it kind of helps me just analyze in a different way.
        12:42 Because I\'m guilty of this also. Like, I\'ve mentioned it in a couple of other training videos. There\'s been plenty of properties that I flipped in the past that I ripped out.
        12:53 A basically 10 year old kitchen and bathroom because I wanted to have a brand new kitchen and bathroom. And in reality, I could have put less money into the property and probably made more money.
        13:06 I wouldn\'t have sold it at a higher price. There\'s no question about that. But I would have made more money.
        13:13 So when I\'m looking at these two different numbers here, when I\'m looking at estimated flip profit of 62,000, I know that that\'s taken into consideration me doing a 50 dollar square foot rehab on this joint.
        13:26 But when I look at Delta per square foot, since that\'s comparing the listing price per square foot to the average price per square foot, I kind of in my mind start to go, well in order for me to make money on this, I might be able to do a whole tail type of deal on it.
        13:46 I\'m not doing a full renovation, I\'m not doing brand new everything. And if I can go in there and spend 20 or 30 dollars a square foot, looks like I might still be able to sell in the middle of the road where most homes do sell.
        14:04 There\'s more buyers anyway. And I might add some equity, I might be able to do that and make it make sense.
        14:11 So I\'m not actually gonna pull up the flip calculator and everything and run the numbers on it. I just want to highlight the ways to, Think about the way that the underlying data model works.
        14:27 Because like I keep beating this horse, the reason is because the better you understand it, the easier it\'s gonna make your life in finding opportunities.
        14:41 And again, I know for me like my average flip deal, we usually make at least 50, 60,000 bucks on it, even if they\'re smaller flips.
        14:52 Um, our wholesale deals that by the way, yeah we\'ve done a few. Wholesale deals of properties that we\'re listed on the MLS, we use this tool to find them, we put them under contract and resold them for higher than they were listed for on the MLS anyway.
        15:09 Um. I know that our average on those is 20 to 30,000 bucks as well. So my point is taking a couple of hours to really put everything that I\'m teaching you here into practice and understanding how it works.
        15:27 It could net you an extra deal a month. It could actually yield you an extra five deals a month. But even if it only added one deal a year to your deal flow or to your rental portfolio or to your flipping portfolio venture, it\'s certainly worth it.
        15:48 So take the time, learn the stuff, play around with it. If you have any questions. May my team are always here.',
                'uid_code' => 'FS4',
                'url' => 'https://youtu.be/IC_8koiE1l4',
                'youtube_video_id' => 'IC_8koiE1l4',
            ],
            [
                'category' => 'Filters & Sorting',
                'embed_code' => null,
                'topic' => 'Cash Flow - Filtering and Sorting Tutorial',
                'summary' => '🎯 Understanding Cash Flow Filters:
        - Highlight: Using cash flow filters to identify profitable rental properties.
        - Details: The filters focus on expected monthly cash flows per door, with an emphasis on achieving $200-$300 per month, per door.
        
        📍 Applying Location Filters:
        - Highlight: Importance of adding specific location filters to refine search results.
        - Details: Initially, the example used was Kent County in Delaware, which drastically reduced the results from 4,400 to 45, indicating the effectiveness of precise location targeting.
        
        🔍 Analyzing Results and Adjustments:
        - Highlight: Adjustments to enhance search accuracy.
        - Details: Incorporating additional filters like the Delta per square foot helps exclude overpriced properties, ensuring both cash flow and equity.
        
        🛠 Deep Dive into a Property Example:
        - Highlight: Realistic examination of properties for potential investment.
        - Details: Example detailed the analysis of a property projected to generate $1,300 monthly but requiring significant renovation costs exceeding typical estimates.
        
        💡 Infinite Cash on Cash Return Calculation:
        - Highlight: Discussion on financial calculations in investment scenarios.
        - Details: The video addressed cases where calculations might show infinite returns due to full financing scenarios, emphasizing the need for accurate data inputs.
        
        📊 Utilizing Data Effectively:
        - Highlight: Importance of understanding and using platform data effectively.
        - Details: The tutorial stressed the significance of considering various data points like rent comps and potential renovation costs to make informed investment decisions.
        
        🔄 Adjusting Investment Strategies:
        - Highlight: Tailoring filters according to personal investment strategies.
        - Details: The flexibility of the platform allows users to adjust parameters based on their specific investment criteria and goals.
        
        Insights based on numbers:
        - The example provided in the video used specific numerical thresholds (e.g., $300 monthly cash flow) to filter and evaluate investment properties, demonstrating the impact of precise financial criteria on real estate investment decisions.
        - Discussion on property renovation highlighted the financial implications of unexpected costs, with an example renovation budget adjustment from $10 per square foot to a more realistic $110,000 total, affecting overall investment viability.
        ',
                'summary_html' => '<p>🎯 Understanding Cash Flow Filters:</p><ul><li>Highlight: Using cash flow filters to identify profitable rental properties.</li><li>Details: The filters focus on expected monthly cash flows per door, with an emphasis on achieving $200-$300 per month, per door.</li></ul><p><br></p><p>📍 Applying Location Filters:</p><ul><li>Highlight: Importance of adding specific location filters to refine search results.</li><li>Details: Initially, the example used was Kent County in Delaware, which drastically reduced the results from 4,400 to 45, indicating the effectiveness of precise location targeting.</li></ul><p><br></p><p>🔍 Analyzing Results and Adjustments:</p><ul><li>Highlight: Adjustments to enhance search accuracy.</li><li>Details: Incorporating additional filters like the Delta per square foot helps exclude overpriced properties, ensuring both cash flow and equity.</li></ul><p><br></p><p>🛠 Deep Dive into a Property Example:</p><ul><li>Highlight: Realistic examination of properties for potential investment.</li><li>Details: Example detailed the analysis of a property projected to generate $1,300 monthly but requiring significant renovation costs exceeding typical estimates.</li></ul><p><br></p><p>💡 Infinite Cash on Cash Return Calculation:</p><ul><li>Highlight: Discussion on financial calculations in investment scenarios.</li><li>Details: The video addressed cases where calculations might show infinite returns due to full financing scenarios, emphasizing the need for accurate data inputs.</li></ul><p><br></p><p>📊 Utilizing Data Effectively:</p><ul><li>Highlight: Importance of understanding and using platform data effectively.</li><li>Details: The tutorial stressed the significance of considering various data points like rent comps and potential renovation costs to make informed investment decisions.</li></ul><p><br></p><p>🔄 Adjusting Investment Strategies:</p><ul><li>Highlight: Tailoring filters according to personal investment strategies.</li><li>Details: The flexibility of the platform allows users to adjust parameters based on their specific investment criteria and goals.</li></ul><p><br></p><p>Insights based on numbers:</p><ul><li>The example provided in the video used specific numerical thresholds (e.g., $300 monthly cash flow) to filter and evaluate investment properties, demonstrating the impact of precise financial criteria on real estate investment decisions.</li><li>Discussion on property renovation highlighted the financial implications of unexpected costs, with an example renovation budget adjustment from $10 per square foot to a more realistic $110,000 total, affecting overall investment viability.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Filters & Sorting',
                'transcript' => '00:01 All right, here we go. This is going to be an exciting one. We are going to talk about filtering by cash flow.
        00:09 So that is another gold filter here and it is a great way to find rental properties. First thing I\'ll make mention here is when we\'re looking at the min max, it is basing this off a per month amount of cash flow.
        00:29 So most of the values that you\'ll see anywhere on the site are basically referring to 1247. A month in rent, estimated cash flow per month in rent, so on and so forth.
        00:43 So when we\'re looking for estimated cash flow, most people I talk to tell me that they\'re shooting for two to three hundred dollars per month per door.
        00:53 So we\'re going to put three hundred dollars in here and get right to it. Now, you\'ll notice that it\'s returning forty four hundred results and the simple reason for that is because I got way too excited and I didn\'t put in any sort of location filters at all so it\'s just looking at everything in bright
        01:22 MLS territory that potentially would yield greater than a three hundred dollar per month cash flow. So let\'s dial that in a little bit.
        01:35 Let\'s throw a location in there. Let\'s do, we\'re going to do county. We\'ll do, we\'ll do Delaware. Let\'s apply that.
        01:56 Okay, obviously that cut down our results significantly there, down to 45. Now, we can start looking through some of the results.
        02:07 Actually, maybe there\'s a Kent County in Maryland too. Interesting. Excuse me. So, we can start looking through the results. But, I already know that one of the things that I\'m going to want to change is Look, we can see on this result right here, uhm, that it\'s saying that this could bring in $2,800
        02:34 a month in rent, and estimated cash flow on that is $1,300 a month, but the delta is negative. So, that\'s why usually, even if I\'m looking for rental properties, I still overlay and have a delta per square foot minimum in there, that way it removes any properties that are potentially listed higher than
        03:01 what they\'re worth. worth at a median value or after a repair value, uhm, and that way we can have our cake and eat it too.
        03:11 We\'re looking for opportunities where there\'s going to be cash flow and there should be some sort of equity also. So let\'s go back and.
        03:21 And let\'s put our 65 hours in here. Again, that\'s my number play around with it. See what works and make sense for you.
        03:34 But now we\'re getting much more dialed in now. We got 16 results to look at. Now, again, keep in mind going back to the data definitions and how some of the values are calculated and all that.
        03:50 You have to keep that in mind because let\'s look at this. This has boards on the window and everything. and it looks like it\'s going to cash flow $1,082 a month, but keep in mind this surface level cash flow analysis is only taken into consideration that you\'re going to do a $10 per square foot renovation
        04:14 . If there\'s boards on the window, I know without even looking at it, I\'m going to spend significantly more than $10 a square foot.
        04:22 That being said, it might still work, it might still make sense. Let\'s take a look. Oh yeah, it\'s beautiful. So, way more than $10 a square foot in renovation, right?
        04:44 So that\'s what\'s in there by default, $10 a square foot. Just looking at this, we\'re just going to throw a number at it real quick.
        04:50 Let\'s just say $100. $110,000 in rehab. That updated our mortgage amount. It still is basing it off of 70% of purchase plus rehab is the financed amount.
        05:04 If we wanted to change that, we could basically change it. Let\'s change this and say I\'m going to get a mortgage for $175,000, oops what did I do?
        05:22 There we go. Now we\'re $175,000. 103% financed, wonderful. Then let\'s look at income and expenses. It\'s pulling in that $1,800 a month for a 3 bedroom in that area.
        05:37 Let\'s see is that, there\'s a lot of rent comps, there were 33 rent comps. That it pulled this rental data from.
        05:49 So low end of the spectrum is $1,069, high end $2,800 and average is $1,800. So you could go a little bit deeper into that if you wanted to.
        05:59 but surface level $33 rent comps. I feel pretty damn good about that $1,800 estimate. So this is good enough to see if this makes sense to look further at.
        06:17 So now for the if we just look at a cash flow analysis of it. It\'s looking like it\'s going to cash flow $248 a month.
        06:28 Cash on cash is actually infinite. We actually need to fix that because any time that it\'s negative it actually means it\'s infinite because remember we just plugged in here that we\'re getting a mortgage for the entire amount.
        06:43 So in theory with the way we just did this we have zero cash out of pocket. That is a beautiful deal.
        06:50 It could still make $248 hours a month. Okay. And yep, see we actually put money in our pocket technically. So there you go.
        07:02 That\'s a good way to pull things out. But it\'s not about the property that we\'re looking at there. It\'s about the method.
        07:09 Of filtering and sorting to be able to find those properties. I\'ll also mention here that there\'s not a whole lot of results here.
        07:21 There\'s only 16. But let\'s just for the sake of driving this point home, let\'s add another couple. We\'ll add Sussex County and we\'ll add Newcastle County.
        07:33 So that\'s basically the entire state of Delaware. Still not a whole lot, 42 results. But, my point is, if you want to make this a little bit easier to digest, you can also sort by either delta per square foot or cash flow from high to low.
        07:52 Let\'s do that. Okay, so, ooh look at this. That, that might be accurate, because, uh, it\'s a Rehoboth Beach property.
        08:06 Sometimes you have to watch on manufactured properties. Again, depending on when you\'re watching this video. As of right now, as of May 2024, the data algorithm does not take into consideration for cash flow purposes, it doesn\'t take into consideration ground rent or land rent, or even HOA fees or anything
        08:31 like that. So, when I see a mobile home, or mobile pre-1976, a lot of times there is land rent, there is an HOA.
        08:40 And that\'s not taken into consideration in the cash flow number. It will in the future, but for right now, it doesn\'t.
        08:49 So, let\'s just see, does it tell us here? Well. The field is not there. Actually, it\'s saying that there is no HOA.
        09:03 That is not always accurate. Sometimes agents don\'t put the correct information in here. So, you\'d have to do a little bit more due diligence.
        09:13 But, I can believe, let\'s see, rent comps are 23 rent comps that it pulled these values from. Low, $2,700. Average, $4,300.
        09:26 And high rent, $10,000 per month. Now, I\'m sure that that is blending an average across some Airbnbs in short term.
        09:35 Rentals and all that type of stuff. But hey, maybe there\'s something there. And if that was not my cup of tea, if I didn\'t want to look at mobile homes, And I could specify the structure types that I do want and basically just not select manufactured or mobile.
        10:02 So let\'s just put a couple in. Okay, let\'s try just do that and reapply this. Alright, so now we\'re back down to 12 results.
        10:22 So same thing here. I would look at this and surface level. I would be like, okay, well, say in 13 65 in cash flow.
        10:33 But looking at that thing, that looks rough. So again, that probably needs more than a $10 a square foot rehab.
        10:44 But just doing the mental math again, even if we, Instead of, let\'s see, wow that place is tiny. 640 square feet.
        10:57 Let\'s say instead of $6,400, because obviously it\'s gonna need way more than that. I mean, this place basically might need to be torn down.
        11:08 So, maybe uh, maybe it\'s not worth it. But for the sake of it, let\'s just say that we put $126,000 into this.
        11:18 Let\'s leave that. Let\'s say we can get this $2,000 a month in rent. Still doesn\'t look bad. Now, the way we just did this, we would have $53,000 into it.
        11:34 But again, the purpose isn\'t to analyze any of these deals specifically. The purpose is to highlight potentially and then figure out from there what makes sense, what actually does match your buy box, and what\'s worth looking further into.
        11:53 So, to wrap up, I\'ll just touch on that one more time. Even when I am using estimated cash flow, because I\'m looking for rental properties, I usually don\'t have a overlay that delta per square foot.
        12:05 Maybe I\'ll drop it down a little bit. Let\'s just do this real quick, just to drive the point home. So So, again, based on what your investment strategy is, maybe you you\'re okay with not having a whole bunch of equity in a property and you look more for, you know, really nice neighborhoods where an asset\'s
        12:32 going to appreciate better than it would in some of these lower value, lower value lower income rental neighborhoods. And in that case, you would want to drop that delta lower or get rid of it entirely.
        12:46 So, as always, test around with that, play around, see what makes sense for you, see what kind of results it spits out, and go find some deals.',
                'uid_code' => 'FS3',
                'url' => 'https://youtu.be/EHoc5fjT1po',
                'youtube_video_id' => 'EHoc5fjT1po',
            ],
            [
                'category' => 'Filters & Sorting',
                'embed_code' => null,
                'topic' => 'DOM - Filtering and Sorting Tutorial',
                'summary' => '📆 Basics of DOM Filters:
        - Highlight: Introduction to DOM filtering for property searches.
        - Details: The tutorial begins by setting a simple filter based on days a property has been on the market, showing how this can narrow down search results significantly.
        
        🔄 Practical Filtering Examples:
        - Highlight: Application of DOM filters in different scenarios.
        - Details: Demonstrates using DOM filters to find new listings (less than 7 days) or older listings (over 90 days), which might be open to lower offers.
        
        🗺️ Advanced Filtering Techniques:
        - Highlight: Using DOM in conjunction with other filters.
        - Details: Stacking DOM filters with location and property type filters to refine searches and improve the efficiency of finding potential deals.
        
        Insights based on numbers:
        - Initial filtering reduced the list from 31,000 to 82 properties, showcasing the power of combining DOM with other criteria to focus on more relevant listings.
        - Adjusting DOM settings further to properties listed for under 7 days brought the results down to just 14, indicating the effectiveness of this filter in isolating new market entries.
        ',
                'summary_html' => '<p>📆 Basics of DOM Filters:</p><ul><li>Highlight: Introduction to DOM filtering for property searches.</li><li>Details: The tutorial begins by setting a simple filter based on days a property has been on the market, showing how this can narrow down search results significantly.</li></ul><p><br></p><p>🔄 Practical Filtering Examples:</p><ul><li>Highlight: Application of DOM filters in different scenarios.</li><li>Details: Demonstrates using DOM filters to find new listings (less than 7 days) or older listings (over 90 days), which might be open to lower offers.</li></ul><p><br></p><p>🗺️ Advanced Filtering Techniques:</p><ul><li>Highlight: Using DOM in conjunction with other filters.</li><li>Details: Stacking DOM filters with location and property type filters to refine searches and improve the efficiency of finding potential deals.</li></ul><p><br></p><p>Insights based on numbers:</p><ul><li>Initial filtering reduced the list from 31,000 to 82 properties, showcasing the power of combining DOM with other criteria to focus on more relevant listings.</li><li>Adjusting DOM settings further to properties listed for under 7 days brought the results down to just 14, indicating the effectiveness of this filter in isolating new market entries.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Filters & Sorting',
                'transcript' => '0:01 Welcome back, in this training we\'re going to talk about the Days On Market D.O.M. filter and how that can be useful and how to use it in a real world application.
        0:14 So again, for a simple little case study here. As we have no filters applied at all right now, let\'s just put in a $65.00 Delta filter.
        0:27 Let\'s go to location and let\'s just pick, we\'re gonna pick something like Jersey stuff. Let\'s pick Camden County, Gloucester, and whatever.
        0:43 Let\'s do Salem. Cool. Alright, let\'s run that filter first, see what we get. And then we\'ll stack the data. Let\'s put on market filters on top to show how that can be useful and beneficial.
        1:03 that\'s taking a minute because it\'s cutting down from the list of 31,000 that was active. So, alright, we got 82 results with those filter criteria applied.
        1:21 And by the way, I\'ve never mentioned this in any of the previous videos, but a quick easy way to see some of the stuff and filters that we\'ve is as soon as a filter in that group is applied, it shows up blue.
        1:36 So, I can just look at this quick and easy and be like, okay, well I have gold filters, location, and more applied, but I don\'t have a days on market filter.
        1:48 So, minimum days on market or maximum days on market, and then we have closed date filter. We\'ll talk about that in a minute as well.
        1:58 So, one of the ways that this can be useful is if I\'m only looking for new days new properties, very simple, I can just say I don\'t want to see properties that have been listed for more than let\'s say 7 days.
        2:12 Oops. Then apply that filter, and it only gives me 14 results and look at this place. Looks awesome. And inversely, if I want to go after properties that have been listed for a while, suggesting or indicating that they may take a less than listing price offer, I could say, I only want to see things where
        2:43 they\'ve been on market for a minimum of 90 days, and I get 21 results, and I can go through and potentially make some offers on those.
        2:55 And stacking this with the sort function again kind of can make life easier for you. All right, so I got 361 days on market.
        3:09 I\'m longest to shortest now, so maybe that\'s helpful for me in finding the results that I\'m looking for. Now, let\'s talk about this other one, the close date filter.
        3:20 So like it shows here, this only is able to be filled out when you\'re look looking for close properties, which is another really big piece that we have not yet talked about at all in any of the trainings yet, there\'s going to be future ones on it.
        3:37 since this is pulling from the MLS directly and we have access to all of the data, that means you have access to all the data, so it makes comping properties really easy for you.
        3:49 So, if we go to closed, I\'ll just show you here, so let\'s do closed, we\'ll take, well for right now let\'s take this out, let\'s take the delta out, let\'s leave our counties in there.
        4:04 And let\'s take this out, and let\'s put in earliest closed date, let\'s say we only want to see things that have closed in the last month, so let\'s go back to April 8th, okay and we don\'t have to put anything in for latest closed date, so let\'s just run that.
        4:30 So without applying a whole bunch of other filters, we can just see surface level, and those three counties, there were 737 properties.
        4:40 That closed, now if I was trying to comp a property specifically, obviously I wouldn\'t be looking for location as a county, I would probably be looking as the city or the zip code.
        4:54 So, just to give a quick example, let\'s just put in pick this 08009 or let\'s just type in Berlin. Alright, so we\'ll delete the counties and for city we\'ll just put in Berlin.
        5:12 Let\'s run that. Alright, so 49 properties that have closed in the last one month in Berlin. Now, if we really wanted to get dialed in, maybe we would go back a little bit further.
        5:30 Let\'s say, let\'s go to January 1st. We can apply that. Now we have 208 properties in that range. Not sure why the map did that there.
        5:43 There we go. And you can put a max in there as well. So if I only want to see things close between January and February, obviously we can do that as well.
        6:00 And I can still filter by this. I can And put a delta in there. Now, the beauty of doing that and stacking delta in there with properties that have already closed, it kind of leads you to believe that depending on the number that you put in, if If there\'s a high delta or a high flip.
        6:30 that shows up on a closed property, it leads you to believe that it was bought by an investor of some capacity.
        6:38 So, that can be a really, really powerful thing as well. So, that should be a pretty good start. Starting point for days on market filters and how they can be useful, again just to touch on one last point, let\'s go back to active real quick and might have to reset that first.
        7:01 Actually, I\'m not sure, let\'s see, let\'s apply that. Okay, yeah, we\'re good. So, if we go back to active. So we\'re back to active, but let\'s let\'s change this back, let\'s put a couple of counties in here.
        7:23 Camden, Gloucester, and I think we had Salem. Alright, so let\'s run this one more time. And we do have the Delta in there.
        7:36 So one of the things that we were doing as a case study when we were building this all out is we were doing minimum of 90 days.
        7:48 Okay, we got 21 results. And then basically we were adding all of those to a list, exporting them, and going and making Bye.
        7:57 Bye. Essentially automated offers on everything, which will be a feature build-in actually coming soon. So, reach out to us if you\'re not on that waiting list so we can get your account approved for automated offers when it\'s available.
        8:15 But many different ways that you can use this as I\'ve probably said a hundred times already. Test around with it.
        8:23 See how it works for you. Understand the way that it filters and the way that filtering and sorting data with these advanced tools can make everything a lot better for you in finding exactly what it is that you\'re looking for and getting some more offers out there locking up some extra deals.',
                'uid_code' => 'FS5',
                'url' => 'https://youtu.be/xa2l-4HDuVw',
                'youtube_video_id' => 'xa2l-4HDuVw',
            ],
            [
                'category' => 'Filters & Sorting',
                'embed_code' => null,
                'topic' => 'Additional Filters and Advanced Strategies',
                'summary' => '🔍 Effective Use of Price Ranges:
        - Highlight: Simplifying property searches with price filters.
        - Details: Demonstrates how setting a specific price range can immediately eliminate irrelevant listings, such as mistakenly priced properties.
        
        🏘️ Property Type Selection:
        - Highlight: Tailoring searches to specific property types.
        - Details: Allows users to select from various property types available in the system to focus on those that best fit their investment criteria.
        
        🗝️ Keyword Filtering Capability:
        - Highlight: Enhancing search precision with keyword filters.
        - Details: Shows how to use keywords like "cash only" to find properties fitting specific transaction criteria, improving the efficiency of finding potential deals.
        
        Insights based on numbers:
        - Utilizing keyword filters reduced the list from 335 properties to 17, showcasing how specific terms related to transaction types can streamline searches.
        ',
                'summary_html' => '<p>🔍 Effective Use of Price Ranges:</p><ul><li>Highlight: Simplifying property searches with price filters.</li><li>Details: Demonstrates how setting a specific price range can immediately eliminate irrelevant listings, such as mistakenly priced properties.</li></ul><p><br></p><p>🏘️ Property Type Selection:</p><ul><li>Highlight: Tailoring searches to specific property types.</li><li>Details: Allows users to select from various property types available in the system to focus on those that best fit their investment criteria.</li></ul><p><br></p><p>🗝️ Keyword Filtering Capability:</p><ul><li>Highlight: Enhancing search precision with keyword filters.</li><li>Details: Shows how to use keywords like "cash only" to find properties fitting specific transaction criteria, improving the efficiency of finding potential deals.</li></ul><p><br></p><p>Insights based on numbers:</p><ul><li>Utilizing keyword filters reduced the list from 335 properties to 17, showcasing how specific terms related to transaction types can streamline searches.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Filters & Sorting',
                'transcript' => '0:01 So if the amount of filters and sorting that we talked about already to show you how to find things that are going to match your buy box and yield you more deals if that wasn\'t enough we still have more.
        0:16 So this box. Right here more. There\'s some things in here that are really self explanatory and kind of surface level that we probably don\'t have to talk too much about but we\'ll just breeze over it real quick.
        0:30 Obviously price range is pretty self explanatory. Only reason I\'m bringing it up. But all is because since it is so simple, don\'t overlook it.
        0:40 This can be a really useful thing in first and foremost weeding out any messed up data. I gave the example a couple times as we were going through and doing some demo here that some.
        0:54 Sometimes you\'ll see a property listed for $2,000 or something. If you just simply had a price range filter in there of you know 100,000 to 200,000 it\'s going to get rid of that because in reality that property is probably not.
        1:11 For 2000 bucks it was probably supposed to be listed for rent and somebody messed up and is getting yelled at by their broker right now.
        1:21 And same thing with a upper threshold. Again speaking for myself personally I don\'t want to buy million dollar properties. To flip or rent it\'s just not what I do.
        1:33 Maybe it is what you do. If that\'s the case then wonderful. This low threshold price point is going to be more applicable to you.
        1:41 I would filter out anything that\'s under 800,000. The idea and the goal of all of this is to whittle down the list of potentially 30, 40, 50,000 properties to only the ones that make sense for you.
        1:57 So don\'t overlook that. I usually like to put price range in my filters. Structure type. These are all of the types of properties that are listed in bright MLS.
        2:14 Again depending on when you\'re watching this video, we could have other MLS integrations coming in the future. But right now.
        2:22 These are the property types that are listed. So you can select multiple or just select one, whatever works for you.
        2:32 Or you can select none. But it does help to in on exactly what it is you\'re looking for as always.
        2:41 Remove listings with unit numbers basically. Let\'s do a quick sample here. You know what? I won\'t. I don\'t want this video to go too long.
        2:48 But if I remove listing with unit numbers in theory, it should get rid of this right here. Which I probably wouldn\'t want to do because.
        2:56 Thank you know, the logic behind this when it was built in programs was if you\'re not looking for apartments or condos or anything like that, then you can just remove those from the list.
        3:09 This actually is a good buy. So. Buy that deal if it hasn\'t sold yet. Square feet again self-explanatory. Some of these basic ones I\'m not going to touch on because you\'re probably already familiar with them because other listing platforms used them.
        3:25 Remove listings with HOA. You could do that if you want. Bedroom count. Again self-explanatory. Keyword filter. This is a cool one.
        3:37 So let\'s uh set up a little sample here. Let\'s put in 65 Delta. we\'re gonna do different. Let\'s just do filter by state.
        3:48 Let\'s do Maryland. And let\'s run that. So right now the filters we have applied is 65 Delta, men, Maryland, state and structure type.
        4:04 We have detached end of row and interior. Okay. So we got 335 results. I could sort them if I want.
        4:14 But what we\'re going to do here is let\'s put in the word cash. Let\'s just see what that does. Alright so that, Cut that list down to 17 properties.
        4:28 Essentially what this is doing is it\'s looking through the listing description. So let\'s just take this one for example. you go.
        4:38 Cash is right in the description. So the idea is, If the agent put anything descriptive in here, you\'re able to kind of filter through and cash only transaction.
        4:52 I mean that\'s how much better could it get than that. one that I, Like to do often, we may not get one in Maryland, but let\'s see.
        5:03 Squatter. in Maryland. Maybe let\'s let\'s take that out. So we\'re searching everywhere. Just gotta be a squatter. Property somewhere. Come on.
        5:14 Alright, let\'s take Delta out. I know there\'s gotta be a squatter. Oh, well I spelled it wrong. That\'ll do it.
        5:22 Alright, let\'s rerun it. Alright, we got six. We got six properties. So that is a great way to find deals.
        5:33 could search investor. 1100 properties. Obviously we\'ve removed all the location and other filters there. So we\'re all over the place.
        5:47 the limits are only capped by your imagination. So the more you start going through some listening descriptions of properties that usually match your buy box start to look for patterns start to look for a key words or phrases a lot of the time.
        6:05 You\'ll see that a specific listing agent they use the same type of words and verbiage in their listings which is a perfect segue because you can also filter by listing agent if you wanted to.
        6:20 And the last couple in here. You can filter by rent per month or ARV estimate minimum maximum. So play around with all those.
        6:31 Honestly, I don\'t use these a whole lot. I know some of our users do. Personally, I would rather overlay the estimated.
        6:41 Flip profit or estimated cash flow as opposed to ARV or rent per month. Because rent per month is not indicative of cash flow.
        6:54 So your mileage may vary with these. They may be more fitting for you or less fitting for you. Thank you.
        7:00 As always, test around, see how it works, see what works for you and go buy some deals.',
                'uid_code' => 'FS6',
                'url' => 'https://youtu.be/qQ9iAh51IIY',
                'youtube_video_id' => 'qQ9iAh51IIY',
            ],
            [
                'category' => 'Filters & Sorting',
                'embed_code' => null,
                'topic' => 'The GOLD: Valuable Tips for Setting Up Saved Searches',
                'summary' => '🌟 Fundamentals of Saved Searches:
        - Highlight: Importance of utilizing saved searches for efficient deal hunting.
        - Details: The tutorial begins with a walkthrough of creating and applying saved searches, demonstrating how to customize search parameters to match specific investment criteria.
        
        🔧 Setting Specific Criteria:
        - Highlight: Detailed setup of search filters for targeted results.
        - Details: Shows how to refine searches by location, price range, and property type, emphasizing the importance of defining a clear \'buy box\' to filter out unwanted listings effectively.
        
        🔄 Dynamic Search Adjustments:
        - Highlight: How to adapt searches to evolving investment strategies.
        - Details: Discusses the flexibility of saved searches, including how to adjust parameters and update searches to stay aligned with current investment goals.
        
        📈 Advanced Search Techniques:
        - Highlight: Leveraging advanced filters for optimal property selection.
        - Details: Explains the strategic use of additional filters like \'Days on Market\' and \'Cash Flow\' to identify high-potential investment opportunities quickly.
        
        🛠 Practical Application and Maintenance:
        - Highlight: Ensuring saved searches remain effective and up to date.
        - Details: Covers practical aspects such as troubleshooting and updating saved searches to handle common issues like software bugs or changing market conditions.
        
        Insights based on numbers:
        - Example settings within the tutorial effectively reduce the initial listing pool from thousands to a manageable number, demonstrating the powerful filtering capability of the platform.
        ',
                'summary_html' => '<p>🌟 Fundamentals of Saved Searches:</p><ul><li>Highlight: Importance of utilizing saved searches for efficient deal hunting.</li><li>Details: The tutorial begins with a walkthrough of creating and applying saved searches, demonstrating how to customize search parameters to match specific investment criteria.</li></ul><p><br></p><p>🔧 Setting Specific Criteria:</p><ul><li>Highlight: Detailed setup of search filters for targeted results.</li><li>Details: Shows how to refine searches by location, price range, and property type, emphasizing the importance of defining a clear \'buy box\' to filter out unwanted listings effectively.</li></ul><p><br></p><p>🔄 Dynamic Search Adjustments:</p><ul><li>Highlight: How to adapt searches to evolving investment strategies.</li><li>Details: Discusses the flexibility of saved searches, including how to adjust parameters and update searches to stay aligned with current investment goals.</li></ul><p><br></p><p>📈 Advanced Search Techniques:</p><ul><li>Highlight: Leveraging advanced filters for optimal property selection.</li><li>Details: Explains the strategic use of additional filters like \'Days on Market\' and \'Cash Flow\' to identify high-potential investment opportunities quickly.</li></ul><p><br></p><p>🛠 Practical Application and Maintenance:</p><ul><li>Highlight: Ensuring saved searches remain effective and up to date.</li><li>Details: Covers practical aspects such as troubleshooting and updating saved searches to handle common issues like software bugs or changing market conditions.</li></ul><p><br></p><p>Insights based on numbers:</p><ul><li>Example settings within the tutorial effectively reduce the initial listing pool from thousands to a manageable number, demonstrating the powerful filtering capability of the platform.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Filters & Sorting',
                'transcript' => '00:02 Alright, I hope you\'re ready. I hope you didn\'t skip any of the previous ones, but truth be told this is the most valuable, most beneficial thing, most awesome feature and function, currently on the entire software.
        00:18 It\'s what\'s going to find you the most deals and that\'s setting up saved searches where alerts. So let\'s jump right into it.
        00:27 We got this box right here. So if I click it, I already do have two different saved searches, so let\'s- just click one of them.
        00:36 So I made this going through the tutorials here as well. So when I click that, it automatically updates of the fields to whatever I had put in.
        00:52 Okay, 75,000 to 300,000. These different property types. That is basically the filter. Okay. Um, let\'s actually start from scratch. I\'m just gonna refresh the whole page.
        01:07 so we\'re gonna create a new saved search here. So let\'s start at the top. Let\'s say we\'re looking for rentals.
        01:17 We want to make 300 hours a month in cash flow. 65 delta to overlay with that. Location. We\'re gonna go.
        01:27 We\'re gonna go broad. Let\'s just say. Let\'s say I don\'t want to own rentals and jurors. Let\'s just say the whole state of Pennsylvania.
        01:36 This is gonna be a lot, but that\'s okay. Day\'s home market. We won\'t worry about for this price. Thank you.
        01:44 Change. Let\'s say 75,000 to 300,000. Structure type. If I\'m talking about rentals, I\'m gonna be looking at detached end of row.
        01:59 Interior row. And twins. the way, there\'s definitely a few areas that I have no desire to own rentals. If I go back to location, I can add them in here.
        02:13 So first would probably be Chester. I have no desire to own stuff there. That being said, I\'ll buy anywhere for the right deal.
        02:24 I have no desire to own rentals and fill it off here. I don\'t know why there\'s two in there. I somebody sp- I spelled it wrong somewhere.
        02:35 That\'s funny. Alright, cool. So let\'s run with this. Let\'s apply that. Alright, we got 39 results. So basically what we just did, we built out our buying criteria.
        02:50 That\'s essentially- our buy box. So I can click this little save button right here. And I\'m gonna name this PA Reynolds.
        02:59 And if there was anything else descriptive in there that\'s kinda gonna make my life easier. And remembering what exactly this search and filter is- I could just make it descriptive.
        03:11 I like to make things as descriptive as possible. let\'s say PA Reynolds 300. Or something. Save that. Okay, it\'s going to tell us.
        03:24 It just saved that. Now if I click this drop down, we\'ll see that, boom, it\'s in the results now. So I could switch to this.
        03:34 I got my 26 results there. I can switch to this. it just changed. Something must not work there. didn\'t grab the state again.
        03:44 So could be a bug. We may have to fix up. By the time you see this, that\'ll probably already be fixed.
        03:50 But anyway, the point, that I want to make is a useful way of doing this. There\'s two different trains of thought.
        04:01 You can either go really broad with your filter and then make a saved search and alert out of it. Or you can go really tight.
        04:11 It depends what your strategy is and how much buying in a certain neighborhood matters to you over another. If I changed anything in here.
        04:22 say I increase this to 500. Let\'s apply that. Now if I once this loads and I click this again. Like you just saw it\'s gonna ask me if I wanna- overwrite that filter and I can either choose to overwrite it or not.
        04:45 what your goals are. take in a minute there. must be a bug somewhere. There we are. Yep. Yep. There\'s a bug.
        04:55 We\'ll get that fixed. This is embarrassing. No seriously that\'s a perfect opportunity to mention that if you ever do see issues or bugs or anything like that we\'re constantly improving and developing this software further so there\'s more features.
        05:12 There\'s more functionality. Just reach out to myself or my team. We have a full developer team on staff on payroll and they address things really really quickly so we\'ll probably actually make a bug report button.
        05:28 We\'re section in the near future but in the meantime, reach out to me or my team. We\'ll get things addressed.
        05:34 So hold on one second. here we go. So refresh the page which is always a great way. To fix things.
        05:45 Let\'s go back into our saved search here. by the way, again just to demonstrate and kind of pull together some of the stuff from the previous training videos.
        05:56 What I personally usually . Like to do if this is something that I look at often. Once I just click into it here, I\'m just going to sort by days on market shortest to longest.
        06:08 Because if I\'m looking regularly, probably the only ones that I haven\'t seen are the newest ones. Okay, so I could just look through this stuff and then I know that everything beyond two days on market, I just looked yesterday.
        06:22 I don\'t really need to look. I know what it is. Okay, so this is, again if I click that, I could override it.
        06:34 Let\'s, create a new one here. I\'m just gonna refresh the page again. And let\'s say we wanna do, thousand dollar flip profit minimum.
        06:46 I\'m gonna overlay and do a delta in there as well. As you\'ll know. Notice that\'s how I kinda like to do things.
        06:54 Let\'s say let\'s say Sussex County. I\'m sorry that in all of my tutorials I always pick one of the fifteen counties that we operated.
        07:05 If you\'re outside of those, I\'m sorry. Sussex County, days on market will leave alone. Actually no, let\'s do this. We\'re gonna do Sussex County.
        07:16 We got those gold filters in there. And let\'s say that we only wanna, see things that have been listed for more than 60 days.
        07:26 so this is a great way to do things. If I save this, obviously right now I have 60 properties in here.
        07:36 What\'s gonna happen when I, save this, not only, let\'s do it. so 6, 60 plus days on market. Alright, so I save that.
        07:51 Obviously any time that I log into the platform it makes it really simple to just pull up that search so I can, see what\'s up and what\'s new in that.
        08:00 But the real value is as soon as a new property matches this filter criteria, I\'m gonna get an email alert letting me know that there\'s a new property that matches that filter.
        08:13 So, for example if there was a property that was listed that matched the had the same estimated flip profit and delta but it only been listed for 58 days in two days once it\'s, It\'s still in an active status.
        08:32 It\'s gonna send you an email alert saying hey this property was just added to your list go check it out.
        08:39 So that can be effective in having a days on market filter on there or not even if we didn\'t have that.
        08:45 and a new property. Pretty was listed. You\'re gonna get an alert. another way that that alert could work is let\'s just say that we had a price range in here.
        08:57 You know let\'s just say that we had a hundred thousand to a hundred twenty five thousand. there was a property and it already was listed for making it up 30 days and they just did a price cut from a hundred fifty thousand down to a hundred twenty four thousand now.
        09:18 That property matches this filter. It\'s going to send you an email alert. You\'re going to know and you\'ll be able to check it out.
        09:25 So a lot of different ways that setting these can be useful and effective for you based on your strategy. For me personally, I usually have between six to ten different filters and alerts set up.
        09:41 We just did two simple samples there like if I was looking for flips or if I was looking for rentals, another one could be if I was looking for properties that were 60 or 90 days.
        09:54 Plus days on market and specific counties. That could be a third. I could set up a fourth one for keywords.
        10:02 Again the areas that I want to buy but with the keyword cash investors or bring a flashlight or something like that.
        10:10 That can be- a great way and sometimes these are going to overlap you know. You may end up getting an alert saying that this property just listed on your rental filter and it may also pop up on your cash keyword filter.
        10:28 that\'s okay. You know, this ultimately the idea is to make sure that you don\'t miss anything while not overwhelming you either.
        10:37 So there\'s a couple simple, effective ways to use them and set everything up. Last thing I\'ll show in this training here.
        10:47 Is if you ever want to edit any of these obviously you can just go into them and change things around and click to save button and overwrite.
        10:56 You can do it that way. Or you can go into my profile. you can come over to my alerts. That\'s kind of the second name for it.
        11:05 We call them filters or alerts because when you set save searches you do get alerts. And if you wanted to delete any of them you could do it from here.
        11:14 If this wasn\'t relevant to me anymore I could just click the check box. Let\'s delete these two. Boom. They are gone.
        11:21 Now if I go back to the home page click my drop down. That\'s all I got. So hopefully that\'s useful and helpful.
        11:29 Like I said this is one of the ways that we found a handful of the properties just in testing with this who are an agent, you are a broker and maybe you\'re not even an investor yourself.
        11:55 I should have brought this up earlier because this is huge. the way that a bunch of our users that are brokers are using this is when they set up a search or a filter.
        12:07 They\'re essentially setting up a different search for each one of their buyer clients. If you know your buyer client likes to buy flips that he can make 50,000 bucks in Camden County.
        12:26 simple, let\'s let\'s just run that. Alright, 44 results. I can just save this as John Smith buy box. Thanks. Boom, save that.
        12:41 And then as soon as something else pops up, I\'m gonna know and I can just forward it right on to my client and get them the deals that they\'re looking for as well.
        12:51 Because I don\'t I mentioned it in any other point of this software, but our goal is not to represent buyers and collect a buyer\'s agent commission in any way, shape, or form.
        13:04 We simply want to put the properties in front of you and then we could care less if you go directly to the listing agent and say, Hey, I\'ll give you both sides of the commission or if you already have an existing relation.
        13:17 Ship with, agent that\'s represented you in the past. Wonderful. Do that. Deal, do that business with them. that\'s totally fine.
        13:23 That\'s cool. Do whatever you have to to get the deals done at the best prices that you can. We\'re just here to facilitate, baby.
        13:30 So, see you on the next one.
        13:33 Congratulations, you made it to the end of the training series here going through filtering and sorting, and how the inner workings of the platform work, and I\'m so excited for you, you don\'t even know.
        13:47 Most people don\'t make it this far in anything that\'s not what they do in life, and those that can persevere through and learn something new and then actually put it to use, you\'re the ones that are going to succeed.
        14:02 So I\'m super excited for you on your journey to find more properties, buy more properties at deep per discounts, never missed the best deals, and you\'re well on your way to making it happen.
        14:13 So I commend you on that. Let\'s go get some deals done.',
                'uid_code' => 'FS8',
                'url' => 'https://youtu.be/IdDeUUubpKA',
                'youtube_video_id' => 'IdDeUUubpKA',
            ],
            [
                'category' => 'Filters & Sorting',
                'embed_code' => null,
                'topic' => 'How to sort for maximum speed and efficency',
                'summary' => '🔄 Sorting Basics:
        - Highlight: Utilizing sorting features to streamline searches.
        - Details: Focuses on using simple sorting methods like low to high and high to low price to quickly filter through listings.
        
        📈 Specialized Sorting Filters:
        - Highlight: Application of advanced sorting filters for specific goals.
        - Details: Examples include sorting by cash flow and days on market, which help identify properties likely to offer good returns or needing quick sales.
        
        Insights based on numbers:
        - Sorting by \'Days on Market\' can quickly highlight older listings, potentially more open to negotiation, showing how sorting can directly influence investment strategies.
        
        ',
                'summary_html' => '<p>🔄 Sorting Basics:</p><ul><li>Highlight: Utilizing sorting features to streamline searches.</li><li>Details: Focuses on using simple sorting methods like low to high and high to low price to quickly filter through listings.</li></ul><p><br></p><p>📈 Specialized Sorting Filters:</p><ul><li>Highlight: Application of advanced sorting filters for specific goals.</li><li>Details: Examples include sorting by cash flow and days on market, which help identify properties likely to offer good returns or needing quick sales.</li></ul><p><br></p><p>Insights based on numbers:</p><ul><li>Sorting by \'Days on Market\' can quickly highlight older listings, potentially more open to negotiation, showing how sorting can directly influence investment strategies.</li></ul><p><br></p><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Filters & Sorting',
                'transcript' => '0:01 What\'s up, this is gonna be a quick one, we\'re gonna talk about the sort, feature or function, and since we\'ve already talked about this and a bunch of the previous training videos, we\'ll make this quick and directly to the point, because you probably already get the idea.
        0:19 And see, it\'s benefit and how it can just make searching a little bit more efficient for you. We\'re gonna put in Delaware County, which is right behind my head there, Delaware County, got it on the wall.
        0:34 Let\'s let\'s just apply. That Delta over 65, Delaware County, 24 results. So we\'ll go through each one of them here, even though they\'re pretty self-explanatory, low to high.
        0:51 Obviously that makes sense. I had a low 1.8 million going back. This is where I would put in that maximum price threshold because I don\'t want to see stuff at these price points.
        1:09 A-V-M. So- I\'m just going to filter by this, hide a low. Which is probably yeah, it\'s going to be very similar to this sort that we just had in place.
        1:20 We can sort by rent hide a low. Again I think for me personally I think that sort. But is less useful than cash flow because I don\'t really care if this property is bringing in 8500 hours a month if it\'s going to lose money.
        1:41 On the other hand if we switch to cash flow. This brings in significantly. Less but it looks like it should cash flow.
        1:52 Flip profit high to low. Again I don\'t think that\'s going to really be relevant. A lot of times with larger properties.
        2:02 The results are skew. The ARVs and AVMs are even skewed. And personally I just don\'t have the balls to flip a one or two million dollar house.
        2:12 So maybe it could work. Maybe it\'s for you. But I would put in some additional filters to get that stuff out of there so I can have a.
        2:21 clearer look at what I want to do. That actually looks crazy too. Marcus Hook, uh, thousand dollar ARV. I would argue that very, very much.
        2:35 But there\'s probably also no 3300 square foot houses. in motion. Marcus Hook, other than that. Not the point. We can do by Delta per square foot.
        2:47 180 bucks, 176 going down. These all market shortest to longest. This is one that I use really, really often. On any of my saved searches.
        3:01 Because if I\'m looking every day or every other day, and all I really have to do is put this sort in place.
        3:11 And I only have to look at the things that have been listed in the last day or, two. And I know that everything else in the list, I\'ve already seen them.
        3:20 So that\'s a really effective one. And lastly, these all market longest to shortest. Again, that can be useful if your strategy is to go after properties that have been listed indicating that they may take a less than list price offer.
        3:36 . So, play around with these, see how they work for you. Ultimately, you\'re probably going to end up with different saved searches.
        3:44 And you\'ll have a different beneficial way of sorting each one of those lists. So understanding how they work and how they- Benefit U is obviously really important.
        3:56 It\'s going to save you time and yield you more deals. So that\'s it for this one.',
                'uid_code' => 'FS7',
                'url' => 'https://youtu.be/ELOCTVIw1lw',
                'youtube_video_id' => 'ELOCTVIw1lw',
            ],
            [
                'category' => 'Advanced Features',
                'embed_code' => null,
                'topic' => 'Exporting Data  Simplifying Your Workflow',
                'summary' => '🛠 Exporting Data:
        - Highlight: Simplifies data management.
        - Details: Shows how to export property data by first creating lists and then downloading the data in Excel format for further analysis and manipulation.
        
        📊 Comprehensive Data Access:
        - Highlight: Access to extensive property data.
        - Details: Explains the range of data available in the export, including list prices, square footage, automated valuations, rental estimates, and more.
        
        Insights based on numbers:
        - Exporting data allows users to manipulate detailed property information, enhancing personalized analysis and strategy development.
            ',
                'summary_html' => '<p><strong>🛠 </strong><strong style="color: var(--tw-prose-bold);">Exporting Data</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Simplifies data management.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Shows how to export property data by first creating lists and then downloading the data in Excel format for further analysis and manipulation.</li></ul><p><br></p><p><strong>📊 </strong><strong style="color: var(--tw-prose-bold);">Comprehensive Data Access</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Access to extensive property data.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Explains the range of data available in the export, including list prices, square footage, automated valuations, rental estimates, and more.</li></ul><p><br></p><h4><strong>Insights based on numbers:</strong></h4><ul><li>Exporting data allows users to manipulate detailed property information, enhancing personalized analysis and strategy development.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Advanced 101',
                'transcript' => '0:01 Welcome back in this training video. We\'re going to talk about exporting data. So this is one of those features that since we created this, even when it was a really elementary user interface and platform and nothing was really sexy or even worked that wonderful, a lot of users wanted to be able to export
        0:25 data because they love to our value generation models and everything and wanted access to that so they could play with and manipulate the data and now that does exist.
        0:36 this is the perfect follow-up video to lists and saved properties the way that we export data is through first-create. So if I go into my list here, I this list with two properties which isn\'t going to be the greatest example because it\'s only two records.
        0:57 But, when I am in that list, I can see from the desktop, and I should mention, this really is only fitting on desktop anyway.
        1:07 You can do it from mobile, but who wants to do to open an Excel file from their cell phone, it\'s probably not super useful.
        1:16 So, from here I can see this button, download, boom, if I just click that, quick and easy. Boom, there it is, it gave me, exported all of the records.
        1:31 or I should say all of the fields from those two records. So we can see a lot of data on each of these properties, some stuff that isn\'t even displayed on the front end of the webpage here.
        1:45 So if you are a data guy and you want to be able to manipulate some of this, this is your way to be able to do it.
        1:55 I\'m not going to touch on all of these but we\'ll go through here real quick. You got your list price obviously, your square footage, price per square foot, lots of square foot, the public remarks, and then here you have all of your automated Generated.
        2:15 Valuations, you got your low rent, comparable rentals, median, so you got all this stuff here. Cash flow, this is your photos right there, you see when the listing was entered into the MLS or into the UR system if it\'s a wholesale property, you see a whole lot of stuff, latitude and Longitude for Geo
        2:42 . O-Positioning, Land Lease, have access to a lot of stuff. Data in here, so this could be really useful Depending on what your customized strategy is I\'m not even going to go into it because if you are interested in exporting data You probably have your own unique plan or strategy of what you want to
        3:06 do with that data, so The sky is the limit. That is how you do it. You can also So I showed how to do it from the home page here.
        3:18 I believe if you go into my profile, my favorites. you can do it from right here as well if you want.
        3:27 Boom, there it goes, saved it again. Okay, so there you go. Exporting data, making your life simple and easy, and you have to spend the hundreds of thousands of dollars on the data algorithms and databases to be able to do some of the data processing that we do to come up with those automated valuations
        3:48 and rent values and AVMs and ARVs and all that other stuff you can have access to it manipulate it in any way that you see fitting for your use case and go do deals',
                'uid_code' => 'AF3',
                'url' => 'https://youtu.be/V4OyS5NXRGs',
                'youtube_video_id' => 'V4OyS5NXRGs',
            ],
            [
                'category' => 'Advanced Features',
                'embed_code' => null,
                'topic' => 'Saving Properties and Lists',
                'summary' => '🛠 Creating and Saving Lists:
        - Highlight: Efficient management of property interests.
        - Details: Demonstrates how to create lists, add properties to them, and manage these lists to organize potential investments effectively.
        📊 Bulk Offer Strategy:
        - Highlight: Streamlined bulk offer process.
        - Details: Shows how to save properties to a list for making bulk offers, which helps in targeting specific properties and managing offers systematically.
        📅 Managing Lists:
        - Highlight: Easy tracking and organization.
        - Details: Explains how to view, edit, and delete saved lists and properties from your profile, enhancing organizational efficiency.
        Insights based on numbers:
        - The tutorial emphasizes organizing properties in lists to streamline processes and increase efficiency, reducing time spent on managing offers and deals.
        ',
                'summary_html' => '<p><strong>🛠 </strong><strong style="color: var(--tw-prose-bold);">Creating and Saving Lists</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Efficient management of property interests.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Demonstrates how to create lists, add properties to them, and manage these lists to organize potential investments effectively.</li></ul><p><br></p><p><strong>📊 </strong><strong style="color: var(--tw-prose-bold);">Bulk Offer Strategy</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Streamlined bulk offer process.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Shows how to save properties to a list for making bulk offers, which helps in targeting specific properties and managing offers systematically.</li></ul><p><br></p><p><strong>📅 </strong><strong style="color: var(--tw-prose-bold);">Managing Lists</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Easy tracking and organization.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Explains how to view, edit, and delete saved lists and properties from your profile, enhancing organizational efficiency.</li></ul><p><br></p><h4><strong>Insights based on numbers:</strong></h4><ul><li>The tutorial emphasizes organizing properties in lists to streamline processes and increase efficiency, reducing time spent on managing offers and deals.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Advanced 101',
                'transcript' => '0:02 Alright, here we go. This is going to be a really exciting one. We\'re going to talk about saving properties and saved lists.
        0:10 And to start, I\'m just going to put in some baseline stuff as a filter. I always like that. 65 delta per square foot min.
        0:23 County, I\'m just going to, I\'m going to pick Sussex County in Delaware. actually, because I think it will make demonstration.
        0:35 One of the great purposes of this a little bit better. I\'m actually going to throw a 90 days on market minimum filter in there as well So there\'s a lot of different reasons That you may want to save properties in a list One of them is when you intend to make bulk offers on properties rather than just
        0:59 saying okay I\'m gonna make an offer on all 45 of these records that popped up. Maybe ah I want to target specific ones.
        1:11 so as I\'m going through the records Just quick and easy. I\'m not actually gonna worry about the property itself. I want to keep this as short and sweet as possible.
        1:20 But the purpose is if I\'m going through, I like this property. I can click this little heart button and it\'s gonna ask me if I want to add it to a list or create a new list.
        1:31 Right now I\'m on add to and if I click there I can see I don\'t have any lists yet. So I\'m gonna click this button which is gonna get rid of the drop down and let me type in here.
        1:43 So I\'m gonna name this let\'s call it 5, 14, 21, 22, 24, 90 plus days on market, make offers. And you don\'t have to make the name that long, you can literally call it general, list 1, list 2, whatever you want.
        2:03 This is just how I like to title things so it\'s descriptive and I can remember what it was that I was trying to accomplish with that list.
        2:11 Click save. Keep scrolling through, see what else I like. Again, I\'m not focusing on the properties here, I\'m just focusing on the feature or function.
        2:22 So I don\'t want to create a new list, I want to create, I want to add this to the list that I just created.
        2:28 So I\'m gonna click here, now I\'m on add to, click there. on, And I like this a lot. Ugly little blue thing, add to.
        2:39 Great. Okay. So now I\'ve added a couple properties to my list. And the way I can access that, from the desktop version here, we have this little heart icon, and on the mobile version, it\'s the same icon, you\'ll see it in the toolbar.
        2:58 Toolbar at the top, if you click on that, it\'s gonna show you your list of properties. And just to demonstrate this here, I\'m gonna create one more list real quick.
        3:08 Instead of adding this to the one we already created, let\'s do a new one. I\'m gonna call this list 1, who cares.
        3:18 Now, if I click the little heart icon, I can see all of the lists that I have here and the different properties that are in them.
        3:25 So if I click into this one, very simply, just gonna see them for now. And again, the purpose of this is to keep everything in one place and really organized and neat for you.
        3:35 That way you\'re able to track what you have going on. So let\'s say I have four offers out on these properties, having them all in one place makes that much simpler for me.
        3:45 Now, the other part of this is if you want to remove any properties from your list, or delete a list altogether, you can\'t do it from the main page right now.
        3:59 As of the time of recording this, the way that you delete a property from a favorites list, or delete the list entirely, is you\'re actually going to go to your profile.
        4:09 over to the my favorites section, and this functions kind of the same way. Here\'s our list. We have our four properties in there.
        4:17 Let\'s say I want to delete this one. Just click the delete button there. It removed it. Let\'s remove big blue here.
        4:28 we go. Now I just have the two records in there. And let\'s say I didn\'t like this list one and I want to delete it altogether.
        4:35 Click into it. And I can just click delete right here. Quick and easy. Alright, so that\'s all for this one.
        4:46 I want you to definitely play around with it because this is one of them features that until you get you start using it and seeing the benefit and leverage that it has, you\'re not really gonna fully grasp it.
        5:00 But at the end of the day, the more organized and systematized you are in the and what you do with your internal systems and processes, it\'s really gonna increase your efficiency and that\'s kind of the goal and point of all this.
        5:15 The more you increase your efficiency, the less time you\'re gonna be able to spend and still result in buying more properties, making more offers and doing more deals.
        5:26 So that\'s the goal.',
                'uid_code' => 'AF2',
                'url' => 'https://youtu.be/aBLvie5wOHo',
                'youtube_video_id' => 'aBLvie5wOHo',
            ],
            [
                'category' => 'Advanced Features',
                'embed_code' => null,
                'topic' => 'Intro to Advanced Features',
                'summary' => 'This video introduces key features of the Revamp365.ai platform, emphasizing how they can optimize the property search and evaluation process for real estate professionals.
        
        🛠 List and Save Properties:
        - Highlight: Underutilized but powerful tool.
        - Details: Focuses on the importance of using lists and saving properties to track and manage potential investments effectively.
        
        📈 Data Exporting Capabilities:
        - Highlight: Exclusive to premium users.
        - Details: Allows the exportation of detailed property data for advanced analysis, available only to premium subscribers.
        
        📊 Various Viewing Options:
        - Highlight: Adaptable display settings.
        - Details: Discusses different property views like map, thumbnail, and list views to suit various user preferences for analyzing properties.
        
        📝 Advanced Calculators:
        - Highlight: Enhancing investment analysis.
        - Details: The rental and flip calculators help evaluate potential returns and can generate detailed reports for sharing with investors and partners.
        
        🤖 Integration of Property AI:
        - Highlight: Cutting-edge feature integration.
        - Details: Introduces AI functionalities for enhancing property evaluations, a unique feature not commonly found in similar platforms.
            ',
                'summary_html' => '<p>This video introduces key features of the Revamp365.ai platform, emphasizing how they can optimize the property search and evaluation process for real estate professionals.</p><p><br></p><p><strong>🛠 List and Save Properties:</strong></p><ul><li>Highlight: Underutilized but powerful tool.</li><li>Details: Focuses on the importance of using lists and saving properties to track and manage potential investments effectively.</li></ul><p><br></p><p><strong>📈 Data Exporting Capabilities:</strong></p><ul><li>Highlight: Exclusive to premium users.</li><li>Details: Allows the exportation of detailed property data for advanced analysis, available only to premium subscribers.</li></ul><p><br></p><p><strong>📊 Various Viewing Options:</strong></p><ul><li>Highlight: Adaptable display settings.</li><li>Details: Discusses different property views like map, thumbnail, and list views to suit various user preferences for analyzing properties.</li></ul><p><br></p><p><strong>📝 Advanced Calculators:</strong></p><ul><li>Highlight: Enhancing investment analysis.</li><li>Details: The rental and flip calculators help evaluate potential returns and can generate detailed reports for sharing with investors and partners.</li></ul><p><br></p><p><strong>🤖 Integration of Property AI:</strong></p><ul><li>Highlight: Cutting-edge feature integration.</li><li>Details: Introduces AI functionalities for enhancing property evaluations, a unique feature not commonly found in similar platforms.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Advanced 101',
                'transcript' => '0:01 what it is. Alright so at this point if you are seeing this video it means you\'ve already gone through the other training segments and training series and now we to the fun stuff the meat and potatoes that if you figure out how to tweak and leverage some of the things that you\'re going to learn in this
        0:21 segment that\'s what\'s going to separate you from the rest. That\'s what\'s going to yield you that extra one deal a month, that extra five deals a month, whatever it is that you\'re looking to accomplish.
        0:34 I promise you if you listen to everything in this segment, and you put at least some of it into practical use, not all of it\'s going to be applicable to you as always, but the better you understand and utilize and leverage these tools.
        0:49 The more successful you\'re going to be. Okay, so I\'m going to pull this up here. I have a super sophisticated slide to show you here.
        1:00 So this is the stuff that we\'re going to go over in this training video. So we\'re going to go over lists and saved properties.
        1:10 Honestly, that\'s why this is first in the list chronologically here. Because I think it\'s one of the most slept on tools.
        1:18 And a lot of people don\'t even know what you can do with lists and saving properties and the value in doing so.
        1:28 So that will help out a lot. Exporting data. This is another huge one. You\'re not going to have access to this unless you\'re on a premium subscription plan, but if you are, you\'ll basically be able to export any sort of data that you want from different lists and everything.
        1:45 And we\'ll talk about that in the And the value in doing that, you probably already have a million different ideas coming into mind, uhm and you\'ll have access to that and we\'ll show you how to do it.
        1:57 This is basically leveraging the different views. So, the way that properties are displayed, sometimes you got the map view on mobile, personally I like thumbnail view a lot better, list view can be applicable if you\'re kinda without going into into a property record, you\'re trying to analyze one property
        2:19 to the next at a surface level. Property suppression, again this is another big slept on one that a lot of people aren\'t even aware exists.
        2:30 and it can really boost efficiency and productivity. Rental calculator, surface level uhm rental calculator and flip calculator, most users know that they exist and they\'ve played with them before.
        2:44 But don\'t really understand the full power of what you can do with them. You can create reports, you can share those reports with partners, investors, lenders, the uhm the potential is actually massive.
        3:00 Property AI This is one that I was super excited about and there\'s so many different use cases for that honestly I haven\'t even scratched the surface of yet.
        3:13 So we\'re gonna deep dive into that and what the future might look like and hold. At the time of recording this I don\'t think other real estate marketplace platform that already has AI plugged into it, not We have a lot of different AI models.
        3:37 for how our valuations and things like that are calculated and populated in the first place. But we actually tied AI in on the front end for the user to be able to experience within the property records.
        3:51 So we\'re going to deep dive into that and show you how to leverage that and some of the things that might be coming in the future that are really, really exciting.
        4:01 Sharing, sharing different property records and how they\'re being all uhm doing things like sharing a property are going to boost your user score.
        4:13 Some different hacks and tricks. How to comp properties. From a specific property record. We\'ll go into that. That\'s one of the key functions that if you understand how to do this more efficiently, you\'re going to do a lot better.
        4:30 You\'re going to buy more deals. You\'re going to spend your time better. And then comping from search, again, the fact that you have Any properties that listed, any properties that were cancelled, any properties that were expired, closed, pending, and everything in between.
        4:53 So being able to comp with the advanced data that you already provides to you really makes for an awesome experience and makes it that much easier to find comps and sometimes it makes it easier to find comps that you wouldn\'t have found in the first place.
        5:12 This is another newer feature that we\'re rolling out right now and there\'s going to be some awesome updates coming in the near future.
        5:21 I\'ll just leave it with that and more. So as this software develops, we are constantly every week pushing new updates live, we\'re pushing new features, we\'re listening to the feedback of our users of the platform and seeing what it is that you guys want.
        5:39 So if there\'s anything that doesn\'t exist, I can\'t make any promises, timeline, but make those suggestions, let us know, we\'re all about community, and that\'s developing exactly what it is that our users want to help you find and buy more deals at steeper discounts while spending less time doing it, 
        5:59 so see you on the next one.',
                'uid_code' => 'AF1',
                'url' => 'https://youtu.be/6r_7p1cl7i4',
                'youtube_video_id' => '6r_7p1cl7i4',
            ],
            [
                'category' => 'Advanced Features',
                'embed_code' => null,
                'topic' => 'Suppressing properties you don\'t want to see',
                'summary' => '🛠 Suppressing Properties:
        - Highlight: Easily hide properties you don\'t want to see.
        - Details: Click the eye icon with a slash through it to hide a property from future searches.
        - 📊 Managing Suppressed Properties:
        - Highlight: Restore suppressed properties if needed.
        - Details: Access the suppressed tab in your profile to manage or restore hidden properties.
        Insights based on numbers:
        - This feature enhances the efficiency of property searches by allowing users to focus only on relevant listings.
        ',
                'summary_html' => '<p><strong>🛠 </strong><strong style="color: var(--tw-prose-bold);">Suppressing Properties</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Easily hide properties you don\'t want to see.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Click the eye icon with a slash through it to hide a property from future searches.</li></ul><p><br></p><p><strong>📊 </strong><strong style="color: var(--tw-prose-bold);">Managing Suppressed Properties</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Restore suppressed properties if needed.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Access the suppressed tab in your profile to manage or restore hidden properties.</li></ul><p><br></p><h4><strong>Insights based on numbers:</strong></h4><ul><li>This feature enhances the efficiency of property searches by allowing users to focus only on relevant listings.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Advanced 101',
                'transcript' => '0:02 Here is a quick little feature that you might not have known existed So when you\'re looking through any of the thumbnails here, you can see not only is there the little heart icon that saves the property to a list, but there\'s also this little eye with a slash through it icon and the purpose of that 
        0:24 is if you keep seeing a record that were a property you that you really don\'t like and you just don\'t want to see it anymore, you don\'t want to be bogged down by setting your eyeballs on it, you can simply click this button and it\'s gonna hide that property.
        0:41 And when it does that, you shouldn\'t see that property anymore, even if I run a new search and I think that was in Springfield, if I run a new search, I should not see that property in Springfield.
        0:52 Okay, so that can be useful. For example, if there\'s something that, whatever, maybe you don\'t like the numbers of it or for whatever reason, you\'re just not interested in it, now you don\'t have to see it anymore.
        1:05 If there\'s ever a property that you accidentally suppress, or you want to remove it from a suppression list all you got to do is go to your profile over to this suppression suppressed tab boom there it is I could delete it from that suppression list now if I go back and I do another search that property
        1:29 now works would display again when I run a search so keep it short, sweet and simple suppression',
                'uid_code' => 'AF5',
                'url' => 'https://youtu.be/9Jc6AvTogQY',
                'youtube_video_id' => '9Jc6AvTogQY',
            ],
            [
                'category' => 'Advanced Features',
                'embed_code' => null,
                'topic' => 'Flip Calculator Training',
                'summary' => '🛠 Introduction to the Flip Calculator:
        - Highlight: Simplifies fix-and-flip analysis.
        - Details: The flip calculator automates key calculations, allowing users to quickly determine the profitability of potential flip properties.
        - 📈 ROI Summary Tab:
        - Highlight: Quick financial overview.
        - Details: The default settings assume a $50 per square foot renovation cost and a six-month holding period, providing an initial profit estimate.
        - 💡 Customizing Purchase Assumptions:
        - Highlight: Tailoring financial inputs.
        - Details: Users can adjust purchase price, renovation costs, closing costs, and loan details to reflect their specific investment scenario.
        - 📝 Expenses and Holding Costs:
        - Highlight: Detailed financial breakdown.
        - Details: Includes monthly expenses such as taxes, insurance, utilities, and maintenance, as well as customizable holding period assumptions.
        - 💵 Cash Flow and Profit Analysis:
        - Highlight: Net profit insights.
        - Details: Calculates expected profit, cash on cash return, and annualized ROI based on user-defined inputs and assumptions.
        - 🏦 Financing and Mortgage Options:
        - Highlight: Flexible loan scenarios.
        - Details: Adjusts mortgage amount, interest rates, and loan terms to see how different financing options impact overall profitability.
        - 📊 Advanced Scenario Planning:
        - Highlight: What-if analyses.
        - Details: Allows for quick modifications to see how changes in sale price, renovation costs, and holding periods affect profitability, aiding in making informed decisions.
        Insights based on numbers:
        - The flip calculator provides a comprehensive snapshot of potential profits, helping users to quickly assess whether a property is worth further analysis or an offer.
        ',
                'summary_html' => '<p><strong>🛠 </strong><strong style="color: var(--tw-prose-bold);">Introduction to the Flip Calculator</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Simplifies fix-and-flip analysis.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> The flip calculator automates key calculations, allowing users to quickly determine the profitability of potential flip properties.</li></ul><p><br></p><p><strong>📈 </strong><strong style="color: var(--tw-prose-bold);">ROI Summary Tab</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Quick financial overview.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> The default settings assume a $50 per square foot renovation cost and a six-month holding period, providing an initial profit estimate.</li></ul><p><br></p><p><strong>💡 </strong><strong style="color: var(--tw-prose-bold);">Customizing Purchase Assumptions</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Tailoring financial inputs.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Users can adjust purchase price, renovation costs, closing costs, and loan details to reflect their specific investment scenario.</li></ul><p><br></p><p><strong>📝 </strong><strong style="color: var(--tw-prose-bold);">Expenses and Holding Costs</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Detailed financial breakdown.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Includes monthly expenses such as taxes, insurance, utilities, and maintenance, as well as customizable holding period assumptions.</li></ul><p><br></p><p><strong>💵 </strong><strong style="color: var(--tw-prose-bold);">Cash Flow and Profit Analysis</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Net profit insights.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Calculates expected profit, cash on cash return, and annualized ROI based on user-defined inputs and assumptions.</li></ul><p><br></p><p><strong>🏦 </strong><strong style="color: var(--tw-prose-bold);">Financing and Mortgage Options</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Flexible loan scenarios.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Adjusts mortgage amount, interest rates, and loan terms to see how different financing options impact overall profitability.</li></ul><p><br></p><p><strong>📊 </strong><strong style="color: var(--tw-prose-bold);">Advanced Scenario Planning</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> What-if analyses.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Allows for quick modifications to see how changes in sale price, renovation costs, and holding periods affect profitability, aiding in making informed decisions.</li></ul><p><br></p><h4><strong>Insights based on numbers:</strong></h4><ul><li>The flip calculator provides a comprehensive snapshot of potential profits, helping users to quickly assess whether a property is worth further analysis or an offer.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Advanced 101',
                'transcript' => '00:01 If you are here to find flips, this is the video for you. We are going to dive into the flip calculator again that is built into the system just like the rental property calculator, except this is tailored to you for your fix and flips.
        00:18 So we are going to jump right in, I already have a search pulled up here, I got a filter are in, and we\'ll we\'re just gonna pick this first one in the list here.
        00:28 Let\'s just look real quick, surface level we can see asking price is 165, it is in Aston, Pennsylvania. We got our rent here, we got AVM of $277, ARV of $319, we do have an $88 per square foot delta.
        00:50 But we also have a $53,000 estimated flip profit. So, let\'s dive right in. Okay, so when we pull up the record, just go through some of the pictures quick and easy here.
        01:07 Doesn\'t look like it\'s in terrible shape. Okay, so obviously I would look a little bit deeper. But, for the sake of keeping this video as direct and efficient as possible, we\'re just going to come all the way down to the bottom.
        01:23 We got our flip camera. So, just like the rental calculator, this starts on the ROI Summary, which again takes into consideration some of the basic system data.
        01:39 Defaults, which would be a $50 per square foot renovation. So, it\'s basically saying $50 times $12.87 is what it\'s doing by default.
        01:53 As always, we can come in and customize any of these values, but as it starts right here, that gives you an idea of just where the system is coming up with those numbers.
        02:04 It\'s basing this off of price. Purchasing at $165,000, doing a $50 per square foot renovation, and typically holding the property for six months and some of the carrying costs, closing costs.
        02:17 with that, that should put this at a $50,000 profit. Now we want to customize some of those values. So we\'re going to come back to purchase assumptions and real quick we\'ll just go through some of these as the system generated values to explain the logic behind it.
        02:37 Then we\'ll customize some of the values to make it fit our needs a little bit better. Alright, so Like I said, it\'s looking at the purchase price based off of what the asking price is right now It\'s doing a $50 per square foot renovation by default Closing cost, it\'s pulling that in there based off of
        02:59 percentage of the purchase price and mortgage It is calculating based off of getting a mortgage at 90% of your purchase plus rehab It\'s giving us a default of a 10% interest rate on a 30-year amortization schedule Based off of that, our monthly payment would be $1811 Now, unlike the rental property calculator
        03:31 Here, it\'s pulling in the system-generated ARV Now, again, your mileage may vary Maybe you don\'t want to go all out and make this the nicest property or one of the nicest 3-5 properties the area and maybe you do want to pull this value or maybe you look at this and you\'re like I can get way more than
        03:54 that ARV whatever, whatever your goal sale price is, is what you\'re going to plug in, not there, here, okay? So, for the sake of this, we\'ll leave it as is, and it\'s showing us, based off of all of these numbers, how much cash we\'re going to need.
        04:15 Let\'s jump over to expenses and holding. Again, these are monthly values, so we can see our taxes here, it\'s just dividing that by 12 to give us monthly values.
        04:29 If there\'s anything, excuse me. That you want to plug in here. Maybe you need to pay your landscaper $100 a month while you own the property Whatever you can plug all that in here, but it\'s showing us our total expenses plus our debt service, which is giving us our monthly carrying costs here.
        04:51 It is the amount of days that we\'re gonna hold onto the property. By default, the system has six months in there.
        05:00 If you want to change this, you anticipate to only own it for three months, whatever that is. You can change this to nine.
        05:07 Ninety days, change it to whatever your heart desires. Commissions by default, system\'s got five percent. Transfer tax, one percent. And concessions, one percent.
        05:20 And if you sell properties yourself and you want to modify this, make it two and a half percent, transfer tax in your area is higher, whatever.
        05:29 Play with those numbers as you see fit. As you do, you can come back to the ROI summary, and it\'s going to give you a snapshot of everything here, based on what we have plugged in.
        05:42 We\'re at a 22% cash on cash, ROI is 20%, and annualized ROI is 45%. Now this matters for you because speed matters with flips, obviously.
        05:56 won\'t dive deeper into the investment analysis side of everything, but let\'s go back and let\'s modify some of these numbers.
        06:03 Let\'s say we got an offer accepted at $150 or $200. an offer for $150 on this property. Okay. And let\'s say we don\'t like that $64,000 rehab.
        06:21 We\'re planning it\'s going to be $80,000. let\'s say the lender that we\'re using is funding 100%. Which by the way if you need connections for any lenders or anything like that reach out to me or the team.
        06:40 We have a lot of different great companies and lenders that we can connect you with. So let\'s say we\'re getting $233,000 financed and let\'s say we\'re paying 12%.
        06:57 we\'re paying 2 points. And with that we think that we can actually hit an ARV of $350,000. based on what we plugged in, since we\'re getting a mortgage for basically the same price, well I increased because I put in points after the fact.
        07:18 But we should need about $4,660, which makes sense because we plugged in the points after the fact, but let\'s roll with it.
        07:28 We look through here, and we\'re like man, we can bang this thing out quick. We can get this rehab done in 4 weeks, be sold in, you know, 12 weeks.
        07:38 So let\'s just plug in 90 days in there. And you can see as I change that, it\'s modifying my holding costs.
        07:49 It\'s showing that to us quick and easy there. So if I change that back to 180, it\'s basically saying that I\'m going to spend $16,911 in holding costs.
        08:01 If I broke this down to a day, I can see that my holding costs are really $93 per day. Okay, so let\'s say we\'re going to be 95 days till we sell.
        08:14 We have in-house 80,000. So we\'re just going to be paying the buyer\'s agent 2.5%. Transfer tax, we\'re not paying any concessions.
        08:26 let\'s, whatever it is. Let\'s plug in another number here. Let\'s say that we are going to spend 100 hours a month with our landscaper.
        08:33 That updated this and our monthly carry, as well as this carry number. and let\'s bounce back to ROI summary. It\'s giving us a snapshot of it here.
        08:45 We\'re selling for $350,000. We bought for $150,000. Our total is total cost, that $96,000 is basically a combination of this holding cost as well as our rehab and closing cost in points.
        09:03 And our debits, $12,250, which is basically this right here, which is commissions, transfer tax, concessions, and our any other things that are not a cash out of pocket expense during the project, rather they are debits on the HUD, on the settlement statement when you\'re selling, that\'s $12 that\'s where
        09:25 that\'s coming out of and based on all of those values there, we\'re looking at a flip profit of $90,000, okay so again, you can play with any of these values in here as much as your heart desires, make everything fit exactly what you\'re looking for, but the idea again is to just make the initial analysis
        09:49 of this as quick as humanly possible for you, so you can determine whether it makes sense to spend another 15, 20, 30 minutes analyzing this deal and really dialing in on what your offer needs to be.
        10:03 Rather than spending an hour analyzing every single deal you come across, trying to see if it makes sense to even make an offer on.
        10:11 So use that, leverage it, and go do some deals.',
                'uid_code' => 'AF7',
                'url' => 'https://youtu.be/UxA_OYRNHNs',
                'youtube_video_id' => 'UxA_OYRNHNs',
            ],
            [
                'category' => 'Advanced Features',
                'embed_code' => null,
                'topic' => 'Rental Calculator Training',
                'summary' => '🛠 Introduction to the Rental Calculator:
        - Highlight: Enhances deal analysis efficiency.
        - Details: The calculator provides a quick, streamlined way to assess rental properties, saving time for investors by automating key calculations.
        - 🏠 Property Record Overview:
        - Highlight: Accessing property data.
        - Details: Users can view property photos, descriptions, and calculated rent estimates before diving into detailed analysis.
        - 💡 ROI Summary Tab:
        - Highlight: Quick financial overview.
        - Details: This tab shows rent, cash flow, and Delta calculations, helping investors decide quickly if a property warrants further investigation.
        - 📝 Purchase Assumptions Tab:
        - Highlight: Customizable financial inputs.
        - Details: Users can modify purchase price, renovation costs, closing costs, and loan details to tailor the analysis to their specific scenario.
        - 📊 Income and Expenses Tab:
        - Highlight: Detailed financial breakdown.
        - Details: This tab includes rent estimates, taxes, insurance, vacancy rates, CapEx, maintenance, utilities, and management fees, providing a thorough financial picture.
        - 💵 Cash Flow Analysis:
        - Highlight: Net income insights.
        - Details: The system calculates monthly cash flow, allowing investors to see potential earnings and tweak assumptions to optimize outcomes.
        - 📈 Debt Pay Down and Appreciation:
        - Highlight: Long-term investment metrics.
        - Details: The calculator considers debt pay down and property appreciation, highlighting the overall financial benefits of the investment over time.
        - 🏦 Mortgage and Financing Options:
        - Highlight: Flexible loan scenarios.
        - Details: Users can adjust mortgage amounts, interest rates, and loan terms to see how different financing options impact the investment\'s profitability.
        - 💡 Advanced Scenario Planning:
        - Highlight: Detailed what-if analyses.
        - Details: The calculator allows for quick modifications to see how changes in rent, renovation costs, and financing affect cash flow and ROI, aiding in making informed decisions.
        Insights based on numbers:
        - By automating and simplifying complex calculations, the rental calculator enables users to spend more time making offers and closing deals, rather than being bogged down by manual analysis.
        ',
                'summary_html' => '<p><strong>🛠 </strong><strong style="color: var(--tw-prose-bold);">Introduction to the Rental Calculator</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Enhances deal analysis efficiency.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> The calculator provides a quick, streamlined way to assess rental properties, saving time for investors by automating key calculations.</li></ul><p><br></p><p><strong>🏠 </strong><strong style="color: var(--tw-prose-bold);">Property Record Overview</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Accessing property data.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Users can view property photos, descriptions, and calculated rent estimates before diving into detailed analysis.</li></ul><p><br></p><p><strong>💡 </strong><strong style="color: var(--tw-prose-bold);">ROI Summary Tab</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Quick financial overview.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> This tab shows rent, cash flow, and Delta calculations, helping investors decide quickly if a property warrants further investigation.</li></ul><p><br></p><p><strong>📝 </strong><strong style="color: var(--tw-prose-bold);">Purchase Assumptions Tab</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Customizable financial inputs.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Users can modify purchase price, renovation costs, closing costs, and loan details to tailor the analysis to their specific scenario.</li></ul><p><br></p><p><strong>📊 </strong><strong style="color: var(--tw-prose-bold);">Income and Expenses Tab</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Detailed financial breakdown.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> This tab includes rent estimates, taxes, insurance, vacancy rates, CapEx, maintenance, utilities, and management fees, providing a thorough financial picture.</li></ul><p><br></p><p><strong>💵 </strong><strong style="color: var(--tw-prose-bold);">Cash Flow Analysis</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Net income insights.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> The system calculates monthly cash flow, allowing investors to see potential earnings and tweak assumptions to optimize outcomes.</li></ul><p><br></p><p><strong>📈 </strong><strong style="color: var(--tw-prose-bold);">Debt Pay Down and Appreciation</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Long-term investment metrics.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> The calculator considers debt pay down and property appreciation, highlighting the overall financial benefits of the investment over time.</li></ul><p><br></p><p><strong>🏦 </strong><strong style="color: var(--tw-prose-bold);">Mortgage and Financing Options</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Flexible loan scenarios.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Users can adjust mortgage amounts, interest rates, and loan terms to see how different financing options impact the investment\'s profitability.</li></ul><p><br></p><p><strong>💡 </strong><strong style="color: var(--tw-prose-bold);">Advanced Scenario Planning</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Detailed what-if analyses.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> The calculator allows for quick modifications to see how changes in rent, renovation costs, and financing affect cash flow and ROI, aiding in making informed decisions.</li></ul><p><br></p><h4>Insights based on numbers:</h4><ul><li>By automating and simplifying complex calculations, the rental calculator enables users to spend more time making offers and closing deals, rather than being bogged down by manual analysis.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Advanced 101',
                'transcript' => '00:01 Welcome back got a real treat for you in store on this one We are gonna do a deep dive into the rental property calculator That\'s built into the system and show you how it\'s gonna make analyzing deals that much quicker, easier, more streamlined, giving you more time to make more offers on good properties
        00:22 . So we\'re gonna jump right in. We\'re just gonna click the first property that we have in a list here. We already have a search pulled up.
        00:29 Let\'s just jump right in. Actually before I do, I should highlight from a surface level before even going into the record.
        00:37 I can see here that it\'s telling me $19.90 a month in rent and it should be able to cash flow $682 per month.
        00:46 And we do have a delta also. Okay, so let\'s just dive right into it. So we\'ll pull up the record.
        00:54 We can scroll through the photos and everything and see what\'s up, see what we think. skip all these sections here.
        01:02 We\'ve already gone over most of them and we\'re going to come right down to the rental calculator. Now you\'ll notice after that there\'s also a flip calculator but for the sake of this training we\'re just going to talk about the rental calculator specifically.
        01:21 So by default you can see there\'s three different tabs here and as soon as you pull up a property record it\'s going to be on the ROI summary first.
        01:32 So now, again, remember going back to some of the previous trainings about how the system does some of its things.
        01:39 Its calculations and valuations and remember that it\'s a imperfect, flawed logic that will never be perfect. But if you remember the way that for rental rehabs, when you\'re looking to purchase a property and you\'re looking to analyze it as a rental or looking at the numbers that the system will give 
        02:02 is automatically calculating based on a rental is the system\'s always taking into consideration ten dollars per square foot for renovations.
        02:14 If we scroll down and we were like, dude, this thing is a shell. It\'s going to need 50, 60, 70, $80 a square foot.
        02:21 We know that this cash flow is no good. We know that we\'re going to have to play with a lot of the numbers.
        02:27 To make it more accurate and then see if it\'s still going to cash flow or if there\'s equity or whatever we\'re looking for in our rental.
        02:37 Okay, so. When you start on the ROI summary, you can see some of the values here and we\'ll come back to that, but let\'s start at the beginning.
        02:48 Let\'s go to purchase assumptions. So by default, the system. Is taken into consideration that you\'re buying for the asking price.
        03:00 And maybe you want to modify that for right now. We won\'t mess with it. Then like I did just mentioned the renovations, it\'s automatically taken into account $10 per square foot for renovation.
        03:13 Which looking through this right here, maybe that\'s not too far off. Maybe we need to spend a little bit more.
        03:18 I\'m not sure. There\'s some paneling here, but this should be enough to get us hand grenade close to see if it makes sense to spend another 20 minutes analyzing this deal or not.
        03:31 The whole point idea here, the concept is so you can spend 30 seconds to be able to do this real quickly to see if the property even makes sense to spend any more time on at all.
        03:45 And taking that one step backwards even further is before you even go into the property record, as soon as you start to conceptualize in your mind how these auto generated system values, like cash flow, are calculated, you already start to paint a picture and it takes you even less time to decide that
        04:08 . Do I want to look at this deal and is it for me or not? Okay, so coming down the list here, closing costs by default, that\'s plugging in a value for you as well.
        04:21 Maybe you want to play with it, maybe you have connections at the title company, whatever, points on your loan, you can plug that in if you wanted to, and then total into debt.
        04:32 You can plug that points on your can plug that in if to, and then total into debt. excuse me, it\'s showing you as if you\'re purchasing this and you\'re going to get a mortgage for 70% of the this value right here which comes out to this and I\'ll just show you real quick if we change this let\'s say we 
        05:02 wanted to purchase for 150 900 instead that didn\'t work let\'s just type it all out 150 900 okay yeah it should update of the other values here for me I mean it changed this it changed total end of deal it changed my mortgage amount if I did want to change this I could do that if I said I want to Get 
        05:29 a mortgage for a hundred seventy thousand dollars. I could do that as well Okay, now it\'s saying that I am a hundred percent financed it up to I know that I\'m not going to be at a seven percent interest rate We know that as of the time of recording this we\'re in a crazy interest rate environment Who 
        05:51 knows where we\'re going to be three years months from now. Let\'s say ah I\'m going to pay eight percent, whatever.
        05:57 And maybe I don\'t want to calculate it as a thirty year mortgage, maybe I want to calculate it as a twenty year or twenty five year amortization, whatever.
        06:08 You can play with those values, you get the idea. Okay? And as we do that, again you can see, let\'s change this, it changes your monthly payment there, so it\'s calculating all this for you quick and easy.
        06:22 Okay? Let\'s just leave it at that for now. AVM, this doesn\'t matter a whole lot unless you you know, you actually care about equity, which is everyone.
        06:34 So this matters. You can come back up here. You can see the accuracy score and try to figure out, is this AVM actually what I want to account for or do I want to edit that there at all, right?
        06:50 So again, for the system, recommend what it does, excuse me, automatically. For most people, for most landlords that I personally, taking my bias into this, most landlords I know don\'t try to.
        07:02 They tend make their rental properties like something that you would see on the market as a fix and flip, right?
        07:08 They\'re not trying to go premium finishes. Therefore, the system tries to take the AVM, the middle of the road value, as opposed to the ARV approach.
        07:18 But regardless, without diving deeper into that, let\'s just say that I think after we do our little minimal rental grade rehab here, I mean this does have a metal roof, but let\'s just say that instead of $18,000, let\'s say we\'re gonna put $60,000 into this.
        07:39 if we do that, let\'s leave this here, now we\'re at 81% financed. Let\'s say we believe that a being into it for $213k, we don\'t think it\'s gonna be worth $325k, we think it\'s Alright, now we got all this completed to our liking, we\'re gonna move on to the next tab here, income and expenses.
        08:10 So, by default, we\'re it\'s pulling in the average rental value here. again, we can come up and we can see average rental rent, low rent, high rent.
        08:25 We can dig a little bit deeper if we want to and go pull our own rental comps. But we can see right here as well that there\'s a sizable amount of rental comps that it pulled all of these values from.
        08:37 However, if this works for you and makes sense, as always, do your own due diligence, dive deeper, go verify some rental comps and everything.
        08:46 But again, for a quick, easy summary. Let\'s say that this value is good. Or let\'s say we think it\'s a little bit high because we know the area.
        08:55 Okay, let\'s change it to $1,900. Other income, if you had things like a garage, you were gonna rent out or coin-operated laundry or whatever it is, you can plug that in here as well for additional income.
        09:10 by default, it\'s taking into consideration These are all monthly values, by the way. It\'s showing us here that the taxes for this property are $408 per year.
        09:22 This is why Delaware is so cool. Property taxes are so cheap. But it\'s taking that and dividing it into a monthly value.
        09:33 Insurance per month, it\'s giving us $145 bucks. Vacancy. So I think I can tell you for vacancy, capex, and maintenance and repairs, it\'s basically doing all this based off of 5% of whatever growth.
        09:50 rent you have in here. So let me just show you, let\'s change this to $2,500. See it changed all of those values there for us.
        10:01 And again, we can make modify these if they don\'t fit our liking, but the idea is to just make it.
        10:10 by default, it\'s giving you some. Values to work off of and again, you can get pretty darn close to where you need to be utilities management.
        10:22 You can plug all of these values in if you want to. The one thing I will bring up and highlight again right now as of the time of recording this training If a property does have an HOA or anything like that, which this one does not, so if it has an HOA or ground rent or land lease, anything like that
        10:45 , this rental calculator is not taking any of that into consideration. So a lot of times in the system right now you\'ll see manufactured homes that are on a land lease and it looks like they\'re going to cash flow really well until you dig a little bit deeper and notice that there\'s 600 hours a month 
        11:04 in land lease and you were looking at it you\'re like man this thing is going to cash flow 700 hours a month and then you take into consideration that the land lease that the system is new.
        11:15 Not again right now as of May 2024 the system is not taking that stuff into consideration. It will in the future but something to be aware of for now.
        11:25 All right so as you change some of these values this is giving you your expense percent as a ratio. So let\'s just plug in let\'s say that our management we\'re not going to self-manage we\'re going to spend 10%.
        11:42 management. So as we change any of these values you can see it\'s updating the expense percentage. I\'m not gonna go super deep into that as far as investment analysis and everything but I can tell you surface level by default.
        11:59 For single-family rental properties in my area anyway 35% is usually the norm if you can get lower than that it\'s probably because you\'re in a area like this with really low property taxes or maybe you self-manage, whatever.
        12:15 So play with these values as you see fit. Monthly debt service this is just regurgitating. Regurgitating what it\'s showing us back one tab so 1367 it should be the same that it\'s showing us right here.
        12:30 Yup, monthly payment. Okay so if all of these values are the are what we\'re looking at let\'s take some of this back out.
        12:38 Let\'s take the management back out. Let\'s say we\'re going to self-manage. And what else, what else, what else? Well let\'s leave that for now.
        12:47 Okay. So it\'s showing us that with all of these values that we plugged in, we\'re going to cash flow $67 a month.
        12:55 So now we\'re done modifying what we\'re Let\'s bounce back to ROI summary and this is going to break down all of that stuff for us.
        13:07 So cash flow $68 a month. It\'s going to give us our cash on cash. It\'s going to give us our cap rate.
        13:15 Now for cash on cash, let\'s just dive into that a little bit. If I go back and actually I know this isn\'t working properly right now either.
        13:24 You This scenario that we have in here right now is basically showing us that, you know, we have 19% of our own cash into this deal.
        13:36 Let\'s just do it this way real quick just to demonstrate what I\'m trying to show you here. Let\'s say that we get a mortgage for $220,000.
        13:45 Okay. Go back to ROI summary. Now cash on cash. It\'s going to show negative. In reality what that means is your cash on cash is technically infinite because you have no money into the deal at all.
        13:59 So we actually have to fix this. It should never show negative. When you have no money into the deal your cash on cash in theory should be infinite.
        14:08 Whatever. Let\'s go back and see. Let\'s say $170,000 is what we have financed. that scenario our cash on cash would be 37%.
        14:21 And some of these metrics matter more to different people. It depends on what your goals are, what your strategies are.
        14:29 Some people ah really care most about cash flow and don\'t care nearly as much about cash on cash and sometimes vice versa.
        14:38 Sometimes people care more about the equity in the deal or equity at purchase, which again is reflected here. this is where that AVM matters.
        14:50 That\'s where that\'s pulling from. So we can see equity at purchase. It\'s showing us $86,000. If we go back and.
        14:57 And we change this to $250,000 is what we think the property will be worth after we put our $60,000 renovation into it.
        15:08 Now we can come back, now it\'s showing us that we\'re only going to have $36,000 in equity at purchase. Debt service coverage ratio based on what we plugged in.
        15:20 NOI. So some of these other things, these last three, I like to look at these and this can be helpful when you\'re actually looking at a 30 year investment summary.
        15:34 of a property or however long you intend to hold for. a lot of times people only look at cash flow and maybe they look at equity a little bit, but I know that debt pay down gets neglected a lot.
        15:47 And that can be massive because I could look at this deal and I\'m like, it\'s only cash flow and $68 a month and yeah, I mean I have some equity, but if I were to sell and I were to pay commissions and close, closing costs and transfer and all this stuff, that deal is not that sexy.
        16:04 But I start to look at debt pay down appreciation and depreciation. and it starts to change my entire perspective of the deal, right?
        16:15 If, again, right now the system is only taking into consideration fixed rate mortgages, no balloons, or anything like that, we can change the term, we could make this a 20 year, 25 year, whatever, but it\'s basically simple interest and based off of that amortization schedule of that 30 year fixed rate
        16:38 mortgage that we have plugged in here, it\'s basically averaging that, obviously your debt pay down based on your amortization schedule is going to be lower in the beginning, higher towards the middle and end, but across that entire duration of holding the property, it\'s basically averaging that all out
        17:00 and saying that on average your tenant is basically paying down $472 per month worth of that mortgage, worth of that debt.
        17:11 So that\'s actually pretty huge, you know, because I\'m really only looking at the realized gain of $68 a month, but in reality, that, that property is really putting $472 a month in debt.
        17:28 not realizing that gain right now, but nonetheless, it is there. And the same can be said about appreciation. So, what this is looking at is again essentially the AVM that we\'re putting in there and then it\'s taking, I believe it\'s 4% average appreciation rate and saying that if your property appreciates
        17:51 4% annually broken down to a monthly value essentially you are gaining $833 per month in appreciation on that property. So you start to add these up, you got your debt pay down and your appreciation, that\'s it whatever 5th, 14th, 100th, I\'m so bad at mental math, and then plus your cash flow, it starts
        18:15 to change the way that we look at this debt. So, that being said, of course we want to rely on cash flow, it needs to cash flow and everything for most people to make sense of it, however, I know a lot of people that buy properties that are massively cash flow negative because they are banking on these
        18:31 other things. and the last thing there is depreciation. Again, that\'s taking into account the AVM that you have plugged in on the purchases, services.
        18:42 Assumptions here. And then I believe that\'s taking into account 27 and a half years for depreciation, breaking that down across a month.
        18:55 And basically you\'re able to depreciate $648 per month on that property. So that\'s not a realized gain obviously, but it\'s something to keep in the back of your mind.
        19:09 And again, this is all just done to give you a quick snapshot of what you\'re looking at with your deal and see if it makes sense for you.
        19:19 You can. Modify any of the values you can do whatever you want in here and hopefully this makes analyzing deals a little bit simpler and quicker for you.
        19:31 So just to give you a second example here. Now that we just kind of went through that one slowly let\'s let\'s pick another one here.
        19:38 let\'s see let\'s see Seaford Delaware okay let\'s let\'s do this one real quick. The goal here is to show you how quick and easy this should be for you once you\'re familiar with the system and how it works.
        19:52 So I\'m just going to look here 343 bucks a month in cash flow is what it\'s saying. I\'m going to jump right into it.
        20:01 Let\'s go to purchase assumptions. Okay, so I don\'t have a lot of photos here. Let\'s just say by looking through let\'s say we\'re going to spend I don\'t know, $35,000 in rehab.
        20:15 Okay, I like all this stuff. I\'m going to leave it the same. I\'m going to come to income and expenses.
        20:20 I know this area well. Let\'s say I\'m going to rent it for $1,500. All these other things, I know the way they calculate, 5%, 5%, 5%.
        20:32 Taxes are in there. Let\'s just leave it. Come over to ROV. I\'m like, okay, well, $170 a month in cashflow.
        20:41 Let\'s change this a little bit. Let\'s say that we want the whole thing financed. We\'re going to cash out when we\'re done.
        20:49 Okay. All right. Now I\'m going to be negative $252 a month. So if that\'s your goal, which by the way, if it\'s still your goal to cash out every single thing on every deal, you\'re probably searching for unicorns right now.
        21:06 whatever. You can get the gist from there. You can play with this, but it\'s really quick and easy to see if it does make sense to dive a little bit deeper and into analyzing that deal or not for you.
        21:18 And what I mean by that is going and looking up actual rental comps in the area, looking at other comps to determine is this AVM good or not, play with your mortgage, play with your interest points, everything else.
        21:33 You get the idea. It\'s quick, it\'s easy. Go do some deals.',
                'uid_code' => 'AF6',
                'url' => 'https://youtu.be/jE8t21xJXgI',
                'youtube_video_id' => 'jE8t21xJXgI',
            ],
            [
                'category' => 'Advanced Features',
                'embed_code' => null,
                'topic' => 'Exploring Different Views in the Desktop Version',
                'summary' => '🗺 Map View:
        - Highlight: Default view showing properties on a map.
        - Details: Useful for geographically-focused searches.
        - 📸 Thumbnail View:
        - Highlight: Displays properties as thumbnails.
        - Details: Provides a visual overview of listings.
        - 📄 List View:
        - Highlight: Shows properties in a list format.
        - Details: Ideal for quick comparisons without entering individual records.
        Insights based on numbers:
        - The tutorial demonstrates the flexibility of switching views to improve the efficiency and user experience during property searches.
        ',
                'summary_html' => '<p><strong>🗺 </strong><strong style="color: var(--tw-prose-bold);">Map View</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Default view showing properties on a map.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Useful for geographically-focused searches.</li></ul><p><br></p><p><strong>📸 </strong><strong style="color: var(--tw-prose-bold);">Thumbnail View</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Displays properties as thumbnails.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Provides a visual overview of listings.</li></ul><p><br></p><p><strong>📄 </strong><strong style="color: var(--tw-prose-bold);">List View</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Shows properties in a list format.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Ideal for quick comparisons without entering individual records.</li></ul><p><br></p><h4><strong>Insights based on numbers:</strong></h4><ul><li>The tutorial demonstrates the flexibility of switching views to improve the efficiency and user experience during property searches.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Advanced 101',
                'transcript' => '0:01 This will be a quick one here. We\'re just going to talk about the different views and why you might want to use or leverage them based on what your strategy is or what you\'re trying to do.
        0:12 So from the desktop version here, right in this top toolbar, we can see this little map icon looking button. If we click that, we can see that we\'re by default on the on map view where we have the map on the left hand side and we can click a record and also look through here.
        0:33 But if we click this and we just go to thumbnail view, maybe you like looking at. Looking at everything this way, a bit better.
        0:41 Whatever works for you. Everything still works and functions the same. So if we just did a search here. let\'s say we\'re gonna do Delaware County and let\'s say 90 days on market minimum just as a quick little sample.
        1:05 results. And if I clicked here, went back to my map view, boom, same two results. Good. Simple. Pretty self-explanatory. And then also, if I click, we can see list view, says test.
        1:21 It\'s actually not in testing anymore. Just gotta fix that. But this can be helpful for you if you\'re just trying to compare some different things, your mileage may vary, and you just want to be able to do it quickly and easily with this.
        1:39 Without even going into the record, this list view could be really good for that for you. So let\'s take this out of here, let\'s get some more results.
        1:48 so let\'s do view, move my face, be good if you\'re just quickly trying to go through and compare whatever it is that you\'re looking for.
        1:59 And then same thing, if you\'re want to see the actual property record, just click into it and do whatever it is that you need to do from there.
        2:09 So again, this is just another feature or function to make. customized the user experience for you based on what it is that you want to do, what you\'re looking for, and how you want to search and sort for properties.',
                'uid_code' => 'AF4',
                'url' => 'https://youtu.be/TDNbvIG-nqQ',
                'youtube_video_id' => 'TDNbvIG-nqQ',
            ],
            [
                'category' => 'Advanced Features',
                'embed_code' => null,
                'topic' => 'Property AI: Revolutionizing Real Estate',
                'summary' => '🛠 Introduction to Property AI:
        - Highlight: First real estate platform with integrated AI.
        - Details: Property AI allows users to ask questions and get insights directly within property records, eliminating the need for external tools.
        - 🔍 AI-Powered Queries:
        - Highlight: Ask specific property-related questions.
        - Details: Users can ask about renovation costs, neighborhood information, and more, receiving instant, detailed responses.
        - 🏠 Neighborhood Insights:
        - Highlight: Understand local area specifics.
        - Details: AI provides information about the neighborhood, including history, education, economy, and more, helping investors make informed decisions.
        - 💡 Creative Financing:
        - Highlight: Structuring unique deals.
        - Details: AI helps draft questions for sellers about creative financing options and can generate sample agreements, simplifying negotiations.
        - 📊 Automated Analysis:
        - Highlight: Enhances deal evaluation.
        - Details: AI leverages integrated data to offer estimates on property values, rental income, and potential ROI, streamlining the analysis process.
        - ✉️ Marketing and Communication:
        - Highlight: Generates marketing materials.
        - Details: AI can draft emails and marketing content to help secure investment from partners or clients, tailored to the details of the property.
        - 📧 Drip Campaigns:
        - Highlight: Create effective follow-up sequences.
        - Details: AI can craft multi-email sequences to keep potential investors engaged, ensuring timely communication and decision-making.
        Insights based on numbers:
        - The Property AI feature significantly reduces the time spent on manual analysis and communication, allowing for faster, data-driven decision-making.
        ',
                'summary_html' => '<p><strong>🛠 </strong><strong style="color: var(--tw-prose-bold);">Introduction to Property AI</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> First real estate platform with integrated AI.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Property AI allows users to ask questions and get insights directly within property records, eliminating the need for external tools.</li></ul><p><br></p><p><strong>🔍 </strong><strong style="color: var(--tw-prose-bold);">AI-Powered Queries</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Ask specific property-related questions.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> Users can ask about renovation costs, neighborhood information, and more, receiving instant, detailed responses.</li></ul><p><br></p><p><strong>🏠 </strong><strong style="color: var(--tw-prose-bold);">Neighborhood Insights</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Understand local area specifics.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> AI provides information about the neighborhood, including history, education, economy, and more, helping investors make informed decisions.</li></ul><p><br></p><p><strong>💡 </strong><strong style="color: var(--tw-prose-bold);">Creative Financing</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Structuring unique deals.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> AI helps draft questions for sellers about creative financing options and can generate sample agreements, simplifying negotiations.</li></ul><p><br></p><p><strong>📊 </strong><strong style="color: var(--tw-prose-bold);">Automated Analysis</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Enhances deal evaluation.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> AI leverages integrated data to offer estimates on property values, rental income, and potential ROI, streamlining the analysis process.</li></ul><p><br></p><p><strong>✉️ </strong><strong style="color: var(--tw-prose-bold);">Marketing and Communication</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Generates marketing materials.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> AI can draft emails and marketing content to help secure investment from partners or clients, tailored to the details of the property.</li></ul><p><br></p><p><strong>📧 </strong><strong style="color: var(--tw-prose-bold);">Drip Campaigns</strong><strong>:</strong></p><ul><li><span style="color: var(--tw-prose-bold);">Highlight:</span> Create effective follow-up sequences.</li><li><span style="color: var(--tw-prose-bold);">Details:</span> AI can craft multi-email sequences to keep potential investors engaged, ensuring timely communication and decision-making.</li></ul><p><br></p><h4>Insights based on numbers:</h4><ul><li>The Property AI feature significantly reduces the time spent on manual analysis and communication, allowing for faster, data-driven decision-making.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Advanced 101',
                'transcript' => '00:00 Whoo, I don\'t know if you\'re ready for this one, we are going to talk about property AI. So, again, as of the time of recording this, This is a new feature, just recently released and the reality is AI is evolving really rapidly, really quickly.
        00:23 One of the things that I can say that I\'m really proud of with our system. is this is the first real estate marketplace that I know of and I searched that already has AI integrated into into the system.
        00:39 So what you had to do before this was anytime you had a property that you wanted to ask questions about or try to understand or analyze things know about the neighborhood, know how to write a agreement, would have to basically copy and paste a lot of different inputs and information.
        01:01 Into a AI software like chat GPT or something like that. What we did is we fully integrated AI directly into the property records.
        01:13 So just going to dive in. Into what that looks like right now and again this is a rapidly evolving thing so this may change and get a lot more sophisticated and a lot better.
        01:27 better use cases as time evolves but surface level let\'s dive right into it we\'re in a property record here right up at the top we can see we got close, save, share, and we have this AI button if we click that we could click any of these simple examples here and I guess for the sake of demonstration 
        01:50 let\'s do let\'s just click this one how much would this house cost to renovate it\'s basically just taking that and putting it in the box here let\'s ask that first examples are good examples to get you started they are admittedly not the best use case because the reality is you already have access to this
        02:11 information and data below in some of the automated valuations and calculators that are built in and stuff but here\'s the answer it gave us according to the provided property details estimate for a full rehab I\'m not going to read it all because you can pause and read it if you chose to do so but it 
        02:27 gives you an answer that\'s the point now this becomes really interesting and useful depending on your level of creativity so let\'s just say that we already did analyze this we already did run some different calculators on it and everything and we like the deal but maybe we\'re not from the area we don\'t
        02:47 know anything about Aston Pennsylvania so I can click into here and I can say tell me, oops caps lock, tell me a little bit about Aston, PA not from the area it\'s going to think there for a second basically what it\'s going to do is just like if you were using chat GPT or anything like that this as of
        03:21 right now actually is integrated with open AI the company that chat GPT is built on top of and it\'s it\'s going out there and it\'s searching for an answer and it\'s leveraging AI technology and we can see here Athens a town located in Delaware County history, we got some history, got the location, education
        03:47 , community, recreation, economy, government, transportation, beautiful. I like it, I like it, let\'s say we read through that and we\'re still wondering what the, what the median income is.
        04:01 Okay, let\'s just ask that. What\'s the, if I could type. So, the limits with this are again really only capped by your imagination and this can be a really awesome thing.
        04:26 Don\'t have the exact current median income. Alright, so that\'s not a super helpful response there. But, let\'s just say that we like this deal and for the sake of it.
        04:40 Let\'s say that this seller of the property, maybe they had in the property description, open to creative financing, right? The seller open to carrying back a mortgage.
        04:54 We could say, the seller of this property said would carry back a mortgage. start with this. What do I need to ask them?
        05:12 we could go, the information approach we\'re doing. We\'re just trying to figure something out. We\'re trying to understand something. we can ask for input, advice, whatever.
        05:20 But what I\'m going to show you in a second, is where I think this poses a massive opportunity and life a little bit easier for you.
        05:31 Alright, this is going to give you some information. So, these are talking points that you would have to go over with the seller.
        05:39 What are the terms going to be? What\'s the down payment? What are the monthly payments? Is there a balloon? Is there a premium?
        05:44 Alright, so this is really giving you all of the bullet points that you would need to discuss with the seller to be able to structure a creative deal like this.
        05:55 So, we went through that. At, let\'s say, cool now, I want you to draft a sample AOS for this deal.
        06:10 assuming it\'s probably gonna tell us don\'t use this as legal advice, have your attorney review it, yadda yadda yadda, because any service you use is probably gonna tell you that, but let\'s see what it spits out here.
        06:22 see if it is at least a usable template that we can just use as a draft to maybe modify, maybe send it over to our attorney or maybe just take it and run with it and get a deal locked up.
        06:38 right, yep, it\'s telling us, consult with the attorney but look right here it did it gave us a template so we could just copy and paste all this right into Microsoft Word and we could fill this out and we could we have something that we can work off of from there alright last example I\'ll show you here
        06:57 again you\'re only limited by your level of creativity here but one of the other use cases that I have personally used this for is real simply like making marketing material based off of this right so actually we\'ll do two examples here I really like this deal but I need 20k from a private investor of
        07:25 mine. Write an email to him selling him on Y with We should invest in this deal let\'s see what it spits out here for us the reason that this works well and is useful is because The AI is actually plugged into this The page, so it can see all of the details about the property It can see some of the valuations
        07:59 It can see some of the rental values, flip values, AVM, ARV And all those other things Alright, so this wrote an email for us here So we can copy and paste this into the email We got some details about the property We got some highlights Look, it plugged in Exactly what we were talking about there the
        08:20 seller agreed to carry back a mortgage. Here\'s our projected returns Here\'s what I\'m looking for Look the the the possibilities here are limitless man and we didn\'t have to do anything other than ask AI a question and tell it what we wanted to do the last example I\'ll give you here is say that we were
        08:45 looking at this property not for us but for a client of ours For an investor buyer Let\'s say Let\'s give it context But I think it\'s a good idea It\'s good fit for an investor client of mine He\'s slow to make decisions I want you to craft a 5 email drip sequence to Bill him ugh to consider this deal.
        09:35 Bill, you gotta make quick decisions brother, okay? Maybe a five email sequence is gonna be too slow and you\'re gonna miss out.
        09:43 Let\'s see what, AI gives to us here, see if it\'s useful. whatever it spits out, obviously we can customize, modify it to our liking, and we could also, also plug in a little bit more information and details about Bill here that would craft the messaging to him and make it that much better.
        10:06 But for the sake of this example, we\'re just running quick and easy. All right, five email drip sequence. Email one, here\'s our subject.
        10:20 Discover a promising real estate investment in Aston P. A. Excuse me. All right, come across a real estate opportunity so you can read through.
        10:30 I\'ll follow up with details. Real soon. Your name. Here\'s another one. Your next smart investment. Boom. A little bit more details about the property.
        10:41 I believe it aligns with your investment strategy. Check this out. Estimated rehab. Email for the urgency of the market. I love it.
        10:51 It\'s tying into the fact that this deal might not be around. For much longer. And look at that final call to action.
        11:00 I love that. Wanted to reach out one last time. Let\'s hop on a call, Bill. This deal isn\'t going to be around forever.
        11:09 So, there you go. There\'s some ideas and some different use cases that you could leverage this AI to whatever your needs might be.
        11:21 AI is the way of the future. The reality is there\'s a lot of AI that already happens on the platform in the background.
        11:29 A lot of our algorithms to generate some of our AVMs, ARVs, stuff like that. was heavily predicated at first on AI coming up with different algorithms to do some of the calculations.
        11:46 And now that we have AI tied into the front end and you and your user experience has access to this right within a property window, which by the way this works on mobile as well, works great.
        12:00 really, it\'s going to be, it\'s going to be game changing. So, leverage this, let us know your use cases and where you\'re seeing the most value out of this because this is going to be one of the areas that we focus, future.
        12:13 upon because it\'s going to be massive. So, use it, test it out, let us know your results, go do some deals.',
                'uid_code' => 'AF8',
                'url' => 'https://youtu.be/gaJR5jvBBW8',
                'youtube_video_id' => 'gaJR5jvBBW8',
            ],
            [
                'category' => 'Sell YOUR Deals',
                'embed_code' => null,
                'topic' => 'HOW TO List Properties on the Platform 🏡',
                'summary' => 'Summary
        - 🏠 Introduction to Property Listing: The video starts with an overview of listing properties on the platform.
        - 📋 Navigating to My Properties: Instructions on accessing the \'My Properties\' section or the \'My Profile\' section.
        - 🔍 Uploading Properties: Detailed steps for uploading a property, including accepting terms and conditions and filling in property details.
        - 📝 Property Details: Describes the required fields such as tax ID, zoning, structure type, status, square footage, lot size, etc.
        - 📸 Adding Images: How to upload and organize property images.
        - 🕒 Processing and Approval: Information on the processing time for property listings and how to edit property details once approved.
        - 📈 Viewing Property Engagement: Explains how to view who has looked at the property listing and their contact information for follow-up.
        
        How to List Properties on the Platform 🏡
        Introduction
        Listing properties on the revamp365.ai platform is a straightforward process designed to help users showcase their properties effectively. This guide provides a step-by-step tutorial on how to list your properties, including essential tips for maximizing the visibility and accuracy of your listings.
        Navigating to My Properties
        To begin, navigate to the \'My Properties\' section from the main menu. This section provides access to all your property uploads. If it\'s your first time uploading a property, you will need to register and agree to the terms and conditions.
        Uploading Properties
        Once registered, you will see the \'Upload Property\' button. Click on it to start the upload process. You will be required to fill in various details about the property, including:
        - Tax ID: The property\'s tax identification number.
        - Zoning: The zoning code or description of the property\'s zoning.
        - Structure Type: Select the type of property from the dropdown menu.
        - Status: Indicate if the property is active, pending, or sold.
        - Property Details: Include square footage, lot size, number of bedrooms and bathrooms, year built, and HOA information if applicable.
        - Pricing and Valuation: Enter the list price, estimated values for median value, after-repair value (ARV), average rent, and estimated rehab costs.
        Adding Images
        To add images, open the folder containing your property photos and drag and drop them into the upload section. You can rearrange the order of the images to highlight the best features of the property.
        Processing and Approval
        After uploading, the system will process the property listing, which can take up to two hours. Once approved, you can edit any details by navigating to the property listing and updating the information as needed.
        Viewing Property Engagement
        The platform allows you to see who has viewed your property. By clicking on the \'Views\' tab, you can see the names, emails, and phone numbers of logged-in users who have viewed your listing. This information is crucial for following up with potential buyers and closing deals more efficiently.',
                'summary_html' => '<h2>Summary</h2><ul><li>🏠 Introduction to Property Listing: The video starts with an overview of listing properties on the platform.</li></ul><p><br></p><ul><li>📋 Navigating to My Properties: Instructions on accessing the \'My Properties\' section or the \'My Profile\' section.</li></ul><p><br></p><ul><li>🔍 Uploading Properties: Detailed steps for uploading a property, including accepting terms and conditions and filling in property details.</li></ul><p><br></p><ul><li>📝 Property Details: Describes the required fields such as tax ID, zoning, structure type, status, square footage, lot size, etc.</li></ul><p><br></p><ul><li>📸 Adding Images: How to upload and organize property images.</li></ul><p><br></p><ul><li>🕒 Processing and Approval: Information on the processing time for property listings and how to edit property details once approved.</li></ul><p><br></p><ul><li>📈 Viewing Property Engagement: Explains how to view who has looked at the property listing and their contact information for follow-up.</li></ul><h3><br></h3><h3><strong>How to List Properties on the Platform 🏡</strong></h3><p>Introduction</p><p>Listing properties on the revamp365.ai platform is a straightforward process designed to help users showcase their properties effectively. This guide provides a step-by-step tutorial on how to list your properties, including essential tips for maximizing the visibility and accuracy of your listings.</p><p><br></p><p><strong>Navigating to My Properties</strong></p><p>To begin, navigate to the \'My Properties\' section from the main menu. This section provides access to all your property uploads. If it\'s your first time uploading a property, you will need to register and agree to the terms and conditions.</p><p><br></p><p><strong>Uploading Properties</strong></p><p>Once registered, you will see the \'Upload Property\' button. Click on it to start the upload process. You will be required to fill in various details about the property, including:</p><ul><li>Tax ID: The property\'s tax identification number.</li><li>Zoning: The zoning code or description of the property\'s zoning.</li><li>Structure Type: Select the type of property from the dropdown menu.</li><li>Status: Indicate if the property is active, pending, or sold.</li><li>Property Details: Include square footage, lot size, number of bedrooms and bathrooms, year built, and HOA information if applicable.</li><li>Pricing and Valuation: Enter the list price, estimated values for median value, after-repair value (ARV), average rent, and estimated rehab costs.</li></ul><p><strong>Adding Images</strong></p><p>To add images, open the folder containing your property photos and drag and drop them into the upload section. You can rearrange the order of the images to highlight the best features of the property.</p><p><br></p><p><strong>Processing and Approval</strong></p><p>After uploading, the system will process the property listing, which can take up to two hours. Once approved, you can edit any details by navigating to the property listing and updating the information as needed.</p><p><br></p><p><strong>Viewing Property Engagement</strong></p><p>The platform allows you to see who has viewed your property. By clicking on the \'Views\' tab, you can see the names, emails, and phone numbers of logged-in users who have viewed your listing. This information is crucial for following up with potential buyers and closing deals more efficiently.</p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Sell YOUR Deals',
                'transcript' => '0:01 welcome back in this video we are going to talk about listing your properties on the platform and go through a demonstration of exactly how to do that so we\'re gonna navigate to we could go to my profile or we can go right to my properties which is really just taking us to our profile and then this section
        0:22 right here my uploads all right so if this is your first time uploading a property instead of saying upload it\'s going to ask you to register if you click that it\'s basically just going to give you these terms and conditions read through them agree to them and once you do then you will see this upload
        0:48 property button right into it let\'s click upload and we\'re just going to do a sample here of a property that i already have in here but let\'s go through the process 407 as i\'m typing that there we go the system\'s working basically it\'s trying to pull the correct G eo address for that property that way
        1:12 it can mark it and everything on a map so i\'m just gonna click that as soon as i do it\'s gonna fill out all these fields for me here so it pulled in the county, the latitude and longitude now we\'re good to continue on so tax ID number I\'m not going to go through and fill all this out because I don\'t 
        1:36 want this video to be long but put in tax ID number, zoning for this usually we just put the zoning code or if it\'s a residential property you can even just put RES type you\'re going to select from the drop-down what property type it is, status if you\'re just listing this now, it\'s going to be active
        2:01 , square footage, lot size, beds, bass, year built, is there an HOA, yes or click it if no, or I\'m sorry it\'s no right now you would click it if it\'s yes, we should change that, heating type, you\'re going to select from the drop down and if there does happen to be multiple systems you can select multiple
        2:24 in here, okay, cooling type, same thing, taxes, tax assessment, list price, okay, which is obviously important, and and then some of these you\'re going to put in as your estimations for the median value, the ARV, average rent, estimated rental rehab, estimated flip rehab, estimated cash flow.
        2:50 All of these fields 1, 2, 3, 4, 5, 6 are optional. However, I highly recommend them. the end of the day, the system is going to do its own automated valuations anyway.
        3:06 However, you are the expert. You know your deal. You know the areas that you want to do business in much better than a software algorithm does.
        3:17 So put your numbers in here. don\'t be unrealistic or you\'ll just get a bad reputation and people will just gloat over your deals when you send them out.
        3:28 but another purpose of putting these values in here, on the regular search page, how it displays. Is the estimated cash flow or estimated flip profit, those values.
        3:42 As long as you fill a value in here, it\'s going to display your values rather than the system generate. that can be important, municipality school district, listing office for us, that would be our company name, listing agent, well for.
        4:01 For us it would actually be Josh. And then this remarks field, this is where you\'re going to put your listing description.
        4:11 whatever you want to put about the property, that\'ll all go. Right here. Images, self-explanatory. You\'re just going to open the folder that you have with photos.
        4:24 let\'s see can I find we\'ll do a real example here revamp What\'s going on? I would just take my photos.
        4:47 I\'m not going to do all of them because it\'ll take too long for this video. Let\'s just take these first nine.
        5:02 we\'ll drag and drop. And we\'ll just take a second. Thank you. once they are finished uploading, it\'s gonna show like this.
        5:09 And you can move around and reorganize them. If you need to. And then you\'re just gonna click upload. I\'m not gonna do it here cuz again this is a sample.
        5:21 But from there what\'s gonna happen? system is, going to process that property and it\'s gonna pull some of the comps and do some of the auto evaluations that the system does anyway.
        5:33 It could take about an hour, sometimes up to two hours depending upon when you upload. Our database basically runs on the hour so if you upload at 210 in the afternoon it may take Til the next hour to process that.
        5:54 Once it does you\'ll see here that it\'ll show as approved. And. here this is how you could edit any of the details or information about the property that you already uploaded.
        6:08 So I can see all the photos here and everything. all the details and information I put in. This is that remarks field.
        6:14 That\'s the definition we put. Smells like cats and needs a good clean out. we got all our details. Details. We\'ll see you in the details here.
        6:21 Property details. And we could edit any of this if we wanted to. If we did, we would just click save and update it.
        6:27 Okay. Then from here, this is just gonna be an all-encompassing video. From here, we can go over to the views tab.
        6:39 Which by the way, just from this dashboard view right here. Here, we can see the address, the status of the property and how many views it has.
        6:47 Again, let\'s go back into it and now click into this views tab. that loads, anyone that is a logged in user, you\'re gonna be able to see when they viewed this property.
        7:01 Their name, email, and phone number is long- when they have a profile and that way you can kinda gauge who\'s really looking at your deal and you can reach out to them to be proactive and sell your property.
        7:17 We\'re gonna go into the marketing in a separate video. I don\'t want this to go on too long but as a quick overview that\'s how you upload your property.
        7:27 up. Once it processes on the back end. You\'ll see that it\'s approved. You can go into it and edit and modify any of your data here.
        7:37 and get exposed.',
                'uid_code' => 'SD2',
                'url' => 'https://youtu.be/wB-yHXwjcrw',
                'youtube_video_id' => 'wB-yHXwjcrw',
            ],
            [
                'category' => 'Affiliate Partnerships',
                'embed_code' => null,
                'topic' => 'Earn Money with Our Affiliate Program',
                'summary' => 'Summary
        - 💰 Introduction to the Affiliate Program: Overview of how to earn money through the affiliate program by sharing a link.
        - 📈 Earning Potential: Earn up to 33% of subscription costs for each referral, with various commission rates depending on your subscription plan.
        - 🔗 Getting Started: Instructions on accessing the affiliate section, watching the training video, and obtaining your affiliate link.
        - 🌐 Sharing the Link: How to share your affiliate link and track referrals through cookies.
        - 🗓️ Commission Payments: Explanation of the commission payment schedule, with earnings paid out monthly.
        
        Earn Money with Our Affiliate Program
        Introduction
        The affiliate program on the revamp365.ai platform provides an excellent opportunity to earn recurring revenue by sharing a unique link with your network. This guide explains how the program works and how you can maximize your earnings.
        
        Earning Potential
        You can earn up to 33% of the subscription costs for each referral. The commission rates are as follows:
        - Non-members: 11%
        - Essentials members: 22%
        - Accelerator members: 33%
        
        Getting Started
        To get started, log in to your profile and navigate to the affiliate section. If the section is grayed out, you need to watch the affiliate training video in the Revamp University. Once completed, you\'ll have access to your affiliate link.
        
        Sharing the Link
        Share your affiliate link with friends, family, and clients. When someone uses your link to sign up, you earn a commission. The system tracks referrals using cookies, ensuring that even if someone signs up within 30 days of clicking your link, you will still receive credit.
        
        Commission Payments
        Commissions are paid out monthly, with the first payment occurring the month after a new subscription is activated. This provides a steady stream of income as long as your referrals remain subscribed.
        
        Conclusion
        The affiliate program on the revamp365.ai platform is a simple and effective way to generate additional revenue. By leveraging your network and sharing your affiliate link, you can earn substantial commissions. For any questions or further assistance, the support team is available to help you get the most out of the affiliate program.',
                'summary_html' => '<b>WHEN YOU HAVE COMPLETED THE VIDEO - Click the checkmark in the upper right corner!</b><br><br>
        
        <h2>Summary</h2><ul><li>💰 Introduction to the Affiliate Program: Overview of how to earn money through the affiliate program by sharing a link.</li><li>📈 Earning Potential: Earn up to 33% of subscription costs for each referral, with various commission rates depending on your subscription plan.</li><li>🔗 Getting Started: Instructions on accessing the affiliate section, watching the training video, and obtaining your affiliate link.</li><li>🌐 Sharing the Link: How to share your affiliate link and track referrals through cookies.</li><li>🗓️ Commission Payments: Explanation of the commission payment schedule, with earnings paid out monthly.</li></ul><p><br></p><h3><strong>Earn Money with Our Affiliate Program</strong></h3><p><strong>Introduction</strong></p><p>The affiliate program on the revamp365.ai platform provides an excellent opportunity to earn recurring revenue by sharing a unique link with your network. This guide explains how the program works and how you can maximize your earnings.</p><p><br></p><p><strong>Earning Potential</strong></p><p>You can earn up to 33% of the subscription costs for each referral. The commission rates are as follows:</p><ul><li>Non-members: 11%</li><li>Essentials members: 22%</li><li>Accelerator members: 33%</li></ul><p><br></p><p><strong>Getting Started</strong></p><p>To get started, log in to your profile and navigate to the affiliate section. If the section is grayed out, you need to watch the affiliate training video in the Revamp University. Once completed, you\'ll have access to your affiliate link.</p><p><br></p><p><strong>Sharing the Link</strong></p><p>Share your affiliate link with friends, family, and clients. When someone uses your link to sign up, you earn a commission. The system tracks referrals using cookies, ensuring that even if someone signs up within 30 days of clicking your link, you will still receive credit.</p><p><br></p><p><strong>Commission Payments</strong></p><p>Commissions are paid out monthly, with the first payment occurring the month after a new subscription is activated. This provides a steady stream of income as long as your referrals remain subscribed.</p><p><br></p><h3><strong>Conclusion</strong></h3><p>The affiliate program on the revamp365.ai platform is a simple and effective way to generate additional revenue. By leveraging your network and sharing your affiliate link, you can earn substantial commissions. For any questions or further assistance, the support team is available to help you get the most out of the affiliate program.</p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Affiliate Partnerships',
                'transcript' => '0:02 Alright, here we go. In this video, we are going to be talking about how to earn money with us through our affiliate program, so you can not just use your affiliate link, this as a tool and as a software to be able to find deals, but it can be a recurring revenue source that can bring in consistent money
        0:23 for you and your business simply for sharing an affiliate link with friends or clients that you work with and you\'ll be able to earn up to 33% of their subscription cost for your for the life of their subscription so a lot of other companies have affiliate programs and everything and frankly we wanted
        0:49 to be able to pay the highest rates uhm in the industry and we believe that we have a really awesome tool so being able to make it so any of our users uhm or even people that are not users you\'ll be able to earn through the affiliate program whether you\'re a paying member or not so uhm it\'s going to 
        1:18 create a lot of opportunities for opportunity and ways to make recurring revenue so who is this for uhm to that point you do not need to be a paying member on the platform.
        1:31 however, being a customer already of the platform, excuse me, uhm that will earn you higher affiliate percentages. So to break it down really simply, uhm here let\'s pull up, Alright, so let\'s pull it up here.
        1:54 Let\'s move my face out of the way. Alright, so if you you are not a member of the platform, basically you will be able to earn 11% on any recurring subscriptions from sharing your affiliate link with someone.
        2:12 If you\'re on the essentials program, you\'ll earn 22% for any subscriptions and the accelerator program will earn you 33% commissions on any affiliate that signs up through your affiliate link.
        2:34 So, this is an awesome program. Awesome way to be able to, first and foremost, maybe offset the cost of your subscription.
        2:44 Maybe you\'re signed up to the monthly um accelerator program, right? If you\'re on the accelerator and you\'re paying monthly, you didn\'t opt to do the annual payment, which seems to a lot of money, by the way.
        3:01 Uhm, you know, so if you just line up five additional friends, family members, clients of yours. and they sign up for the platform through your affiliate link, you could easily earn another $150 every month just through sharing a link.
        3:24 So, if you have an email list or anything like that, you could earn into the thousands really, really easily. I know, excuse me, I know a lot of guys that are affiliates for different software platforms that earn $150 and tens of thousands of dollars every month, not from creating a software but by simply
        3:45 sharing that software with their network. So uh, alright, here is how you will do that. when you log in and go into your profile, you will see this button at the top here, my referrals.
        4:01 At first, it\'s probably going to be greyed out. The reason it will be greyed out. I should have left it greyed out here to show you in this demo.
        4:13 But if you click it and it\'s greyed out, it\'s basically going to tell you, go watch in the learn section, Revamp University.
        4:22 Watch the affiliate training video series. So you\'ll be able to see that here. This video is part of that training series.
        4:30 Um, so there\'s not a whole lot to it. There\'s not. An extensive amount of info or indoctrination that we\'re going to try to put you through.
        4:41 We want to make this as simple as humanly possible. But as soon as you watch the training video, check it off that it\'s completed and come back to your profile.
        4:52 And then this section is going to be available to you. All that you really need to do from here is copy this link and share this with anybody and just tell them, hey here\'s this software, I use it, it\'s helped me find deals, it\'s helping me do more business, you should should check it out, and if you
        5:13 do, please just sign up under my link. Okay, so let\'s uhm open that, oops. Let\'s open this URL in a new window.
        5:29 So this is all it\'s going to do. You\'re going to send this link to your friend. They\'re gonna go here and they\'re just gonna sign up.
        5:41 As soon as they do, they\'re gonna show down here as an affiliate of our of yours. And if they do sign up to a subscription package, you\'ll be able to see from here how much you\'re gonna earn on a monthly basis from referring that customer.
        6:02 Okay. And what\'s really cool about this is if somebody opens your url and they do nothing, if they don\'t sign up, if they don\'t create an account for a week, for two weeks, and then, then they log in and sign up, create an account, this will still be a free account.
        6:39 It\'s tracking that URL in the form of cookies in web cookies to uhm their IP address to their profile. And if they want a do sign up within 30 days of receiving this URL from you, that affiliate referral is going to be attributed to your account.
        7:04 So basically it\'s a great way to kind of hack the system and and game us a little bit if you share this URL and a bunch of people open the URL from you and maybe they don\'t sign up in that day but they get another email from us because we do a lot of marketing and they decide to sign up at that point
        7:25 two weeks later, your uhm affiliate URL is still going to be trending. user and when they sign up, as long as it\'s within 30 days, the cookies do erase and update after 30 days, that user sign up will be attributed to you.
        7:43 So that\'s again, trying to keep this process as simple as humanly possible. We\'re rolling this all out right now. It is June of 2024.
        7:54 We are just rolling this out. Uhm, what we are planning to do is any affiliate earnings will likely be paid out the month after June 24th.
        8:09 That person subscribes. So let\'s just say you sent me an affiliate link. I signed up under you. Today is June 10th So now my my subscription schedule Renews on the 10th of every month You would get your affiliate commissions the month after That user signs up and then you would get that commission every
        8:35 month month after that user signs up so if you have any questions on this or anything just reach out to me or anyone on the team and we\'ll try to get any questions or queries you have answered Looking forward to working with a lot of different people to share this software and earn some money together
        9:01 . So, looking forward to it. Talk to you soon.',
                'uid_code' => 'AA1',
                'url' => 'https://youtu.be/eLnj8fjrSPE',
                'youtube_video_id' => 'eLnj8fjrSPE',
            ],
            [
                'category' => 'Sell YOUR Deals',
                'embed_code' => null,
                'topic' => 'Listing Your Properties on Revamp 365 Platform',
                'summary' => '📋 Overview of Listing Properties: The video begins by outlining the process of listing properties for sale on the Revamp 365 platform, highlighting that this could include wholesale deals, properties already owned, or those not listed on the MLS.
        
        🛠️ Initial Setup: As soon as a user visits the site, wholesale properties are displayed at the top of the list by default, providing more visibility to these listings.
        
        🏷️ Thumbnail Features: Key features displayed on the property thumbnails include monthly rental income, AVM, ARV, estimated cash flow delta, and estimated flip profit, which users can manipulate to better reflect property values.
        
        💼 Listing Details: Users are required to input detailed information about their properties, with a focus on providing accurate data for potential buyers.
        
        📊 Automated Valuations: The platform generates automated valuations, but users can override these with their own values, leveraging their expertise in the property market.
        
        🌐 Platform Traffic: Listing properties on Revamp 365 taps into a dedicated audience of investment deal seekers, increasing the likelihood of quick sales.
        
        🔧 Ongoing Tool Enhancements: The platform is continually updated with new tools to improve the user experience, including email marketing capabilities and enhanced data enrichment for listed properties.
        
        📧 Email Marketing: Built-in email marketing tools allow users to promote their properties to a wider audience without additional costs.
        
        🔍 User Activity Insights: The platform provides detailed analytics on user interactions with listed properties, helping sellers identify potential buyers.
        
        🏢 Claiming MLS Listings: For properties already listed on the MLS, users can claim these on the platform to gain access to user activity data.
        
        🛡️ Privacy Controls: New features allow sellers to toggle the visibility of property addresses, sharing them only with vetted buyers.
        
        💸 Affiliate Program: The platform offers an affiliate reselling program, enabling users to earn a recurring revenue by referring new members to Revamp 365.
        Insights Based on Numbers
        - Monthly Rental Income: Displayed prominently on property thumbnails, this figure helps investors quickly assess the potential cash flow of a property.
        - Automated Valuations: The AVM and ARV provide a starting point for property value, which can be fine-tuned by the user to reflect more accurate market conditions.
        - User Interaction Metrics: Detailed analytics on views and interactions offer valuable insights into buyer interest and behavior, helping sellers refine their marketing strategies.
        ',
                'summary_html' => '<p>📋 <strong>Overview of Listing Properties</strong>: The video begins by outlining the process of listing properties for sale on the Revamp 365 platform, highlighting that this could include wholesale deals, properties already owned, or those not listed on the MLS.</p><p><br></p><p>🛠️ <strong>Initial Setup</strong>: As soon as a user visits the site, wholesale properties are displayed at the top of the list by default, providing more visibility to these listings.</p><p><br></p><p>🏷️ <strong>Thumbnail Features</strong>: Key features displayed on the property thumbnails include monthly rental income, AVM, ARV, estimated cash flow delta, and estimated flip profit, which users can manipulate to better reflect property values.</p><p><br></p><p>💼 <strong>Listing Details</strong>: Users are required to input detailed information about their properties, with a focus on providing accurate data for potential buyers.</p><p><br></p><p>📊 <strong>Automated Valuations</strong>: The platform generates automated valuations, but users can override these with their own values, leveraging their expertise in the property market.</p><p><br></p><p>🌐 <strong>Platform Traffic</strong>: Listing properties on Revamp 365 taps into a dedicated audience of investment deal seekers, increasing the likelihood of quick sales.</p><p><br></p><p>🔧 <strong>Ongoing Tool Enhancements</strong>: The platform is continually updated with new tools to improve the user experience, including email marketing capabilities and enhanced data enrichment for listed properties.</p><p><br></p><p>📧 <strong>Email Marketing</strong>: Built-in email marketing tools allow users to promote their properties to a wider audience without additional costs.</p><p><br></p><p>🔍 <strong>User Activity Insights</strong>: The platform provides detailed analytics on user interactions with listed properties, helping sellers identify potential buyers.</p><p><br></p><p>🏢 <strong>Claiming MLS Listings</strong>: For properties already listed on the MLS, users can claim these on the platform to gain access to user activity data.</p><p><br></p><p>🛡️ <strong>Privacy Controls</strong>: New features allow sellers to toggle the visibility of property addresses, sharing them only with vetted buyers.</p><p><br></p><p>💸 <strong>Affiliate Program</strong>: The platform offers an affiliate reselling program, enabling users to earn a recurring revenue by referring new members to Revamp 365.</p><h2>Insights Based on Numbers</h2><ul><li><strong>Monthly Rental Income</strong>: Displayed prominently on property thumbnails, this figure helps investors quickly assess the potential cash flow of a property.</li><li><strong>Automated Valuations</strong>: The AVM and ARV provide a starting point for property value, which can be fine-tuned by the user to reflect more accurate market conditions.</li><li><strong>User Interaction Metrics</strong>: Detailed analytics on views and interactions offer valuable insights into buyer interest and behavior, helping sellers refine their marketing strategies.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Sell YOUR Deals',
                'transcript' => '00:01 In this training segment, we are going to talk about listing your properties for sale on the Rebam 365 platform. So, that could be wholesale deals that you have, properties that you already own and maybe you don\'t want to list them on the MLS or you are a wholesaler, broker, agent, whatever and you already
        00:22 do have your properties listed on the MLS and you want to be able to claim them on the platform so you\'ll be able to leverage some of the tools and features that we\'re going to talk about here, this series will break that all down for you.
        00:37 So the idea is of this first initial video in the series here is to kind of give you a high level overview of what listing properties on the platform looks like, some of the benefits of doing so.
        00:51 and overall what that process looks like and what you\'re able to access as a result of doing this. So, we\'re gonna dive right into it here.
        01:04 for starters, just some things to bring up as a high level. As soon as a user, a buyer, a website member, comes on to look for properties before they even put in a search filter, before they sort properties at all, as soon as this page loads, the default default does display all wholesale properties 
        01:30 right at the top of the list. Your properties will be displayed there as well. The idea is have them top of mind, top of list, so they get more eyeballs than any other MLS listings or anything like that.
        01:45 So that will be a nice little feature and perk for you as well as you\'ll be able to have your own store, if you will, which will basically be like a a filtered list of only your properties so you can share that URL with your email address.
        02:05 Your email list or your buyers or your clients, whatever that might look like. other things to touch on real quick, right here from the thumbnail view without even going into the record, obviously the one of the main features and functions of the platform is displaying here.
        02:26 amount of monthly rental income, the AVM, the ARV, estimated cash flow, delta, and estimated flip profit. These are all numbers that you will have the ability to manipulate. you\'ll see what I mean here.
        02:41 Actually, let\'s just go right into a record. you\'ll see right here, the wholesale debt. This is basically a high level overview.
        02:51 This is all the data and information that you\'re going to be inputting about your property that you\'re selling. Again, your wholesale deal, the property that you own, whatever.
        03:00 Whatever it is, you\'re going to be putting in this information and you can see right here we got seller AVM, seller ARV, so on and so forth.
        03:10 Now let me take a step back real quick because I kind of contradicted myself there. Slightly, right now, as of the time of recording this, it is displaying all of the system generated values right here.
        03:24 We actually already did an update, we just have not put it yet, so by the time you watch this video, the function will exist, where if you do choose to put in any of these values here in this state, section which you can leave blank by the way, but if you do put them in here, that\'s what\'s gonna display
        03:45 on the thumbnail. So essentially the idea is you are the expert, you should know everything, the more intimate details about the property and the comps and the potential use cases and all of those things.
        03:59 So you should know better than the data algorithm that\'s coming up with automated values. So that\'s what\'s going to display to the user.
        04:07 With that being said, as you come down to the auto valuation section, if the user wants to see any of those auto-generated values, they will see here.
        04:19 Our model on the back end, after you upload a property, goes and pulls all of this data and pulls it all in and enriches the data and user experience for the platform members, okay?
        04:36 So, let\'s just go over real quick. Some of the features and benefits of being able to list your properties on here.
        04:46 Obviously, you\'re tapping directly into the traffic and engaged users that are already on this platform. Which is a massive benefit in itself because the people that are coming here and using this platform They\'re not looking for anything other than investment deals.
        05:06 So we have really engaged buyers, okay? On top of that though, we\'re building out right now I mean we\'re actively always iterating and releasing new tools all the time.
        05:22 A heavy focus of ours right now is on tools to make your life and your experience posting properties on YouTube.
        05:32 here that much better. We want this to be the platform that you want to post all your inventory on because you find more buyers quicker and sell your deals faster.
        05:44 For higher numbers than you ever thought you could and I know that that works and it exists because we\'re living proof of it that\'s that\'s how this whole platform came to exist in the first place.
        05:58 to dive into this a little bit, if you click on your icon up here, let\'s just go to my properties.
        06:03 It\'s basically going to bring you to my profile page and over here. to the my uploads tab. So once you\'re there, you\'re going to see, it\'s going to ask you to register for listing wholesale deals.
        06:18 You got some terms and conditions in here that I won\'t go through all of it in the video you can read through.
        06:24 But basically it\'s saying that you have the right and the authority to list the property that you intend to list on our platform.
        06:33 Maybe you are the owner of it or you have the property under contract to purchase with an assignable agreement whatever the case may be.
        06:44 Go through here, read the terms and conditions. Agree, you do that now the button here says upload property. So we can click that.
        06:55 As soon as we do it\'s going to give us the ability to list a property here. And for what it\'s worth, side note, this user interface is all going to be updated really, really soon.
        07:07 It actually already exists. Again, the update is just not finished. But, I\'m probably not going to update this video anyway because the functionality will be the same.
        07:18 The only difference will be that the user interface will be a little bit bit different and a little bit prettier for you.
        07:26 So, I\'m not going to show you in this video specifically. We\'ll dive into uploading a property in the specific training video for uploading a Again, stay in high level here and going over some of the features and perks of doing this.
        07:43 So, after you do upload a property, what\'s going to happen is, it\'s going to take probably about a hour. Our system basically runs a bunch of different API calls and goes and gets some of the rental data, some of the comparable sales data, and enriches that data.
        08:01 Just like it does with any MLS property. After that is completed, again it usually takes an hour, sometimes it\'s quicker, sometimes it takes 20 minutes, sometimes it\'s an hour and 20 minutes.
        08:13 But after that processes, your property will then be live on the main page, you\'ll be able to view it live, share the URL on wherever, social media, your emails.
        08:25 list, and start pushing that out there to your buyers to try to attract someone and get some offers on that deal.
        08:32 So the value of value in doing that for you. There\'s there\'s many different layers to it. First and foremost. Once you do that, you will be able to see any user activity on your property.
        08:45 Since I\'m in a demo account here. I can\'t really show you, but you\'ll see in the specific training video. You\'ll be able to see things like the number of views your property had, who is viewing your property, who is clicking on your property.
        08:59 Who is spending a lot of time looking at your property, who is sharing your property, and that way you can reach out to anybody that is think has indicators that they may be a prospective buyer, right?
        09:14 So that\'s the idea with that as well as from here you will be able to do email marketing built into the platform.
        09:25 So that\'s one of our latest updates that we\'re pushing out right now. Again, there\'s going to be a training video in this series.
        09:32 On that specifically but we really wanted you to be able to have email capability tools already included with your membership.
        09:43 I know for us personally and my company, we spend about $550 a month on our email service provider right now and after just releasing this email capability built into here, we\'ll literally be able to cancel that and still be able to do just as much email marketing with any of our deals to our buyers.
        10:08 So again, there will be a specific training video on how to use and leverage that. And yeah, I think that covers pretty much everything.
        10:18 thing in a nutshell of uploading your properties on to here. There will also be another point, I know again, for my companies.
        10:31 Sometimes we do list our wholesale deals on the MLS. If that\'s the case, your property is going to end up on the platform anyway.
        10:39 But there is a function to be able to claim that property as yours, so it\'ll display for you in your dashboard here, and you\'ll be able to see the number of views, who\'s viewing it, who\'s interacting with it, and and all of those things, so you\'ll have more control and more of a pulse on what\'s going
        11:02 on with your property. Last thing I mentioned in this video before we get into the specifics of how to post your property on here.
        11:15 Another feature that we\'re rolling out again right now is the ability to for you to toggle on or off whether you want your full property address to display on the platform or not.
        11:29 A lot of wholesalers like to shield the addresses of their property and only show it to vetted buyers and you will have that capability with this next release that we\'re pushing live and again from your doorstep.
        11:45 Dashboard here, you\'ll be able to see, again demo account, but you\'ll be able to see not only the number of views and what users are viewing.
        11:56 Viewing your property, but you\'ll also be able to see a list of users that are requesting the full address of your property, which really indicates they have a high level of interest and maybe motivation in submit submitting an offer on your deal.
        12:17 I almost forgot and I would regret if I didn\'t interject this into here, but one of the other perks of Listing your properties on here and being a seller on the platform is with the plan that you\'re on with the ability to list your deals on here.
        12:35 That also comes with affiliate reselling tier 3 and that\'s basically just our affiliate partner program. It earns you the highest percentage you earn 33% of anyone that signs up under you.
        12:50 So that\'s and what that looks like is as you start posting your deals on here and you start using the email tools and functions that are already built in and you already have access to and you start sharing your affiliate links, anybody that comes to the platform and signs up on any of the subscription
        13:10 plans, you earn in perpetuity. 33% of whatever their subscription cost is. So if they\'re paying $100 a month, $150 a month, or if they\'re on an advanced plan, you\'re going to earn that every single month, whether it\'s $33, $50, for every single user that signs up under you.
        13:31 So now not only are you able to sell your deals quicker for a higher amount than you\'ve ever been able to before, ENDS have a professional essentially storefront for you.
        13:45 We\'re all of your deals that you intend to sell, but you also have a recurring revenue model built in so you can earn with us as the platform goes.
        13:57 We truly care about the wholesale industry and even the retail industry and brokers and how we helping everybody that has investment deal inventory, helping you to grow and scale your business and a big part of that is scaling revenue.
        14:16 So we\'re a really excited to be able to offer this and it\'s going to be a great way to continually bring in money month over month for you.',
                'uid_code' => 'SD1',
                'url' => 'https://youtu.be/gDp-0Jneo7A',
                'youtube_video_id' => 'gDp-0Jneo7A',
            ],
            [
                'category' => 'Sell YOUR Deals',
                'embed_code' => null,
                'topic' => 'Email Marketing Tool Tutorial 👍',
                'summary' => '📧 Introduction to Email Marketing Tool: The video begins with an overview of the email marketing tool on the revamp365.ai platform, focusing on marketing properties.
        📂 Navigating to My Properties: It shows how to access the \'My Properties\' section and details the properties available.
        📊 Marketing Tab Overview: An introduction to the Marketing Bulk Mail Manager page, which initially has no email lists uploaded.
        📝 Uploading Email Lists: Detailed instructions on uploading email lists either manually or via CSV file, emphasizing the importance of data enrichment.
        🔍 Segmenting Email Lists: Explains how to segment email lists by tags, counties, zip codes, or price range for targeted marketing.
        ✉️ Sending Marketing Emails: Demonstrates sending a marketing email, including selecting recipients and composing the email with subject lines and body text.
        🖼️ Email Formatting and Appearance: Shows how the email will appear in different email services, with a focus on Gmail and Outlook.
        🚀 Driving Traffic to Property Listings: Highlights the clickable elements in the email that drive recipients to property listings on the platform.',
                'summary_html' => '<p>📧 <strong>Introduction to Email Marketing Tool</strong>: The video begins with an overview of the email marketing tool on the revamp365.ai platform, focusing on marketing properties.</p><p><br></p><p>📂 <strong>Navigating to My Properties</strong>: It shows how to access the \'My Properties\' section and details the properties available.</p><p><br></p><p>📊 <strong>Marketing Tab Overview</strong>: An introduction to the Marketing Bulk Mail Manager page, which initially has no email lists uploaded.</p><p><br></p><p>📝 <strong>Uploading Email Lists</strong>: Detailed instructions on uploading email lists either manually or via CSV file, emphasizing the importance of data enrichment.</p><p><br></p><p>🔍 <strong>Segmenting Email Lists</strong>: Explains how to segment email lists by tags, counties, zip codes, or price range for targeted marketing.</p><p><br></p><p>✉️ <strong>Sending Marketing Emails</strong>: Demonstrates sending a marketing email, including selecting recipients and composing the email with subject lines and body text.</p><p><br></p><p>🖼️ <strong>Email Formatting and Appearance</strong>: Shows how the email will appear in different email services, with a focus on Gmail and Outlook.</p><p><br></p><p>🚀 <strong>Driving Traffic to Property Listings</strong>: Highlights the clickable elements in the email that drive recipients to property listings on the platform.</p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Sell YOUR Deals',
                'transcript' => '0:01 Welcome back in this training series. We are going to talk about marketing either your wholesale properties or even other listings that are on the platform that are not your listings.
        0:15 Alright, so we\'re going to go to the my properties section. And soon assuming for demonstration purposes here, let\'s say that it is a property that you uploaded.
        0:29 We can go to see details for any properties you have up. And we\'re gonna go to this marketing tab. All right, so here is what the marketing bulking looks mail manager page looks like.
        0:49 So as it stands right now in this demo account, we don\'t have any email list uploaded at all. So I\'m gonna walk you through that.
        1:00 But the goal here overall is to make this so this is our email marketing tool that we use for our business.
        1:11 So that does mean that we have to get some data in. In here we have to have some emails, some contacts, and the better that list, excuse me, and data is, the better we\'re going to be.
        1:27 be able to segment and choose who to market to. we\'re not going to go super deep into that and the psychology behind who to target and why, we\'ll leave that for another time.
        1:39 So to keep it as simple and streamlined as we can here, there\'s a couple different ways that we can get data into here.
        1:48 We can click this download sample and we can see from right here, this is the format if we were gonna upload a CSV file with data.
        2:03 It\'s gonna ask for email, tags, phone number, name, and you don\'t have to fill out all these fields. Obviously you do have to fill out email because this is for an email list.
        2:16 If you don\'t want to put in the counties that your buyers invest or do business in, that\'s fine. Same thing with zip codes or price range.
        2:26 This is just a way to enrich your data if you have access to that information. So here to start, let\'s not go the bulk upload approach.
        2:40 Let\'s just go and simply click this add an email button here. I\'m going to enter myself and I will do a tag here.
        2:50 Let\'s just a tag called myself test. That way in the future if I wanted to just grab my own email to send a test to myself I would be able to easily do that by searching through the tags.
        3:06 Let\'s put a name in here. My keyboard doesn\'t want to work today. Counties. Let\'s, let\'s select some. you don\'t have to do this.
        3:21 just leave the rest blank. so now it\'s showing that I have one email in my list here. So that\'s one way that you can.
        3:31 You can do single entry to get records into your email list. The second way, like I just showed with downloading that sample CSV file.
        3:42 You If you already have an email marketing list, you can just format it the same way that it\'s shown in that sample.
        3:51 And then just click this Upload a Mail List. List button right here. And you\'re gonna drop your file directly into there.
        4:00 Let me see if I have a sample real quick. so I found a sample. Mail list here, so I\'m just gonna drag.
        4:08 Maybe I can\'t drag and drop. Hang on. hang on. Okay, so you can\'t drag and drop, you just have to click the button and then select from your file explorer there.
        4:19 Now I\'m just gonna click this import button. And it\'s gonna start to, import that list here. It might take a minute.
        4:29 selected the wrong list. The other one that I just uploaded now is probably, Processing on the back end, but while that\'s working doesn\'t really affect us too much here I wanted to send a marketing email out to my list I could segment that list and select only people that purchase in the county that 
        4:52 I\'m going to send the marketing email out for or I could select a price range or whatever I want to do there.
        4:58 We could buy tags, great. Let\'s just say that we\'re going to send this to our entire list, we\'re just going to select this.
        5:06 If I did segment, soon as you put filter options in. It\'s going to change your list, same thing, I can select all there.
        5:15 But let\'s just say we have no tags selected. And let\'s just say that I do want to send this as a sample.
        5:22 First, I\'m going to send it to myself as a test. I\'m going to select who I want to send it to.
        5:29 And I\'m going to select send mail from this drop down. And it\'s going to bring me to this window. Now I\'m going to search for the property here.
        5:39 We\'re going to use that 407 Fairview as an example. As soon as I type that it should load. Yep, there we go.
        5:48 Going to select it from the list right there. And it\'s basically going to pull in the information and data about that subject.
        5:58 React, make this work. Whatever you choose. You can put any emojis or whatever in here if you want to. you believe is a good strong email subject line.
        6:21 Then for the description, this is basically whatever you want to say in the body of the email message and you can actually format this whatever way you choose.
        6:34 So, I\'ll ah, I\'ll drop a sample in here real quick. here you go, here\'s just a quick sample you can.
        6:45 And so, what the text however you want so your email stands out to your reader and hopefully clicks through and takes an interest in your property. I\'m going to do now is we see that this is selected to send only to me.
        7:01 Here\'s the property. Here\'s the subject, body of our message. I\'m just going to click send. that comes through. In a second, we\'ll see what that looks like.
        7:11 right, boom, that quick. Not even a minute later and we got the email. And it\'s important to note that this is going to look slightly different in.
        7:21 Different email services, Outlook tends to look the absolute worst out of all of them. Obviously, Gmail is a huge one and this actually looks a whole lot better in Gmail.
        7:34 But I\'m on Outlook on my computer here, so this is what it looks like. can see, we got a picture of the property which does work as a URL, so we could click on this and it\'s going to drive the user to your page.
        7:47 Same thing with this right here, this is how it works. All the emails are going to be laid out. It\'s going to say click to see the full address.
        7:54 If they click it, it\'s going to drive them to your page. All they can see is off market deal. the county and for the price that you\'re marketing at.
        8:06 Beds, baths, square footage, a button for more info, which again, looks terrible promise it doesn\'t look like that on gmail men here\'s our text formatted the way that we did at the bottom of the email it\'s gonna have your photo from your profile your name phone number email and address if you share it
        8:36 in your profile okay so there you go that is how you can utilize this email tool on the platform to send out your properties really quickly really professionally and get more eyeballs on your deals so just to demonstrate here let\'s click the picture it drive directly to that property listing boom there
        9:04 we go okay hopefully that\'s helpful any questions reach out to me or the team',
                'uid_code' => 'SD3',
                'url' => 'https://youtu.be/Y0X_q5JTEyc',
                'youtube_video_id' => 'Y0X_q5JTEyc',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Search ANY Property',
                'summary' => '🔍 Researching Off-Market Properties
        Highlight: How to look up and evaluate properties that are not listed on the MLS.
        Details: The video explains methods for researching off-market properties using a search tool, whether for determining value, refinancing, or investment analysis.
        🏠 Understanding Public Records vs. MLS Listings
        Highlight: Difference between public records and MLS listings.
        Details: When searching for a property, the system provides public record details rather than MLS listings, which can impact the data displayed.
        📊 AVM & ARV Calculation Methods
        Highlight: How automated property valuations work.
        Details: The system calculates an Automated Valuation Model (AVM) and After-Repair Value (ARV) based on comparable properties sold in the last six months, within a mile, and within a similar size range.
        ⚠️ Property Type Considerations
        Highlight: Ensuring accurate property type classification.
        Details: The system may incorrectly categorize row homes or twin homes as detached single-family houses, leading to inaccurate valuations. Users should verify property type manually.
        📑 Tools for Property Analysis
        Highlight: Additional tools for investors and buyers.
        Details: The platform provides a rental calculator, flip calculator, and access to MLS history to aid decision-making.
        🛠 AI-Powered Property Insights
        Highlight: AI-assisted analysis for investors.
        Details: Users can ask AI about city details, investment potential, and even generate legal documents like an Agreement of Sale directly within the tool.
        📈 Insights Based on Numbers:
        The AVM and ARV calculations prioritize comps within a mile radius, adjusting for square footage differences within ±20%.
        Public records do not always distinguish between property types, which can lead to valuation discrepancies.
        ',
                'summary_html' => '<h3>🔍 Researching Off-Market Properties</h3><ul><li><strong>Highlight:</strong> How to look up and evaluate properties that are not listed on the MLS.</li><li><strong>Details:</strong> The video explains methods for researching off-market properties using a search tool, whether for determining value, refinancing, or investment analysis.</li></ul><p><br></p><h3>🏠 Understanding Public Records vs. MLS Listings</h3><ul><li><strong>Highlight:</strong> Difference between public records and MLS listings.</li><li><strong>Details:</strong> When searching for a property, the system provides public record details rather than MLS listings, which can impact the data displayed.</li></ul><p><br></p><h3>📊 AVM &amp; ARV Calculation Methods</h3><ul><li><strong>Highlight:</strong> How automated property valuations work.</li><li><strong>Details:</strong> The system calculates an Automated Valuation Model (AVM) and After-Repair Value (ARV) based on comparable properties sold in the last six months, within a mile, and within a similar size range.</li></ul><p><br></p><h3>⚠️ Property Type Considerations</h3><ul><li><strong>Highlight:</strong> Ensuring accurate property type classification.</li><li><strong>Details:</strong> The system may incorrectly categorize row homes or twin homes as detached single-family houses, leading to inaccurate valuations. Users should verify property type manually.</li></ul><p><br></p><h3>📑 Tools for Property Analysis</h3><ul><li><strong>Highlight:</strong> Additional tools for investors and buyers.</li><li><strong>Details:</strong> The platform provides a rental calculator, flip calculator, and access to MLS history to aid decision-making.</li></ul><p><br></p><h3>🛠 AI-Powered Property Insights</h3><ul><li><strong>Highlight:</strong> AI-assisted analysis for investors.</li><li><strong>Details:</strong> Users can ask AI about city details, investment potential, and even generate legal documents like an Agreement of Sale directly within the tool.</li></ul><h3>📈 Insights Based on Numbers:</h3><ul><li>The AVM and ARV calculations prioritize comps within a mile radius, adjusting for square footage differences within ±20%.</li><li>Public records do not always distinguish between property types, which can lead to valuation discrepancies.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => 'In this training video, we are going to dive into how to look up and research a property that is not maybe listed on the MLS currently or maybe was not ever even listed in the past and there\'s no MLS record of it.
        
        So, there\'s a couple different reasons that this could be useful for you. Maybe you\'re talking directly with the seller of a property that\'s off-market. You\'re trying to determine value. You\'re trying to figure out your max allowable offer, or maybe it\'s a property that you own and you kind of want to see some comps because you\'re considering refinancing, whatever. There\'s a million reasons you would want to look up a property.
        
        Alright, so you\'re going to do that with this search bar at the top here from the most mobile app. There\'s, in the toolbar, a little magnifying glass that would do the same function as what I\'m going to show you here. And we\'re just going to punch an address in here. So I was recently just looking at this property. I flipped this a few years ago.
        
        B-E-L-F-I-E-L-D, can\'t talk and type at the same time. Alright, as you start typing, the address should populate there, and then you just click it. And it\'s going to load there. And at first, you\'ll see a Zillow estimate pops up here. Okay, there we go. It takes a second for our database to populate AVM and ARV, and here we go.
        
        We\'re now in the record. So first thing that I want to highlight and point out is if this is your first time doing this, you\'re probably already used to the platform and kind of how things look and work and feel. And this looks somewhat like an MLS listing when you open it up. The way that I like to look at this is when we search a property the way that we just did, we\'re not looking at the listing, we\'re looking at essentially the public record for that property.
        
        So where this comes into play is let\'s say there was a property that was currently listed and we searched this property this way. When we open this pop-up, this is what we\'re going to be looking at. It\'s going to be the public records details for that property. Not necessarily the listing. There\'s a caveat to that. I\'ll show you in a second, but let\'s dig into it here.
        
        So we got some of the property details that you\'d be able to find on other platforms. You have pricing and tax history. So, I bought this, oh man, I guess I bought it in 2020. This was a great flip. There we go.
        
        You can also sometimes pull up ownership information, and right now we\'re in the property details section. If I jump over to this property value tab, that\'s where I can kind of dial in on that AVM and ARV.
        
        These values, what it\'s doing is the system is leveraging our database of comps and in real-time, it is assessing an AVM and an ARV based on all of the available comps in our database. So this follows the same rules as ARV and AVM for listings. What that means, summarized real quickly, is it\'s looking for comps that have sold in the last six months in the same zip code.
        
        It favors properties that are within a mile of the subject property, and it\'s looking for properties that are plus or minus 20% of the square footage of the subject property and the same property type. So in this case, it would be single-family detached. That\'s an important one to highlight because this is not a flawless method.
        
        Without getting too technical, this one worked properly. It\'s a detached property, and it comped it based on it being a detached property. Sometimes the public records data that\'s available doesn\'t delineate the actual property type. For the most part, public records are just going to say single-family detached. It\'s not going to say if it\'s a twin, a row home, or an end of row, which are all property types in the MLS.
        
        So my word of caution here is if you\'re searching a twin, a row home, an end of row, or any of those property types, even if you\'re looking at manufactured mobile homes, just make sure that before you put any confidence in the AVM and ARV that it\'s displaying the actual property type.
        
        We\'ll try to pull up another example here in a second with a row home and we\'ll see what the system spits out for us, but just something to be aware of there.
        
        Okay, so we got our AVM, we got our ARV. If we scroll down a little bit more, it\'s automatically showing us those recent comps that it\'s pulling the data set from. We could look at any of those. We could tighten up the criteria here and just dial in on exactly what it is that we\'re looking for, but very similar to the way the MLS listing tab with AVMs and ARVs works.
        
        So we also have the calculator tab here. As always, you have your rental calculator and your flip calculator.
        
        It\'s worth mentioning that the purchase price is really basing that off of the AVM. You\'re probably not purchasing at that, so plug in your numbers as you see fit, whatever. And you\'ll be good to go there.
        
        Then this last tab here, MLS history. Basically, if there\'s any active or recent listings within the last 18 months, they\'ll display here and you would be able to link to that.
        
        I\'m going to show you a different example here. So let\'s close out of this record. This is a current active listing, right? 614 Fern Street, Darby. I\'m going to punch that in here.
        
        Now, to the point I was making earlier, when I go into this, I\'m going to click this. I\'m now going into the public record for 614 Fern Street. So I\'m not looking at the listing, I\'m looking at essentially the public record for 614 Fern Street.
        
        Wait a second. Let\'s see. So this is a twin. Let\'s see if I can demonstrate what I\'m talking about here, where it might not populate at all.
        
        Sometimes that happens. Could just be taking a second because we are getting info on it. We\'re seeing the year built. Looks like it\'s still not populating owner information, and our AVM and ARV is for some reason not populating.
        
        It could be because the property type wasn\'t delineated in the public record. But if you bounce over to this MLS history tab, it should show that there\'s a current active listing for this property as well. I should be able to click it and open that record right here.
        
        For some reason, doing a demo and it\'s not cooperating. So let\'s just go to the next one. We\'ll see if we can replicate this again.
        
        Alright, so this is an Interior Row Townhouse. Let\'s look up the public record of this one. 321 Welsh Circle. This one did work. So let\'s see. The point here is that sometimes, when you have an interior row, a twin, or an end of row, you go into the record and it\'ll display AVM and ARV based on the property being detached.
        
        In that case, it\'s more than likely that your AVM and ARV are going to be very high because you\'re comping based off of the house being a detached single-family and it\'s not.
        
        So my word of caution there is for any property that\'s not single-family detached, just make sure that you look at this field right here and ensure that the property type is correct.
        
        If we bounce over to this MLS history tab, we can see the current available listing. I could click this and go into it from here.
        
        Last thing that I\'ll highlight is that you do have the ability to share any public record. If you click this little button right here, you can copy the URL. This just copies it to your clipboard so you can paste it in an email, text message, whatever. You can also share it directly to email, Facebook, X, LinkedIn, whatever.
        
        Lastly, you do have property AI built into here as well. So anything that you could want to know or ask about that property, you have the ability to do with the AI that\'s built in.
        
        That\'s how you can look up the public record for any property, whether it\'s listed or not. You can run calculations, figure out your MAO, determine what you can offer on a property, and share it with whoever you need to—your partner, your investor, your client, whatever.
        
        Any questions on this? Just hit up the team.',
                'uid_code' => 'DD9',
                'url' => 'https://youtu.be/WHhkC0Km86M',
                'youtube_video_id' => 'WHhkC0Km86M',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'Comping Properties Made Easy! 🏡',
                'summary' => '🏡 Multiple Ways to Comp a Property
        Highlight: Understanding different methods of property comparison.
        Details: The video covers multiple approaches to determining property value using software, including auto-generated comps, manual radius-based searches, and active listing analysis.
        🔍 Quick Snapshot Comps
        Highlight: Utilizing the auto-comparable properties feature.
        Details: The system automatically selects comps based on property type, sales within six months, and square footage range, providing a quick estimate of property value.
        📍 Manual Comping with Radius Searches
        Highlight: Fine-tuning comps by location.
        Details: Users can manually define a radius around a property to locate comparable sales, refine search criteria, and analyze market activity.
        📊 Using a Map View for Comps
        Highlight: Visualizing market activity.
        Details: The map feature allows users to pinpoint recent sales, pending deals, and active listings for better market understanding.
        📈 Active & Pending Listings for Market Trends
        Highlight: Identifying supply and demand.
        Details: Reviewing active and pending listings helps assess current market conditions, influencing pricing and investment decisions.
        📂 Organizing Comps into Lists
        Highlight: Keeping comps structured for future reference.
        Details: Users can save selected comps into organized lists, allowing easy access when reevaluating property value in the future.
        📈 Insights Based on Numbers:
        The system defaults to six-month closed sales, within a mile radius, and ±20% square footage for accuracy.
        Active and pending listings provide real-time market trends, complementing closed sales data.
        Creating a comp list ensures organized valuation tracking over time.
        ',
                'summary_html' => '<h3>🏡 Multiple Ways to Comp a Property</h3><ul><li><strong>Highlight:</strong> Understanding different methods of property comparison.</li><li><strong>Details:</strong> The video covers multiple approaches to determining property value using software, including auto-generated comps, manual radius-based searches, and active listing analysis.</li></ul><p><br></p><h3>🔍 Quick Snapshot Comps</h3><ul><li><strong>Highlight:</strong> Utilizing the auto-comparable properties feature.</li><li><strong>Details:</strong> The system automatically selects comps based on property type, sales within six months, and square footage range, providing a quick estimate of property value.</li></ul><p><br></p><h3>📍 Manual Comping with Radius Searches</h3><ul><li><strong>Highlight:</strong> Fine-tuning comps by location.</li><li><strong>Details:</strong> Users can manually define a radius around a property to locate comparable sales, refine search criteria, and analyze market activity.</li></ul><p><br></p><h3>📊 Using a Map View for Comps</h3><ul><li><strong>Highlight:</strong> Visualizing market activity.</li><li><strong>Details:</strong> The map feature allows users to pinpoint recent sales, pending deals, and active listings for better market understanding.</li></ul><p><br></p><h3>📈 Active &amp; Pending Listings for Market Trends</h3><ul><li><strong>Highlight:</strong> Identifying supply and demand.</li><li><strong>Details:</strong> Reviewing active and pending listings helps assess current market conditions, influencing pricing and investment decisions.</li></ul><p><br></p><h3>📂 Organizing Comps into Lists</h3><ul><li><strong>Highlight:</strong> Keeping comps structured for future reference.</li><li><strong>Details:</strong> Users can save selected comps into organized lists, allowing easy access when reevaluating property value in the future.</li></ul><h3>📈 Insights Based on Numbers:</h3><ul><li>The system defaults to six-month closed sales, within a mile radius, and ±20% square footage for accuracy.</li><li>Active and pending listings provide real-time market trends, complementing closed sales data.</li><li>Creating a comp list ensures organized valuation tracking over time.</li></ul><p><br></p>',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => 'Welcome back. In this training video, we\'re going to dive into how to comp a property in multiple different ways using the software and when these different methods might be applicable for you.
        
        So, we\'re going to dive right into it here. I\'m going to use this property as an example just because I don\'t want to use this one that has no pictures. So, if we went into the listing just like we would if we were looking at a property, the first and pretty much self-explanatory way to comp a property is by scrolling down past the auto valuation section. Your AVM/ARV is displayed, and below that, there’s a section where it automatically links comparable properties.
        
        This is good for a quick snapshot of what’s going on. By default, the system only gives us the same property type, closed sales within six months, and properties within ±20% of the subject property’s square footage. If we want to, we can zero in a little bit closer on the radius around the subject property and look at comps like that. This is great for getting a quick estimate, especially if an AVM or ARV doesn’t seem accurate.
        
        Now, I’m going to show you the second method of how to comp with more depth. First, I’ll copy the address—just clicking on it automatically copies it to the clipboard. Then, I’ll go back to the search filters and input the address as our center point. Let’s say we want to search for comps within a one-mile radius.
        
        To start, I’ll walk you through how I do this internally. We’re going to look at closed and pending sales. If you notice when I select closed sales, the days-on-market filter highlights itself. That’s because the system automatically sets the earliest close date to six months before today. If you don’t want that, you can adjust it, but it’s useful for keeping relevant data.
        
        Now, we have a good start—closed and pending sales within a one-mile radius that closed in the last six months. When running this, the software provides access to everything available on the MLS (in this case, Bright MLS), as well as enriched data on historical listings.
        
        Zooming in on the map, we can tighten the search radius if we want. If I see a lot of single-family homes, I can filter by structure type to match the subject property. However, for this example, we’ll keep it open. Personally, I prefer using the map view when comping properties. Even in the mobile version, you can switch to map view from the toolbar if you prefer a visual layout.
        
        The red marker represents our subject property. Around it, I see various prices—$85,000, $175,000, $123,000, and $160,000. I can click on these properties to see photos and evaluate conditions, helping me determine the ARV. The purple markers indicate pending sales, like a $150,000 pending property with nice features.
        
        Another step I take is layering in active listings or active under contract properties. This gives insight into current market supply and demand. For example, I might see an active listing at $179,000 behind our subject property, which I can analyze.
        
        From the map view, I can also click on any property to access its public record. It takes a second to load, but it provides AVM, ARV, price history, and other valuable insights. This helps get a complete picture of a property’s value, potential worth, and possible exit strategy.
        
        For a more structured approach, I like to refine my search radius. Let’s adjust it to a half-mile. Now, we have 40 results. To organize the best comps, I go through the listings, selecting the ones that most closely match my property. Sorting by price from high to low, I add my preferred comps to a list. This keeps everything structured and easily accessible.
        
        If I’m buying a property, I might reference this list three months later to see what comps I originally used for valuation. To access it, I simply go to my lists and find the records I saved.
        
        This approach provides a complete way to comp properties using the software. There are many more features that enhance the process, but we’ll cover those in future videos.
        
        ',
                'uid_code' => 'DD9.1',
                'url' => 'https://youtu.be/oCswRaC5a5M',
                'youtube_video_id' => 'oCswRaC5a5M',
            ],
            [
                'category' => 'Data Definitions',
                'embed_code' => null,
                'topic' => 'nan',
                'summary' => 'nan',
                'summary_html' => '<h3>📝 Step 1: Create Your Account</h3>
        <ul>
          <li><strong>Highlight:</strong> You’ll need an account to access all features.</li>
          <li><strong>Details:</strong> If you haven’t already, sign up or log in to your Revamp365.ai account to get started.</li>
        </ul>
        <p><br></p>
        
        <h3>✅ Step 2: Watch & Mark Complete</h3>
        <ul>
          <li><strong>Highlight:</strong> Finish the training video.</li>
          <li><strong>Details:</strong> Once the video ends, click the checkmark to mark this step complete and unlock the next training.</li>
        </ul>
        <p><br></p>
        
        <h3>➡️ Step 3: Continue the Journey</h3>
        <ul>
          <li><strong>Highlight:</strong> Follow the path we laid out for success.</li>
          <li><strong>Details:</strong> After completing this video, move on to the next step in the training series to keep building your deal-finding machine.</li>
        </ul>
        ',
                'thumbnail_code' => null,
                'training_post_title' => 'Where to Get Started',
                'transcript' => 'nan',
                'uid_code' => 'DD1.1',
                'url' => 'https://youtu.be/nTG-5FChocc',
                'youtube_video_id' => 'nTG-5FChocc',
            ],
        ];

        foreach ($videos as $video) {
            $postId = $postTitleToId[$video['training_post_title']] ?? null;
            if (!$postId)
                continue;
            DB::table('training_videos')->insert([
                'training_post_id' => $postId,
                'category' => $video['category'] ?? null,
                'topic' => $video['topic'] ?? null,
                'embed_code' => $video['embed_code'] ?? null,
                'summary' => $video['summary'] ?? null,
                'summary_html' => $video['summary_html'] ?? null,
                'url' => $video['url'] ?? null,
                'youtube_video_id' => $video['youtube_video_id'] ?? null,
                'transcript' => $video['transcript'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}