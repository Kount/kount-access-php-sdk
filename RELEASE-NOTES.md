kount-access-php-sdk 2.1.1
===========================
**09/11/2017**
* Updated README.md, removed session id config changed mentioned for php.ini.  Session ID is now up to 32 characters, not exactly 32 anymore.

**07/14/2017**

### New features
* added composer support
* Kount_access_exception class handling custom exceptions
* added unit test suite with phpunit

### Improvements
* code cleaned and various small improvements made
* removed help functions which were deprecated.
* separated the function __call_endpoint responsible for making the curl call into it's own separate Kount_access_curl service class.

### Bugfixes
* added definitions for error variables in Kount_access_curl_service
