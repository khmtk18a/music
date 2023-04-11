<?php

namespace App\Components;

use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;
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

    public function __construct(private Client $client, private HttpClientInterface $httpClient)
    {
    }

    #[LiveProp(writable: true)]
    public string $keywords = '';

    #[LiveProp]
    public ?\SimpleXMLElement $trackList = null;

    /**
     * Song search.
     *
     * @return array<array-key,mixed>
     */
    public function getSong(): array
    {
        /** @var Query */
        $select = $this->client->createSelect();
        $select->setRows(20);

        if ('' !== $this->keywords) {
            $select->setQuery(sprintf('name:"%s"', $this->keywords));
        }

        /** @var Result */
        $resultSet = $this->client->execute($select);

        return $resultSet->getData();
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
