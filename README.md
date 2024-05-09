# Simple PayPal REST API example with PHP (and AJAX)

[![Project status: active – The project has reached a stable, usable state and is being actively developed.](https://www.repostatus.org/badges/latest/active.svg)](https://www.repostatus.org/#active)
[![Project releases](https://img.shields.io/github/v/release/tozielinski/pp-php-ec-example?logo=github&include_prereleases)](https://github.com/tozielinski/pp-php-ec-example/releases)
[![Project contributors](https://img.shields.io/github/contributors/tozielinski/pp-php-ec-example?logo=github)](https://github.com/tozielinski/pp-php-ec-example/graphs/contributors)
[![Project license](https://img.shields.io/github/license/tozielinski/pp-php-ec-example?logo=github)](https://github.com/tozielinski/pp-php-ec-example/LICENSE)
<!-- [![Project build Status](https://badges.netlify.com/api/docsydocs.svg?branch=main)](https://app.netlify.com/sites/docsydocs/deploys) -->

## Usage

You have to create PayPal REST sandbox credentials and copy/move config/Config.php.sav to config/Config.php. Insert the credentials into config/Config.php as client_id and client_secret for the correct environment.

## Attention

the approval window will not close automatically, because the return_url is missing in the payload/payment_source/experience_context! Close the tab or window manually and go back to the checkout-window.

