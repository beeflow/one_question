# Instalation and configuration

Before you start, please create copy `.env.example` file to `.env` and 
change `DATABASE_URL`

Then on your console, in project directory, run

```bash
php bin/console doctrine:database:create
```

Next you need to migrate database schema:

```bash
php bin/console doctrine:migrations:migrate
```

# Transfers

There are two transfers prepared as a symfony command

## Countries

First you need to fetch countries:

```bash
php bin/console countries:fetch
```

Next you need to fetch currencies rates:

```bash
php bin/console currencies:fetch
```

## Authorization

First you need to add new user.
```bash
php bin/console bin/console fos:user:create
```

## API Endpoints

### Login
```bash
POST /api/login_check
```

Payload
```
_username=USER&_password=PASSWORD
```

Headers:
```
Content-Type: application/x-www-form-urlencoded
```
### All countries with currencies and rates

```bash
GET /api/v1/
```
Headers
```
Authorization: Bearer [TOKEN]
```

### Find country

```bash
GET /api/v1//Poland
```
Headers
```
Authorization: Bearer [TOKEN]
```