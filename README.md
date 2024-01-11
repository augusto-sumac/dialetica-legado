# editoradialetica-autores-v2

Versão 2.0 da plataforma de autores da dialética.

# Banco de dados

A base de dev está na pasta **sql**. Trata-se de uma base já com dados e a senhas das tabelas **users** e **autor** foram alteradas para **1234**.

Crie uma base de dados no mysql com o nome **phixies_app_dialetica**:

```sql
create database "phixies_app_dialetica";
```

Importe a base de dados para o mysql/mariadb:

```bash
mysql -u root -p phixies_app_dialetica < sql/phixies_app_dialetica.sql
```

# Variáveis de ambiente

Crie um aquivo **.env.dev** na raiz com o conteúdo abaixo

```env
#DEV
DEV_MODE=true

#APP
APP_KEY="s~jI)y>hS%LLh^nW4HPI|P);2!C@FYPM!sB;^K~!Oen4&^xWUrzUGII@Z6_r/D'Y"
APP_NAME="Editora Dialética"

#DATABASE
DB_HOST="localhost"
DB_PORT=3306
DB_USER="root"
DB_PASS=""
DB_DATABASE="phixies_app_dialetica"
# Quando ativo, grava as instruções insert, update, delete
# na pasta storage/logs/sql
DB_PROFILE=true

#SMTP
SMTP_HOST="mail.dialeticaautores.phixies.com"
SMTP_PORT=465
SMTP_AUTH=true
SMTP_SECURE="ssl"
SMTP_SENDER_EMAIL="no-reply@dialeticaautores.phixies.com"
SMTP_SENDER_USER="no-reply@dialeticaautores.phixies.com"
SMTP_SENDER_PASS="x8ZTQp3phYTLlwOmKZDFnYI"
SMTP_SENDER_NAME="Editora Dialética"

#PLUG NOTAS
PLUG_NOTAS_CPF_CNPJ="32431939000105"
PLUG_NOTAS_EMAIL="atendimento@editoradialetica.com"
PLUG_NOTAS_SANDBOX=true
PLUG_NOTAS_SANDBOX_API_KEY="2da392a6-79d2-4304-a8b7-959572c7e44d"

#CIELO
CIELO_SANDBOX_MERCHANT_ID='e919f678-c8bf-486a-8933-8392114fc54a'
CIELO_SANDBOX_MERCHANT_KEY='VBBFBPPALIBLHDEBOIVZTKELLRBDRLBGLDPLLOIB'
CIELO_SANDBOX=true

# ARTICLES
ARTICLE_MAX_FILE_SIZE=50
```

# Iniciar servidor de dev

## Via servidor embutido do php

```bash
php -S 0.0.0.0:8000
```

## Via docker

Abra o terminal

Acesse a pasta **.docker**

```bash
docker-compose up
```

# Acesse no navegador

```url
http://0.0.0.0:8000
```
