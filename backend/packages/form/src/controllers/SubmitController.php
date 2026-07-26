<?php
namespace EventLab\Form\Controllers;

class SubmitController
{
    public function handle($args): string
    {
        $action = $args->action ?? null;
        switch ($action) {
            case 'incomming':
                return $this->validateForm($args);
            // validate the form, put in cache, tell handler to handle it

            default:
                if (function_exists('http_response_code')) {
                    http_response_code(400);
                }

                return json_encode([
                    'status'  => 'error',
                    'message' => "Invalid action: '{$action}'",
                ], JSON_PRETTY_PRINT);
        }

    }

    private function validateForm($args)
    {
        // print_r($args);

        // return json_encode([
        //     'status'  => 'error',
        //     'message' => "Error, some fields are mandatory.",
        // ]);

        return json_encode([
            'status'  => 'success',
            'message' => "handling the form.",
        ]);
    }
}
