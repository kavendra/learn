<?php

return array(

  /*
  |--------------------------------------------------------------------------
  | Integrator Key
  |--------------------------------------------------------------------------
  | An Integrator Key is a unique ID for each DocuSign integration
  | Having an Integrator Key lets DocuSign “tag” each integration
  | and provides an additional layer of security
  |
  */

  'integrator_key' => env('DOCUSIGN_INTEGRATOR_KEY', ''),

  /*
  |--------------------------------------------------------------------------
  | The Docusign Account Email
  |--------------------------------------------------------------------------
  | Email of the Docusign Account Owner
  |
  */

  'email' => env('DOCUSIGN_EMAIL', ''),

  /*
  |--------------------------------------------------------------------------
  | The Docusign Account Password
  |--------------------------------------------------------------------------
  |
  |
  */

  'password' => env('DOCUSIGN_PASSWORD', ''),

  /*
  |--------------------------------------------------------------------------
  | The version of DocuSign API
  |--------------------------------------------------------------------------
  | Options: (Ex: v1, v2)
  |
  */

  'version' => env('DOCUSIGN_VERSION', ''),

  /*
  |--------------------------------------------------------------------------
  | The DocuSign Environment
  |--------------------------------------------------------------------------
  | (options: demo, test, www)
  |
  */

  'environment' => env('DOCUSIGN_ENVIRONMENT', ''),

  /*
  |--------------------------------------------------------------------------
  | The DocuSign Account Id
  |--------------------------------------------------------------------------
  |
  |
  */
  'account_id' => env('DOCUSIGN_ACCOUNT_ID', ''),
);
