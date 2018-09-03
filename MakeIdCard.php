<?php

/**
 * Created by PhpStorm.
 * User: 27394
 * Date: 2017/3/15
 * Time: 10:54
 */
class idcard_gen
{
    protected        $_debug    = false;//是否打印DEBUG信息
    protected        $_idcard   = '';//身份证号
    protected        $_end      = false;
    protected        $pow       = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];//权重
    protected        $szVerCode = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];//检验码
    protected static $_instance;//

    //防止用户跳过单例模式
    protected function __construct()
    {
    }

    /*
     * 设置初始身份证号码
     **/
    protected function init($idcard)
    {
        if (!$idcard) {
            if (!$this->_idcard) {
                $this->_idcard = '440982' . date('Ymd', rand(strtotime('1950-01-01'), time() - 10 * 86400 * 365));
            }
        } else {
            $this->_idcard = $idcard;
        }
    }

    //将身份证后面四位作为一个整数加一
    protected function idplus()
    {
        $idcard = $this->_idcard;
        if (strlen($idcard) < 18) {
            $idcard = str_pad($idcard, 18, '0', STR_PAD_RIGHT);
        }
        $last = substr($idcard, -4);
        $last++;
        if ($last > 9999) {
            $this->_end = true;

            return;
        }
        $this->_idcard = substr($idcard, 0, -4) . sprintf('%04d', $last);
    }

    /*
     * 产生一个身份证
     * @return string 身份证
     **/
    protected function do_gen()
    {
        //穷举直至算法正确
        do {
            $this->idplus();
            if ($this->_end) {
                return;
            }
            if ($this->_debug) {
                echo 'checking: ' . $this->_idcard . " ...\n";
            }
            $total = 0;
            for ($i = 0, $len = strlen($this->_idcard); $i < ($len - 1); $i++) {
                $power = $this->_idcard[$i] * $this->pow[$i];
                $total += $power;
            }
            $mod = $total % 11;
        } while (substr($this->_idcard, 17, 1) != $this->szVerCode[$mod]);

        return $this->_idcard;
    }

    /*
     * 需要生成几个身份证号码
     * @param int $num 需要生成身份证的个数
     * @return array 身份证
     **/
    protected function multi_gen($num = 1)
    {
        $num = intval($num);
        if ($num < 1) {
            exit('param is not valid');
        }
        $idcards = [];
        for ($i = 0; $i < $num; $i++) {
            if ($tmp = $this->do_gen()) {
                $idcards[] = $tmp;
            }
            if ($this->_end) {
                break;
            }
        }

        return $idcards;
    }

    /*
     * 需要生成几个身份证号码
     * @param string $idcard 初始化身份证前面14位。如果不填，则系统自动生成一个。
     * @param int $num 需要生成身份证的个数
     * @return array 身份证
     **/
    public static function gen($num = 1, $idcard = '')
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        self::$_instance->init($idcard);

        return self::$_instance->multi_gen($num);
    }

}

$cards = idcard_gen::gen(10);
print_r($cards);