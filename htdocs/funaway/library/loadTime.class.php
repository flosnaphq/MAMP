<?php
class loadTime
{
    private $time_start     =   0;
    private $time_end       =   0;
    private $time           =   0;
    
	public function startTimer()
	{
        $this->time_start= microtime(true);
	}
	
    public function stopTimer()
	{
		$this->time_end = microtime(true);
        $this->time = $this->time_end - $this->time_start;
        echo "Loaded in " . $this->time . " seconds\n";
	}
}

?>