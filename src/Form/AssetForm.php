<?php

namespace Drupal\drupal_assets_attachment\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Executable\ExecutableManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AssetForm.
 */
class AssetForm extends EntityForm {

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * The Asset entity.
   *
   * @var \Drupal\drupal_assets_attachment\Entity\Asset
   */
  protected $entity;

  /**
   * Array of conditions Ids
   *
   * @var string[]
   */
  protected $conditions_list = [
    'node_type',
    'request_path',
    'user_role',
  ];

  /**
   * Constructs a ContainerForm object.
   *
   * @param \Drupal\Core\Executable\ExecutableManagerInterface $condition_manager
   *   The ConditionManager for building the insertion conditions.
   */
  public function __construct(ExecutableManagerInterface $condition_manager) {
    $this->conditionManager = $condition_manager;
  }

  /**
   * {@inheritdoc}
   *
   * This routine is the trick to DependencyInjection in Drupal. Without it the
   * __construct method complains of no arguments.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.condition')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $asset = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $asset->label(),
      '#description' => $this->t("Label for the Asset."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $asset->id(),
      '#machine_name' => [
        'exists' => '\Drupal\drupal_assets_attachment\Entity\Asset::load',
      ],
      '#disabled' => !$asset->isNew(),
    ];

    $form['type'] = [
      '#type' => 'radios',
      '#title' => $this->t('File to attach'),
      '#required' => TRUE,
      '#default_value' => $asset->getType(),
      '#options' => [
        'style' => 'Stylesheet (.css)',
        'script' => 'Script (.js)'
      ]
    ];

    $form['file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('File'),
      '#upload_location' => 'public://attached_assets',
      '#upload_validators' => [
        'file_validate_extensions' => ['css js'],
      ],
      '#default_value' => $asset->getFileId(),
      '#required' => TRUE,
    ];


    $form['conditions_tabs'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Attachment Conditions'),
      '#parents' => ['conditions_tabs'],
    ];

    // Build the conditions plugin form.
    $conditions_form = [];
    foreach ($this->conditions_list as $condition_id) {
      $condition = $this->conditionManager->createInstance($condition_id);
      $default_config = $asset->getConditions()[$condition_id] ?? [];

      $condition->setConfiguration($default_config);
      $form_state->set(['conditions', $condition_id], $condition);
      $asset->setConditions($condition_id, $default_config);

      $condition_form['#type'] = 'details';
      $condition_form['#title'] = $condition->getPluginDefinition()['label'];
      $condition_form['#group'] = 'conditions_tabs';
      $conditions_form[$condition_id] = $condition_form + $condition->buildConfigurationForm([], $form_state);
      unset($conditions_form[$condition_id]['negate']);
    }

    $form['conditions_collection'] = $conditions_form;
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $asset = $this->entity;
    $status = $asset->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Asset.', [
          '%label' => $asset->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Asset.', [
          '%label' => $asset->label(),
        ]));
    }
    $form_state->setRedirectUrl($asset->toUrl('collection'));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $form_state->set('file', $form_state->getValue('file')[0]);
    $asset = $this->entity;

    $conditions = $asset->getConditions();
    foreach ($conditions as $condition_id => $condition_config) {
      $condition = $form_state->get(['conditions', $condition_id]);
      $condition->submitConfigurationForm($form, $form_state);
      $this->entity->setConditions($condition_id, $condition->getConfiguration());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);

    if (isset($form_state->getValue('file')[0])) {
      $file_id = $form_state->getValue('file')[0];
      $file_type = $form_state->getValue('type');
      $file = File::load($file_id);

      if (($file->getMimeType() == 'text/css' && $file_type != 'style') || ($file->getMimeType() == 'text/javascript' && $file_type != 'script')) {
        $form_state->setErrorByName('type', $this->t('The uploaded file mime type doesn\'t match the specified type of file'));
        $form_state->setRebuild(TRUE);
      }
    }
  }

}
