<?php
class format {
	/**
	 * 日期格式化
	 * 
	 * @param $timestamp
	 * @param $showtime
	 */
	public static function date($timestamp, $showtime = 0) {
		$times = intval($timestamp);
		if(!$times) return true;
		$lang = pc_base::load_config('system','lang');
		if($lang == 'zh-cn') {
			$str = $showtime ? date('Y-m-d H:i:s',$times) : date('Y-m-d',$times);
		} else {
			$str = $showtime ? date('m/d/Y H:i:s',$times) : date('m/d/Y',$times);
		}
		return $str;
	}
	
	/**
	 * 获取当前星期
	 * 
	 * @param $timestamp
	 */
	public static function week($timestamp) {
		$times = intval($timestamp);
		if(!$times) return true;
		$weekarray = array(L('Sunday'),L('Monday'),L('Tuesday'),L('Wednesday'),L('Thursday'),L('Friday'),L('Saturday')); 
		return $weekarray[date("w",$timestamp)]; 
	}

    /**
     * 持续天数，不满一天按一天计算
     */
	public static function holdday($starttimestamp, $endtimestamp) {
        $start = intval($starttimestamp);
        $end = intval($endtimestamp);
        $hold = $end - $start;
        if ($hold < 0) {
            return 1;
        }
        $days = ceil($hold/(24*3600));
        return $days;
	}
}
?>
