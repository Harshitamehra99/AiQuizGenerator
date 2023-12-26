<?php

namespace Drupal\aiquiz_generator\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class QuizController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function generateQuiz() {
    $build = [
      '#markup' => $this->t('Hello World!'),
    ];
    return $build;
  }

}