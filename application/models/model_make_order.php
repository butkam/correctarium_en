<?php

class Model_Make_Order extends Model
{
  public function setOrderId()
  {
    return md5(uniqid("1e856a8ea4e8f5bb5ce43feb268f937b", true));
  }

  public function saveOrderData($data)
  {
    $connection = new MongoClient();
    $collection = $connection->correctarium->orders;
    
    return $collection->insert($data);
  }
}
