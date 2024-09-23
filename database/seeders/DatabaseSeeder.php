<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // Category::create([
        //     'name' => 'Faturamatik',
        //     'slug' => 'faturamatik',
        //     'icon' => 'heroicon-o-home',
        //     'description' => 'Faturamatik is a simple invoicing app that helps you create and manage invoices.',
        // ]);

        // Category::create([
        //     'name' => 'Orta Yazılım',
        //     'slug' => 'orta-yazilim',
        //     'icon' => 'heroicon-o-home',
        //     'description' => 'Orta Yazılım is a simple invoicing app that helps you create and manage invoices.',
        // ]);

        // Category::create([
        //     'name' => 'DevOrhan',
        //     'slug' => 'devorhan',
        //     'icon' => 'heroicon-o-home',
        //     'description' => 'Yazılım is a simple invoicing app that helps you create and manage invoices.',
        // ]);

        // Category::create([
        //     'name' => 'RTE',
        //     'slug' => 'rte',
        //     'icon' => 'heroicon-o-home',
        //     'description' => 'Yazılım is a simple invoicing app that helps you create and manage invoices.',
        // ]);

        // Project::create([
        //     'name' => 'Cisoft Store',
        //     'slug' => 'cisoft-store',
        //     'category_id' => 1,
        //     'color' => '#3498db',
        //     'status' => 'active',
        //     'priority' => 'high',
        //     'due_date' => now()->addDays(rand(1, 30)),
        // ]);

        // Project::create([
        //     'name' => 'Cisoft Panel',
        //     'slug' => 'cisoft-panel',
        //     'category_id' => 1,
        //     'color' => '#2ecc71',
        //     'status' => 'active',
        //     'priority' => 'low',
        //     'due_date' => now()->addDays(rand(1, 30)),
        // ]);

        // Project::create([
        //     'name' => 'Düğün Mevsimi',
        //     'slug' => 'dugun-mevsimi',
        //     'category_id' => 2,
        //     'color' => '#f1c40f',
        //     'status' => 'active',
        //     'priority' => 'medium',
        //     'due_date' => now()->addDays(rand(1, 30)),
        // ]);

        // Project::create([
        //     'name' => 'TaskFill',
        //     'slug' => 'taskfill',
        //     'category_id' => 3,
        //     'color' => '#ee5253',
        //     'status' => 'active',
        //     'priority' => 'high',
        //     'due_date' => now()->addDays(rand(1, 30)),
        // ]);

        // Project::create([
        //     'name' => 'Ekol Sanat Akademi',
        //     'slug' => 'ekol-sanat-akademi',
        //     'category_id' => 4,
        //     'color' => '#f39c12',
        //     'status' => 'active',
        //     'priority' => 'high',
        //     'due_date' => now()->addDays(rand(1, 30)),
        // ]);
    }
}
