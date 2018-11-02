<?php 
class Calendar extends FatModel{
	private $year;
	private $month;
	private $data;
	private $calendar;
	public function __construct($month,$year){
		$this->year = $year;
		$this->month = $month;
	}
	
	public function generateMonthCalendar(){
		$this->calendar = array();
		$startDate = $this->year.'-'.$this->month.'-01';
		$startDay = date('w', strtotime($startDate));
		for($j=0;$j<$startDay;$j++){
			$this->calendar[] = ""; 
		}
		
		$d = new DateTime($startDate); 
		$noOfDays =  $d->format( 't' );
		for($i=1;$i<=$noOfDays;$i++){
			$this->calendar[] = $i; 
		}
		
		
		$tdays =  count($this->calendar);
		for($i = $tdays;$i<35;$i++){
			$this->calendar[] = ""; 
		}
		
		return $this->calendar;
	}
	
	public static function getStartDate($defaultDate, $month, $year){
		$dt = $year.'-'.$month.'-1';
		if(strtotime($defaultDate) <= strtotime($dt)){
			return $dt;
		}
		return $defaultDate;
	}
	
	public static function getEndDate($defaultDate, $month, $year){
		$d = new DateTime($year.'-'.$month.'-1'); 
		$dt =  $d->format('Y-m-t');
		if(strtotime($defaultDate) > strtotime($dt)){
			return $dt;
		}
		return $defaultDate;
	}
	
	
	
	
}?>