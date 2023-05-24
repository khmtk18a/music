<?php

namespace App\Components;

use Schranz\Search\SEAL\EngineInterface;
use Schranz\Search\SEAL\Search\Condition\SearchCondition;
use Schranz\Search\SEAL\Search\Result;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('search')]
class SearchComponent
{
    use DefaultActionTrait;

    public function __construct(private EngineInterface $engine, private HttpClientInterface $httpClient)
    {
    }

    #[LiveProp(writable: true)]
    public string $keywords = '';

    #[LiveProp]
    public ?\SimpleXMLElement $trackList = null;

    /**
     * Song search.
     */
    public function getSong(): Result
    {
        $searcher = $this->engine->createSearchBuilder()
            ->addIndex('song')
            ->limit(10);

        if ('' !== $this->keywords) {
            $searcher = $searcher->addFilter(new SearchCondition($this->keywords));
        }

        return $searcher->getResult();
    }

    /**
     * Fetch song detail.
     *
     * @return void
     */
    #[LiveAction]
    public function detail(#[LiveArg] string $url)
    {
        $response = $this->httpClient->request('GET', $url);
        if (200 !== $response->getStatusCode()) {
            return;
        }

        $html = $response->getContent();

        preg_match('/key1=([a-zA-Z0-9]+)/', $html, $matches);

        $response = $this->httpClient->request(
            'GET',
            sprintf('https://www.nhaccuatui.com/flash/xml?html5=true&key1=%s', $matches[1])
        );

        $this->trackList = simplexml_load_string($response->getContent());
    }
}
