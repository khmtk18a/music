<?php

namespace App\Components;

use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('search')]
class SearchComponent
{
    use DefaultActionTrait;

    public function __construct(private Client $client)
    {
    }

    #[LiveProp(writable: true)]
    public string $keywords = '';

    /**
     * Song search.
     *
     * @return array<array-key,mixed>
     */
    public function getSong()
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
}
