<?php

namespace Drupal\drupal_assets_attachment\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Executable\ExecutableManagerInterface;
use Drupal\file\Entity\File;
use Drupal\user\Entity\User;

/**
 * Class AssetsAttachmentManager.
 *
 * @package Drupal\drupal_assets_attachment\Manager
 */
class AssetsAttachmentManager {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * Constructs a new AttachedAssetsService class.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\Core\Executable\ExecutableManagerInterface $condition_manager
   *   The ConditionManager for building the insertion conditions.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ExecutableManagerInterface $condition_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->conditionManager = $condition_manager;
  }

  /**
   * Add the asset to the attachments array.
   *
   * @param array $attachments
   *   The page attachments array.
   */
  public function attachAssets(array &$attachments) {
    $assets = $this->loadAttachedAssets();
    foreach ($assets as $asset) {
      if (!$this->evaluateCondition($asset)) {
        continue;
      }

      $type = $asset->getType();
      $fid = $asset->getFileId()[0];
      $file = File::load($fid);
      $url = file_url_transform_relative(file_create_url($file->getFileUri()));

      switch ($type) {
        case 'style' :
          $attachments['#attached']['html_head_link'][] = $this->attachStyle($url);
          break;

        case 'script' :
          $attachments['#attached']['html_head'][] = $this->attachScript($file, $asset->label());
          break;

        default:
          break;
      }

    }
  }

  /**
   * Returns assets entities.
   *
   * @return array
   *   The entities array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function loadAttachedAssets(): array
  {
    $entityManager = $this->entityTypeManager
      ->getStorage('asset');
    $ids = $entityManager->getQuery()
      ->condition('status', 1)
      ->execute();

    return (array) $entityManager->loadMultiple($ids);
  }

  /**
   * Returns a style asset element.
   */
  private function attachStyle($url) {
    return [
      [
        "rel" => 'stylesheet',
        "href" => $url,
        "media" => 'all'
      ],
    ];
  }

  /**
   * Returns a script asset element.
   */
  public function attachScript($file, $assetLabel) {
    return [
      [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#value' => file_get_contents($file->getFileUri()),
      ],
      "attached_asset_{$assetLabel}",
    ];
  }

  /**
   * Evaluates an asset entity conditions.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function evaluateCondition($asset) {
    $conditions = $asset->getConditions();
    $flag = TRUE;
    // Define the context of each condition.
    $contexts = [
      'node_type' => ['node', \Drupal::routeMatch()->getParameter('node')],
      'request_path' => [],
      'user_role' => ['user', User::load(\Drupal::currentUser()->id())]
    ];
    foreach ($conditions as $condition_id => $condition_config) {
      /* @var \Drupal\system\Plugin\Condition\RequestPath $condition */
      $condition = $this->conditionManager->createInstance($condition_id);
      $condition->setConfiguration($condition_config);

      if (!empty($contexts[$condition_id])) {
        // Set the condition context.
        $condition->setContextValue($contexts[$condition_id][0], $contexts[$condition_id][1]);
      }

      // Evaluate the condition.
      if (!$condition->evaluate()) {
        return FALSE;
      }
    }

    return $flag;
  }

}
