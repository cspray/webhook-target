# Webhook Target

A self-hosted, containerized application to provide a secure, long-term target for webhooks. The application will 
store any received webhook in a Postgres database. This tool is designed to allow a developer to easily debug and 
develop interactions with a service that sends POST HTTP requests to their application. 

Please review this entire README before using this software.

## Requirements

- [PHP 8.3+](https://php.net) (If necessary to run admin password creation tool)
- [Composer](https://getcomposer.org/) or [Git](https://git-scm.com/)
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Just](https://just.systems/)

> This guide can be followed without Just, you'll need to review the justfile 
> for the appropriate Docker commands to execute.

## Installation

Based on what software you have installed to satisfy the requirements listed 
above, execute one of the following commands to install the software.

You can install using Composer:

```shell
composer require cspray/webhook-target
```

You can install using Git:

```shell
git clone https://github.com/cspray/webhook-target
```

## Usage

### Step 1: Prepare Environment

Before running the application, ensure that the appropriate environment 
variables have been set. Execute the following command, then adjust the 
resulting `.env` file to be appropriate for your use case.

```shell
cp .env.dist .env
```

Below, we review the default values and steps you can take to adjust those values, 
if necessary.

``
PROFILES=default,dev,docker,web
``

The profiles don't need to be altered unless new services are added after you 
install the software. Generally, this shouldn't be required and is the result of 
a special use case.

---

```
DATABASE_SCHEMA=web_app
DATABASE_HOST=database
DATABASE_PORT=5432
DATABASE_USER=postgres
DATABASE_PASSWORD=password
DATABASE_CONNECTION_LIMIT=1
```

These are default connections for the Postgres database running in Docker
Compose. If you want to change the database, or harden the provided database, 
you'll need to alter these values as appropriate.

---

```
ADMIN_USERNAME=admin
ADMIN_PASSWORD=$2y$10$052.1wII5TGndqOfTM3WfuAZf2Y9HG9dk0Yba8eTPyRKf//GIVLHK
```

Webhook Target provides a single-user login mechanism to view the received
webhooks. The credential used for this login should be set for these 
environment variables. If you change the default password, which is highly recommended, 
be sure to provide a value generated with the [`password_hash`](https://php.net/password_hash) 
function. You can also use `bin/create-admin-user-password` to generate this value, and then 
manually set to this environment variable.

The default password is `password`. Unless you're running this locally, or in an environment
where security is truly not important, it is HIGHLY RECOMMENDED you alter this value.

### Step 2: Update HTTP Server Config

It might be possible, that you need to adjust the HTTP server config based on your use case. 
At minimum, you'll probably want to generate a TLS certificate for the site. You can use a 
service like [Let's Encrypt](https://letsencrypt.org/), and ensure the file is store in the 
location specified by this config.

Review the file at `/resources/config/server.php`.

### Step 3: Build the Docker Containers

Build the app and database Docker Containers.

```shell
just build
```

### Step 4: Bring up the Docker Container

Bring up the app and database.

```shell
just up
```

### Step 5: Run Database Migrations

Ensure the migrations are run. Based on whether you altered the profiles in 
step 1, you'll need to run the migrations for the correct environment.

If your profiles include `dev`:

```text
just migrate-dev-env
```

If you swapped to using the `prod` profile:

```text
just migrate-prod
```

### Step 6: Configure your Webhook Provider

Next, is to configure your webhook provider and receive some webhooks!

The path for the webhook should be:

```text
/webhook/target
```

### Step 7: Review Received Webhooks

Finally, access the site, log in using credentials configured in step 1, and
review the data provided by the webhook.

## What not use PostBin?

You probably should! [PostBin](https://www.postb.in/) is a fantastic service
and if their software satisfies your needs, please use them. However, I was facing 
a problem that required the following considerations, that prohibited using PostBin:

- Tighter control over access to received webhook data
- Ability to receive webhooks over a long period of time

If you don't have these limitations, you probably should use Pastebin. If you 
**do** have them, take a look at using this software!