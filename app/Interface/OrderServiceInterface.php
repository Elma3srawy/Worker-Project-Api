<?php

namespace App\Interface;

use App\Http\Requests\Orders\ChangeStatusRequest;
use App\Http\Requests\Orders\OrderServiceRequest;

interface OrderServiceInterface{
    public function AddOrder(OrderServiceRequest $request);
    public function pendingOrder();
    public function workerChangeStatus(ChangeStatusRequest $request);
    public function getMyOrder();
}

?>
