# Services configuration

### SMS Service

```dotenv
SMS_SERVICE=FAKE
```
The SMS service has been established using Adapter pattern. Hence, you can use multiple service provider without changing the core code. For now, only Twilio has been provided and a Fake service for Unit testing or to use it locally without being charged.

**Available values:** `TWILIO` | `FAKE`

### SMS Service credentials
```dotenv
SMS_SERVICE_KEY=
SMS_SERVICE_SECRET=
```
You get these keys from your SMS provider console. 
For TWILIO credentials, first go to your [account settings](https://www.twilio.com/console/project/settings) then scroll down to API Credentials section. Then fill the values as the following:

```dotenv
SMS_SERVICE_KEY = Twilio account SID
SMS_SERVICE_SECRET = Twilio Auth Token
```

### SMS Phone number
To use the Programmable SMS service, you'll need to have a dedicated phone number to send these SMS. Check this [guide](https://www.twilio.com/docs/sms/quickstart/php#get-a-phone-number) to create a phone number.

```dotenv
SMS_PHONE_NUMBER = +xxxxxxxx
```

### Twilio Verification Service
When using twilio for verification a service must be present to start the verification process. You can create it via code but a better way is to prepare your service from the [console](https://www.twilio.com/console/verify/services). The name of the service will be sent within the SMS as the app name. For the code length make it 6 digits to get along the rest of app parts.

```dotenv
TWILIO_VERIFICATION_SERVICE_ID = Service sid
```

### Twilio 2FA authentication using Authy.
The app is bound to Authy as a 2FA provider. You'll need to create an authy app in the [console](https://www.twilio.com/console/authy/applications) then copy the PRODUCTION API KEY for the newly created app into the following variable.
```dotenv
TWILIO_AUTHY_API_KEY = Authy app's Production API Key
```

Authy require an email and a phone number to create a new user. The email address used with Authy mobile app as an alternative channel for SMS. In our case, we'll opt only for SMS hence we need to use a dummy email address to assign it to the users. Their phone number will be the only identifier.
```dotenv
TWILIO_AUTHY_EMAIL = authy@received.app
```

### SendGrid
Beside SendGrid you can simply use any other mailing service you're subscribed to. The configuration is nearly the same always. Check this [guide](https://sendgrid.com/docs/for-developers/sending-email/laravel/) to learn more how you can set up SendGrid with the current `.env` file.

---

Feel free to create an issue if you have any additional question.
