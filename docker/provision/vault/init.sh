!#/bin/sh
vault secrets enable database

vault write database/config/mysql \
    plugin_name="mysql-database-plugin" \
    connection_url="{{username}}:{{password}}@tcp(secrets-db:3306)/" \
    allowed_roles="my-role" \
    username="vault" \
    password="vault"

vault write database/roles/my-role \
    db_name=vault \
    creation_statements="CREATE USER '{{name}}'@'%' IDENTIFIED BY '{{password}}';GRANT SELECT ON *.* TO '{{name}}'@'%';" \
    default_ttl="1h" \
    max_ttl="24h"

