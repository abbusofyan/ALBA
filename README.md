# Starter Kit For Corsiva Lab's Projects
Version 1.0

# NOTE
- Remove git init with the starter kit repo before working then init to the repo the project belongs to.

## Getting started
To make it easy for you to get started with Vuexy, here's a list of recommended next steps.

## Clone source

- Clone full version in bitbucket to get components. refer https://vuexy.phuongluong.space/
- Clone starter kit of Corsiva Lab

```
cd existing_repo
git clone https://bitbucket.org/corsivalabpteltd/vuexy_full_version.git
git clone https://bitbucket.org/corsivalabpteltd/csl_laravel_starter_kit.git

```

## PHP + Node js

- PHP 8.2
- "> Node 20.10.0"

## Start the project

```
config db (.env + create db)
composer install
php artisan migrate
php artisan key:generate
php artisan db:seed
npm install
npm run dev(stag + local) || npm run build (production)
```
***

## Environment
- [ ] SWAGGER
    ```
     - L5_SWAGGER_CONST_HOST (put the host name)
     - L5_SWAGGER_API_DOCUMENTATION (default: api/documentation)
    ```
- [ ] TWILIO (add in if we have)
    ```
     - TWILIO_SID
     - TWILIO_TOKEN
     - TWILIO_NUMBER
    ```
- [ ] PREFIX_API
    ```
     - PREFIX_API = v1 (default: v1)
    ```

## APIs

There are common APIs available, you don't need to do it anymore

- [ ] Authentication
    ```
     - Login (by either phone or email + password) to get verify code
     - Resend verify code
     - Verify
     - Get new token
     - Logout
     - Register
     - Verify Register
     - Forgot password: enter either phone or email to get OTP || Link to reset password.
     - Reset Password: enter either phone or email.
    ```
- [ ] Current User
    ```
     - Get current user
     - Update current user
     - Change Password
    ```
- [ ] User
    ```
     - Delete a user
    ```
- [ ] Giang put your APIs here
    ```
     - Delete a user
    ```
***

# Menu
```
- Get menu list
- Create a new menu
- Update menu
- Delete menu
```
***
# Role + Permission
-  [ ] Role
    ```
     - Get role list
     - Create a new role
     - Update role
     - Delete role
    ```
  
-  [ ] Permission
    ```
     - Get permission list
     - Create a new permission 
     - Update permission 
     - Delete permission
    ```
***
# Theme Options
```
 - Get theme options list
 - Create a new theme option
 - Update theme option
 - Delete theme option
```
***
# User management + Profile
- Quang Vu: put your readme here
***
# Authentication
- Giang: put your readme here
