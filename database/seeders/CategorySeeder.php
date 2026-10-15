<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mobiles & Tablets',
                'slug' => 'mobiles-tablets',
                'icon' => 'bi-phone',
                'description' => 'Smartphones, feature phones, iPads, tablets and mobile accessories.',
                'subcategories' => [
                    'Smartphones',
                    'Tablets & iPads',
                    'Mobile Accessories',
                    'Smart Watches & Fitness Bands',
                ],
            ],
            [
                'name' => 'Electronics & Appliances',
                'slug' => 'electronics-appliances',
                'icon' => 'bi-laptop',
                'description' => 'Laptops, computers, TVs, refrigerators, air conditioners, and kitchen appliances.',
                'subcategories' => [
                    'Laptops & Desktops',
                    'TVs, Audio & Video',
                    'Kitchen Appliances',
                    'Cameras & Lenses',
                    'Air Conditioners & Coolers',
                ],
            ],
            [
                'name' => 'Vehicles',
                'slug' => 'vehicles',
                'icon' => 'bi-car-front',
                'description' => 'Pre-owned cars, bikes, scooters, commercial vehicles, and spare parts.',
                'subcategories' => [
                    'Cars',
                    'Motorcycles',
                    'Scooters & Activa',
                    'Commercial Vehicles',
                    'Spare Parts & Accessories',
                ],
            ],
            [
                'name' => 'Furniture',
                'slug' => 'furniture',
                'icon' => 'bi-lamp',
                'description' => 'Sofas, dining tables, king size beds, wardrobes, and modern office furniture.',
                'subcategories' => [
                    'Sofas & Dining Sets',
                    'Beds & Wardrobes',
                    'Office Furniture',
                    'Home Decor & Lighting',
                    'Bookshelves & Study Tables',
                ],
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'icon' => 'bi-bag-check',
                'description' => 'Men and women designer clothes, shoes, wristwatches, and ethnic wear.',
                'subcategories' => [
                    'Men Clothing',
                    'Women Ethnic & Western',
                    'Footwear & Sneakers',
                    'Watches & Jewelry',
                    'Bags & Luggage',
                ],
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'icon' => 'bi-flower1',
                'description' => 'Gardening plants, tools, outdoor fixtures, and home improvement items.',
                'subcategories' => [
                    'Plants & Gardening Tools',
                    'Lighting & Ceiling Fans',
                    'Home Improvement & Hardware',
                    'Sanitary & Bath Fittings',
                ],
            ],
            [
                'name' => 'Real Estate',
                'slug' => 'real-estate',
                'icon' => 'bi-buildings',
                'description' => 'Ready to move flats, independent houses, villas, builder floors, and plots.',
                'subcategories' => [
                    'Apartments & Flats',
                    'Houses & Villas',
                    'Plots & Land',
                    'Commercial Offices & Shops',
                    'PG & Room Sharing',
                ],
            ],
            [
                'name' => 'Education & Tutoring',
                'slug' => 'education-tutoring',
                'icon' => 'bi-mortarboard',
                'description' => 'Personal home tutors, competitive exam classes, music, and coding training.',
                'subcategories' => [
                    'Home Tutors (CBSE/ICSE)',
                    'IIT-JEE / NEET Coaching',
                    'Spoken English & IELTS',
                    'Coding & Web Development Classes',
                    'Music & Dance Classes',
                ],
            ],
            [
                'name' => 'Professional Services',
                'slug' => 'professional-services',
                'icon' => 'bi-briefcase',
                'description' => 'Legal advice, GST & Income Tax filing, digital marketing, and event planning.',
                'subcategories' => [
                    'CA & Income Tax Filing',
                    'Legal & Property Documentation',
                    'Website & Software Development',
                    'Event & Wedding Photography',
                    'Architectural & Interior Design',
                ],
            ],
            [
                'name' => 'Other Services',
                'slug' => 'other-services',
                'icon' => 'bi-tools',
                'description' => 'Verified electricians, plumbers, carpenter services, deep cleaning, and movers.',
                'subcategories' => [
                    'Electricians & Plumbers',
                    'Appliance & AC Repair',
                    'Deep House Cleaning',
                    'Packers & Movers',
                    'Drivers & Delivery Services',
                ],
            ],
        ];

        foreach ($categories as $index => $item) {
            $cat = Category::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'icon' => $item['icon'],
                    'description' => $item['description'],
                    'sort_order' => $index + 1,
                ]
            );

            foreach ($item['subcategories'] as $subName) {
                Subcategory::updateOrCreate(
                    [
                        'category_id' => $cat->id,
                        'slug' => Str::slug($subName),
                    ],
                    [
                        'name' => $subName,
                    ]
                );
            }
        }
    }
}
