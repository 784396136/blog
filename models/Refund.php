<?php
namespace models;

class Refund extends Base
{
    //下订单
    public function create($sn,$money,$refund_sn)
    {
        $stmt = self::$pdo->prepare("INSERT INTO refund (sn,money,refund_sn) VALUES(?,?,?)");
        $stmt->execute([
            $sn,
            $money,
            $refund_sn,
        ]);
    }

}