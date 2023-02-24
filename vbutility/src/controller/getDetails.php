<?php

namespace Drupal\vbutility\controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\vbutility\services\MyService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class getDetails extends ControllerBase{

    /**
     * My service
     * 
     * @var \Drupal\vbutility\services\MyService
     */
    protected $myService;
    /**
     * ServiceInfoController constructor.
     *
     * @param \Drupal\vbutility\service\services $my_service
     *   The custom service object.
   */
    public function __construct(MyService $my_service) {
        $this->myService = $my_service;
    }
     /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
        $container->get('vbutility.Firstservice'),
        );
    }

    public function getSites(){
        $html = '';
        $nodes=$this->myService->getNodes();
        foreach($nodes as $node){
            $html .= '<h1>'.$node->getTitle().'</h1>';
            $url = $this->t('<img src="@link">',['@link'=>file_create_url($node->field_image->entity->getFileUri())]);
            $html .= $url;
            $html .= '<p>'.$node->get('body')->value.'</p>';
        }
        return [
            '#markup' => $html,
        ];
    }

    public function getuse(){
        $users =$this->myService->getUsers();
        $html = '<table>';
        foreach($users as $user){
            $mail = $user->get('mail')->getString();
            $name = $user->get('name')->getString();
            $html .= '<tr><td>'.$mail.'</td>'.'<td>'.$name.'</td></tr>';
        }
        $html .= '</table>';
        return [
            '#markup' => $html,
        ];
    }
}