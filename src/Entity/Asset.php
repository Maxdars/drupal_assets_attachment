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

}
