<?php

namespace App\Console\Commands\Base;

use App\Models\EGovService;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Settings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // Permissions
        $createdPermissions = 0;
        $jsonFile = storage_path('configs/permissions.json');
        if (File::exists($jsonFile)) {
            $jsonContent = File::get($jsonFile);
            $permissions = json_decode($jsonContent, true);

            if (is_array($permissions)) {
                foreach ($permissions as $permission) {
                    if (!Permission::where('name', $permission['name'])->exists()) {
                        Permission::create($permission);
                        $createdPermissions++;
                    }
                }
            }

            if ($createdPermissions > 0) {
                $this->info("Permissions {$createdPermissions} write done! ✔️");
            } else {
                $this->info("Permissions already exists! ✔️");
            }
        } else {
            $this->error("Permissions file not found! ❌");
        }
    }

}
