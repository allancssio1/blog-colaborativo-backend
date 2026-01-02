<?php

namespace App\Infra\Http\Controllers;

class HealthController 
{
  public function check(): void
  {
    http_response_code(200);
    echo json_encode([
      'message' => 'Api Running',
    ]);
  }
}