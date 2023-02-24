<?php 
namespace Drupal\vbutility\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\vbutility\Event\ExampleEvent;
// use Drupal\Core\Entity\EntityManager;

class DataForm extends FormBase{
    
   
    public function getFormId() {
        return 'courseForm';
       }


    public function buildForm( array $form,FormStateInterface $form_state) {
        
        
        $form ['name'] = [
            '#type' => 'textfield',
            // '#title' => $this->t('EnterName '),
            '#required' => 'TRUE',
            '#placeholder'=>'course Name'
         ];    
          
        
         $form ['age'] = [
            '#type' => 'number',
            // '#title' => $this->t('Enter course Id '),
            '#required' => 'TRUE',
            '#placeholder'=>'age'
         ];
      
         $form ['address'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Address'),
            '#required' => 'TRUE'
         ];
                
        
         
        $form['actions']['#type'] = 'actions';
        
        
        $form['action']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#button_type' => 'primary',
        ];
        // $form['#theme'] = 'course_add_form';
        return $form;

    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $name = $form_state->getValue('name');
        $age = $form_state->getValue('age');
        $address =$form_state->getValue('address');
        $connection = \Drupal::service("database");

        try{
            $connection
                    ->insert("form_data")
                    ->fields([
                        "name",
                        "age",
                        "address", 
                    ])
                    ->values([
                        $name,
                        $age,
                        $address
                    ])
                    ->execute();}
        catch(Exception $e){
            $this->messenger()->addStatus($this->t('wrong'));
        }
    
        // dump($name,$age,$address);exit;
        // load the Symfony event dispatcher object through services
        $dispatcher = \Drupal::service('event_dispatcher');
        $event = new ExampleEvent($form_state->getValue('name'));
        $dispatcher->dispatch(ExampleEvent::SUBMIT, $event);
        
        \Drupal::messenger()->addMessage(t("Deatils are stored in Database"));
        foreach ($form_state->getValues() as $key => $value) {
          \Drupal::messenger()->addMessage($key . ': ' . $value);
        }
    }
}