<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    protected $table = 'anchong_goods';
    public $timestamps = false;
    protected $primaryKey = 'goods_id';
    protected $fillable = ['title', 'desc'];
    /*
     * 根据条件进行商品查询
     * */
    public function scopeName($query,$keyName,$keySid)
    {
        //两个条件的查询
        return $query->where('title', 'like', "%{$keyName}%")->where('sid','=',$keySid);
    }

    public function scopeType($query,$keyType,$keySid){
        return $query->where('type', 'like', "%{$keyType}%")->where('sid','=',$keySid);
    }}
