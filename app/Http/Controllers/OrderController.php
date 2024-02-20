<?php
namespace App\Http\Controllers;

use App\Services\IikoService;

class OrderController extends Controller
{
    protected $iikoService;

    public function __construct(IikoService $iikoService)
    {
        $this->iikoService = $iikoService;
    }

    public function index()
    {
        $orders = $this->iikoService->getOrders();
        echo ($orders);
        // Return orders or pass them to a view...
    }
}
