<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      title="API Budget Buddy",
 *      version="0.0.1",
 *      description="API Budget Buddy",
 *      @OA\Contact(
 *          name="Andre Markov",
 *          email="andremarkov@icloud.com"
 *      )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
