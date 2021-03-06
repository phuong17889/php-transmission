# PHP Transmission API

[![Build Status](https://travis-ci.org/kleiram/transmission-php.png)](https://travis-ci.org/kleiram/transmission-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kleiram/transmission-php/badges/quality-score.png?s=21ff8ce00b8b381734360405aeaf2b48875a8137)](https://scrutinizer-ci.com/g/kleiram/transmission-php/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4df441fd-864e-4491-a226-b35b79911af8/mini.png)](https://insight.sensiolabs.com/projects/4df441fd-864e-4491-a226-b35b79911af8)

This library provides an interface to the [Transmission](http://transmissionbt.com)
bit-torrent downloader. It provides means to get and remove torrents from
the downloader as well as adding new torrents to the download queue.

## Installation

Installation is easy using [Composer](https://getcomposer.org):

```json
{
    "require": {
        "phuong17889/transmission-php": "dev-master"
    }
}
```

## Usage

Using the library is as easy as installing it:

```php
<?php
use phuong17889\transmission\Transmission;

$transmission = new Transmission();

// Getting all the torrents currently in the download queue
$torrents = $transmission->all();

// Getting a specific torrent from the download queue
$torrent = $transmission->get(1);

// (you can also get a torrent by the hash of the torrent)
$torrent = $transmission->get(/* torrent hash */);

// Adding a torrent to the download queue
$torrent = $transmission->add(/* path to torrent */);

// Removing a torrent from the download queue
$torrent = $transmission->get(1);
$transmission->remove($torrent);

// Or if you want to delete all local data too
$transmission->remove($torrent, true);

// You can also get the Trackers that the torrent currently uses
// These are instances of the Transmission\Model\Tracker class
$trackers = $torrent->getTrackers();

// You can also get the Trackers statistics and info that the torrent currently has
// These are instances of the Transmission\Model\trackerStats class
$trackerStats = $torrent->getTrackerStats();

// To get the start date/time of the torrent in UNIX Timestamp format
$startTime = $torrent -> getStartDate();

// To get the number of peers connected
$connectedPeers = $torrent -> getPeersConnected();

// Getting the files downloaded by the torrent are available too
// These are instances of Transmission\Model\File
$files = $torrent->getFiles();

// You can start, stop, verify the torrent and ask the tracker for
// more peers to connect to
$transmission->stop($torrent);
$transmission->start($torrent);
$transmission->start($torrent, true); // Pass true if you want to start the torrent immediatly
$transmission->verify($torrent);
$transmission->reannounce($torrent);
```

To find out which information is contained by the torrent, check
[`phuong17889\transmission\models\Torrent`](https://github.com/phuong17889/transmission-php/tree/master/src/models/Torrent.php).

By default, the library will try to connect to `localhost:9091`. If you want to
connect to another host or post you can pass those to the constructor of the
`Transmission` class:

```php
<?php
use phuong17889\transmission\Transmission;

$transmission = new Transmission('example.com', 33);

$torrents = $transmission->all();
$torrent  = $transmission->get(1);
$torrent  = $transmission->add(/* path to torrent */);

// When you already have a torrent, you don't have to pass the client again
$torrent->delete();
```

It is also possible to pass the torrent data directly instead of using a file
but the metadata must be base64-encoded:

```php
<?php
use phuong17889\transmission\Transmission;
$transmission = new Transmission('example.com', 33);
$torrent = $transmission->add(/* base64-encoded metainfo */, true);
```

If the Transmission server is secured with a username and password you can
authenticate using the `Client` class:

```php
<?php
use phuong17889\transmission\Client;
use phuong17889\transmission\Transmission;

$client = new Client();
$client->authenticate('username', 'password');
$transmission = new Transmission();
$transmission->setClient($client);
```

Additionally, you can control the actual Transmission setting. This means
you can modify the global download limit or change the download directory:

```php
<?php
use phuong17889\transmission\Transmission;

$transmission = new Transmission();
$session = $transmission->getSession();

$session->setDownloadDir('/home/foo/downloads/complete');
$session->setIncompleteDir('/home/foo/downloads/incomplete');
$session->setIncompleteDirEnabled(true);
$session->save();
```

## Testing

Testing is done using [PHPUnit](https://github.com/sebastianbergmann/phpunit). To
test the application, you have to install the dependencies using Composer before
running the tests:

```bash
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
$ phpunit --coverage-text
```

## Integration into frameworks

Currently, there's a [Symfony](https://github.com/chellem/TransmissionBundle)
bundle in development which allows you to easily use this library within Symfony.

## Changelog

    Version     Changes

    0.1.0       - Initial release

    0.2.0       - Rewrote the entire public API

    0.3.0       - Added support for authentication

    0.4.0       - The library now requires at least PHP 5.3.2
                - Added support for getting files downloaded by torrent
                - Added support for getting trackers used by a torrent
                - Added support for getting peers connected to
                - The torrent now contains:
                    * Whether it is finished
                    * The up- and download rate (in bytes/s)
                    * The size of the download (when completed)
                    * The ETA of the download
                    * The percentage of the download completed
                - Made the authentication more flexible
                - The client now sends an User-Agent header with each request
                - Added support for starting, stopping, veryfing and
                  requesting a reannounce of torrents

    0.5.0       - Fix a bug in the authentication/authorization mechanism
                - A whole lot of other stuff including management of the
                  Transmission session (setting global download speed limit
                  and toggling the speed limit among others).

## License

This library is licensed under the BSD 2-clause license.

    Copyright (c) 2014, Ramon Kleiss <ramonkleiss@gmail.com>
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:

    1. Redistributions of source code must retain the above copyright notice, this
       list of conditions and the following disclaimer.
    2. Redistributions in binary form must reproduce the above copyright notice,
       this list of conditions and the following disclaimer in the documentation
       and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
    ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
    ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
    LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
    ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
    SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

    The views and conclusions contained in the software and documentation are those
    of the authors and should not be interpreted as representing official policies,
    either expressed or implied, of the FreeBSD Project.
