<?php

namespace Drupal\drupal_assets_attachment\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Asset entity.
 *
 * @ConfigEntityType(
 *   id = "asset",
 *   label = @Translation("Asset"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\drupal_assets_attachment\AssetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\drupal_assets_attachment\Form\AssetForm",
 *       "edit" = "Drupal\drupal_assets_attachment\Form\AssetForm",
 *       "delete" = "Drupal\drupal_assets_attachment\Form\AssetDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\drupal_assets_attachment\AssetHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "asset",
 *   admin_permission = "administer site configuration",
 *   config_export = {
 *     "id",
 *     "label",
 *     "file",
 *     "type",
 *     "conditions"
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/asset/{asset}",
 *     "add-form" = "/admin/structure/asset/add",
 *     "edit-form" = "/admin/structure/asset/{asset}/edit",
 *     "delete-form" = "/admin/structure/asset/{asset}/delete",
 *     "collection" = "/admin/structure/asset"
 *   }
 * )
 */
class Asset extends ConfigEntityBase implements AssetInterface {

  /**
   * The Asset ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Asset label.
   *
   * @var string
   */
  protected $label;

  /**
   * The asset file id.
   *
   * @var integer
   */
  protected $file;

  /**
   * The asset type (style or script).
   *
   * @var string
   */
  protected $type;

  /**
   * The insertion conditions.
   *
   * Each item is the configuration array.
   *
   * @var array
   */
  protected $conditions = [];

  /**
   * {@inheritdoc}
   */
  public function setConditions($condition_id, $condition) {
    $this->conditions[$condition_id] = $condition;
  }

  /**
   * {@inheritdoc}
   */
  public function getConditions() : array {
    return $this->conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function setFileId($fid) {
    return $this->file = $fid;
  }

  /**
   * {@inheritdoc}
   */
  public function getFileId() {
    return $this->file;
  }

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    return $this->type = $type;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->type;
  }

}
