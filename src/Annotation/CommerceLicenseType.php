<?php

namespace Drupal\commerce_license\Annotation;

use Drupal\Component\Annotation\Plugin;

/** * Defines the payment type plugin annotation object.
 *
 * Plugin namespace: Plugin\Commerce\LicenseType.
 *
 * @see plugin_api
 *
 * @Annotation
 */
class CommerceLicenseType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The payment type label.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;
}