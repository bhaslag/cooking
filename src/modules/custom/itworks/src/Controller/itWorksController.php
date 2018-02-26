<?php
/**
 * Created by PhpStorm.
 * User: wihas
 * Date: 12/02/18
 * Time: 13:10
 */

namespace Drupal\itworks\Controller;

use DrupalCodeGenerator\Command\Drupal_8\Controller;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel\KernelEvents;

class itWorksController extends Controller{
  
  public function content() {

    return [
      '#title' => 'my custom title',
      '#markup' => 'It works',
    ];
  }

  public function alsoWorks() {

    return [
      '#title' => 'Also works',
      '#markup' => 'Also works',
    ];
  }

  public function eventPage() {

    \Drupal::service('event_dispatcher')->dispatch(
//      'KernelEvents::REQUEST', new LocaleEvent($, $lids)
    );
    
    return [
      '#title' => 'This is the event page',
      '#markup' => 'Welcome',
    ];
  }

  public function redirectPage() {

    return [
      '#title' => 'This is the redirect page',
      '#markup' => 'Welcome',
    ];
  }
  
}
