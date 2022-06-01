<?php

namespace System;

class DTO
{
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $item){
            if(property_exists($this, $key)){
                $this->{$key} = $item;
            }
        }
    }
}