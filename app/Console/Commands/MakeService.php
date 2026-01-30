<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        
        $directory = app_path('Services');
        $path = "{$directory}/{$name}.php";

        if (! File::exists($directory)) File::makeDirectory($directory, 0755, true);

        if (File::exists($path)) {
            $this->error('Service already exists!');
            return;
        }
        
        $stub = <<<PHP
<?php

namespace App\Services;

class {$name}
{
    //
}
PHP;

        File::put($path, $stub);

        $this->info("Service class {$name} created successfully at {$path}");
    }
}
