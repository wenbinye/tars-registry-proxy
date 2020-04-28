<?php


namespace wenbinye\tars\registry\impl;


use DI\Annotation\Inject;
use kuiper\di\annotation\Service;
use wenbinye\tars\client\EndpointF;
use wenbinye\tars\client\QueryFServant as QueryFServantClient;
use wenbinye\tars\registry\servant\QueryFServant;

/**
 * Class QueryFServantImpl
 * @package wenbinye\tars\registry\impl
 * @Service()
 */
class QueryFServantImpl implements QueryFServant
{
    /**
     * @var QueryFServantClient
     */
    private $registryQueryClient;

    /**
     * @var array
     */
    private $hostMapping;

    /**
     * QueryFServantImpl constructor.
     * @Inject({"hostMapping"="registry.host_mapping"})
     * @param QueryFServantClient $registryQueryClient
     * @param array $hostMapping
     */
    public function __construct(QueryFServantClient $registryQueryClient, array $hostMapping)
    {
        $this->registryQueryClient = $registryQueryClient;
        $this->hostMapping = $hostMapping;
    }

    /**
     * @inheritDoc
     */
    public function findObjectById($id)
    {
        /** @var EndpointF[] $endpoints */
        $endpoints = $this->registryQueryClient->findObjectById($id);

        foreach ($endpoints as $endpoint) {
            $endpoint->host = $this->replaceHost($endpoint->host);
        }
        return $endpoints;
    }



    /**
     * @inheritDoc
     */
    public function findObjectById4Any($id, &$activeEp, &$inactiveEp)
    {
        // TODO: Implement findObjectById4Any() method.
    }

    /**
     * @inheritDoc
     */
    public function findObjectById4All($id, &$activeEp, &$inactiveEp)
    {
        // TODO: Implement findObjectById4All() method.
    }

    /**
     * @inheritDoc
     */
    public function findObjectByIdInSameGroup($id, &$activeEp, &$inactiveEp)
    {
        // TODO: Implement findObjectByIdInSameGroup() method.
    }

    /**
     * @inheritDoc
     */
    public function findObjectByIdInSameStation($id, $sStation, &$activeEp, &$inactiveEp)
    {
        // TODO: Implement findObjectByIdInSameStation() method.
    }

    /**
     * @inheritDoc
     */
    public function findObjectByIdInSameSet($id, $setId, &$activeEp, &$inactiveEp)
    {
        // TODO: Implement findObjectByIdInSameSet() method.
    }

    private function replaceHost(string $host): string
    {
        foreach ($this->hostMapping as $rule => $newHost) {
            if (fnmatch($rule, $host)) {
                return $newHost;
            }
        }
        return $host;
    }
}