<?php

namespace Drupal\drupal_assets_attachment\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Asset entities.
 */
interface AssetInterface extends ConfigEntityInterface {

  /**
   * Sets the Asset entity conditions.
   *
   * @param $condition_id
   *   The condition machine name.
   * @param $condition
   *   The condition configuration.
   */
  public function setConditions($condition_id, $condition);

  /**
   * Gets the Asset entity conditions.
   */
  public function getConditions();

  /**
   * Sets the Asset entity file id.
   *
   * @param $fid
   *   The file id.
   */
  public function setFileId($fid);

  /**
   * Gets the Asset entity file id.
   */
  public function getFileId();

  /**
   * Sets the Asset entity type.
   *
   * @param $type
   *   The asset type (either style or script).
   */
  public function setType($type);

  /**
   * Gets the Asset entity type.
   */
  public function getType();
}
