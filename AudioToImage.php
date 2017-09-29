<?php
class AudioToImage{
	var $input_file = "";
	var $bit_depth = 32; // -b
	var $sample_rate = 20; // -r
	var $number_of_channel = 1; // -c
	var $image_width = 200;
	var $image_height = 40;
	function __construct($input = NULL)
	{
		if($input !== NULL)
		{
			$this->input_file = $input;
		}
	}
	function set_sample_rate($sample_rate)
	{
		$this->sample_rate = $sample_rate;
	}
	function set_bit_depth($bit_depth)
	{
		$this->bit_depth = $bit_depth;
	}
	function set_number_of_channel($number_of_channel)
	{
		$this->number_of_channel = $number_of_channel;
	}
	function set_image_width($image_width)
	{
		$this->image_width = $image_width;
	}
	function set_image_height($image_height)
	{
		$this->image_height = $image_height;
	}
	function generate_png()
	{
		$data = shell_exec("sox suara.wav -b $bit_depth -c $number_of_channel -r $sample_rate -t raw - | od -t u1 -v - | cut -c 9- | sed -e 's/\ / /g' -e 's/ / /g' -e 's/ /,/g' | tr '\n' ','");
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = str_replace(",,", ",0,", $data); 
		$data = trim($data, ",");
		$wave = explode(",", $data);
		$number_of_sample = count($wave);
		$factor = $number_of_sample/$this->image_width; // float
		$samples = array();
		$image = imagecreatetruecolor($this->image_width, $this->image_height);
		
		$x1 = 0; $y1 = 0; $x2 = $this->image_width - 1; $y2 = $this->image_height - 1;
		$white = imagecolorallocate($image, 255, 255, 255);
		$black = imagecolorallocate($image, 0, 0, 0);
		ImageFilledRectangle($image , $x1 , $y1 , $x2 , $y2 , $white);
		for($i = 0; $i < $this->image_width; $i++)
		{
			$j = round($factor*$i);
			if($j < 0)
			{
				$j = 0;
			}
			if($j >= $number_of_sample)
			{
				$j = $number_of_sample - 1;
			}
			$samples[$i] = round($wave[$j] * $this->image_height / 256);
			$y1 = round(($this->image_height/2) - ($samples[$i]/2));
			$y2 = round(($this->image_height/2) + ($samples[$i]/2));
			$x1 = $x2 = $i;
			imageline($image, $x1, $y1, $x2, $y2, $black);
		}
		imagecolortransparent($image, $white);
		return $image;
	}
}

$wave2png = new AudioToImage("suara.wav");
$image = $wave2png->generate_png();
header("Content-Type: image/png");
imagepng($image);

?>