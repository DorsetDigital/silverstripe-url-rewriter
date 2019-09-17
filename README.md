# Silverstripe URL Rewriter

Provides a simple method of rewriting the URLs of assets.


# Requirements
*Silverstripe 4.x

# Installation
* Install the code with `composer require dorsetdigital/silverstripe-url-rewriter`
* Run a `dev/build?flush` to update your project

# Usage

The module won't make any changes to your site unless you do a bit of configuration.  There are a few options you can set, done in a yml file:


```yaml
---
Name: rewriteconfig
---

DorsetDigital\URLRewriter\Middleware:
  old_url: 'https://old.example.com'
  new_url: 'https://cdn-distribution.example.com'
  cdn_rewrite: true
  enable_in_dev: true
```

The options are hopefully fairly self explanatory:

* `cdn_rewrite` - globally enables and disables the module (default false - disabled)
* `old_url` - the full URL you wish to rewrite (eg. https://somebucket.s3.aws.com)
* `new_url` - the new URL you wish to use (eg. https://somedistribution.cloudfront.net)
* `enable_in_dev` - enable the CDN in dev mode (default false)

# Notes

* The module is disabled in the CMS / admin system, so rewrites do not currently happen here
* When enabled, the module will always add an HTTP header of `X-Rewrites: Enabled` to show that it's working, even if none of the other rewrite operations are carried out.  If this is not present and you think it should be, ensure that you have set `cdn_rewrite` to true, that you have specified the `cdn_domain` in your config file and that you have `enable_in_dev` set to true if you are testing in dev mode.
 
