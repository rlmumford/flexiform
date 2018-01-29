<?php

namespace Drupal\flexiform\Plugin\FormComponentType;

use Drupal\flexiform\FormElementPluginManager;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\WidgetPluginManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\field_ui\Form\EntityDisplayFormBase;
use Drupal\flexiform\FormEntity\FlexiformFormEntityManager;
use Drupal\flexiform\FlexiformEntityFormDisplay;
use Drupal\flexiform\FormComponent\FormComponentBase;
use Drupal\flexiform\FormComponent\ContainerFactoryFormComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Component class for field widgets.
 */
class FormElementComponent extends FormComponentBase implements ContainerFactoryFormComponentInterface {

  /**
   * Element plugin manager service.
   *
   * @var \Drupal\flexiform\FormElementPluginManager
   */
  protected $pluginManager;

  /**
   * The context handler.
   *
   * @var \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected $contextHandler;

  /**
   * The element plugin associated with this componenet.
   *
   * @var \Drupal\flexiform\FormElement\FormElementInterface
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $name, array $options, FlexiformEntityFormDisplay $form_display) {
    return new static(
      $name,
      $options,
      $form_display,
      $container->get('plugin.flexiform.form_element_manager'),
      $container->get('context.handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($name, $options, FlexiformEntityFormDisplay $form_display, FormElementPluginManager $plugin_manager, ContextHandlerInterface $context_handler) {
    parent::__construct($name, $options, $form_display);

    $this->pluginManager = $plugin_manager;
    $this->contextHandler = $context_handler;
  }

  /**
   * Get the form element plugin.
   */
  protected function getPlugin() {
    if (empty($this->plugin)) {
      $this->plugin = $this->pluginManager->createInstance($this->options['form_element'], $this->optiosn['settings']);
      if ($this->plugin instanceof ContextAwarePluginInterface) {
        $this->contextHandler->applyCopntextMapping($this->plugin, $this->getFormEntityManager()->getContexts());
      }
    }

    return $this->plugin;
  }

  /**
   * Render the component in the form.
   */
  public function render(array &$form, FormStateInterface $form_state, RendererInterface $renderer) {
    $element = [
      '#parents' => $form['#parents'],
      '#array_parents' => $form['#array_parents'],
    ];
    $element['#parents'][] = $this->name;
    $element['#array_parents'][] = $this->name;
    $element += $this->getPlugin()->form($element, $form_state);
    $form[$this->name] = $element;
  }

  /**
   * {@inheritdoc}
   */
  public function extractFormValues(array $form, FormStateInterface $form_state) {
    // No form values to extract.
  }

  /**
   * {@inheritdoc}
   */
  public function getAdminLabel() {
    return $this->options['admin_label'];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $sform = [];
    $sform['admin_label'] = [
      '#title' => t('Admin Label'),
      '#description' => t('Only shown on administrative pages'),
      '#type' => 'textfield',
      '#default_value' => $this->options['admin_label'],
      '#required' => TRUE,
    ];
    $sform += $this->getPlugin()->settingsForm($sform, $form_state);
    return $sform;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary += $this->getPlugin()->settingsSummary();
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsFormSubmit($values, array $form, FormStateInterface $form_state) {
    $options['admin_label'] = $values['admin_label'];
    $options += $this->getPlugin()->settingsFormSubmit($values, $form, $form_state);
    return $options;
  }
}