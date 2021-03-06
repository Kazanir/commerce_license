<?php

namespace Drupal\commerce_license\Plugin\Commerce\LicenseType;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\commerce\BundlePluginInterface;
use Drupal\commerce_license\Entity\LicenseInterface;

/**
 * Defines the interface for payment method types.
 */
interface LicenseTypeInterface extends BundlePluginInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Gets the payment method type label.
   *
   * @return string
   *   The payment method type label.
   */
  public function getLabel();

  /**
   * Builds a label for the given payment method.
   *
   * @param \Drupal\commerce_payment\Entity\PaymentMethodInterface $payment_method
   *   The payment method.
   *
   * @return string
   *   The label.
   */
  public function buildLabel(LicenseInterface $payment_method);

}
