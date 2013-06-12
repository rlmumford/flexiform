<?php
/**
 * @file
 * Party integrations with flexiform.
 */

/**
 * Getter callback for attached entities.
 */
function flexiform_entity_getter_party_data_set($flexiform, $base_entity, $entities, $namespace, $config) {
  $settings = $flexiform->entities[$namespace];

  // Get the party entity.
  $party = $entities[$settings['parameter']];
  $controller = party_get_crm_controller($party, $config['data_set_name']);
  return $controller->getEntity(0, TRUE);
}

/**
 * Setter callback for attached entities.
 */
function flexiform_entity_setter_party_data_set($flexiform, $base_entity, $entity, $entity_type, $entities, $namespace, $config) {
  $settings = $flexiform->entities[$namespace];

  // Get the party entity. We like to party. We like to, we like to party.
  $party = $entities[$settings['parameter']];
  $controller = party_get_crm_controller($party, $config['data_set_name']);
  $controller->attachEntity($entity);
  $controller->save();
}