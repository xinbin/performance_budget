<?php

namespace Drupal\performance_budget\Entity;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Manipulates entity type information.
 *
 * This class contains primarily bridged hooks for compile-time or
 * cache-clear-time hooks. Runtime hooks should be placed in EntityOperations.
 */
class PerformanceBudgetTypeInfo implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * EntityTypeInfo constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   */
  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }

  /**
   * Adds links to performance_budget entities.
   *
   * This is an alter hook bridge.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface[] $entity_types
   *   The master entity type list to alter.
   *
   * @see hook_entity_type_alter()
   */
  public function entityTypeAlter(array &$entity_types) {
    $entity_type = $entity_types['performance_budget'];
    $entity_type->setLinkTemplate('canonical', "/admin/config/development/performance-budget/{performance_budget}");
  }

  /**
   * Adds operations on performance_budget entities.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity on which to define an operation.
   *
   * @return array
   *   An array of operation definitions.
   *
   * @see hook_entity_operation()
   */
  public function entityOperation(EntityInterface $entity) {
    $operations = [];
    if ($this->currentUser->hasPermission('access performance_budget information')) {
      if ($entity->hasLinkTemplate('canonical')) {
        $operations['performance_budget_view'] = [
          'title' => $this->t('View Run History'),
          'weight' => -1,
          'url' => $entity->toUrl('canonical'),
        ];
      }
      if ($entity->hasLinkTemplate('queue_form')) {
        $operations['web_page_archive_queue'] = [
          'title' => $this->t('Start Run'),
          'weight' => 0,
          'url' => $entity->toUrl('queue_form'),
        ];
      }

    }
    return $operations;
  }

}
