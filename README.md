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

## API Endpoints

### All countries with currencies and rates

```bash
GET /api/v1/
```

### Find country

```bash
GET /api/v1//Poland
```
