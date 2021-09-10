# Bank Account API

## Documentação:

A documentação dos endpoints pode ser acessada no link:
https://app.swaggerhub.com/apis-docs/thainam/banking-account/1.0.0#/

## Configuração:

1 - Clone o repositório.

2 - Entre no diretório clonado.

3 - Com o Docker (e docker-compose) instalado em sua máquina, execute o seguinte comando:
```
docker-compose -d --build
```
> Pode ir pegar um café enquanto o Docker builda as imagens! ☕

4 - Teste a aplicação dando um get no endpoint de users (usando o Postman, por exemplo):
```
http://api.localhost/v1/users
```

Obs - Caso queira acessar a documentação da API sem acessar o link descrito no início, basta acessar via browser que será redirecionado:
```
http://api.localhost
```

## Testes

1 - Acesse o container da aplicação para realizar os testes:
```sh
docker exec -it app sh
```
2 - Execute o comando abaixo para rodar os testes:
```sh
vendor/bin/phpunit
```

>Caso queira ver o code coverage, está em:
```
/app/coverage/index.html
```

Pronto! É só começar a brincar! 🎮

## License
MIT
**Free Software, Yeeeah!**
