<?php

namespace Kuaidi100QueryBundle\Service;

use Kuaidi100QueryBundle\Controller\AddressResolutionController;
use Kuaidi100QueryBundle\Controller\AutoNumberController;
use Kuaidi100QueryBundle\Controller\PollController;
use Kuaidi100QueryBundle\Controller\QueryController;
use Kuaidi100QueryBundle\Controller\SyncLogisticsController;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouteCollection;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;

#[AutoconfigureTag(name: 'routing.loader')]
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
        return 'kuaidi100_controller' === $type;
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->addCollection($this->controllerLoader->load(QueryController::class));
        $collection->addCollection($this->controllerLoader->load(SyncLogisticsController::class));
        $collection->addCollection($this->controllerLoader->load(PollController::class));
        $collection->addCollection($this->controllerLoader->load(AddressResolutionController::class));
        $collection->addCollection($this->controllerLoader->load(AutoNumberController::class));

        return $collection;
    }
}
