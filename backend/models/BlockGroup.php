<?php

/* 
 * Author : Peter Odon
 * Author : peter@audmaster.com
 * Each line should be prefixed with  * 
 */
namespace backend\models;
use Yii;

class BlockGroup extends \yii\db\ActiveRecord
{
   public static function tableName()
    {
        return 'tbl_block_group';
    }
}
