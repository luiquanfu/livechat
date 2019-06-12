<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Language extends Command
{
    protected $signature = 'language {module}';
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $module = $this->argument('module');
        app('App\Http\Controllers\Command\Language')->index($this, $module);
    }
}
