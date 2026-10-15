<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure public/samples folder exists in storage
        $samplesDir = storage_path('app/public/samples');
        if (!File::exists($samplesDir)) {
            File::makeDirectory($samplesDir, 0755, true);
        }

        // 2. Create demo users
        $users = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+91 98260 12345',
                'city' => 'Gwalior',
            ],
            [
                'name' => 'Priya Verma',
                'email' => 'priya@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+91 94251 56789',
                'city' => 'Indore',
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+91 91110 98765',
                'city' => 'Bhopal',
            ],
            [
                'name' => 'Demo Seller',
                'email' => 'demo@localmarket.in',
                'password' => Hash::make('password123'),
                'phone' => '+91 98765 43210',
                'city' => 'Delhi',
            ],
        ];

        $createdUsers = [];
        foreach ($users as $u) {
            $createdUsers[] = User::updateOrCreate(
                ['email' => $u['email']],
                $u
            );
        }

        // 3. Define sample listings data
        $listingsData = [
            // Gwalior listings (to test city: gwalior, category: electronics-appliances, etc.)
            [
                'user_index' => 0,
                'category_slug' => 'electronics-appliances',
                'subcategory_name' => 'Laptops & Desktops',
                'type' => 'product',
                'title' => 'Apple MacBook Air M2 (8GB RAM, 256GB SSD) - Space Grey',
                'description' => "Selling my barely used Apple MacBook Air M2 in Space Grey. Purchased 8 months ago, 97% battery health with original 30W charger, braided MagSafe cable, and box. Used only for coding and study work. Zero scratches or dents, always kept in hard shell case. Bill available.",
                'price' => 74500,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Gwalior',
                'area' => 'City Center',
                'phone' => '+91 98260 12345',
                'status' => 'active',
                'is_featured' => true,
                'views_count' => 142,
                'image_config' => [
                    'label' => 'MacBook Air M2',
                    'sub' => 'Electronics / Space Grey',
                    'bg_start' => '#1e293b',
                    'bg_end' => '#0f172a',
                    'icon' => 'LAPTOP',
                    'files' => ['macbook_m2_1.svg', 'macbook_m2_2.svg'],
                ],
            ],
            [
                'user_index' => 0,
                'category_slug' => 'electronics-appliances',
                'subcategory_name' => 'TVs, Audio & Video',
                'type' => 'product',
                'title' => 'Sony Bravia 55 inch 4K Ultra HD Smart LED Google TV',
                'description' => "Sony Bravia 55 inch 4K LED Google TV in excellent working condition. Supports Dolby Atmos, 4K HDR, Netflix, Prime, YouTube. Wall mount bracket included. Selling because upgrading to OLED.",
                'price' => 38000,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Gwalior',
                'area' => 'Thatipur',
                'phone' => '+91 98260 12345',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 89,
                'image_config' => [
                    'label' => 'Sony Bravia 55" 4K',
                    'sub' => 'Smart Google TV',
                    'bg_start' => '#0f172a',
                    'bg_end' => '#1e1b4b',
                    'icon' => 'TELEVISION',
                    'files' => ['sony_tv_1.svg'],
                ],
            ],
            [
                'user_index' => 0,
                'category_slug' => 'mobiles-tablets',
                'subcategory_name' => 'Smartphones',
                'type' => 'product',
                'title' => 'iPhone 14 Pro 128GB Deep Purple (With Bill & Box)',
                'description' => "iPhone 14 Pro 128 GB Deep Purple in mint physical condition. Battery health 88%. Comes with genuine box, cable, and invoice. AppleCare expired last month. Screen protector applied from day 1.",
                'price' => 62000,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Gwalior',
                'area' => 'Lashkar',
                'phone' => '+91 98260 12345',
                'status' => 'active',
                'is_featured' => true,
                'views_count' => 275,
                'image_config' => [
                    'label' => 'iPhone 14 Pro',
                    'sub' => '128GB Deep Purple',
                    'bg_start' => '#3b0764',
                    'bg_end' => '#1e1b4b',
                    'icon' => 'IPHONE',
                    'files' => ['iphone14_1.svg', 'iphone14_2.svg'],
                ],
            ],
            [
                'user_index' => 1,
                'category_slug' => 'vehicles',
                'subcategory_name' => 'Cars',
                'type' => 'product',
                'title' => 'Maruti Suzuki Swift VXi 2021 (Petrol) - 1st Owner',
                'description' => "Maruti Suzuki Swift VXi Petrol, single hand driven, 28,000 KM with complete authorized service record. Valid comprehensive insurance until Nov 2026. Chilled AC, pioneer touchscreen system, clean interior. Non-accidental guaranteed.",
                'price' => 525000,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Indore',
                'area' => 'Vijay Nagar',
                'phone' => '+91 94251 56789',
                'status' => 'active',
                'is_featured' => true,
                'views_count' => 310,
                'image_config' => [
                    'label' => 'Swift VXi 2021',
                    'sub' => '28,000 km / 1st Owner',
                    'bg_start' => '#0369a1',
                    'bg_end' => '#0f172a',
                    'icon' => 'CAR',
                    'files' => ['swift_1.svg', 'swift_2.svg'],
                ],
            ],
            [
                'user_index' => 1,
                'category_slug' => 'vehicles',
                'subcategory_name' => 'Motorcycles',
                'type' => 'product',
                'title' => 'Royal Enfield Classic 350 Reborn (Dark Stealth Black)',
                'description' => "Royal Enfield Classic 350 Reborn dual-channel ABS in Stealth Black alloy wheels edition. 11,500 kms driven, pristine condition, fitted with leg guard, sump guard, and touring seat. Price slightly negotiable.",
                'price' => 165000,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Indore',
                'area' => 'Palasia',
                'phone' => '+91 94251 56789',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 195,
                'image_config' => [
                    'label' => 'Royal Enfield 350',
                    'sub' => 'Classic Reborn Dual ABS',
                    'bg_start' => '#18181b',
                    'bg_end' => '#27272a',
                    'icon' => 'BIKE',
                    'files' => ['bullet_1.svg'],
                ],
            ],
            [
                'user_index' => 2,
                'category_slug' => 'real-estate',
                'subcategory_name' => 'Apartments & Flats',
                'type' => 'product',
                'title' => '3 BHK Luxury Flat in DB City Bhopal - 1650 sq ft',
                'description' => "Spacious 3 BHK apartment with modular kitchen, Italian tile flooring, wooden wardrobes in all bedrooms, covered car parking, 24/7 power backup, swimming pool, and gym in society. Ready to move immediately.",
                'price' => 6800000,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Bhopal',
                'area' => 'Arera Colony',
                'phone' => '+91 91110 98765',
                'status' => 'active',
                'is_featured' => true,
                'views_count' => 450,
                'image_config' => [
                    'label' => '3 BHK Luxury Apartment',
                    'sub' => '1650 Sq.Ft / Arera Colony',
                    'bg_start' => '#134e4a',
                    'bg_end' => '#042f2e',
                    'icon' => 'BUILDING',
                    'files' => ['flat_bhopal_1.svg'],
                ],
            ],
            [
                'user_index' => 0,
                'category_slug' => 'real-estate',
                'subcategory_name' => 'Apartments & Flats',
                'type' => 'product',
                'title' => '2 BHK Ready to Move Flat near Jiwaji University, Gwalior',
                'description' => "East facing 2 BHK ready to move flat located 2 minutes from Jiwaji University. Semi-furnished, lift, reserved car parking, boring water 24 hours. Bank loan approved from SBI and HDFC.",
                'price' => 2850000,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Gwalior',
                'area' => 'City Center',
                'phone' => '+91 98260 12345',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 180,
                'image_config' => [
                    'label' => '2 BHK Modern Flat',
                    'sub' => 'City Center, Gwalior',
                    'bg_start' => '#064e3b',
                    'bg_end' => '#022c22',
                    'icon' => 'APARTMENT',
                    'files' => ['flat_gwalior_1.svg'],
                ],
            ],
            [
                'user_index' => 3,
                'category_slug' => 'furniture',
                'subcategory_name' => 'Sofas & Dining Sets',
                'type' => 'product',
                'title' => 'Teak Wood 6-Seater Dining Table with Cushioned Chairs',
                'description' => "Handcrafted genuine Sheesham/Teak wood dining table set with 6 comfortable high back chairs and 8mm beveled glass top. Excellent polished finish. Relocating to another city hence selling at attractive price.",
                'price' => 18500,
                'country' => 'India',
                'state' => 'Delhi',
                'city' => 'Delhi',
                'area' => 'Lajpat Nagar',
                'phone' => '+91 98765 43210',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 98,
                'image_config' => [
                    'label' => 'Teak Wood Dining Set',
                    'sub' => '6 Seater / Glass Top',
                    'bg_start' => '#78350f',
                    'bg_end' => '#451a03',
                    'icon' => 'FURNITURE',
                    'files' => ['dining_table_1.svg'],
                ],
            ],
            // Services
            [
                'user_index' => 0,
                'category_slug' => 'education-tutoring',
                'subcategory_name' => 'Home Tutors (CBSE/ICSE)',
                'type' => 'service',
                'title' => 'Experienced Mathematics & Physics Tutor (Class 9th to 12th)',
                'description' => "M.Sc Physics with 7+ years of teaching experience providing personal and small-batch coaching for Class 9th-12th CBSE, ICSE, and MP Board. Doubt clearance, chapter-wise test series, and special preparation for Board Exams. Free 2-day demo class available.",
                'price' => 600,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Gwalior',
                'area' => 'Morar',
                'phone' => '+91 98260 12345',
                'status' => 'active',
                'is_featured' => true,
                'views_count' => 210,
                'image_config' => [
                    'label' => 'Maths & Physics Coaching',
                    'sub' => 'Class 9-12 / Home Tutor',
                    'bg_start' => '#1e3a8a',
                    'bg_end' => '#172554',
                    'icon' => 'EDUCATION',
                    'files' => ['tutor_1.svg'],
                ],
            ],
            [
                'user_index' => 1,
                'category_slug' => 'other-services',
                'subcategory_name' => 'Appliance & AC Repair',
                'type' => 'service',
                'title' => 'Quick AC Service, Jet Pump Cleaning & Gas Refilling',
                'description' => "Doorstep Split and Window AC servicing by certified technicians. Foam jet wash, gas leak check, copper piping, and compressor troubleshooting. 30 days post-service guarantee on all repairs. Available 7 days a week.",
                'price' => 399,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Indore',
                'area' => 'Bhawarkua',
                'phone' => '+91 94251 56789',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 135,
                'image_config' => [
                    'label' => 'AC Repair & Service',
                    'sub' => 'Doorstep / Same Day',
                    'bg_start' => '#0c4a6e',
                    'bg_end' => '#082f49',
                    'icon' => 'TOOLS',
                    'files' => ['ac_service_1.svg'],
                ],
            ],
            [
                'user_index' => 3,
                'category_slug' => 'professional-services',
                'subcategory_name' => 'Website & Software Development',
                'type' => 'service',
                'title' => 'Custom Laravel Web Application & E-Commerce Development',
                'description' => "Professional full-stack web developer offering custom Laravel, PHP, MySQL, and Vue/Blade application development. REST API integrations, payment gateways (Razorpay, Stripe), SEO friendly layouts, and speed optimization.",
                'price' => 12000,
                'country' => 'India',
                'state' => 'Delhi',
                'city' => 'Delhi',
                'area' => 'Connaught Place',
                'phone' => '+91 98765 43210',
                'status' => 'active',
                'is_featured' => true,
                'views_count' => 320,
                'image_config' => [
                    'label' => 'Web App Development',
                    'sub' => 'Laravel / API / Portal',
                    'bg_start' => '#312e81',
                    'bg_end' => '#1e1b4b',
                    'icon' => 'CODE',
                    'files' => ['web_dev_1.svg'],
                ],
            ],
            [
                'user_index' => 2,
                'category_slug' => 'fashion',
                'subcategory_name' => 'Watches & Jewelry',
                'type' => 'product',
                'title' => 'Seiko 5 Sports Automatic Watch (Black Dial, Stainless Steel)',
                'description' => "Authentic Seiko 5 Sports automatic mechanical watch with see-through case back and Day-Date display. 100m water resistant. Used sparingly on weekends. Comes with warranty card and retail box.",
                'price' => 14500,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Bhopal',
                'area' => 'MP Nagar',
                'phone' => '+91 91110 98765',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 110,
                'image_config' => [
                    'label' => 'Seiko 5 Automatic',
                    'sub' => '100m Water Resistant',
                    'bg_start' => '#27272a',
                    'bg_end' => '#09090b',
                    'icon' => 'WATCH',
                    'files' => ['seiko_1.svg'],
                ],
            ],
            [
                'user_index' => 3,
                'category_slug' => 'home-garden',
                'subcategory_name' => 'Plants & Gardening Tools',
                'type' => 'product',
                'title' => 'Exotic Bonsai Plants & Ceramic Planters Collection (Set of 4)',
                'description' => "Healthy 5-year aged Ficus and Jade Bonsai plants potted in imported glazed ceramic pots. Low maintenance indoor/balcony air purifying plants. Soil nutrients and care guide included.",
                'price' => 2400,
                'country' => 'India',
                'state' => 'Delhi',
                'city' => 'Delhi',
                'area' => 'Rohini',
                'phone' => '+91 98765 43210',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 74,
                'image_config' => [
                    'label' => 'Bonsai Plants Set',
                    'sub' => 'Ceramic Pots / Air Purifying',
                    'bg_start' => '#14532d',
                    'bg_end' => '#052e16',
                    'icon' => 'PLANT',
                    'files' => ['bonsai_1.svg'],
                ],
            ],
            [
                'user_index' => 0,
                'category_slug' => 'other-services',
                'subcategory_name' => 'Electricians & Plumbers',
                'type' => 'service',
                'title' => 'Professional Electrician & Inverter/Wiring Installation in Gwalior',
                'description' => "Certified electrician with 10 years experience for home wiring, MCB tripping issues, inverter setup, geyser fitting, and ceiling fan repair. Quick arrival in City Center, Lashkar, Morar, and Thatipur.",
                'price' => 250,
                'country' => 'India',
                'state' => 'Madhya Pradesh',
                'city' => 'Gwalior',
                'area' => 'City Center',
                'phone' => '+91 98260 12345',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => 95,
                'image_config' => [
                    'label' => 'Certified Electrician',
                    'sub' => 'Wiring / Inverter / Repair',
                    'bg_start' => '#854d0e',
                    'bg_end' => '#422006',
                    'icon' => 'ELECTRICAL',
                    'files' => ['electrician_1.svg'],
                ],
            ],
        ];

        foreach ($listingsData as $data) {
            $category = Category::where('slug', $data['category_slug'])->first();
            $subcategory = null;
            if (!empty($data['subcategory_name'])) {
                $subcategory = Subcategory::where('category_id', $category->id)
                    ->where('name', $data['subcategory_name'])
                    ->first();
            }

            $user = $createdUsers[$data['user_index']];

            $slug = Str::slug($data['title']) . '-' . Str::random(5);

            $listing = Listing::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'subcategory_id' => $subcategory ? $subcategory->id : null,
                'listing_type' => $data['type'],
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'],
                'price' => $data['price'],
                'country' => $data['country'],
                'state' => $data['state'],
                'city' => $data['city'],
                'area' => $data['area'],
                'contact_phone' => $data['phone'],
                'status' => $data['status'],
                'is_featured' => $data['is_featured'],
                'views_count' => $data['views_count'],
            ]);

            // Create SVG image files and attach to listing
            $imgConfig = $data['image_config'];
            foreach ($imgConfig['files'] as $i => $filename) {
                $filepath = $samplesDir . '/' . $filename;
                if (!File::exists($filepath)) {
                    $svgContent = $this->generateSvg(
                        $imgConfig['label'],
                        $imgConfig['sub'],
                        $imgConfig['bg_start'],
                        $imgConfig['bg_end'],
                        $imgConfig['icon']
                    );
                    File::put($filepath, $svgContent);
                }

                ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => 'samples/' . $filename,
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }
    }

    private function generateSvg(string $title, string $subtitle, string $bgStart, string $bgEnd, string $badge): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $safeSub = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');
        $safeBadge = htmlspecialchars($badge, ENT_QUOTES, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$bgStart}" />
      <stop offset="100%" stop-color="{$bgEnd}" />
    </linearGradient>
    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
      <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
    </pattern>
  </defs>
  <rect width="800" height="600" fill="url(#grad)" />
  <rect width="800" height="600" fill="url(#grid)" />
  
  <rect x="50" y="45" width="160" height="34" rx="17" fill="rgba(255,255,255,0.12)" />
  <circle cx="68" cy="62" r="6" fill="#00a49f" />
  <text x="82" y="67" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="12" font-weight="700" fill="#ffffff" letter-spacing="1">LOCALMARKET</text>

  <rect x="590" y="45" width="160" height="34" rx="6" fill="#ffce32" />
  <text x="670" y="67" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="12" font-weight="800" fill="#002f34" text-anchor="middle" letter-spacing="1">{$safeBadge}</text>

  <circle cx="400" cy="270" r="110" fill="rgba(255,255,255,0.06)" />
  <circle cx="400" cy="270" r="85" fill="rgba(255,255,255,0.09)" />
  
  <text x="400" y="275" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="42" font-weight="800" fill="#ffffff" text-anchor="middle">★</text>

  <text x="400" y="440" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="28" font-weight="700" fill="#ffffff" text-anchor="middle">{$safeTitle}</text>
  <text x="400" y="480" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="16" fill="rgba(255,255,255,0.75)" text-anchor="middle">{$safeSub}</text>

  <line x1="100" y1="530" x2="700" y2="530" stroke="rgba(255,255,255,0.1)" stroke-width="1" />
  <text x="400" y="560" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="12" fill="rgba(255,255,255,0.5)" text-anchor="middle">Verified Classified Listing • LocalMarket India</text>
</svg>
SVG;
    }
}
