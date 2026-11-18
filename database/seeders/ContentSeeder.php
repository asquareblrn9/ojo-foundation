<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Post;
use App\Models\Programme;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Database\Seeder;

/**
 * Seeds the foundation's website content (pages, sections, programmes,
 * founder, and the introductory letter as the first article).
 *
 * Idempotent: existing records are never overwritten, so content edited
 * in the admin dashboard is preserved if the seeder is run again.
 * Placeholder photographs (database/seeders/images) are Unsplash-licensed
 * stock images to be replaced with the foundation's own photos.
 */
class ContentSeeder extends Seeder
{
    private ImageService $images;

    public function run(ImageService $images): void
    {
        $this->images = $images;

        $this->home();
        $this->about();
        $this->programmesPage();
        $this->getInvolved();
        $this->contact();
        $this->legal();
        $this->programmes();
        $this->team();
        $this->introductionPost();
    }

    private function home(): void
    {
        $page = $this->page('home', 'Home', seoTitle: 'Fola Ojo Foundation — A Helping Hand');

        $this->section($page, 'hero', 'hero', 1,
            title: 'No child should be too poor to learn.',
            subtitle: 'Fola Ojo Foundation is a faith-based nonprofit raising a generation of educated, skilled and self-reliant young people.',
            image: 'hero-children.jpg',
            content: [
                'eyebrow' => 'Faith-based nonprofit · Milwaukee, WI',
                'primary_label' => 'Sponsor a child',
                'primary_url' => '/get-involved',
                'secondary_label' => 'Our story',
                'secondary_url' => '/about',
            ],
        );

        $this->section($page, 'intro', 'text', 2,
            title: 'What we did informally for 20 years, we now do formally — to reach more lives.',
            subtitle: 'Who we are',
            image: 'helping-hand.jpg',
            content: [
                'body' => "Fola Ojo Foundation Inc is a faith-based nonprofit initiative committed to raising a generation of educated, skilled, and self-reliant young people.\n\nFor about 20 years, we have been informally training, mentoring, and supporting young people — paying school fees, teaching skills, counselling them, and helping them find their path in life. We have seen firsthand how a little help can transform a struggling youth into a responsible, self-reliant adult.",
                'highlight_value' => '20',
                'highlight_label' => 'years of mentoring and supporting young people',
                'cta_label' => 'Read our story',
                'cta_url' => '/about',
            ],
        );

        $this->section($page, 'mission_vision', 'mission_vision', 3,
            title: 'Our mission & vision',
            image: 'children-group.jpg',
            content: [
                'vision_title' => 'No child should be too poor to learn.',
                'vision_text' => 'Every child deserves a chance to dream. Every dream deserves an opportunity to flourish. And no child\'s future should be buried beneath the crushing weight of poverty.',
                'mission_title' => 'Breaking the cycle of generational poverty.',
                'mission_text' => 'Our mission is to break the vicious cycle of generational poverty by providing educational opportunities, scholarships, mentorship, and essential learning resources to children from economically disadvantaged families.',
            ],
        );

        $this->section($page, 'focus', 'features', 4,
            title: 'Our focus',
            subtitle: 'We believe that when you empower one youth, you empower an entire family and community.',
            content: [
                'eyebrow' => 'Areas of intervention',
                'items' => [
                    ['icon' => 'graduation-cap', 'title' => 'Education', 'text' => 'Helping brilliant but less privileged youths achieve education and stay in school.'],
                    ['icon' => 'compass', 'title' => 'Training', 'text' => 'Mentorship, life skills, and career guidance.'],
                    ['icon' => 'wrench', 'title' => 'Skills Acquisition', 'text' => 'Equipping youths with practical, income-generating skills — computer literacy, tailoring, barbering, catering, plumbing, electrical, and more.'],
                ],
            ],
        );

        $this->section($page, 'programmes', 'collection', 5,
            title: 'What we do',
            subtitle: 'Through the generosity of individuals, organisations, corporate partners and compassionate supporters, we seek to provide:',
            content: ['eyebrow' => 'Our programmes'],
        );

        $this->section($page, 'stats', 'stats', 6,
            title: 'Our commitment',
            content: [
                'items' => [
                    ['value' => '20+', 'label' => 'Years mentoring and supporting young people'],
                    ['value' => '3', 'label' => 'Focus areas: education, training and skills'],
                    ['value' => '4', 'label' => 'Ways we support children and families'],
                ],
            ],
        );

        $this->section($page, 'quote', 'quote', 7,
            content: [
                'quote' => 'Talent is universal, but opportunity is not.',
                'attribution' => 'What we believe',
            ],
        );

        $this->section($page, 'news', 'collection', 8,
            title: 'News, events & stories',
            subtitle: 'Updates on our work, activities and the young people we serve.',
            content: ['eyebrow' => 'Latest updates'],
        );

        $this->section($page, 'events', 'collection', 9,
            title: 'Recent activities & events',
            content: ['eyebrow' => 'In the community'],
        );

        $this->section($page, 'gallery', 'collection', 10,
            title: 'Moments that matter',
            subtitle: 'Photographs from our outreaches, classrooms and community activities.',
            content: ['eyebrow' => 'Gallery'],
        );

        $this->section($page, 'involve', 'cta', 11,
            title: 'Join us in changing lives',
            subtitle: 'Let us give children more than sympathy. Let us give them opportunity. Let us give them more than hope. Let us give them an education.',
            image: 'school-walk.jpg',
            content: [
                'eyebrow' => 'Get involved',
                'items' => [
                    ['icon' => 'heart-handshake', 'title' => 'Sponsor a child', 'text' => 'Help a brilliant child enrol in school, stay in school and complete their education.', 'url' => '/get-involved#sponsor'],
                    ['icon' => 'hand-coins', 'title' => 'Make a donation', 'text' => 'Every gift helps provide scholarships, textbooks, uniforms and learning materials.', 'url' => '/get-involved#donate'],
                    ['icon' => 'handshake', 'title' => 'Become a partner', 'text' => 'Churches, leaders and organisations of goodwill — partner with us to reach more lives.', 'url' => '/get-involved#partner'],
                ],
            ],
        );
    }

    private function about(): void
    {
        $page = $this->page('about', 'About Us', seoDescription: 'Learn about Fola Ojo Foundation — A Helping Hand: our story, founder\'s message, mission, vision and what we believe.');

        $this->section($page, 'hero', 'hero', 1,
            title: 'A helping hand for the next generation',
            subtitle: 'A faith-based nonprofit committed to raising a generation of educated, skilled, and self-reliant young people.',
            image: 'helping-hand.jpg',
            content: ['eyebrow' => 'About us'],
        );

        $this->section($page, 'overview', 'text', 2,
            title: 'Who we are',
            image: 'mentorship.jpg',
            content: [
                'eyebrow' => 'Foundation overview',
                'body' => "Fola Ojo Foundation Inc is a faith-based nonprofit initiative committed to raising a generation of educated, skilled, and self-reliant young people.\n\nBased in Milwaukee, Wisconsin, USA, the foundation was established by Pastor Fola Ojo to formalise and expand two decades of work supporting young people.",
            ],
        );

        $this->section($page, 'story', 'text', 3,
            title: 'A journey of 20 years',
            image: 'classroom.jpg',
            content: [
                'eyebrow' => 'Our story',
                'body' => "For about 20 years now, we have been informally training, mentoring, and supporting young people — paying school fees from our own pocket, teaching skills, counselling them, and helping them find their path in life. We have seen firsthand how a little help can transform a struggling youth into a responsible, self-reliant adult.\n\nWhat we did informally for 20 years with limited resources, we now want to do formally, properly, and on a larger scale — so we can reach more lives. That is why we birthed Fola Ojo Foundation Inc, a nonprofit organisation.",
            ],
        );

        $this->section($page, 'founder', 'founder', 4,
            title: 'A message from our founder',
            content: [
                'eyebrow' => 'Founder\'s message',
                'salutation' => 'Dear Esteemed Friend,',
                'body' => "Let me introduce you to a vision that has been burning in our heart for years. I am Pastor Fola Ojo, Founder of Fola Ojo Foundation (FOF) – A Helping Hand, based in Milwaukee, Wisconsin, USA.\n\nWe are currently introducing the Foundation to friends, families, churches, leaders, and organisations of goodwill like you who believe in the future of the next generation. In the coming days, we will be sharing how you can partner with us as we launch our first official empowerment project.\n\nFor now, we simply ask for your prayers, your goodwill, and the opportunity to keep you informed about our work.\n\nThank you for taking the time to know about us.",
                'signoff' => 'With sincere regards and God\'s blessings,',
                'name' => 'Pastor Fola Ojo',
                'role' => 'Founder & President, Fola Ojo Foundation (FOF) – A Helping Hand',
            ],
        );

        $this->section($page, 'vision', 'text', 5,
            title: 'No child should be too poor to learn.',
            image: 'children-group.jpg',
            content: [
                'eyebrow' => 'Our vision',
                'body' => "Every child deserves a chance to dream. Every dream deserves an opportunity to flourish. And no child's future should be buried beneath the crushing weight of poverty.\n\nAcross our communities, countless brilliant, talented, and promising children are denied access to quality education, not because they lack intelligence, ambition, or determination, but simply because their parents lack the financial resources to send them to school.\n\nAt the Fola Ojo Foundation, we believe that poverty should never be a life sentence, and education should never be a privilege reserved for children born into wealthy families.\n\nWe believe that within every disadvantaged child lies an untapped reservoir of greatness. Among these children are tomorrow's doctors, engineers, teachers, scientists, entrepreneurs, inventors, and transformational leaders. But without education, their dreams may never see the light of day.\n\nOur foundation exists to change that narrative.",
            ],
        );

        $this->section($page, 'mission', 'text', 6,
            title: 'Breaking the cycle of generational poverty',
            content: [
                'eyebrow' => 'Our mission',
                'body' => 'Our mission is to break the vicious cycle of generational poverty by providing educational opportunities, scholarships, mentorship, and essential learning resources to children from economically disadvantaged families.',
            ],
        );

        $this->section($page, 'objectives', 'list', 7,
            title: 'Our objectives',
            content: [
                'eyebrow' => 'What we are committed to',
                'items' => [
                    ['title' => 'Identify promising children', 'text' => 'We are committed to identifying promising children whose educational aspirations are threatened by poverty and helping them pursue their dreams.'],
                    ['title' => 'Remove financial barriers', 'text' => 'We seek to remove the financial barriers that stand between vulnerable children and the classrooms where their futures can be shaped.'],
                    ['title' => 'Raise self-reliant young people', 'text' => 'We equip youths with education, mentorship, life skills and practical, income-generating skills so they can become responsible, self-reliant adults.'],
                ],
            ],
        );

        $this->section($page, 'values', 'list', 8,
            title: 'What we believe',
            content: [
                'eyebrow' => 'Core values',
                'items' => [
                    ['icon' => 'scale', 'title' => 'Education is a right', 'text' => 'Education is a right, not a reward for being born into privilege.'],
                    ['icon' => 'wallet', 'title' => 'Destiny is not for sale', 'text' => 'No child\'s destiny should be determined by the size of their parents\' bank account.'],
                    ['icon' => 'sparkles', 'title' => 'Talent is universal', 'text' => 'Talent is universal, but opportunity is not.'],
                    ['icon' => 'sunrise', 'title' => 'Hope for families', 'text' => 'A child educated today can become the answer to a family\'s poverty tomorrow.'],
                    ['icon' => 'users', 'title' => 'One child, many lives', 'text' => 'When we educate one child, we empower a family, strengthen a community, and invest in the future of an entire nation.'],
                ],
            ],
        );

        $this->section($page, 'approach', 'list', 9,
            title: 'Our approach',
            subtitle: 'Through the generosity of individuals, organisations, corporate partners, and compassionate supporters, the Fola Ojo Foundation seeks to provide:',
            content: [
                'eyebrow' => 'What we do',
                'items' => [
                    ['icon' => 'graduation-cap', 'title' => 'Educational scholarships', 'text' => 'Financial assistance to help disadvantaged children enrol in school, remain in school, and complete their education.'],
                    ['icon' => 'book-open', 'title' => 'School supplies & learning materials', 'text' => 'Essential textbooks, uniforms, stationery, and other resources needed for meaningful learning.'],
                    ['icon' => 'compass', 'title' => 'Mentorship & personal development', 'text' => 'Guidance, encouragement, and opportunities that help children develop confidence, character, leadership skills, and a sense of purpose.'],
                    ['icon' => 'house', 'title' => 'Support for vulnerable families', 'text' => 'Working with struggling families to help ensure that financial hardship does not force their children to abandon their education.'],
                ],
            ],
        );

        $this->section($page, 'team', 'collection', 10,
            title: 'Our leadership',
            content: ['eyebrow' => 'Leadership'],
        );
    }

    private function programmesPage(): void
    {
        $page = $this->page('programmes', 'Our Programmes', seoDescription: 'Scholarships, school supplies, mentorship, skills acquisition and family support — the programmes of Fola Ojo Foundation.');

        $this->section($page, 'hero', 'hero', 1,
            title: 'Opening classroom doors, building futures',
            subtitle: 'We cannot educate every disadvantaged child overnight. But together, we can ensure that more children are given the opportunity to learn, grow, and succeed.',
            image: 'classroom.jpg',
            content: ['eyebrow' => 'Our programmes'],
        );
    }

    private function getInvolved(): void
    {
        $page = $this->page('get-involved', 'Get Involved', seoDescription: 'Sponsor a child, make a donation, volunteer or become a partner of Fola Ojo Foundation.');

        $this->section($page, 'hero', 'hero', 1,
            title: 'Sponsor a child. Make a donation. Become a partner.',
            subtitle: 'Poverty may describe a child\'s present circumstances, but it must never be allowed to dictate that child\'s destiny. Your generosity can help rewrite that story.',
            image: 'volunteer.jpg',
            content: ['eyebrow' => 'Get involved'],
        );

        $this->section($page, 'why', 'text', 2,
            title: 'Why your support matters',
            image: 'child-writing.jpg',
            content: [
                'eyebrow' => 'The need',
                'body' => "Somewhere today, a brilliant child is sitting at home while other children are in school. Not because that child is unwilling to learn, but because the family cannot afford the cost of education.\n\nSomewhere, a future physician may never enter a classroom. A potential engineer may never discover the wonders of science. A future national leader may never learn to read.\n\nA scholarship can open a classroom door. A textbook can awaken curiosity. A school uniform can restore dignity. And a single act of compassion can change the trajectory of a child's life forever.",
            ],
        );

        $this->section($page, 'ways', 'list', 3,
            title: 'Ways to help',
            subtitle: 'Whether you sponsor a child\'s education, contribute educational materials, volunteer your expertise, or make a financial donation, your involvement can help create opportunities that extend far beyond the classroom.',
            content: [
                'eyebrow' => 'Join us in changing lives',
                'items' => [
                    ['icon' => 'heart-handshake', 'title' => 'Sponsor a child', 'text' => 'Cover a child\'s school fees so they can enrol, stay in school and complete their education.', 'anchor' => 'sponsor'],
                    ['icon' => 'hand-coins', 'title' => 'Make a donation', 'text' => 'Fund scholarships, textbooks, uniforms and the learning materials children need.', 'anchor' => 'donate'],
                    ['icon' => 'book-open', 'title' => 'Contribute materials', 'text' => 'Donate textbooks, stationery, uniforms and other educational resources.', 'anchor' => 'partner'],
                    ['icon' => 'hand-heart', 'title' => 'Volunteer your expertise', 'text' => 'Mentor, teach a skill, or lend your professional experience to young people.', 'anchor' => 'volunteer'],
                    ['icon' => 'handshake', 'title' => 'Become a partner', 'text' => 'Churches, leaders and organisations of goodwill can partner with us to reach more lives.', 'anchor' => 'partner'],
                    ['icon' => 'heart', 'title' => 'Pray with us', 'text' => 'For now, we simply ask for your prayers, your goodwill, and the opportunity to keep you informed.', 'anchor' => 'newsletter'],
                ],
            ],
        );

        $this->section($page, 'sponsor', 'text', 4,
            title: 'Sponsor a child',
            content: [
                'eyebrow' => 'Child sponsorship',
                'body' => "A scholarship can open a classroom door. Sponsoring a child helps a brilliant but less privileged young person enrol in school, remain in school and complete their education.\n\nWe will soon be sharing how you can partner with us as we launch our first official empowerment project. Register your interest below and we will be in touch.",
            ],
        );

        $this->section($page, 'volunteer', 'form', 5,
            title: 'Become a volunteer',
            subtitle: 'Share your time, skills and experience with young people who need a helping hand.',
            content: [
                'eyebrow' => 'Volunteer',
                'items' => [
                    ['title' => 'Mentoring', 'text' => 'Guide young people with encouragement, life skills and career advice.'],
                    ['title' => 'Teaching & tutoring', 'text' => 'Support learners with their studies and computer literacy.'],
                    ['title' => 'Skills training', 'text' => 'Teach a trade such as tailoring, barbering, catering, plumbing or electrical work.'],
                    ['title' => 'Outreach & events', 'text' => 'Help organise outreaches, events and fundraising activities.'],
                ],
            ],
        );

        $this->section($page, 'partner', 'form', 6,
            title: 'Partner with us',
            subtitle: 'We are introducing the Foundation to friends, families, churches, leaders and organisations of goodwill who believe in the future of the next generation.',
            content: ['eyebrow' => 'Partnerships'],
        );
    }

    private function contact(): void
    {
        $page = $this->page('contact', 'Contact Us', seoDescription: 'Get in touch with Fola Ojo Foundation — A Helping Hand, Milwaukee, Wisconsin, USA.');

        $this->section($page, 'hero', 'hero', 1,
            title: 'We would love to hear from you',
            subtitle: 'Whether you would like to partner with us, sponsor a child, volunteer, or simply learn more about our work, please get in touch.',
            image: 'families.jpg',
            content: ['eyebrow' => 'Contact us'],
        );

        $this->section($page, 'form', 'form', 2,
            title: 'Send us a message',
            subtitle: 'Fill in the form and a member of our team will respond as soon as possible.',
        );
    }

    private function legal(): void
    {
        $site = 'Fola Ojo Foundation Inc';

        Page::firstOrCreate(['slug' => 'privacy-policy'], [
            'title' => 'Privacy Policy',
            'is_system' => true,
            'is_published' => true,
            'seo_description' => "How {$site} collects, uses and protects your personal information.",
            'body' => <<<HTML
<p><em>This policy is a starting template. Please review it with a qualified adviser and update it in Admin → Pages before launch.</em></p>
<h2>Who we are</h2>
<p>{$site} ("the Foundation", "we", "us") is a nonprofit organisation based in Milwaukee, Wisconsin, USA. This policy explains how we handle personal information collected through this website.</p>
<h2>Information we collect</h2>
<ul>
<li><strong>Contact enquiries:</strong> your name, email address, phone number (optional), subject and message.</li>
<li><strong>Volunteer applications:</strong> your name, contact details, location, areas of interest, skills, availability and motivation.</li>
<li><strong>Partnership enquiries:</strong> your organisation, contact details, website and message.</li>
<li><strong>Newsletter:</strong> your email address and, if provided, your name.</li>
<li><strong>Technical data:</strong> your IP address and browser information, used to protect our forms from abuse.</li>
</ul>
<h2>How we use your information</h2>
<p>We use your information only to respond to your enquiry, process your volunteer application or partnership enquiry, send you updates you have asked for, and keep this website secure. We do not sell or rent your personal information.</p>
<h2>Newsletter</h2>
<p>You can unsubscribe at any time using the link in any email we send.</p>
<h2>Cookies</h2>
<p>This website uses essential cookies to keep forms secure and to operate the site. If analytics are enabled, we use them to understand how the site is used.</p>
<h2>Data retention and security</h2>
<p>We keep personal information only as long as necessary for the purposes above and protect it with appropriate technical and organisational measures.</p>
<h2>Your rights</h2>
<p>You may ask us to access, correct or delete the personal information we hold about you by contacting us through our Contact page.</p>
<h2>Changes to this policy</h2>
<p>We may update this policy from time to time. The latest version will always be available on this page.</p>
HTML,
        ]);

        Page::firstOrCreate(['slug' => 'terms'], [
            'title' => 'Terms & Conditions',
            'is_system' => true,
            'is_published' => true,
            'seo_description' => "The terms that apply to your use of the {$site} website.",
            'body' => <<<HTML
<p><em>These terms are a starting template. Please review them with a qualified adviser and update them in Admin → Pages before launch.</em></p>
<h2>Use of this website</h2>
<p>By using this website you agree to these terms. The content on this website is provided for general information about the work of {$site}.</p>
<h2>Intellectual property</h2>
<p>Unless otherwise stated, the content, logos and images on this website belong to the Foundation or are used with permission. You may share our content for non-commercial purposes with appropriate credit.</p>
<h2>Donations and sponsorship</h2>
<p>Information about donations, sponsorship and partnership will be provided when these programmes open. Any specific terms will be communicated at that time.</p>
<h2>Submissions</h2>
<p>When you submit a form you confirm that the information you provide is accurate and that you are entitled to share it. You must not submit unlawful, offensive or misleading content.</p>
<h2>External links</h2>
<p>We are not responsible for the content of external websites linked from this site.</p>
<h2>Limitation of liability</h2>
<p>We make every effort to keep this website accurate and available, but we do not guarantee that it will be error-free or uninterrupted.</p>
<h2>Changes</h2>
<p>We may update these terms from time to time. Continued use of the website means you accept the updated terms.</p>
HTML,
        ]);
    }

    private function programmes(): void
    {
        $programmes = [
            [
                'title' => 'Educational Scholarships',
                'icon' => 'graduation-cap',
                'image' => 'classroom.jpg',
                'short_description' => 'Financial assistance to help disadvantaged children enrol in school, remain in school, and complete their education.',
                'description' => '<p>Across our communities, countless brilliant, talented and promising children are denied access to quality education — not because they lack intelligence, ambition or determination, but simply because their parents lack the financial resources to send them to school.</p><p>Our scholarships remove the financial barriers that stand between vulnerable children and the classrooms where their futures can be shaped.</p>',
                'objectives' => [
                    'Identify promising children whose educational aspirations are threatened by poverty.',
                    'Help disadvantaged children enrol in school.',
                    'Help children remain in school and complete their education.',
                ],
                'activities' => [
                    'Providing financial assistance towards school fees.',
                    'Supporting brilliant but less privileged youths to achieve education and stay in school.',
                ],
            ],
            [
                'title' => 'School Supplies & Learning Materials',
                'icon' => 'book-open',
                'image' => 'school-supplies.jpg',
                'short_description' => 'Essential textbooks, uniforms, stationery, and other resources needed for meaningful learning.',
                'description' => '<p>A textbook can awaken curiosity. A school uniform can restore dignity. We provide the essential resources children need for meaningful learning, so that a lack of materials never stands in the way of a child\'s education.</p>',
                'objectives' => [
                    'Ensure children have the materials they need to learn.',
                    'Restore dignity and confidence through essentials such as school uniforms.',
                ],
                'activities' => [
                    'Providing textbooks and stationery.',
                    'Providing school uniforms.',
                    'Supplying other learning resources needed in the classroom.',
                ],
            ],
            [
                'title' => 'Mentorship & Personal Development',
                'icon' => 'compass',
                'image' => 'mentorship.jpg',
                'short_description' => 'Guidance, encouragement, and opportunities that help young people develop confidence, character, leadership skills, and a sense of purpose.',
                'description' => '<p>For about 20 years, we have been mentoring and counselling young people and helping them find their path in life. We have seen firsthand how a little help can transform a struggling youth into a responsible, self-reliant adult.</p><p>Our mentorship and training focus on life skills, career guidance and personal development.</p>',
                'objectives' => [
                    'Help young people develop confidence, character and leadership skills.',
                    'Help young people discover a sense of purpose and find their path in life.',
                ],
                'activities' => [
                    'Mentorship and counselling.',
                    'Life skills training.',
                    'Career guidance.',
                ],
            ],
            [
                'title' => 'Skills Acquisition',
                'icon' => 'wrench',
                'image' => 'skills-electrical.jpg',
                'short_description' => 'Equipping youths with practical, income-generating skills — computer literacy, tailoring, barbering, catering, plumbing, electrical, and more.',
                'description' => '<p>We believe that when you empower one youth, you empower an entire family and community. Our skills acquisition programme equips young people with practical, income-generating skills so they can become self-reliant.</p>',
                'objectives' => [
                    'Equip youths with practical, income-generating skills.',
                    'Raise self-reliant young people who can support themselves and their families.',
                ],
                'activities' => [
                    'Computer literacy.',
                    'Tailoring and barbering.',
                    'Catering.',
                    'Plumbing and electrical work.',
                ],
            ],
            [
                'title' => 'Support for Vulnerable Families',
                'icon' => 'house',
                'image' => 'families.jpg',
                'short_description' => 'Working with struggling families to help ensure that financial hardship does not force their children to abandon their education.',
                'description' => '<p>A child educated today can become the answer to a family\'s poverty tomorrow. We work with struggling families so that financial hardship does not force their children to abandon their education.</p>',
                'objectives' => [
                    'Keep children from vulnerable families in school.',
                    'Help break the cycle of generational poverty.',
                ],
                'activities' => [
                    'Working alongside struggling families to support their children\'s education.',
                ],
            ],
        ];

        foreach ($programmes as $i => $data) {
            if (Programme::withTrashed()->where('title', $data['title'])->exists()) {
                continue;
            }

            Programme::create([
                'title' => $data['title'],
                'icon' => $data['icon'],
                'featured_image' => $this->storeImage($data['image'], 'programmes'),
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'objectives' => $data['objectives'],
                'activities' => $data['activities'],
                'outcomes' => [],
                'is_published' => true,
                'is_featured' => true,
                'sort_order' => $i,
            ]);
        }
    }

    private function team(): void
    {
        TeamMember::firstOrCreate(['name' => 'Pastor Fola Ojo'], [
            'position' => 'Founder & President',
            'bio' => 'Pastor Fola Ojo founded Fola Ojo Foundation (FOF) – A Helping Hand to formalise and expand about 20 years of training, mentoring and supporting young people.',
            'sort_order' => 0,
            'is_active' => true,
        ]);
    }

    private function introductionPost(): void
    {
        $title = 'Introducing Fola Ojo Foundation (FOF) – A Helping Hand';

        if (Post::withTrashed()->where('title', $title)->exists()) {
            return;
        }

        $author = User::role(Role::SuperAdmin->value)->first();

        Post::create([
            'title' => $title,
            'category_id' => Category::where('name', 'News')->value('id'),
            'author_id' => $author?->id,
            'excerpt' => 'A letter from our founder, Pastor Fola Ojo, introducing the Foundation and its vision to raise a generation of educated, skilled and self-reliant young people.',
            'body' => <<<'HTML'
<p>Dear Esteemed Friend,</p>
<p>Let me introduce you to a vision that has been burning in our heart for years. I am Pastor Fola Ojo, Founder of Fola Ojo Foundation (FOF) – A Helping Hand, based in Milwaukee, Wisconsin, USA.</p>
<h2>A journey of 20 years</h2>
<p>For about 20 years now, we have been informally training, mentoring, and supporting young people — paying school fees from our own pocket, teaching skills, counselling them, and helping them find their path in life. We have seen firsthand how a little help can transform a struggling youth into a responsible, self-reliant adult.</p>
<p>What we did informally for 20 years with limited resources, we now want to do formally, properly, and on a larger scale — so we can reach more lives. That is why we birthed Fola Ojo Foundation Inc, a nonprofit organisation.</p>
<h2>Who we are</h2>
<p>Fola Ojo Foundation Inc is a faith-based nonprofit initiative committed to raising a generation of educated, skilled, and self-reliant young people.</p>
<h2>Our focus</h2>
<ol>
<li><strong>Education:</strong> Helping brilliant but less privileged youths achieve education and stay in school.</li>
<li><strong>Training:</strong> Mentorship, life skills, and career guidance.</li>
<li><strong>Skills acquisition:</strong> Equipping youths with practical, income-generating skills — computer literacy, tailoring, barbering, catering, plumbing, electrical, and more.</li>
</ol>
<blockquote><p>We believe that when you empower one youth, you empower an entire family and community.</p></blockquote>
<h2>Our invitation</h2>
<p>We are currently introducing the Foundation to friends, families, churches, leaders, and organisations of goodwill like you who believe in the future of the next generation. In the coming days, we will be sharing how you can partner with us as we launch our first official empowerment project.</p>
<p>For now, we simply ask for your prayers, your goodwill, and the opportunity to keep you informed about our work.</p>
<p>Thank you for taking the time to know about us.</p>
<p>With sincere regards and God’s blessings,</p>
<p><strong>Pastor Fola Ojo</strong><br>Founder &amp; President<br>Fola Ojo Foundation (FOF) – A Helping Hand<br>Milwaukee, WI, USA</p>
HTML,
            'featured_image' => $this->storeImage('helping-hand.jpg', 'posts'),
            'featured_image_alt' => 'Two hands reaching towards each other',
            'status' => PostStatus::Published,
            'is_featured' => true,
            'published_at' => now(),
            'seo_description' => 'Pastor Fola Ojo introduces Fola Ojo Foundation (FOF) – A Helping Hand, a faith-based nonprofit raising educated, skilled and self-reliant young people.',
        ]);
    }

    /* ----------------------------------------------------------------- */

    private function page(string $slug, string $title, ?string $seoTitle = null, ?string $seoDescription = null): Page
    {
        return Page::firstOrCreate(['slug' => $slug], [
            'title' => $title,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'is_published' => true,
            'is_system' => true,
        ]);
    }

    private function section(Page $page, string $key, string $type, int $order, ?string $title = null, ?string $subtitle = null, ?string $image = null, array $content = []): void
    {
        if ($page->sections()->where('key', $key)->exists()) {
            return;
        }

        PageSection::create([
            'page_id' => $page->id,
            'key' => $key,
            'type' => $type,
            'title' => $title,
            'subtitle' => $subtitle,
            'content' => $content,
            'image' => $image ? $this->storeImage($image, 'pages') : null,
            'sort_order' => $order,
            'is_active' => true,
        ]);
    }

    /** @var array<string, string> Avoid re-processing the same placeholder image. */
    private array $stored = [];

    private function storeImage(string $file, string $directory): ?string
    {
        $source = database_path("seeders/images/{$file}");

        if (! is_file($source)) {
            return null;
        }

        return $this->stored["{$directory}/{$file}"] ??= $this->images->store($source, $directory)['path'];
    }
}
