<?php

namespace App\Http\Controllers\Client\OrderPost;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\ChangeStatusRequest;
use App\Http\Requests\Orders\OrderServiceRequest;
use App\Interface\OrderServiceInterface;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class ClientOrderController extends Controller
{
    use ApiResponses;
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService){
        $this->orderService = $orderService;
    }


    public function addOrder(OrderServiceRequest $request){
        try {
           return $this->orderService->AddOrder($request);
        }
         catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function pendingOrder(){
        try {
           return $this->orderService->pendingOrder();
        }
         catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function workerChangeStatus(ChangeStatusRequest $request){
        try {
            return $this->orderService->workerChangeStatus($request);
        }
         catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function getMyOrder(){
        try {
            return $this->orderService->getMyOrder();
        }
         catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
