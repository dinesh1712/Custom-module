<?php

namespace Drupal\vbutility\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\vbutility\services\MyService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Drupal\Component\Serialization\Json;
use Drupal\node\Entity\Node;
use Drupal\rest\ModifiedResourceResponse;


/**
 * Provides a resource to perform CRUD operations.
 * @RestResource(
 *   id = "Vbutility_resource",
 *   label = @Translation("Custom CRUD Rest Resource for nodes"),
 *   uri_paths = {
 *     "canonical" = "/vb/operations/{id}",
 *     "create" = "/vb/create"
 *   }
 * )
 */

class UtiltyService extends ResourceBase{
    /**
     * My service
     * 
     * @var \Drupal\vbutility\services\MyService
     */
    protected $myService;
    /**
     * ServiceInfoController constructor.
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param array $serializer_formats
     *   The available serialization formats.
     * @param \Psr\Log\LoggerInterface $logger
     *   A logger instance.
     * @param \Drupal\vbutility\service\services $my_service
     *   The custom service object.
    */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        array $serializer_formats,
        LoggerInterface $logger,
        MyService $my_service) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
        $this->myService = $my_service;
    }
 
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
        $configuration,
        $plugin_id,
        $plugin_definition,
        $container->getParameter('serializer.formats'),
        $container->get('logger.factory')->get('my_custom_log'),
        $container->get('vbutility.Firstservice'),
        );
    }
    
    public function get($data=NULL){
        $class_id = \Drupal::requestStack()->getCurrentRequest()->get('id');
        $node=$this->myService->getNode($class_id);
        $response =['title'=>$node->getTitle(),
                    'body'=> $node->get('body')->value];
        return new ResourceResponse($response);
    }
    public function put($data=NULL){
        $request = \Drupal::request();
        $data = $request->getContent();
        $data=json_decode($data);
        // dump($data);
        $class_id = \Drupal::requestStack()->getCurrentRequest()->get('id');
        $node = Node::load($class_id);
        //set value for field
        $node->body->value = $data->body;
        $node->save();
        $response =['title'=>$node->getTitle().' upadated',
                    'body'=> $node->get('body')->value];
        return new ResourceResponse($response);
    }
    public function post($data){
        $node = array(
              'type' => $data['type'],
              'title' => $data['title'],
              'body' => $data['body'],);
        $newNode = $this->myService->createNode($node);
        $response =['message'=> 'Article node is created',
                    'id'=>$newNode->id()];
        return new ResourceResponse($response);
    }
    public function delete(){
        $class_id = \Drupal::requestStack()->getCurrentRequest()->get('id');
        $node = Node::load($class_id);
        $node->delete();
        return new ModifiedResourceResponse(['error' => NULL], 200);
    }
}
