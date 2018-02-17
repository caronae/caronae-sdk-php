# Caronaê - PHP SDK

[![CircleCI](https://circleci.com/gh/caronae/caronae-sdk-php.svg?style=svg)](https://circleci.com/gh/caronae/caronae-sdk-php)
[![Latest Stable Version](https://poser.pugx.org/caronae/caronae-sdk-php/v/stable)](https://packagist.org/packages/caronae/caronae-sdk-php)

SDK utilizado para integrar com as instituições,
permitindo o cadastro e login dos usuários no Caronaê.

## Instalação

Utilize o [Composer](https://getcomposer.org/) para instalar o SDK:

```shell
composer require caronae/caronae-sdk-php
``` 

## Exemplo de uso

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Caronae\CaronaeService;

$caronae = new CaronaeService();

// Credenciais da instituição fornecidas pela equipe do Caronaê
$caronae->setInstitution('INSTITUTION_ID', 'INSTITUTION_PASSWORD');

// Aqui criamos um usuário de exemplo, mas numa aplicação real o usuário
// seria obtido através do banco de dados/API da sua instituição.
// Uma vez que sua instituição autorizou o usuário, envie-o no formato
// abaixo para o Caronaê:
$user = [
    'name' => 'Ada Lovelace', 
    'course' => 'Ciência da Computação', 
    'id_ufrj' => '12345678', 
    'profile' => 'Graduação',
    'profile_pic_url' => 'http://exemplo.com/foto.jpg'
];

try {
    // O método authorize deve ser chamado somente quando o usuário for
    // considerado autorizado a usar o Caronaê. O usuário será logado
    // ou, caso seja seu primeiro acesso, será cadastrado e logado.
    $caronae->authorize($user);
    
} catch (\Exception $e) {
    echo "Ocorreu um erro durante a autenticação. " . $e->getMessage();
    
    // Caso ocorra algum erro, redirecione de volta para o Caronaê com a mensagem
    // de erro que será mostrada para o usuário.
    $redirectURL = $caronae->redirectUrlForError($e->getMessage());
    header('Location: ' . $redirectURL);
    
    die();
}

// Agora que o usuário já foi autenticado no Caronaê, redirecione de volta
// para o aplicativo. O próprio SDK já retorna a URL de redirecionamento:
$redirectURL = $caronae->redirectUrlForSuccess();
header('Location: ' . $redirectURL);

```

## Desenvolvimento

O SDK permite que o endereço da API do Caronaê seja alterado, caso deseje
apontar para um ambiente de testes ou local. 

Para utilizar o ambiente de testes do Caronaê ou outro ambiente, basta 
inicializar o `CaronaeService` passando a URL base desejada. Exemplo:

```php
$caronae = new CaronaeService('https://api.dev.caronae.org');
```