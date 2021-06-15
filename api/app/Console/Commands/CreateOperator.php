<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateOperator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operator:create [id]';

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
     * @return int
     */
    public function handle()
    {
        /*
        Should possibly create a DB user and password for added security and data separation

        Must create a database schema (doesn’t need to live in the same server as other operator schemas)

        Must store DB credentials (user, password, host) on a safe place

        Must migrate DB tables into that new schema

        Must seed some initial data
        */

        // create database (on provision)

        // create user vault on database (on provision)

        // create vault config
        /*
        vault write database/config/mysql \
        plugin_name="mysql-database-plugin" \
        connection_url="{{username}}:{{password}}@tcp(secrets-db:3306)/" \
        allowed_roles="my-role" \
        username="vault" \
        password="vault"
        */

        // create vault role for operator
        /*
        vault write database/roles/my-role \
        db_name=vault \
        creation_statements="CREATE USER '{{name}}'@'%' IDENTIFIED BY '{{password}}';GRANT SELECT ON *.* TO '{{name}}'@'%';" \
        default_ttl="1h" \
        max_ttl="24h"
        */

        return 0;
    }
}
