<?php
/**
 * @file
 * Contains \Drupal\aiquiz_generator\Form\QuizMaker.
 */
namespace Drupal\aiquiz_generator\Form;

use GuzzleHttp\Client;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class QuizMaker extends FormBase {
  /**
   * {@inheritdoc}
   */

  protected $messenger;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger')
    );
  }

  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  public function getFormId() {
    return 'aiquiz_generator_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['text'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'placeholder' => t('Copy & paste the text you would like to generate a quiz for...'),
      ],
      '#title' => t('Enter Text:'),
      '#maxlength' => 400, // 100 chars max (should pull this from a config var)
      '#required' => TRUE,
    ];

    // $form['difficulty'] = [
    //   '#type' => 'select',
    //   '#title' => t('Difficulty:'),
    //   '#empty_option' => '- SELECT -',
    //   '#options' => [
    //     'Male' => t('Beginners'),
		//     'Female' => t('Intermediate'),
    //     'Other' => t('Advance'),
    //   ],
    // ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate Quiz'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('text');
    if (strlen($value) < 300  ) { // Minimum length is 300 characters
      $form_state->setErrorByName('text', $this->t('Text must be atleast 350 characters.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user_text = $form_state->getValues(); // Get user-submitted text
    $generatedQuestions = $this->getGeneratedQuestions($user_text);
    // Display the generated quiz questions (replace this with your logic).
    if (!empty($generatedQuestions)) {
      // Example: Display questions using a message.
      $this->messenger->addMessage($this->t('Generated Quiz Questions:') . '<br>' . implode('<br>', $generatedQuestions));
    } else {
      $this->messenger->addMessage($this->t('Failed to generate quiz questions.'), 'error');
    }
    // $quiz_questions = []; // Replace this with an array of correct answers for comparison.

    // $score = 0;

    // // Compare user answers with correct answers and calculate score.
    // foreach ($quiz_questions as $key => $quiz_question) {
    //   if (isset($user_answers['question_' . $key]) && $user_answers['question_' . $key] === $correct_answer) {
    //     $score++;
    //   }
    // }

    // // Process or display the score.
    // $this->messenger->addMessage($this->t('Your score: @score / @total', ['@score' => $score, '@total' => count($correct_answers)]));
    // You might want to store or handle the score in a database or further process it as needed.
  }

  // Function to interact with the AI service and get generated questions.
  private function getGeneratedQuestions($user_text) {
    // Use Guzzle or Drupal HTTP client to send a POST request to the AI service's API.
    // Replace this with your API endpoint and authentication details.
    $api_key = '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';
    $endpoint = 'https://api.openai.com/v1/engines/text-davinci-003/completions';
  
    $data = array(
      'prompt' => $user_text,
      'max_tokens' => 100,  // Adjust based on your needs
    );
  
    $headers = array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $api_key,
    );
  
    $client = \Drupal::httpClient();
    try {
      $response = $client->post($endpoint, [
        'headers' => $headers,
        'json' => $data,
      ]);
  
      $body = json_decode($response->getBody());
      return $body->choices[0]->text;
    } catch (\Exception $e) {
      // Handle error (log or display error message)
      return '';
    }
    
    // $client = new Client();
    // $apiEndpoint = 'https://api.openai.com/v1/engines/text-davinci-003/completions';
    // $apiKey = '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';

    // try {
    //   $response = $client->post($apiEndpoint, [
    //     'headers' => [
    //       'Authorization' => 'Bearer ' . $apiKey,
    //       'Content-Type' => 'application/json',
    //     ],
    //     'json' => ['text' => $user_text],
    //   ]);

    //   $data = json_decode($response->getBody(), TRUE);

    //   // Extract and return the generated quiz questions from the API response.
    //   return $data['quiz_questions'] ?? [];
    // } catch (\Exception $e) {
    //   \Drupal::logger('aiquiz_generator')->error('API request failed: @error', ['@error' => $e->getMessage()]);
    //   return [];
    // }
  }
}