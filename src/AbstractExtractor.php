<?php

namespace gh640\SjisZip;

interface AbstractExtractor
{

  /**
   * Create an instance.
   */
  public static function create(string $src, string $encoding = 'SJIS');

  /**
   * Create an instance.
   */
  public function __construct($archiver, string $src, string $encoding);

  /**
   * Extract files in zip file.
   */
  public function extract(string $dest);

  /**
   * List files in zip file.
   */
  public function items();

}
