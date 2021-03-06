<?php

namespace Drupal\commerce_license\Plugin\Commerce\EntityTrait;

use Drupal\commerce\Plugin\Commerce\EntityTrait\EntityTraitInterface;
use Drupal\commerce\Plugin\Commerce\EntityTrait\EntityTraitBase;
use Drupal\commerce\BundleFieldDefinition;

/**
 * @CommerceEntityTrait(
 *  id = "commerce_license",
 *  label = @Translation("Provides a license."),
 *  entity_types = {"commerce_product_variation"}
 * )
 */
class PurchasableEntityLicensed extends EntityTraitBase {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    // Builds the field definitions.
    $fields = [];
    $fields['license_type'] = BundleFieldDefinition::create('commerce_plugin_item:commerce_license_type')
      ->setLabel(t('License Type'))
      ->setCardinality(1)
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'commerce_plugin_select',
        'weight' => 20,
      ]);
    return $fields;
  }

}

