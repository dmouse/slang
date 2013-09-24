<?php

namespace Drupal\slang\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\Context\ContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Url;

class SlangSettingsForm extends ConfigFormBase {

  protected $configFactory;

  /**
   * {@inheritdoc}
   */

  public function __construct(ConfigFactory $config_factory, ContextInterface $context){
    parent::__construct($config_factory, $context);
  }

  /**
   * {@inheritdoc}
   */
  public static function create (ContainerInterface $container){
    return new static(
      $container->get('config.factory'),
      $container->get('config.context.free')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'slang_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state){
    $config = $this->configFactory->get('slang.settings');

    $form = array();

    $form['url_end_point'] = array(
      '#type' => 'textfield',
      '#title' => t('End Point'),
      '#default_value' => $config->get('url_end_point'),
      '#description' => t('URL end point'),
    );

    $form['time_out'] = array(
      '#type' => 'textfield',
      '#title' => t('Timeout limit'),
      '#default_value' => $config->get('time_out'),
      '#description' => t('time in seconds'),
    );


    $form['#submit'][] = array($this, 'submitForm');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state){
    parent::validateForm($form, $form_state);

    if (!Url::isValid($form_state['values']['url_end_point'], true) ){
      form_set_error('url_end_point', t('Is not a valid URL'));
    }

    if (!intval($form_state['values']['time_out'], 10))
      form_set_error('time_out', t('The request token lifetime must be a non-zero integer value.'));

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->configFactory->get('slang.settings')
      ->set('url_end_point',$form_state['values']['url_end_point'])
      ->set('time_out',$form_state['values']['time_out'])
    ->save();
  }

}
