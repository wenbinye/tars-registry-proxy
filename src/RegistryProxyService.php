<?php


namespace wenbinye\tars\registry;


use DI\Annotation\Inject;
use kuiper\helper\Arrays;
use wenbinye\tars\client\EndpointF;
use wenbinye\tars\client\QueryFServant;
use wenbinye\tars\rpc\route\Route;

/**
 * Class QueryFServantImpl
 * @package wenbinye\tars\registry\impl
 */
class RegistryProxyService implements QueryFServant
{
    /**
     * @var QueryFServant
     */
    private $registryQueryClient;

    /**
     * @var array
     */
    private $hostMapping;

    /**
     * @var Route[]
     */
    private $routes;

    /**
     * QueryFServantImpl constructor.
     * @Inject({
     *     "hostMapping"="application.registry.host_mapping",
     *     "routes"="application.registry.route_list"
     * })
     */
    public function __construct(QueryFServant $registryQueryClient, array $hostMapping = [], array $routes = [])
    {
        $this->registryQueryClient = $registryQueryClient;
        $this->hostMapping = $hostMapping;
        foreach ($routes as $routeStr) {
            $route = Route::fromString($routeStr);
            $this->routes[$route->getServantName()] = $route;
        }
    }

    /**
     * @inheritDoc
     */
    public function findObjectById($id)
    {
        if (isset($this->routes[$id])) {
            return $this->routeToEndpointList($this->routes[$id]);
        }
        /** @var EndpointF[] $endpoints */
        $endpoints = array_map(function($endpoint) {
            return Arrays::assign(new EndpointF(), get_object_vars($endpoint));
        }, $this->registryQueryClient->findObjectById($id));

        foreach ($endpoints as $endpoint) {
            $endpoint->host = $this->replaceHost($endpoint->host);
        }
        return $endpoints;
    }

    private function routeToEndpointList(Route $route): array
    {
        $endpoints = [];
        foreach ($route->getAddressList() as $address) {
            $endpoint = new EndpointF();
            $endpoint->istcp = ($address->getProtocol() === 'tcp' ? 1 : 0);
            $endpoint->weight = $address->getWeight();
            $endpoint->timeout = $address->getTimeout();
            $endpoint->host = $address->getHost();
            $endpoint->port = $address->getPort();
            $endpoints[] = $endpoint;
        }
        return $endpoints;
    }

    /**
     * @inheritDoc
     */
    public function findObjectById4Any($id, &$activeEp, &$inactiveEp)
    {
        throw new \BadMethodCallException('not implement');
    }

    /**
     * @inheritDoc
     */
    public function findObjectById4All($id, &$activeEp, &$inactiveEp)
    {
        throw new \BadMethodCallException('not implement');
    }

    /**
     * @inheritDoc
     */
    public function findObjectByIdInSameGroup($id, &$activeEp, &$inactiveEp)
    {
        throw new \BadMethodCallException('not implement');
    }

    /**
     * @inheritDoc
     */
    public function findObjectByIdInSameStation($id, $sStation, &$activeEp, &$inactiveEp)
    {
        throw new \BadMethodCallException('not implement');
    }

    /**
     * @inheritDoc
     */
    public function findObjectByIdInSameSet($id, $setId, &$activeEp, &$inactiveEp)
    {
        throw new \BadMethodCallException('not implement');
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