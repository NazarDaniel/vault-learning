<?php

namespace App\Console\Commands;

use Vault\Client;
use AlexTartan\GuzzlePsr18Adapter\Client as GuzzleClient;
use Illuminate\Console\Command;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\Uri;
use Vault\AuthenticationStrategies\AppRoleAuthenticationStrategy;
use Vault\AuthenticationStrategies\TokenAuthenticationStrategy;
use Vault\AuthenticationStrategies\UserPassAuthenticationStrategy;

class CreateOperator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'operator:create {identifier} {dbHost}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an operator. Identifier should be a string with no spaces.';

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
        $dbHostName = $this->argument("dbHost");
        $identifier = $this->argument("identifier");

        $dbSchemaName = "operator-{$identifier}";
        $dbVaultUser = [
            "username" => "vault",
            "password" => "vault"
        ];

        $vaultRoleName = "operator-{$identifier}";
        $vaultHostName = "vault";
        $vaultConfigName = "operator-{$identifier}";
        $vaultToken = 'secret';

        $client = new Client(
            new Uri("http://{$vaultHostName}:8200"),
            new GuzzleClient(),
            new RequestFactory(),
            new StreamFactory()
        );

        $authenticated = $client
            ->setAuthenticationStrategy(
                new TokenAuthenticationStrategy(
                    $vaultToken
                )
            )->authenticate();

        if (empty($authenticated)) {
            $this->error("Authentication to Vault failed");
            return 1;
        }

        $this->info("vaultConfigName = '$vaultConfigName'");
        $this->info("vaultRoleName = '$vaultRoleName'");
        $this->info("dbHostName = '$dbHostName'");
        $this->info("{{username}}:{{password}}@tcp({$dbHostName}:3306)/");


        $configWritten = $client->write(
            "/database/config/{$vaultConfigName}",
            [
                'plugin_name' => "mysql-database-plugin",
                'connection_url' => "{{username}}:{{password}}@tcp({$dbHostName}:3306)/",
                'allowed_roles' => $vaultRoleName,
                'username' => "vault",
                'password' => "vault"
            ]
        );

        if (!$configWritten) {
            $this->error("Config could not be written");
            return 2;
        }

        $client->write(
            "/database/roles/{$vaultRoleName}",
            [
                'db_name' => $dbSchemaName,
                'default_ttl' => "1h",
                'max_ttl' => "24h",
                'creation_statements' => "CREATE USER '{{name}}'@'%'
                    IDENTIFIED BY '{{password}}';
                    GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER,
                    INDEX, LOCK TABLES, TRIGGER
                    ON ${dbSchemaName}.* TO '{{name}}'@'%';"
            ]
        );

        // migrate and seed
        

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
        creation_statements="CREATE USER '{{name}}'@'%' IDENTIFIED BY '{{password}}';
        GRANT SELECT ON *.* TO '{{name}}'@'%';" \
        default_ttl="1h" \
        max_ttl="24h"
        */

        return 0;
    }
}
