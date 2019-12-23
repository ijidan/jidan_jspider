<?php
namespace Lib\Util;

class DateUtil {
    // Second amounts for various time increments
    const YEAR = 31556926;
    const MONTH = 2629744;
    const WEEK = 604800;
    const DAY = 86400;
    const HOUR = 3600;
    const MINUTE = 60;
    
    /*
     * 检测是不是当天的时间戳
     */
    public static function isToday($time)
    {
        return date('Y-m-d', NOW) == date('Y-m-d', $time);
    }
    
    
    /*
     * 返回两个时间相差的天数
     */
    public static function diffDays($aTime, $bTime)
    {
        $dayTimeA = strtotime(date('Y-m-d', $aTime));
        $dayTimeB = strtotime(date('Y-m-d', $bTime));
        $offsetTime = abs($dayTimeA - $dayTimeB);
        return intval($offsetTime / 86400);
    }
    
    /**
     * 获取起始日期
     * @return array
     */
    public static function getRecentStartAndEndDay()
    {
        $startDay1 = date("Y-m-d", strtotime("-1 day"));
        $startDay7 = date("Y-m-d", strtotime("-7 day"));
        $startDay30 = date("Y-m-d", strtotime("-30 day"));
        $startDayAll = date("Y-m-d", 0);
        $endDay = date("Y-m-d");
        return [
            "day1"   => [
                "start_day" => $startDay1,
                "end_day"   => $endDay
            ],
            "day7"   => [
                "start_day" => $startDay7,
                "end_day"   => $endDay
            ],
            "day30"  => [
                "start_day" => $startDay30,
                "end_day"   => $endDay
            ],
            "dayAll" => [
                "start_day" => $startDayAll,
                "end_day"   => $endDay
            ],
        ];
    }
    
    /**
     * 获取时间列表
     * @return array
     */
    public static function getRecentDateRange()
    {
        $startDay1 = date("Y-m-d");
        $startDay7 = date("Y-m-d", strtotime("-6 day"));
        $startDay30 = date("Y-m-d", strtotime("-29 day"));
        $endDay = date("Y-m-d");
        $day1 = self::getDateRange($startDay1, $endDay);
        $day7 = self::getDateRange($startDay7, $endDay);
        $day30 = self::getDateRange($startDay30, $endDay);
        $day6 = $day7;
        array_pop($day6);
        $day29 = $day30;
        array_pop($day29);
    
        return [
            "day1"       => $day1,
            "past_day6"  => $day6,
            "day7"       => $day7,
            "past_day29" => $day29,
            "day30"      => $day30
        ];
    }
    
    /**
     * 获取起始时间范围内的日期列表
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @return array
     */
    public static function getDateRange($startDate, $endDate)
    {
        $sTimestamp = strtotime($startDate);
        $eTimestamp = strtotime($endDate);
        $days = ($eTimestamp - $sTimestamp) / 86400 + 1;
        $date = array();
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $sTimestamp + (86400 * $i));
        }
        return $date;
    }
    
    /**
     * 今天时间
     * @return false|string
     */
    public static function getTodayDate()
    {
        return date("Y-m-d");
    }
    
    /**
     * 昨天时间
     * @return false|string
     */
    public static function getYesterdayDate()
    {
        return date("Y-m-d", strtotime("-1 day"));
    }
}