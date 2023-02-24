<?php

namespace Drupal\vbutility\services;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Controller\ControllerBase;

class myService extends ControllerBase{
    // private $state;
    public  $entityTypeManager;
    
    public function __construct(EntityTypeManager $entityTypeManager){
        
        $this->entityTypeManager = $entityTypeManager;
    }

    public function getNodes(){
        $query = $this->entityTypeManager->getStorage('node')->getQuery();
        $nids = $query->condition('type', 'article')
      ->condition('status', '1')
      ->execute();
      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
      return $nodes;
    }
    public function getNode($id){
        $node = $this->entityTypeManager->getStorage('node')->load($id);
        return $node;
    }
    public function getUsers(){
        $userStorage = $this->entityTypeManager->getStorage('user');
        $query = $userStorage->getQuery();
        $uids = $query
        ->condition('status', '1')
        ->condition('roles', 'content_editor')
        ->execute();

        $users = $userStorage->loadMultiple($uids);
        return $users;
    }

    public function createNode($va){
        $node = $this->entityTypeManager->getStorage('node')->create($va);
        $node->save();
        return $node;
    }
}