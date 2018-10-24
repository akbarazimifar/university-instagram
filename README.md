## Requirements
- PHP >= 7.1.3
- PDO, MbString and JSON PHP Extensions
- [composer](https://getcomposer.org/download)
- [npm](https://nodejs.org)

## Installation
Laravel configuration:
```shell
git clone https://gitlab.com/SubScript/university-instagram.git
cd university-instagram
composer install
php artisan storage:link
cp .env.example .env
php artisan migrate
```

npm configuration:
```shell
npm install
npm run production
```

## Features
- ✅ Users Management (Registration, Sign-In, Logout, Change Password and Edit Profile).
- ✅ View other users profiles and medias.
- ✅ Users profile search.
- ✅ Add, Edit, Delete and view media (Just in the form of a photo and not a video).
- ✅ Like other users medias.
- ✅ Follow and UnFollow other users.
- ✅ Get list of users (Followings, Followers and Media-Likes).
- ✅ Feeds (Homepage) get latest medias from following users.
- ❌ Add comments on media.