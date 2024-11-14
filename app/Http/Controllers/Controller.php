<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;
#[
    OA\Info( version: "1.0.0",  description: "Laravel API Documentation",  title: "Laravel API"),
    OA\Server( url: "http://localhost:8097/api", description: "Local server"),
    OA\Server( url: "https://api.prod.example.com/api",  description: "Production server" ),
    OA\Server( url: "https://api.staging.example.com/api",  description: "Staging server" )
    ]
abstract class Controller
{
    //
}
