<?php

namespace Drupal\drupal_assets_attachment\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AssetForm.
 */
class AssetForm extends EntityForm {

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

    /* You will need additional form elements for your custom properties. */

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

}
