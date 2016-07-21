<?php

namespace App;

class TB
{
    public $a;
    
    //依赖注入:我们的工厂大容器在创建B的时候，发现它还需要A，于是帮我们把A也一起创建了
    //不仅可以在构造方法中注入，也可以在普通的方法里注入，当普通方法被调用的时候
    //laravel发现这个方法的参数里，需要某个对象，它会自动帮我们创建。
    //前提，这些对象laravel都认识即可。比如Request认识，ttt就不认识
    public function __construct(TA $a)
    {
        $this->a =$a;
    }
}
