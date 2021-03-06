<?php

namespace Drupal\commerce_license\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the License entity.
 *
 * @ingroup commerce_license
 *
 * @ContentEntityType(
 *   id = "commerce_license",
 *   label = @Translation("License"),
 *   label_collection = @Translation("Licenses"),
 *   label_singular = @Translation("license"),
 *   label_plural = @Translation("licenses"),
 *   label_count = @PluralTranslation(
 *     singular = "@count license",
 *     plural = "@count licenses",
 *   ),
 *   bundle_label = @Translation("License type"),
 *   bundle_plugin_type = "commerce_license_type",
 *   handlers = {
 *     "access" = "Drupal\commerce_license\LicenseAccessControlHandler",
 *     "list_builder" = "Drupal\commerce_license\LicenseListBuilder",
 *     "storage" = "Drupal\commerce_license\LicenseStorage",
 *     "form" = {
 *       "default" = "Drupal\commerce_license\Form\LicenseForm",
 *       "checkout" = "Drupal\commerce_license\Form\LicenseCheckoutForm",
 *       "create" = "Drupal\commerce_license\Form\LicenseCreateForm",
 *       "edit" = "Drupal\commerce_license\Form\LicenseForm",
 *       "delete" = "Drupal\commerce_license\Form\LicenseDeleteForm",
 *     },
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "commerce_license",
 *   admin_permission = "administer licenses",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "license_id",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *     "uid" = "uid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/licenses/{commerce_license}",
 *     "create-form" = "/admin/commerce/licenses/create",
 *     "edit-form" = "/admin/commerce/licenses/{commerce_license}/edit",
 *     "delete-form" = "/admin/commerce/licenses/{commerce_license}/delete",
 *     "collection" = "/admin/commerce/licenses",
 *   },
 * )
 */
class License extends ContentEntityBase implements LicenseInterface {
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'uid' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    $bundle_manager = \Drupal::service('plugin.manager.commerce_license_type');
    return $bundle_manager->createInstance($this->bundle());
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }


  /**
   *    * {@inheritdoc}
   *       */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('OWner'))
      ->setDescription(t('The user ID of the license owner.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\commerce_license\Entity\License::getCurrentUserId')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the License is published.'))
      ->setDefaultValue(TRUE);

    $fields['product'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Licensed product'))
      ->setDescription(t('The licensed product.'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'commerce_product_variation')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -1,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the license was created.'));

    $fields['granted'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Granted'))
      ->setDescription(t('The time that the license was granted or activated.'))
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 1,
        'settings' => [
          'date_format' => 'custom',
          'custom_date_format' => 'n/Y',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDefaultValue(0);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the license was last modified.'));

    $fields['expiration'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Expiration'))
      ->setDescription(t('The time that the license will expire, if any.'))
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 1,
        'settings' => [
          'date_format' => 'custom',
          'custom_date_format' => 'n/Y',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDefaultValue(0);

    $fields['autoexpire'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Automatic expiration'))
      ->setDescription(t('Whether or not the license will automatically expire.'))
      ->setDefaultValue(TRUE);

    return $fields;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return [\Drupal::currentUser()->id()];
  }

}
