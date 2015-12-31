<?php

class RandomImage {
  private $width = 640;
  private $height = 480;

  private $step = 20;

  private $format = 'png';

  private $image = null;

  private function __construct($width, $height, $step, $format) {
    $this->width = $width;
    $this->height = $height;
    $this->step = $step;
    $this->format = $format;
  }

  private function randomColor() {
    $hex = [];
    for ( $i = 0; $i < 3; $i++ ) {
      $hex[] = str_pad(
        dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT
      );
    }

    return join('', $hex);
  }

  public function generate() {
    $this->image = new Imagick;
    $this->image->newImage(
      $this->width, $this->height,
      new ImagickPixel('#000')
    );
    $this->image->setImageFormat($this->format);

    $rstep = 0;
    for ( $row = 0; $row < $this->width / $this->step; $row++ ) {
      $cstep = 0;

      for ( $col = 0; $col < $this->height / $this->step; $col++ ) {
        $image = new Imagick;
        $image->newImage(
          $this->step, $this->step,
          new ImagickPixel('#' . $this->randomColor())
        );
        $image->setImageFormat($this->format);

        $this->image->compositeimage(
          $image->getImage(),
          Imagick::COMPOSITE_COPY,
          $rstep * $this->step,
          $cstep * $this->step
        );

        $image->destroy();

        $cstep++;
      }

      $rstep++;
    }

    header("Content-type: image/{$this->format}");

    $this->image->blurImage(50, 50, 5);
    $this->image->brightnessContrastImage(-5, 20);

    return $this;
  }

  public function init($width = 100, $height = 100, $step = 25, $format = 'png') {
    return new self($width, $height, $step, $format);
  }

  public function save( $path ) {
    if ( !$this->image ) {
      throw new \Exception("Generate image first", 1);
    }

    $this->image->writeimage($path);
    $this->image->destroy();
  }

  public function output() {
    echo $this->image;
    $this->image->destroy();
  }
}

// RandomImage::init(640, 480, 20)
//   ->generate()
//   ->save('test.png');
