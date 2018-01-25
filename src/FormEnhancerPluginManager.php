<?php

/**
 * @file
 * Contains \Drupal\flexiform\FormEnhancerPluginManager
 */

namespace Drupal\flexiform;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a Form Enhancer Plugin Manager.
 */
class FormEnhancerPluginManager extends DefaultPluginManager {

  /**
   * Constructs a FormEnhancerPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/FormEnhancer', $namespaces, $module_handler, 'Drupal\flexiform\FormEnhancer\FormEnhancerInterface', 'Drupal\flexiform\Annotation\FormEnhancer');
    $this->alterInfo('flexiform_form_enhancer');
    $this->setCacheBackend($cache_backend, 'flexiform_form_enhancer');
  }
}

