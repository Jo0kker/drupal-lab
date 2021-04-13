<?php

namespace Drupal\test_one\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class TestOneForm extends FormBase
{

    public function getFormId()
    {
        return 'test_one_form bonjour';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['description'] = [
            '#type' => 'item',
            '#markup' => $this->t('Please enter the title and accept the terms of use of the site.'),
        ];


        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Enter the title of the book. Note that the title must be at least 10 characters in length.'),
            '#required' => TRUE,
        ];
        $form['mail'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Address mail'),
            '#description' => $this->t('Enter your address mail.'),
            '#required' => TRUE,
        ];

        $form['accept'] = array(
            '#type' => 'checkbox',
            '#title' => $this
                ->t('I accept the terms of use of the site'),
            '#description' => $this->t('Please read and accept the terms of use'),
        );


        // Group submit handlers in an actions element with a key of "actions" so
        // that it gets styled correctly, and so that other modules may add actions
        // to the form. This is not required, but is convention.
        $form['actions'] = [
            '#type' => 'actions',
        ];

        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);

        $title = $form_state->getValue('title');
        $mail = $form_state->getValue('mail');
        $accept = $form_state->getValue('accept');

        if (strlen($title) < 10) {
            // Set an error for the form element with a key of "title".
            $form_state->setErrorByName('title', $this->t('The title must be at least 10 characters long.'));
        }
        if (strlen($mail) < 10) {
            // Set an error for the form element with a key of "title".
            $form_state->setErrorByName('mail', $this->t('Address mail too short.'));
        }

        if (empty($accept)){
            // Set an error for the form element with a key of "accept".
            $form_state->setErrorByName('accept', $this->t('You must accept the terms of use to continue'));
        }

    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $messenger = \Drupal::messenger();
        $messenger->addMessage('Title: '.$form_state->getValue('title'));
        $messenger->addMessage('mail: '.$form_state->getValue('mail'));
        $messenger->addMessage('Accept: '.$form_state->getValue('accept'));
        dd($form_state->getValue('title'));
        // Redirect to home.
        $form_state->setRedirect('<front>');
    }
}
