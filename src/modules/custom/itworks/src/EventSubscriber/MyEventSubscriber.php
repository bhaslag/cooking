<?php
/**
 * Created by PhpStorm.
 * User: wihas
 * Date: 13/02/18
 * Time: 13:26
 */

namespace Drupal\itworks\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\RedirectResponse;

//$session =  new Session();
//$session->start();

class MyEventSubscriber implements EventSubscriberInterface {

  public function onMyEvent (GetResponseEvent $event) {

    $current_route = \Drupal::routeMatch()->getRouteName();
    if($current_route == 'itworks.eventpage') {
      $event->setResponse(new RedirectResponse('http://cooking.lxc/redirectpage'));
    }

    var_dump($current_route);
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onMyEvent');
    return $events;
  }
}