![Received;](https://received.blob.core.windows.net/assets/logo-200px.png)
---

[![Actions Status](https://github.com/sunchayn/received/workflows/Received%20CI/badge.svg)](https://github.com/sunchayn/received/actions)
[![Coverage Status](https://coveralls.io/repos/github/sunchayn/received/badge.svg?branch=master)](https://coveralls.io/github/sunchayn/received?branch=master)
[![StyleCI](https://github.styleci.io/repos/253168130/shield?branch=master)](https://github.styleci.io/repos/253168130)
[![Maintainability](https://api.codeclimate.com/v1/badges/357551f94d5e27135e3a/maintainability)](https://codeclimate.com/github/sunchayn/received/maintainability)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](./LICENSE)

Public bucket for receiving files.

## About

**Received;** is a platform that enable people to reserve a storage space and create a public gateway to receives files from their friends, co-worker or themselves.

the idea is to create one **easy to remember** URL and protect each folder with different password. Hence, the whoever got the link can use the same URL to share files to different folders each with it's unique password.

## Demo
A MVP has been implemented and deployed in the following link:
https://received.azurewebsites.net

## Requirements
- PHP > 7.2
- Composer
- Node & NPM
- Twilio Verify Service
- Twilio Authy
- Twilio Programmable SMS
- Twilio Phone Number
- SendGrid Account ( Or Any mailing service )

## Installation
Received built using Laravel, VueJs and Tailwind. Basic knowledge in these technologies would be helpful for inspecting the code.

**1/ Clone the repository**
```shell script
git clone https://github.com/sunchayn/received.git

# Change directory to the newly created folder
cd received
```

**2/ Install dependencies**
```shell script
# Install PHP dependencies
composer install

# Install JS dependencies
npm install
```

**3/ Prepare .env file**
An example `.env.example` has been shipped with the app. Before proceeding you need to rename this file to `.env` in order to be detected and used by the app.
```shell script
# Using command line
cp .env.example .env
```

**4/ Generate laravel secure key**
Laravel needs a secret key to encrypt the data. It's generated using the following command.
```shell script
php artisan key:generate --ansi
```

**5/ Configure .env values**
Beside the straightforward values like app name, URL and database connection. There's extra values to configure in order to start working with the app. For more details about these values and how to get them check this [Guide](https://github.com/sunchayn/received/blob/master/__guide/SERVICES.md).

```dotenv
# Pick which SMS service to use by the app from this list ( FAKE, TWILIO ).
# Fake service is used for testing and it will consider all requests as valid by default.
SMS_SERVICE=FAKE

# Your SMS service secure credentials ( Twilio )
SMS_SERVICE_KEY=
SMS_SERVICE_SECRET=

# Phone number used to send Programmable SMS
SMS_PHONE_NUMBER=

# You need to create a verification service within your Twilio console (Code length is 6 digits)
# => https://www.twilio.com/docs/verify/api/service#create-a-verification-service
TWILIO_VERIFICATION_SERVICE_ID=

# Your Authy application API key
# => https://www.twilio.com/console/authy/applications
TWILIO_AUTHY_API_KEY=

# This email will be used for all users as we only have the user's phone number and we won't use the Authy app only SMS channel.
TWILIO_AUTHY_EMAIL=authy@received.app
```

**6/ Setup the database**
As you properly configured your `.env` file. You can proceed to populating the database.
```shell script
# Create the tables
php artisan migrate

# Add default plan ( Requried )
php artisan db:seed
```

**7/ Optional - Add cron job**
For production use, two command line has been established to push the unread notifications to user via SMS or Email channel (depending on user preference). To automate this task, a cron job entry must be established:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

All set up!

## Available Composer scripts
```
# Run tests with coverage.
composer run test

# Run PHPCodeSniffer to identify unstyled files.
composer run check

# Fix all fixable stylying issues
composer run fix
```

## Related blog posts

[Received; Public bucket for receiving files](https://dev.to/mazentouati/received-public-bucket-for-receiving-files-24kb)

[Twilio hackathon Project update: Received; UI](https://dev.to/mazentouati/twilio-hackathon-project-update-recieved-ui-4kol)

---
A project submission for [Twilio x DEV community hackathon!](https://dev.to/devteam/announcing-the-twilio-hackathon-on-dev-2lh8)
