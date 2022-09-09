<?php
declare(strict_types=1);
namespace Brodaty\Application;

use JetBrains\PhpStorm\ArrayShape;
use Laminas\Feed\Reader\Reader;
use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\Socket;
use Laminas\Http\Request;

class ApplicationService
{
    const IMPORT_URL = 'https://justjoin.it/feed.atom';

    #[ArrayShape(['title' => "null|string", 'link' => "null|string", 'description' => "null|string", 'items' => "array"])]
    function init(): array
    {
        $httpClient = new Client(null, [
            'maxredirects'    => 5,
            'strictredirects' => false,
            'useragent'       => 'Laminas_Http_Client',
            'timeout'         => 60,
            'connecttimeout'  => 60,
            'adapter'         => Socket::class,
            'httpversion'     => Request::VERSION_11,
            'storeresponse'   => true,
            'keepalive'       => true,
            'outputstream'    => false,
            'encodecookies'   => true,
            'argseparator'    => null,
            'rfc3986strict'   => false,
            'sslcafile'       => null,
            'sslcapath'       => null,
        ]);

        Reader::setHttpClient($httpClient);

        try {
            $rss = Reader::import(self::IMPORT_URL);
        } catch (\Laminas\Feed\Reader\Exception\RuntimeException $e) {
            // feed import failed
            echo "Exception caught importing feed: {$e->getMessage()}\n";
            exit;
        }

        $dateModified = $rss->getDateModified();
        $countOfItems = $rss->count();
        var_dump([
            'Title' => $rss->getTitle(),
            'Modified' => $dateModified,
            'count' => $countOfItems
        ]);

        $offers = [];
        while ($rss->valid()) {
            $rssOffer = $rss->current();
            $offers[$rssOffer->getId()] = json_encode([
                'dateModified' => $rssOffer->getDateModified()->format(DATE_ATOM),
                'title' => $rssOffer->getTitle(),
                'company' => $rssOffer->getAuthor()['name'],
                'salary' => SalaryHelper::getSalaryFromRSSOffer($rssOffer),
            ]);
            $rss->next();
        }

        return $offers;
    }
}