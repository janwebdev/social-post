[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/janwebdev/social-post/badges/quality-score.png)](https://scrutinizer-ci.com/g/janwebdev/social-post/?branch=master)
[![Build Status](https://api.travis-ci.org/janwebdev/social-post.svg?branch=master)](https://www.travis-ci.org/janwebdev/social-post)
[![Coverage Status](https://coveralls.io/repos/github/janwebdev/social-post/badge.svg?branch=master)](https://coveralls.io/github/janwebdev/social-post?branch=master)
[![Latest Stable Version](https://poser.pugx.org/janwebdev/social-post/version)](https://packagist.org/packages/janwebdev/social-post)
[![Total Downloads](https://poser.pugx.org/janwebdev/social-post/downloads)](https://packagist.org/packages/janwebdev/social-post)

----
## What's this?
This is a library that provides API for creating posts on Facebook and Twitter. It was cloned from [this repo](https://github.com/martin-georgiev/social-post) and refactored to support PHP 8.1+


----
## How to install it?
Recommended way is through [Composer](https://getcomposer.org/download/)

    composer require janwebdev/social-post
    

----
## Additional info

Facebook doesn't support non-expiring user access tokens. Instead, you can obtain a permanent page access token. When using such tokens you can act and post as the page itself. More information about the page access tokens from the official [Facebook documentation](https://developers.facebook.com/docs/facebook-login/access-tokens/expiration-and-extension#extendingpagetokens). Some Stackoverflow answers ([here](https://stackoverflow.com/a/21927690/3425372) and [here](https://stackoverflow.com/a/28418469/3425372)) also may help. 

----
## License
This package is licensed under the MIT License.
