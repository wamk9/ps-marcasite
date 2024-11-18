# ps-marcasite
Este foi o desafio técnico para participar do processo seletivo da empresa Marcasite.

O objetivo do teste estava em criar um sistema de inscrições para cursos, com os dados do aluno, diferençiação de valores por curso, integração com um gateway de pagamento (Neste caso foi utilizado o do MercadoPago), com controle de vendas, área administrativa com listagem de todos os inscritos, busca avançada feita pelo back-end sem data-table, possibilidade de edição, exclusão e exportação de inscritos em Excel e PDF.

Este teste foi realizado utilizando PHP 8.2 (Laravel 11) de backend, servindo como API, Vuejs 3 no front-end e banco de dados MySQL, além de outras bibliotecas que foram necessárias.

## Como executar o projeto
Para executar o projeto precisamos do PHP instalado e presente como variável de ambiente, além do NPM, Composer e MySQL.

Com tudo instalado, crie um arquivo `.env` baseado no `.env.example`, modificando as variáveis de banco para o acesso que você utiliza e após, crie um banco de dados de nome `ps-marcasite` junto ao MySQL para após, executar os seguintes comandos abaixo (todos dentro da pasta principal do projeto):

```
php artisan jwt:secret
php artisan migrate
php artisan db:seed
```

- O primeiro comando irá criar um token de validação JWT, esse token é único para cada instância;
- O segundo comando irá rodar a migrate do Laravel, criando assim as tabelas necessárias no banco `ps-marcasite` e;
- O terceiro comando irá preencher o banco de dados com dados de teste necessários para rodar o sistema.

Com os passos acima executados, basta rodarmos o comando `php artisan serve` e `npm run dev` para termos nosso ambiente de testes rodando.

## Configuração do gateway de pagamento (MercadoPago)
Para configurar o gateway de pagamento, vá para a aba configurações do usuário "admin@marcasite.com.br" (A senha de acesso deste usuário é "123456"), e preencha os campos de chave pública e chave privada da integração do CheckoutBricks do MercadoPago, após salvar os dados, o pagamento funcionará para todos os cursos disponíveis.

## Ativação de usuário (Opcional)
Para ativar o usuário, logue no sistema e vá para a URL `/email/verify`, lá será enviado um código de 6 dígitos via email que pode ser visualizado na pasta log do Laravel ou via banco de dados.

Após setar o código, o usuário será redirecionado e estará com sua conta ativada, podendo ser confirmado no menu "Usuários" no painel do administrador (admin@marcasite.com.br).
