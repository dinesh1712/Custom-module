<?php

/**
 * @file
 * Contains \Drupal\example_events\ExampleEventSubScriber.
 */

namespace Drupal\vbutility\EventSubscriber;

use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Drupal\vbutility\Event\ExampleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class ExampleEventSubScriber.
 *
 * @package Drupal\example_events
 */
class ExampleEventSubScriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[ConfigEvents::SAVE][] = array('onSavingConfig', 800);
    $events[ExampleEvent::SUBMIT][] = array('doSomeAction', 800);
    return $events;

  }

  /**
   * Subscriber Callback for the event.
   * @param ExampleEvent $event
   */
  public function doSomeAction(ExampleEvent $event) {
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'vbutility';
    $key = 'form_create';
    $to = \Drupal::currentUser()->getEmail();
    $params['message'] = 'Your Mail'.$event->getReferenceID();
    // $params['node_title'] = $entity->label();
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
  
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== true) {
      \Drupal::messenger()->addMessage(t('There was a problem sending your message and it was not sent.'), 'error');
    }
    else {
      \Drupal::messenger()->addMessage(t('Emailsend')); 
    }
    \Drupal::messenger()->addMessage("The Example Event has been subscribed, which has bee dispatched on submit of the form with " . $event->getReferenceID() . " as Reference");
  }

  /**
   * Subscriber Callback for the event.
   * @param ConfigCrudEvent $event
   */
  public function onSavingConfig(ConfigCrudEvent $event) {
    \Drupal::messenger()->addMessage("You have saved a configuration of " . $event->getConfig()->getName());
  }
}