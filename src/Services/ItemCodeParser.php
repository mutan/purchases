<?php

namespace App\Services;

use App\Resources\ItemCode;
use Exception;

class ItemCodeParser
{
    /**
     * @param string $str
     * @return ItemCode
     * @throws Exception
     */
    public function parseString($str): ItemCode
    {
        $str = strtoupper(trim($str));
        if (!preg_match('#^([A-Z]{1,2})(\d+)$#', $str, $matches)) {
            throw new Exception('Not valid item code');
        } else {
            $data = [
                'type' => strtoupper($matches[1]),
                'number' => $matches[2],
            ];
            if (!in_array($data['type'], ItemCode::ALLOWED_TYPES)) {
                throw new Exception('Wrong item type');
            }

            return new ItemCode($data);
        }
    }
}
