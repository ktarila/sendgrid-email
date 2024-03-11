# Installation and Setup

## Install Dependency

```bash
composer install
```

## set up server and run 

```bash
curl --location 'https://localhost/sendemail' \
--header 'Content-Type: application/json' \
--data-raw '{
  "name": "Patrick",
  "email": "sender@example.com",
  "message": "Hello World!"
}'
```

