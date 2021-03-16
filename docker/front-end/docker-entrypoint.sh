#!/bin/sh

MODULES_INSTALLED="/root/locks/modules_installed"

if test -f "$MODULES_INSTALLED"; then
    echo " * * * * "
    echo "node_modules já instalados"
    echo "caso surjam erros relacionados a dependências não instaladas, use o comando:"
    echo "docker exec -ti glx-frontend npm install"
    echo " * * * * "
else
    echo " * * * * "
    echo "node_modules não instalados!"
    echo "aguarde enquanto os componentes são baixados e instalados"
    echo " * * * * "
    npm install && touch $MODULES_INSTALLED
    npm audit fix
fi

echo "inicializando o servidor"
npm run serve