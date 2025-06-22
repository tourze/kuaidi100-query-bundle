<?php

namespace Kuaidi100QueryBundle\Service;

use Kuaidi100QueryBundle\Controller\AddressResolutionAction;
use Kuaidi100QueryBundle\Controller\AutoNumberAction;
use Kuaidi100QueryBundle\Controller\PollAction;
use Kuaidi100QueryBundle\Controller\QueryAction;
use Kuaidi100QueryBundle\Controller\SyncLogisticsAction;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouteCollection;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;

#[AutoconfigureTag('routing.loader')]
class AttributeControllerLoader extends Loader implements RoutingAutoLoaderInterface
{
    private AttributeRouteControllerLoader $controllerLoader;

    public function __construct()
    {
        parent::__construct();
        $this->controllerLoader = new AttributeRouteControllerLoader();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->autoload();
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return false;
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->addCollection($this->controllerLoader->load(QueryAction::class));
        $collection->addCollection($this->controllerLoader->load(SyncLogisticsAction::class));
        $collection->addCollection($this->controllerLoader->load(PollAction::class));
        $collection->addCollection($this->controllerLoader->load(AddressResolutionAction::class));
        $collection->addCollection($this->controllerLoader->load(AutoNumberAction::class));
        return $collection;
    }
}
