<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SyncCatalogsCommand extends Command
{
    protected $signature = 'catalogs:sync';
    protected $description = 'Sync catalog tables with the config file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $catalogs = config('catalogos');
        foreach ($catalogs as $tableName => $config) {
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) use ($config) {
                    $table->uuid('id')->primary();
                    foreach ($config['fields'] as $field => $type) {
                        $typeParts = explode(',', $type);
                        if (isset($typeParts[1])) {
                            $table->{$typeParts[0]}($field, $typeParts[1])->nullable();
                        } else {
                            $table->{$typeParts[0]}($field)->nullable();
                        }
                    }
                });
                $this->info("Table {$tableName} created successfully.");
            } else {
                $this->info("Table {$tableName} already exists.");
            }
        }
    }
}
